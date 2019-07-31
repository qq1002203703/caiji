<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 *
 * QQ 46502166
 *
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 网页客户端访问器，是curl类的二次封装类
 * 主要让curl方便用代理访问、方便切换浏览器UserAgent、方便切换来路Referer
 * ======================================*/

namespace extend;
use core\Conf;

class HttpClient
{
    public $proxy=[]; //代理ip 端口 种类 格式 ['id'=>111,'ip'=>'8.8.8.5','port'=>80,'type'=>'http']
    public $checkProxyPlugin='';//检测代理ip的插件
    public $getProxyPlugin=''; //获取代理ip的插件
    public $checkResultPlugin=''; //检测结果的插件
    public $isProxy=false; //是否使用代理ip访问
    public $ipExpirationTime=280; //代理ip过期时间(单位秒)
    public $isOpenCurlTimeInterval=false;//是否开启curl访问时间间隔控制
    public $curlTimeInterval=500; //curl每次访问的最小时间间隔(单位毫秒)
    public $isRandomUserAgent=false; //是否使用随机ua
    public $isAutoReferer=false; //是否需要自动获取来路
    public $waitNoProxy=30; //当无法获得有效的代理ip时，程序进行休眠的时间(单位秒)
    public $waitIpLock=120000; //当所有ip被封琐时，程序进行休眠的时间(单位毫秒)
    public $waitCurlFalse=4000;//当curl获取结果为false时 等待多少时间才重新发起下次请求(单位毫秒)
    public $tryTimes=3; //curl返回结果为false时一直重试的次数
    public $encoding=''; //指定源网页的编码 'UTF-8', 'GBK', 'GB2312', 'LATIN1', 'ASCII', 'BIG5', 'ISO-8859-1','CP936'
    public $stopFile='';

    protected $curlTimestamp=0; //上次curl访问的时间戳
    protected $proxyTimestamp=0; //上次获取代理ip的时间戳
    protected $falseCurlCount=0; //curl连续得到false的计数器
    protected $falseResultCount=0; //连续得到错误的计数器
    protected $tipMsg=''; //提示信息
    protected $referer=''; //来路缓存器
    protected $outPutType=1;//输出方式
    protected $isStop=false;//是否中断
    /**
     * @var \extend\Curl;
     */
    protected $curl;
    /**
     * @var \core\Model
     */
    protected $db;

    /**
     * HttpClient的构造函数，进行初始化curl
     * @param array $options
     */
    public function __construct($options=[]){
        $this->curl=new Curl($options);
        if(is_file($this->stopFile.'.lock')){
            rename($this->stopFile.'.lock',$this->stopFile);
        }
    }

    /** ------------------------------------------------------------------
     * 初始化HttpClient，对HttpClient进行必要的配置
     * @param array $options
     *---------------------------------------------------------------------*/
    public function httpSetting(array $options){
        if(is_array($options)){
            foreach ($options as $k =>$item){
                if(isset($this->{$k}) && $item)
                    $this->{$k}=$item;
            }
        }
    }
    public function setDb(){
        $this->db=app('\core\Model');
        return $this;
    }
    public function setCookie($file,$save=true){
        $this->curl->setCookie($file,$save);
        return $this;
    }
    public function setHeader($options){
       $this->curl->setHeader($options);
        return $this;
    }
    public function setProxy($proxy){
        $this->curl->setProxy($proxy);
        return $this;
    }
    public function setOptions(array $options){
        $this->curl->setOptions($options);
        return $this;
    }
    public function setUserAgent($userAgent){
        $this->curl->setUserAgent($userAgent);
        return $this;
    }
    public function getHttpCode(){
        return $this->curl->getHttpCode();
    }
    public function getInfo(){
        return $this->curl->getLastInfo();
    }
    public function getError(){
        return $this->curl->getError();
    }

    /** ------------------------------------------------------------------
     * 用curl发起一个网页请求
     * @param string $url
     * @param string $method
     * @param array $data
     * @return bool|string
     *---------------------------------------------------------------------*/
    public function request($url,$method='get',$data=array()){
        //自动设置来路
        if($this->isAutoReferer){
            if($this->referer)
                $this->setOptions([CURLOPT_REFERER=>$this->referer]);
            $this->referer=$url;
        }
        //随机ua
        if($this->isRandomUserAgent){
            if($ua=$this->getRandomUserAgent()){
                $this->outPut('    使用随机UserAgent:'.$ua.PHP_EOL);
                $this->setUserAgent($ua);
            }else{
                $this->outPut('    随机UserAgent:获取失败'.PHP_EOL);
            }
        }
        if($this->isOpenCurlTimeInterval){
            //检测两次curl访问的时间间隔
            $time=$this->getCurlSpan();
            if($time < 0 )
                msleep((-$time)/1000,300);
        }
        return $this->curl->request($url,$method,$data);
    }

