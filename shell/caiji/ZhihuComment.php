<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 *  采集知乎某个关键词下的问题
 * ======================================*/

namespace shell\caiji;

use core\lib\cache\File;
use extend\HttpClient;
use shell\CaijiCommon;


class ZhihuComment extends CaijiCommon
{
    public $maxPage=2;
    //内容最大长度，mysql text类弄最大长度为65535
    public $maxLength=35535;
    protected $path='cache/shell/caiji/';
    protected $fileBodyName='zhihu_comment';
    protected $task=1;
    protected $stopFile;
    protected $taskName;
    protected $error=[
        -1=>'采集结果为false',
        -2=>'404错误',
        -3=>'结果json转数组出错',
        -4=>'无法匹配',
        -5=>'无法更新数据',
        1=>'评论已经关闭',
    ];
    /**
     * @var HttpClient
     */
    public $client;
    public function run(){
        $table='caiji_zhihu_answer';
        $where=[['iscaiji','eq',0],['comment_count','gt',4],['length','gt',40]];
        $total=$this->model->count([
            'from'=>$table,
            'where'=>$where
        ]);
        $this->init();
        $this->doLoop($total,function ($perPage)use ($table,$where){
            return $this->model->from($table)->_where($where)->limit($perPage)->findAll(true);
        },function ($item)use ($table){
            echo '开始采集comment: id=>'.$item['id'].',from_id=>'.$item['from_id'].' ------------'. PHP_EOL;
            $code=$this->loopAction(1,'https://www.zhihu.com/api/v4/answers/'.$item['from_id'].'/root_comments?order=normal&limit=20&offset=0&status=open',$item['id']);
            if($code>0) {
                $this->outPut(' '.$this->error[$code] . PHP_EOL,true);
                $this->model->from($table)->eq('id',$item['id'])->update(['iscaiji'=>1,'comment_count'=>0,'comment'=>$this->error[$code]]);
                if($this->checkStop())
                    exit();
                return;
            }elseif($code<0){
                $this->outPut(' '.$this->error[$code].PHP_EOL,true);
                exit();
            }else{
                $this->outPut(' 成功采集'.PHP_EOL,true);
            }
            if($this->checkStop())
            exit();
        });
    }
    protected function newClient($opt=[]){
        if(!$this->client){
            $this->client=new HttpClient($opt);
        }
    }

