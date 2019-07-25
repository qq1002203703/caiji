<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 *
 * ======================================*/


namespace shell\caiji;


use extend\Helper;
use shell\Spider;

class Youku extends Spider
{
    protected $fileBodyName='renren';
    protected $error=[
        1=>'视频已失效',
        -1=>'采集结果false',
        -2=>'采集结果http_code不是200',
        -3=>'采集结果json转数组出错',
        -4=>'采集结果格式不正确',
        -5=>'文本过长超过了字段最大值',
        -6=>'保存到数据库失败'
    ];
    public function test(){
        $maxPage=17810;
        $this->http_init();
        for ($i=3842;$i<$maxPage;$i++){
            $url='https://p.comments.youku.com/ycp/comment/pc/commentList?jsoncallback=n_commentList&app=100-DDwODVkv&objectId=54513754&objectType=1&listType=0&currentPage='.$i.'&pageSize=30&sign=ec7458e1af278dfa3854eaf4d8800597&time=1564014996';
            //https://p.comments.youku.com/ycp/comment/pc/objectCommentStatus?jsoncallback=n_objectCommentStatus&app=100-DDwODVkv&objectId=54513754&objectType=1&listType=0&lastCommentId=1000018492559&sign=ec7458e1af278dfa3854eaf4d8800597&time=1564014996
            echo '开始采集第'.$i.'页-----------'.PHP_EOL;
            $res=$this->client->http($url);
            if($res===false){
                exit($this->error[-1].PHP_EOL);
            }
            if($this->client->getHttpCode()!==200){
                echo '  http_code:'.$this->client->getHttpCode().PHP_EOL;
                exit($this->error[-2].PHP_EOL);
            }
            $res=json_decode('{'.Helper::strCut($res,'n_commentList({','})').'}',true);
            if(!$res){
                msleep(30000,500);
                $res=$this->client->http($url);
                if($res===false){
                    exit($this->error[-1].PHP_EOL);
                }
                if($this->client->getHttpCode()!==200){
                    echo '  http_code:'.$this->client->getHttpCode().PHP_EOL;
                    exit($this->error[-2].PHP_EOL);
                }
                $res=json_decode('{'.Helper::strCut($res,'n_commentList({','})').'}',true);
                if(!$res)
                    exit($this->error[-3].PHP_EOL);
            }
            if(!isset($res['data']['comment'])|| !$res['data']['comment']){
                dump($res);
                //$this->dodo($res);
                exit($this->error[-4].PHP_EOL);
            }
            $count=count($res['data']['comment']);
            $isFalse=false;
            for($j=0;$j<$count;$j++){
                $comment=$this->filter($res['data']['comment'][$j]['content']);
                $length=mb_strlen($comment);
                if($length<20){
                    echo '  字数不符合要求跳过-----'.PHP_EOL;
                    continue;
                }elseif ($length<50){
                   if(!$this->saveToSign($comment))
                       $isFalse=true;
                }else
                    if(!$this->saveToText($comment))
                        $isFalse=true;
            }
            if($isFalse)
                exit($this->error[-6].PHP_EOL);
            msleep(5200,500);
        }
    }

    protected function saveToSign($signature){
        $md5=md5($signature);
        if($this->model->from('caiji_renren_name')->eq('sign_md5',$md5)->find(null,true)){
            echo '  重复$signature：'.$signature.'----------- '.PHP_EOL;
            return true;
        }
        $tmp=$this->model->select('id')->from('caiji_renren_name')->eq('sign_md5','')->find(null,true);
        if(!$tmp){
            exit('  没有需要添加签名的项了'.PHP_EOL);
        }
        if($this->model->select('id')->from('caiji_renren_name')->eq('id',$tmp['id'])->update([
            'sign_md5'=>$md5,
            'signature'=>$signature
        ])){
            echo '  更新signature到 id=>'.$tmp['id'].'-------'.PHP_EOL;
            return true;
        }
        return
            false;
    }

    protected function saveToText($str){
        $md5=md5($str);
        $table='caiji_renren_name';
        if($this->model->from($table)->eq('md5',$md5)->find(null,true)){
            echo '  重复text：----------- '.PHP_EOL;
            return true;
        }else{
            $tmp=$this->model->select('id')->from($table)->eq('md5','')->find(null,true);
            if(!$tmp)
                exit('  所有人都添加了text了'.PHP_EOL);
            if($this->model->from($table)->eq('id',$tmp['id'])->update([
                'md5'=>$md5,
                'text'=>$str
            ])){
                echo '  更新text：id=>'.$tmp['id'].'----------- '.PHP_EOL;
                return true;
            }
            return false;
        }
    }

    protected function filter($text){
        return trim(preg_replace([
            '%\[.*?\]%',
            '%\d{2,4}(年|\-|\.)?\d{1,2}(月|\-|\.)?\d{1,2}(日|，|、|！|\.|,| )?%',
            '%[\n\r]+%',
            '%\s{2,}%'
        ],[
            '',
            '',
            ' ',
            ' '
        ],$text));
    }
    protected function http_init(){
        $this->newClient(['opt'=>[
            CURLOPT_TIMEOUT=>8,//下载时应该按目标文件大小设置大一点
            CURLOPT_USERAGENT=>'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Mobile Safari/537.36',
            CURLOPT_REFERER=>'https://v.youku.com/v_show/id_XMjE4MDU1MDE2.html',
            //CURLOPT_COOKIEFILE=>ROOT.'/cache/shell/caiji/youku_cookie.txt'
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
            //'checkResultPlugin'=>'\shell\caiji\Zhihu::check_result',//检测结果是否正常
            //'isProxy'=>false, //是否使用代理ip访问
            //'ipExpirationTime'=>280, //ip过期时间 单位秒
            'isOpenCurlTimeInterval'=>true,//是否开启curl访问时间间隔控制
            'curlTimeInterval'=>1200, //curl每次访问的最小时间间隔 单位毫秒
            //'isRandomUserAgent'=>true, //是否使用随机ua
            //'isAutoReferer'=>true, //是否需要自动获取来路
            //'waitNoProxy'=>20, //当无法获得有效的代理ip时，程序进行休眠的时间(单位秒)
            //'waitIpLock'=>10000, //当所有ip被封琐时，程序进行休眠的时间(单位毫秒)
            //'waitCurlFalse'=>4000,//当curl获取结果为false时 等待多少时间才重新发起下次请求(单位毫秒)
            'tryTimes'=>6,
            'encoding'=>'',
            'stopFile'=>$this->stopFile,
        ]);
    }
}