    /** ------------------------------------------------------------------
     * http请求总入口
     * @param string $url
     * @param string $method
     * @param array $data
     * @return bool|string
     *---------------------------------------------------------------------*/
    public function http($url,$method='get',$data=array()){
        if ($this->isProxy) {
            $this->outPut('http使用代理进行第'.($this->falseResultCount+1).'次访问 : ---'.PHP_EOL);
            $html=$this->http_proxy_check_lock($url,$method,$data);
        } else{//不用代理直接访问
            $html=$this->http_no_proxy($url, $method, $data);
        }
        return $html;
    }

    /** ------------------------------------------------------------------
     * 不用代理直接访问
     * @param string $url
     * @param string $method
     * @param array $data
     * @return bool|string
     *--------------------------------------------------------------------*/
    public function http_no_proxy($url, $method, $data){
        $i=0;
        do{
            $i++;
            $this->outPut(' 第'.$i.'次进行请求--------'.PHP_EOL);
            $html = $this->request($url, $method, $data);
            if($html===false){
                $this->outPut(' 采集结果为false ：'.$url.PHP_EOL);
                $ckRes=false;
                msleep($this->waitCurlFalse,200);
            }else{
                if($html !=='') {
                    if ($this->encoding){
                        $html = $this->curl->encoding($html, $this->encoding);
                    }else{
                        $html=$this->curl->encoding($html) ;
                    }
                }
                if($this->checkResultPlugin && !$this->checkResult($html)){
                    $this->outPut(' 检测结果失败：'.$url.PHP_EOL);
                    $ckRes=false;
                    $this->outPut('  当前IP被封了，等等吧'.PHP_EOL);
                    msleep($this->waitIpLock,10);
                }else
                    $ckRes=true;
            }
        }while( !$ckRes && $i<$this->tryTimes);
        return $html;
    }

    /** ------------------------------------------------------------------
     * 循环检测结果，判断当前代理ip是否被对方服务器封了
     * @param string $url
     * @param string $method
     * @param array $data
     * @return bool|string
     *---------------------------------------------------------------------*/
    protected function http_proxy_check_lock($url,$method='get',$data=array()){
        do {
            $html = $this->http_proxy_check_used($url, $method, $data);
            if($html===false)
                break;
            $ckRes=$this->checkResult($html);
            if(!$ckRes){
                $this->falseResultCount++;
                $this->outPut(' 代理ip被封琐：'.$this->falseResultCount.' 次 ---'.PHP_EOL);
                if($this->isStop)
                    break;
            }else{
                $this->outPut(' 代理ip未被封琐：'.PHP_EOL);
                $this->falseResultCount=0;
            }
            if($this->falseResultCount >0 && $this->falseResultCount %2 ===0){ //连续两次都检测不过，换一次ip
                $this->outPut(' 当前代理ip已经被目标网站封琐，开始更换代理ip---'.PHP_EOL);
                $this->delete();//更换代理
            }
        }while(!$ckRes);
        return $html;
    }

    /** ------------------------------------------------------------------
     * 循环检测结果是否为false，为false的结果为代理不可用，从而更换代理，重新发起请求
     * @param string $url
     * @param string $method
     * @param array $data
     * @return bool|string
     *---------------------------------------------------------------------*/
    protected function http_proxy_check_used($url,$method,$data){
        do{
            $html=$this->http_proxy($url,$method,$data);
            if($html===false){
                $this->falseCurlCount++;
                $this->outPut('    代理ip失效：'.$this->falseCurlCount.'次'.PHP_EOL);
                if(!$this->checkProxy()){
                    $this->outPut('    当前代理ip已失效，开始更换新的代理ip'.PHP_EOL);
                    $this->delete();
                }
                if($this->isStop)
                    break;
            } else{
                $this->falseCurlCount=0;
                $this->outPut('    通过代理访问成功，代理ip:'.$this->proxy['ip'].',port:'.$this->proxy['port'].',type:'.$this->proxy['type'].PHP_EOL);
            }
        }while($html===false);
        return $html;
    }

    /** ------------------------------------------------------------------
     * 获取可用的代理，并用代理发起一个请求
     * @param string $url
     * @param string $method
     * @param array $data
     * @return bool|string
     *---------------------------------------------------------------------*/
    public function http_proxy($url,$method,$data){
        if(!$this->proxy || !$this->checkIpExpire())
            $this->getProxyLoop();
        if(!$this->proxy || !$this->checkIpExpire())
            return false;
        return $this->curl_proxy($url,$method,$data);
    }