    /** ------------------------------------------------------------------
     * 初始化函数
     *---------------------------------------------------------------------*/
    protected function init(){
        //停止文件处理
        $this->stopFile=ROOT.'/'.$this->path.'stop/'.$this->fileBodyName;
        if(is_file($this->stopFile.'.lock')){
            rename($this->stopFile.'.lock',$this->stopFile);
        }else{
            if(!is_file($this->stopFile))
                File::write($this->stopFile,'');
        }
        //初始化HttpClient
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

    static public function check_result($html){
        return strpos($html,'<title data-react-helmet="true">安全验证')===false;
    }

    protected function loopAction($current_page,$nextUrl,$id){
        $isEnd=false;
        $data=[];
        while ($isEnd==false && $current_page <= $this->maxPage){
            $this->outPut(' 开始采集分页：第'.$current_page.'页…………'.PHP_EOL);
            $ret=$this->caijiItem($nextUrl);
            if(is_int($ret)){
                return $ret;
            }
            //$nextUrl='https://www.zhihu.com/api/v4/answers/'.$from_id.'/root_comments?limit=20&offset='.(($current_page-1)*20).'&order=normal&status=open';
            $isEnd=$ret['is_end'];
            $nextUrl=str_replace('.com/answers/','.com/api/v4/answers/',$ret['next']);
            $data[]=$ret['data'];
            $current_page++;
        }
        if($data)
            return $this->saveItem($data,$id) ? 0 : -5;
        return 0;
    }

    protected function caijiItem($nextUrl){
        $res=$this->client->http($nextUrl);
        //检测是不是false
        if($res===false)
            return -1;//结果为false
        //检测是不是404
        if(($httpCode=$this->client->getHttpCode())!==200){
            if($httpCode==403)
                return 1;//评论已经关闭
            return -2;//404错误
        }
        $res=json_decode($res,true);
        if(!$res)
            return -3;//结果json转数组出错
        if(isset($res['paging']) && isset($res['data'])){
            return [
                'is_end'=>$res['paging']['is_end'],
                'next'=>$res['paging']['next'],
                'data'=>$this->getItem($res['data'])
            ];
        }
        return -4;//无法匹配
    }

    protected function getItem($data){
        $ret=[];
        foreach ($data as $key =>$item){
            $item['content']=$this->contentFilter($item['content']);
            if(strlen($item['content']) > $this->maxLength)
                continue;
            $ret[]=$item['created_time'].'{%||%}'.$this->zhihuUserName($item['author']['member']['name']).'{%||%}'.$item['content'];
        }
        return implode('{%|||%}',$ret);
    }

    protected function saveItem($data,$id){
        $update=[];
        $update['comment']=is_array($data) ? implode('{%|||%}',$data) : $data;
        $update['iscaiji']=1;
        if($this->model->from('caiji_zhihu_answer')->eq('id',$id)->update($update))
            return true;
        else{
            dump($update);
            echo $this->model->getSql().PHP_EOL;
            return false;
        }
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

    protected function zhihuUserName($username ,&$count=0){
        return str_replace(['「已注销」', '匿名用户','知乎用户', '隐名埋名'],'隐名埋名',$username,$count);
    }

    /** ------------------------------------------------------------------
     * 答案发布
     *---------------------------------------------------------------------*/
    public function fabu(){
        $table='caiji_zhihu_answer';
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
            $parent=$this->model->select('id,group_name,tag')->from('caiji_zhihu')->eq('from_id',$item['fid'])->find(null,true);
            $ret=$this->client->http('http://www.qfafa.com/portal/fabu/table?pwd=Djidksl$$EER4ds58cmO','post',[
                //'from_id','content','username','comment','cate_name'
                'from_id'=>$item['from_id'],
                'content'=>$this->contentFilter($item['content']),
                'username'=>$item['username'],
                'comment'=>$item['comment'],
                'tag'=>$parent['tag'],
                'cate_name'=>$parent['group_name']??'',
                'table'=>'zhihu_answer',
            ]);
            if(!$ret){
                exit('接口连接失败'.PHP_EOL);
            }
            if($ret==='发布成功'){
                $this->model->from($table)->eq('id',$item['id'])->update(['isfabu'=>1]);
                echo '发布成功';
            }else
                exit('发布失败：'.$ret);
            //exit();
        });
    }

    public function test(){
        $table='caiji_zhihu_answer';
        $where=[['isfabu','eq',1],['isdone','eq',0]];
        $total=$this->model->count([
            'from'=>$table,
            'where'=>$where
        ]);
        $this->newClient();
        $this->doLoop($total,function ($perPage)use ($table,$where){
            return $this->model->select('id,from_id,username,fid')->from($table)->_where($where)->limit($perPage)->findAll(true);
        },function ($item)use ($table){
            echo '正在发布：id=>'.$item['id'].'---------------'.PHP_EOL;
            $parent=$this->model->select('id,group_name,tag')->from('caiji_zhihu')->eq('from_id',$item['fid'])->find(null,true);
            $ret=$this->client->http('http://www.qfafa.com/portal/fabu/xxoo?pwd=Djidksl$$EER4ds58cmO','post',[
                //'from_id','content','username','comment','cate_name'
                'from_id'=>$item['from_id'],
                'tag'=>$parent['tag'],
            ]);
            if(!$ret){
                exit('接口连接失败'.PHP_EOL);
            }
            if($ret==='发布成功'){
                $this->model->from($table)->eq('id',$item['id'])->update(['isdone'=>1]);
                echo '发布成功'.PHP_EOL;
            }else
                exit('发布失败：'.$ret);
            //msleep(3000);
        });
    }

}