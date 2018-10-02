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
 *
 * ======================================*/

namespace extend;
/**
 * Class Curl
 * @package extend
 * @property-read array $options
 * @property-read array $header
 * @property-read string $errorMsg
 * @property-read array $info
 */
class Curl
{
    /**
     * @var int 请求失败再重试的次数,默认为3
     */
    public $tryTimes;
    /**
     * @var bool 是否需要登陆,默认为false
     */
    public $login;
    /**
     * @var string 登陆检测时用到的验证字符串
     */
    public $match;
    /**
     * @var array 超时设置:数组的第一个用于CURLOPT_CONNECTTIMEOUT，第二个用于CURLOPT_TIMEOUT
     */
    public $timeOut=[7,15];
    /**
     * @var array curl参数配置：默认带下面的内容，如果想设置，可以通过$this->setOptions()方法随意增加和覆盖
     */
    protected $options=[
        //浏览器USER AGENT
        CURLOPT_USERAGENT=>'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36',
        //是否跟踪重定向
        CURLOPT_FOLLOWLOCATION =>1,
        //来路url
        //CURLOPT_REFERER=>'',
        //是否返回头部
        CURLOPT_HEADER=>false,
        //cURL函数执行的最长秒数
        CURLOPT_TIMEOUT=>15,
        //在尝试连接时等待的秒数
        CURLOPT_CONNECTTIMEOUT=>7,
        //不直接输出结果
        CURLOPT_RETURNTRANSFER=> 1,
        CURLOPT_ENCODING=>'gzip',

    ];
    protected $header=[];
    /**
     * @var string curl错误信息
     */
    protected $errorMsg='';
    protected $info;
    protected $cookieFile='';
    protected $isDie=false;

    /**--------------------------------------------------------------------------------
     * 构造函数
     * @param array $setting
     *-------------------------------------------------------------------------------*/
    public function __construct($setting=[])
    {
        $this->tryTimes=$setting['tryTimes'] ?? 3;
        $this->login=$setting['login'] ?? false;
        $this->match=$setting['match'] ?? '';
        if(isset($setting['timeOut']) && $setting['timeOut'])
            $this->timeOut=$setting['timeOut'];
        if(isset($setting['opt']) && $setting['opt'] ){
            $this->options=$setting['opt']+$this->options;
        }
    }

    /** ------------------------------------------------------------------
     * 添加一个curl请求，可一次性设置所有参数
     * @param $url
     * @param array $data
     * @param array $options
     *      string $options['method']:默认'get'
     *      array $options['opt']
     *      string $options['cookieFile']
     *      string $options['saveFile']
     *      array $options['header']
     *      array $options['proxy']
     * @return bool|string
     *---------------------------------------------------------------------*/
    public function add($url,$data=[],$options=[]){
        if(!isset($options['method']) || !$options['method'] )
            $options['method']='get';
        if($options){
            if(isset($options['proxy']))
                $this->setProxy($options['proxy']);
            if(isset($options['cookieFile']))
                $this->setCookie($options['cookieFile'],true);
            if(isset($options['header']))
                $this->setHeader($options['header']);
            if(isset($options['opt']))
                $this->setOptions($options['opt']);
            if(isset($options['saveFile']) && $options['saveFile']){
                return $this->download($url,$options['saveFile'],$options['method'],$data);
            }
        }
        return $this->request($url,$options['method'],$data);
    }

    /** ------------------------------------------------------------------
     * curl请求
     * @param string $url
     * @param string $method
     * @param array $data
     * @return bool|string
     *--------------------------------------------------------------------*/
    public function request($url,$method='get',$data=array()){
        if($this->isDie)
            return false;
        if(! Helper::isUrl($url)){
            $this->errorMsg='输入的链接不是有效的链接';
            return false;
        }
        $this->init($url);
        $method=strtolower($method);
        switch ($method){
            case 'get':
                if( !empty($data))
                    $url = $url.(strpos($url, '?') === false ? '?' : '&').http_build_query($data);
                break;
            case 'post':
                $this->options[CURLOPT_POST]=1;
                //POST数据
                $this->options[CURLOPT_POSTFIELDS]=http_build_query($data);
                break;
            case 'json':
                break;
            default:
        }
        $ch = curl_init();
        $this->options[CURLOPT_URL]=  $url;
        curl_setopt_array($ch,$this->options);
        $i=0;
        do{
            $ret = curl_exec($ch);
            $i++;
        }while ($ret === false && $i <= $this->tryTimes && sleep(1) !==false);
        $this->info = curl_getinfo( $ch );
        //如果获取失败
        if ($ret===false) {
            $this->errorMsg=curl_error($ch);
        }
        curl_close($ch);
        if ($ret===false) {
            return false;
        }
        //登陆检测
        if($this->login){
            if(! $this->checkLogin()){
                $this->errorMsg='登陆失败';
                return false;
            }
        }
        return $ret;
    }
    protected function init($url){
        if($this->timeOut && is_array($this->timeOut)){
            $this->options[CURLOPT_CONNECTTIMEOUT]=$this->timeOut[0];
            $this->options[CURLOPT_TIMEOUT]=$this->timeOut[1];
        }
        if(substr($url,0,5)=='https'){
            $this->options[CURLOPT_SSL_VERIFYPEER]=false;
            $this->options[CURLOPT_SSL_VERIFYHOST]=0;
        }
        if($this->header){
            $this->options[CURLOPT_HTTPHEADER]=$this->header;
        }
    }