    /** ------------------------------------------------------------------
     * 通过插件对结果进行判断，看是否是正常的结果，不正常说明ip已被封
     * @param mixed $result
     * @return bool
     *---------------------------------------------------------------------*/
    protected function checkResult($result){
        if($result===false)
            return false;
       return Helper::callback($this->checkResultPlugin,[$result]);
    }


    /** ------------------------------------------------------------------
     * 检测ip是否过期,同时处理过期的代理
     * @return bool 不过期返回真，过期返回假
     *---------------------------------------------------------------------*/
    protected function checkIpExpire(){
        $time=time();
        if ($this->proxy && ($time - $this->proxyTimestamp) < $this->ipExpirationTime)
            return true;
        $this->delete();
        return false;
    }

    /** ------------------------------------------------------------------
     * 用代理方式发起一个curl的网页请求
     * @param string $url
     * @param string $method
     * @param array $data
     * @return bool|string
     *---------------------------------------------------------------------*/
    public function curl_proxy($url,$method,$data=array()){
        return $this->setProxy($this->proxy)->request($url,$method,$data);
    }

    /** ------------------------------------------------------------------
     * 检测代理ip是否可用 可以使用插件检测,也可以用默认方法检测,
     * 默认用百度
     * @return bool
     *---------------------------------------------------------------------*/
    protected function checkProxy(){
        if(!$this->proxy)
            return false;
        if($this->checkProxyPlugin){
            return Helper::callback($this->checkProxyPlugin,[$this->proxy]);
        }else{
            $ret= $this->setOptions([CURLOPT_HEADER=>true, CURLOPT_NOBODY=>true])->setProxy($this->proxy)->request('https://www.baidu.com/');
            $this->curl->setOptions([CURLOPT_NOBODY=>false, CURLOPT_HEADER=>false]);
            return $ret !== false;
        }
    }

    /** ------------------------------------------------------------------
     * 使用插件获取代理ip
     * 插件需要 返回格式 ['ip'=>'19.19.19.19','port'=>80, 'type'=>'http']
     * @return bool
     *--------------------------------------------------------------------*/
    protected function getProxy(){
        $data = Helper::callback($this->getProxyPlugin,[]);
        if(!$data)
            return false;
        else{
            $this->proxyTimestamp=time();
            $this->proxy=$data;
            return true;
        }
    }

    //循环获取代理
    protected function getProxyLoop(){
        do{
            $res=$this->getProxy();
            if(!$res) {
                if($this->checkStop())
                    break;
                $this->outPut('  数据库中没有符合条件的代理ip，等待 '.$this->waitNoProxy.' 秒------'.PHP_EOL);
                sleep($this->waitNoProxy);
            }else
                $this->outPut('  从数据库获得代理ip '.PHP_EOL);
        }while(!$res);
    }

    /** ------------------------------------------------------------------
     * 获取两次curl请求的时间间隔与最小时间间隔之差
     * @return float|int 微秒
     *---------------------------------------------------------------------*/
    protected function getCurlSpan(){
        $now=microtime(true);
        $dif=$now - $this->curlTimestamp-($this->curlTimeInterval*1000);
        $this->curlTimestamp=$now;
        return $dif;
    }

    /** ------------------------------------------------------------------
     * 获取随机浏览器对应的UserAgent
     * @return string
     *---------------------------------------------------------------------*/
    protected function getRandomUserAgent(){
        $userAgentArr=Conf::all('userAgent');
        return $userAgentArr ? $userAgentArr[array_rand($userAgentArr)] : '';
    }


    /** ------------------------------------------------------------------
     * 删除数据库中的代理
     * @param array $data
     *---------------------------------------------------------------------*/
    protected function delete(){
        if(!$this->db)
            $this->setDb();
        if($this->proxy){
            if(isset($this->proxy['id']))
                $this->db->from('proxy')->eq('id',$this->proxy['id'])->delete();
            else
                $this->db->from('proxy')->eq('ip',$this->proxy['ip'])->eq('port',$this->proxy['port'])->delete();
        }
        $this->proxy=[];
        $this->proxyTimestamp=0;
    }

    /** ------------------------------------------------------------------
     * 输出: this->outType==2时会把重要信息写到日志里; this->outType==1时，直接输出
     * @param string $msg 信息
     * @param bool $important 是否是重要信息
     *---------------------------------------------------------------------*/
    protected function outPut($msg){
        if($this->outPutType ==2){
            echo '写入日志=>'.$msg;
        }else
            echo $msg;
    }

    protected function checkStop(){
        if(file_exists($this->stopFile.'.lock')){
            $this->outPut('   lock file exists, end !'.PHP_EOL);
            $this->isStop=true;
            return true;
        }
        return false;
    }
}