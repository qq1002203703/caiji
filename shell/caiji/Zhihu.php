<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 *  对已入库的知乎问题进行采集
 * ======================================*/
namespace shell\caiji;

use core\Conf;
use core\lib\cache\File;
use extend\HttpClient;
use extend\Selector;
use shell\CaijiCommon;

class Zhihu extends CaijiCommon
{
    public $maxAnswer=30;
    //内容最大长度，mysql text类弄最大长度为65535
    public $maxLength=35535;
    protected $path='cache/shell/caiji/';
    protected $fileBodyName='zhihu';
    protected $task=1;
    protected $stopFile;
    protected $taskName;
    protected $error=[
        -1=>'问题采集结果为false',
        3=>'问题结果截取出错',
        -3=>'问题结果json转数组出错',
        -4=>'无法匹配问题',
        -11=>'答案采集结果为false',
        -12=>'答案404错误',
        -13=>'答案结果json转数组出错',
        -14=>'无法匹配答案',
        1=>'问题404错误',
        2=>'问题已经采集过了'
    ];
    /**
     * @var HttpClient
     */
    public $client;
    public function loop(){
        $this->init();
        while (true){
            $config=Conf::all($this->taskName,false,$this->path,false);
            if($config['is_end']){
                $this->saveTask([
                    'current_id'=>0,
                    'total'=>0,
                    'current_page'=>1,
                    'save_question'=>0,
                    'next'=>'',
                    'group_name'=>'',
                    'is_end'=>0,
                ]);
            }
            $this->run();
            if($this->checkStop())
                break;
        }
    }
    public function run(){
        do{
            $config=Conf::all($this->taskName,false,$this->path,false);
            $data=$this->model->from('caiji_zhihu')->eq('iscaiji',0)->gte('id',$config['current_id'])->ne('group_name',$config['group_name'])->order('id')->find(null,true);
            if(!$data){
                $this->saveTask('is_end',1);
                break;
            }
            $this->outPut(' 开始处理：$id=>'.$data['id'].';from_id=>'.$data['from_id'].'-----------------------'.PHP_EOL);
            $data['id']=(int)$data['id'];
            $data['from_id']=(int)$data['from_id'];
            if($data['id'] !== $config['current_id']){
                $config['current_page']=1;
                $config['next']='';
                $config['current_id']=$data['id'];
                $config['save_question']=0;
                $config['next']='';
                $this->saveTask($config);
            }
            $code=$this->question($data['from_id'],$config['save_question'],$config['current_page'],$config['total'],$config['next']);
            if($code<0){
                $this->outPut(' '.$this->error[$code].PHP_EOL,true);
                break;
            }elseif($code>0){
                $this->outPut(' '.$this->error[$code] . PHP_EOL,true);
                $this->updateQuestion($data['id'],$data['group_name']);
                if($this->checkStop())
                    break;
                continue;
            }else{
                $this->outPut(' 成功采集'.PHP_EOL,true);
                $this->updateQuestion($data['id'],$data['group_name']);
            }
            if($this->checkStop())
                break;
        }while(true);
    }
    //采集问题
    public function question($fid,$save_question=0,$current_page=1,$total=0,$next=''){
        if(!$save_question){
            $url='https://www.zhihu.com/question/'.$fid;
            echo '问题链接：'.$url.PHP_EOL;
            $res=$this->client->http($url);
            //检测是不是false
            if($res===false){
                return -1;//采集结果为false
            }
            //检测是不是404
            if($this->client->getHttpCode()!==200){
                dump($this->client->getHttpCode());
                return 1;
            }
            //截取出有用的代码
            $res=trim(Selector::find($res,'regex,cut','<script id="js-initialData" type="text/json">{%|||%}</script>'));
            if(!$res){
                dump($res);
                return 3;//问题结果截取出错
            }
            $res=json_decode($res,true);
            if(!$res){
                dump($res);
                return -3;//问题结果json转数组出错
            }
            $question=$this->getQuestion($res,$fid);
            if(!$question){
                dump($question);
                return -4;//无法匹配问题
            }
            $this->saveQuestion($question,$fid);

            if($current_page===1){
                if(isset($res['initialState']['entities']['answers'])){
                    $answer=$this->getAnswer($res['initialState']['entities']['answers']);
                }else
                    $answer=[];
                unset($res);
                if($answer){
                    echo '保存答案！'.PHP_EOL;
                    $this->saveAnswer($answer,$fid);
                }
                $this->saveTask('current_page',2);
            }
        }else{
            $question=['next'=>$next,'answer_count'=>$total,'from_id'=>$fid];
        }
        $code=0;
        if($question['answer_count']>5){
            $code=$this->loopAnswer($question['next'],$fid,$current_page);
        }
        return $code;
    }

