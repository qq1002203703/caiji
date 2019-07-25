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
use shell\Spider;
use extend\Selector;
class Qiushibaike extends Spider
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
        //签名：45~100
        //简介长度：60~500
       echo  mb_strlen('没必要让所有人知道真实的你，或者是你没有必要不停地向人说其实我是一个什么样的人。因为这是无效').PHP_EOL;
    }
    public function spider_list(){
        $url='https://www.qiushibaike.com/text/page/1/';
        $maxPage=365534+10000;
        $this->http_init();
        for ($i=1;$i<$maxPage;$i++){
            echo '开始采集第'.$i.'页-----------'.PHP_EOL;
            $res=$this->client->http($url.$i);
            if($res===false){
                exit($this->error[-1].PHP_EOL);
            }
            if($this->client->getHttpCode()!==200){
                echo '  http_code:'.$this->client->getHttpCode().PHP_EOL;
                exit($this->error[-2].PHP_EOL);
            }
            $res=Selector::find($res,'regex,multi','%<li><a[^>]+>(?P<name>.*?)</a>%','name','<ul class="index_more_list">{%|||%}</ul>');
            if(!$res){
                exit($this->error[-4].PHP_EOL);
            }
            $isStop=false;
            foreach ($res as $item){
                if(!$this->checkName($item)){
                    echo '  '.$item.'=>不符合要求路过----'.PHP_EOL;
                    continue;
                }
                if(!$this->saveName(['name'=>$item])){
                    echo ($this->error[-6].PHP_EOL);
                    $isStop=true;
                    continue;
                }
            }
            if($isStop)
                exit($this->error[-6].PHP_EOL);
        }
    }
    protected function http_init(){
        $this->newClient(['opt'=>[
            CURLOPT_TIMEOUT=>8,//下载时应该按目标文件大小设置大一点
            CURLOPT_USERAGENT=>'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36',
            CURLOPT_REFERER=>'http://name.renren.com/',
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