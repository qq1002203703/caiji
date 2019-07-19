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

use core\Conf;
use core\lib\cache\File;
use extend\HttpClient;
use extend\Selector;
use shell\CaijiCommon;

class ZhihuPage extends CaijiCommon
{
    public $maxAnswer=30;
    protected $path='cache/shell/caiji/';
    protected $fileBodyName='zhihu_page';
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
    public function run(){
        $table='caiji_bangumi_group';
        $where=[['isdone','eq',0],['isfabu','eq',1]];
        $total=$this->model->count([
            'from'=>$table,
            'where'=>$where
        ]);
        $this->_init();
        $this->doLoop($total,function ($perPage)use ($table,$where){
            return $this->model->from($table)->_where($where)->limit($perPage)->findAll(true);
        },function ($item)use ($table){
            echo '开始处理小组=>'.$item['name'].'------------'. PHP_EOL;
            $config=Conf::all($this->taskName,false,$this->path,false);
            if($config['name']!==$item['name']){
                $config['current_page']=1;
                $config['next']='';
                $config['name']=$item['name'];
                $config['total']=0;
                $this->saveTask($config);
            }
            $code=$this->question($config['name'],$config['current_page'],$config['next']);
            if($code>0) {
                $this->outPut(' '.$this->error[$code] . PHP_EOL,true);
                $this->updateGroup($table,$item['id']);
                if($this->checkStop())
                    exit();
                return;
            }elseif($code<0){
                $this->outPut(' '.$this->error[$code].PHP_EOL,true);
                exit();
            }else{
                $this->outPut(' 成功采集'.PHP_EOL,true);
                $this->updateGroup($table,$item['id']);
            }
            if($this->checkStop())
                exit();
        });
    }
    //采集问题
    public function question($id,$current_page=1,$next=''){
        if($current_page==1){
            $url='https://www.zhihu.com/search?q='.urlencode($id).'&type=content';
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
            $next=$this->getQuestion($res,$id);
            if($next==false){
                dump($res);
                return -4;//无法匹配问题
            }
            unset($res);
            $current_page++;
            $this->saveTask(['next'=>$next,'current_page'=>2]);
        }
        $code=$this->loopQuestion($next,$id, $current_page);
        return $code;
    }

    protected function saveTask($name,$value=null){
        return Conf::editValue($name,$value,$this->fileBodyName.'_task_'.$this->task,'cache/shell/caiji/');
    }

    /** ------------------------------------------------------------------
     * 获取问题
     * @param array $res
     * @param int $id
     * @return bool|string
     *---------------------------------------------------------------------*/
    protected function getQuestion(&$data,$keyword){
        if(!isset($data['initialState']['entities']['answers']))
            return false;
        foreach ($data['initialState']['entities']['answers'] as $item){
            $from_id=$item['question']['id'];
            $ret=$this->saveQuestion($from_id,$keyword);
            if($ret===false)
                exit('无法保存数据');
        }
        //获取next的链接
        if(isset($data['initialState']['search']['generalByQuery'][$keyword]['next']))
            return $data['initialState']['search']['generalByQuery'][$keyword]['next'];
        return false;
    }

    /** ------------------------------------------------------------------
     * getAnswer
     * @param array $res
     *---------------------------------------------------------------------*/
    public function getQuestion2($data,$keyword){
        foreach ($data as $item){
            if($item['object']['type']==='answer'){
                $from_id=$item['object']['question']['id'];
                $res=$this->saveQuestion($from_id,$keyword);
                if($res===false)
                    exit('无法保存数据');
            }
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
     * @return int|bool
     *---------------------------------------------------------------------*/
    protected function saveQuestion($from_id,$keyword){
        if($this->model->from('caiji_zhihu')->eq('from_id',$from_id)->find(null,true))
            return -1;
        return $this->model->from('caiji_zhihu')->insert([
            'from_id'=>$from_id,
            'group_name'=>$keyword,
            'caiji_name'=>'zhihu'
        ]);
    }

    /** ------------------------------------------------------------------
     * 循环获取问题
     * @param string $nextUrl
     * @param int $id
     * @return array|int
     *---------------------------------------------------------------------*/
    protected function loopQuestion($nextUrl,$id,$current_page){
        $isEnd=false;
        while ($isEnd==false && $current_page <= $this->maxAnswer){
            $this->outPut(' 开始采集分页：current_page=>'.$current_page.'…………'.PHP_EOL);
            $ret=$this->caijiQuestion($nextUrl,$id);
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

    protected function caijiQuestion($nextUrl,$id){
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
            $this->getQuestion2($res['data'],$id);
        }
        if(isset($res['paging'])){
            return [
                'is_end'=>$res['paging']['is_end'],
                'next'=>$res['paging']['next']
            ];
        }
        return -14;//无法匹配答案
    }

    /** ------------------------------------------------------------------
     * 初始化函数
     *---------------------------------------------------------------------*/
    protected function _init(){
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
        if(!is_file(ROOT.'/cache/shell/caiji/'.$this->taskName.'.php')){
            Conf::write([
                'current_page'=>1,
                'next'=>'',
                'name'=>'',
            ],$this->taskName,'cache/shell/caiji/');
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

    protected function updateGroup($table,$id){
        $this->model->from($table)->eq('id',$id)->update(['isdone'=>1]);
    }

    static public function check_result($html){
        return strpos($html,'<title data-react-helmet="true">安全验证')===false;
    }

}