    protected function saveTask($name,$value=null){
        return Conf::editValue($name,$value,$this->fileBodyName.'_task_'.$this->task,$this->path);
    }

    /** ------------------------------------------------------------------
     * 获取问题
     * @param array $res
     * @param int $fid
     * @return array
     *---------------------------------------------------------------------*/
    protected function getQuestion(&$res,$fid){
        if(!isset($res['initialState']['question']['answers'][$fid]))
            return [];
        $data=[];
        $data['next']=$res['initialState']['question']['answers'][$fid]['next'];
        $data['answer_count']=$res['initialState']['question']['answers'][$fid]['totals'];
        $data['content']=$this->contentFilter($res['initialState']['entities']['questions'][$fid]['detail']);
        $data['title']=$res['initialState']['entities']['questions'][$fid]['title'];
        $data['author']=$res['initialState']['entities']['questions'][$fid]['author']['name'];
        $topics=$res['initialState']['entities']['questions'][$fid]['topics'];
        if($topics){
            $data['tag']=[];
            foreach ($topics as $item){
                $data['tag'][]=$item["name"];
            }
            $data['tag']=implode(',',$data['tag']);
        }
        return $data;
    }

    /** ------------------------------------------------------------------
     * 内容过滤器
     * @param string $str
     * @return string
     *---------------------------------------------------------------------*/
    protected function contentFilter($str){
        return str_replace('知乎','社区',strip_tags(preg_replace([
            '#<noscript>.+?</noscript>#i',
            '#<figure [^>]+>#',
            '#</figure>#',
            '#<img .+?data-actualsrc="([^"]+)"[^>]*>#',
            '#<br/>#',
            '#<p [^>]*>#',
            '#(<br>){2,}#',
            '#<p>\s*(<br>)*\s*</p>#',
        ],[
            '',
            '',
            '',
            '<img src="$1">',
            '<br>',
            '<p>',
            '<br>',
            '',
        ],$str),'<p><img><br>'));
    }

    /** ------------------------------------------------------------------
     * getAnswer
     * @param array $res
     * @return \Generator
     *---------------------------------------------------------------------*/
    public function getAnswer($answerJsonArr){
        foreach ($answerJsonArr as $item){
            yield [
                'from_id'=>$item['id'],
                'username'=>$this->zhihuUserName($item['author']['name']),
                'content'=>$this->contentFilter($item['content']),
                'comment_count'=>$item['commentCount'] ?? $item['comment_count'],
            ];
        }
    }

    protected function newClient($opt=[]){
        if(!$this->client){
            $this->client=new HttpClient($opt);
        }
    }

    /** ------------------------------------------------------------------
     * 保存问题
     * @param array $data
     * @return bool
     *---------------------------------------------------------------------*/
    protected function saveQuestion($data,$fid){
        $next=$data['next'];
        unset($data['next']);
        if($this->model->from('caiji_zhihu')->eq('from_id',$fid)->update($data)){
            $this->saveTask(['save_question'=>1,'next'=>$next,'total'=>$data['answer_count']]);
            echo '  问题更新成功'.PHP_EOL;
            return true;
        }else{
            echo $this->model->getSql().PHP_EOL;
            dump($data);
            exit('问题更新失败');
        }

    }

    /** ------------------------------------------------------------------
     * 保存答案
     * @param object|array $data
     * @param int $fid
     *---------------------------------------------------------------------*/
    protected function saveAnswer($data,$fid){
        foreach ($data as $item){
            if($this->model->from('caiji_zhihu_answer')->eq('from_id',$item['from_id'])->find(null,true)){
                $this->outPut(' 答案已经采集过了：from_id=>'.$item['from_id'].PHP_EOL);
                continue;
            }
            if(strlen($item['content']) >$this->maxLength)
                continue;
            $item['fid']=$fid;
            $item['length']=mb_strlen(strip_tags($item['content']));
            if(!$this->model->from('caiji_zhihu_answer')->insert($item))
                exit('插入答案失败');
        }
    }