    /** ------------------------------------------------------------------
     * get方式请求
     * @param string $url
     * @param array $data
     * @return mixed
     *---------------------------------------------------------------------*/
    public function get($url,$data=[]){
        return $this->request($url,'get',$data);
    }
    /** ------------------------------------------------------------------
     * post方式请求
     * @param string $url
     * @param array $data
     * @return mixed
     *---------------------------------------------------------------------*/
    public function post($url,$data=[]){
        return $this->request($url,'post',$data);
    }

    /** ------------------------------------------------------------------
     * 下载
     * @param string $url
     * @param string $saveFile
     * @param string $method
     * @param array $data
     * @return bool
     *--------------------------------------------------------------------*/
    public function download($url,$saveFile,$method='get',$data=[]){
        $path=dirname($saveFile);
        if(!is_dir($path)){
            if(!mkdir($path,0755,true)){
                $this->errorMsg='目录没有写入权限：'.$path;
                return false;
            }
        }
        $fp = fopen($saveFile, 'wb');
        $this->options[CURLOPT_FILE]=$fp;
        $ret=$this->request($url,$method,$data);
        fclose($fp);
        return $ret;
    }

    /** ------------------------------------------------------------------
     * curl的cookie来源文件与cookie保存文件设置
     * @param string $cookieFile
     * @param bool $save 新cookie是否覆盖旧cookie
     * @return \extend\Curl $this
     *--------------------------------------------------------------------*/
    public function setCookie($cookieFile,$save=true){
        if(!$cookieFile)
            return $this;
        //cookie存放文件
        if( is_file($cookieFile)){
            $this->options[CURLOPT_COOKIE]=$cookieFile;
            $this->cookieFile=$cookieFile;
            //访问后获取的新cookie覆盖原来的
            if($save)
                $this->options[ CURLOPT_COOKIEJAR]=$cookieFile;
        } else{
            $this->errorMsg='cookie文件不是有效的文件:'.$cookieFile;
        }
        return $this;
    }

    /** ------------------------------------------------------------------
     * curl代理设置
     * @param array $proxy
     * ['ip'=>'host','port'=>80,'type'=CURLPROXY_HTTP]
     * @return \extend\Curl $this
     *--------------------------------------------------------------------*/
    public function setProxy($proxy){
        if(isset($proxy['ip']) && $proxy['ip'])
            $this->options[CURLOPT_PROXY]=$proxy['ip'];
        else
            return $this;
        if(isset($proxy['port']) && $proxy['port'])
            $this->options[CURLOPT_PROXYPORT]=(int) $proxy['port'];
        else
            $this->options[CURLOPT_PROXYPORT]=80;
        //可以是 CURLPROXY_HTTP (默认值) CURLPROXY_SOCKS4、 CURLPROXY_SOCKS5、 CURLPROXY_SOCKS4A 或 CURLPROXY_SOCKS5_HOSTNAME。
        $this->options[CURLOPT_PROXYTYPE]=$proxy['type'] ?? CURLPROXY_HTTP;
        return $this;
    }
    /** ------------------------------------------------------------------
     * curl参数设置
     * @param array $options
     * @return $this
     *--------------------------------------------------------------------*/
    public function setOptions(array $options){
        $this->options=$options+$this->options;
        return $this;
    }

    /** ------------------------------------------------------------------
     * 删除某项参数
     * @param int|string|array $key
     *  @return $this
     *---------------------------------------------------------------------*/
    public function unsetOption($key){
        if(!$key)
            return $this;
       if(is_array($key)){
           foreach ($key as $item ){
               if(isset($this->options[$item])){
                   unset($this->options[$item]);
               }
           }
       }else{
           if(isset($this->options[$key])){
               unset($key);
           }
       }
       return $this;
    }

    /** ------------------------------------------------------------------
     * 设置头部信息
     * @param $options
     * @return $this
     *--------------------------------------------------------------------*/
    public function setHeader($options){
        $this->header=$options;
        return $this;
    }

    /** ------------------------------------------------------------------
     * 魔术方法 可以读取所有的属性
     * @param $name
     * @return mixed
     *--------------------------------------------------------------------*/
    public function __get($name)
    {
        if(isset($this->$name))
            return $this->$name;
        return null;
    }

    /** ------------------------------------------------------------------
     * 检测是否已经登陆
     * @return bool
     *--------------------------------------------------------------------*/
    protected function checkLogin(){
        if(!$this->cookieFile || ! $this->match )
            return false;
        $cookie=file_get_contents($this->cookieFile);
        if($cookie ===false)
            return false;
        if(strpos($cookie,$this->match)===false)
            return false;
        return true;
    }

    /** ------------------------------------------------------------------
     * 字符编码转换，依赖 iconv和mb_convert_encoding扩展
     * @param string $html
     * @param string $in
     * @param string $out
     * @return string
     *--------------------------------------------------------------------*/
    public function encoding($html, $in = '', $out = 'UTF-8'){
        if(!$in){
            return mb_convert_encoding($html,$out,array('UTF-8', 'GBK', 'GB2312', 'LATIN1', 'ASCII', 'BIG5', 'ISO-8859-1','cp936'));
        }else{
            $ret= iconv(  $in,$out.'//IGNORE',$html);
            if($ret===false)
                return mb_convert_encoding($html,$out,array('UTF-8', 'GBK', 'GB2312', 'LATIN1', 'ASCII', 'BIG5', 'ISO-8859-1','cp936'));
            return $ret;
        }
    }
}