    /** ------------------------------------------------------------------
     * 循环获取答案
     * @param string $nextUrl
     * @param int $fid
     * @return array|int
     *---------------------------------------------------------------------*/
    protected function loopAnswer($nextUrl,$fid,$current_page){
        $isEnd=false;
        while ($isEnd==false && $current_page <= $this->maxAnswer){
            $ret=$this->caijiAnswer($nextUrl,$fid);
            if(is_int($ret)){
                return $ret;
            }
            $nextUrl=$ret['next'];
            $isEnd=$ret['is_end'];
            $current_page++;
            $this->saveTask(['next'=>$nextUrl,'current_page'=>$current_page]);
        }
        return 0;
    }

    protected function caijiAnswer($nextUrl,$fid){
        $res=$this->client->http($nextUrl);
        //检测是不是false
        if($res===false)
            return -11;//答案结果为false
        //检测是不是404
        if($this->client->getHttpCode()!==200){
            return -12;//答案404错误
        }
        $res=json_decode($res,true);
        if(!$res)
            return -13;//答案结果json转数组出错
        if(isset($res['data'])){
            $data=$this->getAnswer($res['data']);
            $this->saveAnswer($data,$fid);
        }
        if(isset($res['paging'])){
            return [
                'is_end'=>$res['paging']['is_end'],
                'next'=>$res['paging']['next']
            ];
        }
        return -14;//无法匹配答案
    }

    public function test(){

    }

    protected function init(){
        //停止文件处理
        $this->stopFile=ROOT.'/'.$this->path.'stop/'.$this->fileBodyName;
        if(is_file($this->stopFile.'.lock')){
            rename($this->stopFile.'.lock',$this->stopFile);
        }else{
            if(!is_file($this->stopFile))
                File::write($this->stopFile,'');
        }
        //进度保存文件建立
        $this->taskName=$this->fileBodyName.'_task_'.$this->task;
        if(!is_file(ROOT.'/'.$this->path.$this->taskName.'.php')){
            Conf::write([
                'current_id'=>0,
                'total'=>0,
                'current_page'=>1,
                'save_question'=>0,
                'next'=>'',
                'group_name'=>'',
                'is_end'=>0,
            ],$this->taskName,$this->path);
        }
        $this->newClient(['opt'=>[
            CURLOPT_TIMEOUT=>8,//下载时应该按目标文件大小设置大一点
            CURLOPT_USERAGENT=>'Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; rv:11.0) like Gecko',
            CURLOPT_REFERER=>'https://www.zhihu.com/',
            //CURLOPT_HTTPPROXYTUNNEL=>false,
            //CURLOPT_PROXYTYPE=>CURLPROXY_HTTP,
            //CURLOPT_PROXY=>'http://http-dyn.abuyun.com:9020', //动态
            //CURLOPT_PROXY=>'http://http-cla.abuyun.com:9030', //经典
            //CURLOPT_PROXY=>'http://http-pro.abuyun.com:9010', //专业
            //CURLOPT_PROXYAUTH=>CURLAUTH_BASIC,
            //CURLOPT_PROXYUSERPWD=>'H05K700VIP07918D:1AFA971C73727EFF',
        ]]);
        $this->client->httpSetting([
            'proxy'=>[], //代理ip 端口 种类 格式 ['ip'=>'8.8.8.5','port'=>80,'type'=>'http']
            //'checkProxyPlugin'=>'\shell\caiji\Douban::check_proxy',//检测代理ip的插件
            //'getProxyPlugin'=>'\shell\caiji\Douban::get_proxy', //获取代理ip的插件
            'checkResultPlugin'=>'\shell\caiji\Zhihu::check_result',//检测结果是否正常
            'isProxy'=>false, //是否使用代理ip访问
            'ipExpirationTime'=>280, //ip过期时间 单位秒
            'isOpenCurlTimeInterval'=>true,//是否开启curl访问时间间隔控制
            'curlTimeInterval'=>3200, //curl每次访问的最小时间间隔 单位毫秒
            'isRandomUserAgent'=>true, //是否使用随机ua
            'isAutoReferer'=>true, //是否需要自动获取来路
            'waitNoProxy'=>20, //当无法获得有效的代理ip时，程序进行休眠的时间(单位秒)
            'waitIpLock'=>10000, //当所有ip被封琐时，程序进行休眠的时间(单位毫秒)
            'waitCurlFalse'=>4000,//当curl获取结果为false时 等待多少时间才重新发起下次请求(单位毫秒)
            'tryTimes'=>30,
            'encoding'=>'',
            'stopFile'=>$this->stopFile,
        ]);
    }

    protected function checkStop(){
        return is_file($this->stopFile.'.lock');
    }

    protected function updateQuestion($id,$group_name){
        $this->model->from('caiji_zhihu')->eq('id',$id)->update(['iscaiji'=>1]);
        $this->saveTask('group_name',$group_name);
    }

    static public function check_result($html){
        return strpos($html,'<title data-react-helmet="true">安全验证')===false;
    }

    protected function zhihuUserName($username){
        switch ($username){
            case '「已注销」':
            case '匿名用户':
            case '知乎用户':
                return '隐名埋名';
            default:
                return $username;
        }
    }


//--小组--------------------------------------------------------------------------------------------------------
    public function group(){
        $url='http://bangumi.tv/group/category/all?page=';
        for ($i=1;$i<95;$i++){
            $this->outPut('开始采集第'.$i.'页------'.PHP_EOL);
            $this->newClient();
            $this->client->encoding='UTF-8';
            $res=$this->client->http($url.$i);
            if(!$res){
                $this->outPut('无法获取数据'.PHP_EOL);
                break;
            }
            $res=$this->groupSelector($res);
            if($res){
                $this->saveGroup($res);
            }
            msleep(2000,3000);
        }
    }

    protected function groupSelector($res){
        return Selector::find($res,'regex,multi','#<span class="userImage"><img src="(?P<thumb>[^"]+)" class="avatar"></span>\s*(?P<name>.+?)</a>#',['name','thumb'],'<ul id="memberGroupList" class="browserMedium">{%|||%}</ul>');
    }

    protected function saveGroup($data){
        foreach ($data as $item){
            if($this->model->from('caiji_bangumi_group')->eq('name',$item['name'])->find(null,true))
                continue;
            $item['iscaiji']=1;
            if($this->model->from('caiji_bangumi_group')->insert($item))
                echo '  成功：保存到数据库中'.PHP_EOL;
            else
                echo '  失败：保存到数据库中'.PHP_EOL;
        }
    }

    /** ------------------------------------------------------------------
     * 小组图片下载
     *---------------------------------------------------------------------*/
    public function groupD(){
        $table='caiji_bangumi_group';
        $where=[['isdone','eq',0]];
        $total=$this->model->count([
            'from'=>$table,
            'where'=>$where
        ]);
        $download=new \extend\Download();
        $this->doLoop($total,function ($perPage)use ($table,$where){
            return $this->model->from($table)->_where($where)->limit($perPage)->findAll(true);
        },function ($item)use ($download,$table){
            echo '开始处理：id=>'.$item['id'].'---------------'.PHP_EOL;
            if(strpos($item['thumb'],'no_icon')!==false){
                $this->model->from($table)->eq('id',$item['id'])->update([
                    'thumb'=>'',
                    'isdone'=>1,
                ]);
                echo '  没有图片'.PHP_EOL;
                return;
            }
            $ret=$download->down('http:'.$item['thumb'],'group/{%u%}',false);
            //$data['fileUrl'] 和$data['savePath']
            if(!$ret)
                exit();
            if($this->model->from($table)->eq('id',$item['id'])->update([
                'thumb'=>$ret['fileUrl'],
                'isdone'=>1,
            ]))
                echo '  成功：更新'.PHP_EOL;
            else
                echo '  失败：更新'.PHP_EOL;
            //msleep(1000,2000);
        });
    }

    /** ------------------------------------------------------------------
     * 小组发布
     *---------------------------------------------------------------------*/
    public function groupFabu(){
        $table='caiji_bangumi_group';
        $where=[['isfabu','eq',0],['iscaiji','eq',1]];
        $total=$this->model->count([
            'from'=>$table,
            'where'=>$where
        ]);
        $this->newClient();
        $this->doLoop($total,function ($perPage)use ($table,$where){
            return $this->model->from($table)->_where($where)->limit($perPage)->findAll(true);
        },function ($item)use ($table){
            echo '正在发布：id=>'.$item['id'].'---------------'.PHP_EOL;
            $ret=$this->client->http('http://www.qfafa.com/portal/fabu/group?pwd=Djidksl$$EER4ds58cmO','post',[
                'name'=>$item['name'],
                'thumb'=>$item['thumb']
            ]);
            if(!$ret){
                exit('接口连接失败'.PHP_EOL);
            }
            if($ret==='发布成功'){
                $this->model->from($table)->eq('id',$item['id'])->update(['isfabu'=>1]);
                echo '发布成功';
            }else
                exit('发布失败：'.$ret);
            exit();
        });
    }

}