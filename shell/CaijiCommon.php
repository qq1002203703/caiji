<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 *采集公共类
 * ======================================*/
namespace shell;
use extend\Helper;
abstract  class  CaijiCommon
{
    /**
     * @var bool 是否是调试模式
     */
    protected $debug=false;
    /**
     * 使用插件
     * @param string $plugin
     * @param array $params
     * @return mixed
     */
    protected function usePlugin($plugin,$params){
        return Helper::callback($plugin,$params);
    }
    /** ------------------------------------------------------------------
     * 使用回调函数
     * @param string|\Closure|array $func 回调函数
     * @param array $params 回调函数的参数
     * @return mixed
     *---------------------------------------------------------------------*/
    protected function callback($func,$params){
        return Helper::callback($func,$params);
    }
    /** ------------------------------------------------------------------
     * 格式化字符串，用于文件和图片本地化时的路径与文件名的生成
     * @param string $str
     * @param int $id
     * @param string $num
     * @param string $url
     * @return string
     *--------------------------------------------------------------------*/
    protected function format($str,$id=0,$num='',$url=''){
        $time=time();
        return str_replace([
            '{%Y%}',
            '{%m%}',
            '{%d%}',
            '{%H%}',
            '{%i%}',
            '{%s%}',
            '{%r%}',
            '{%id%}',
            '{%u%}',
            //'{%md5%}'
        ],[
            date('Y',$time),
            date('m',$time),
            date('d',$time),
            date('H',$time),
            date('i',$time),
            date('s',$time),
            $this->randomKeys(8),
            (string)$id,
            //$id.$num,
            md5($url)
        ],$str);
    }
    /** ------------------------------------------------------------------
     * 生成随机字符串
     * @param int $length
     * @return string
     *---------------------------------------------------------------------*/
    protected function randomKeys($length){
        $pattern = '1234567890abcdefghijklmnopqrstuvwxyz';
        $key='';
        for($i=0;$i<$length;$i++) {
            $key .= $pattern{mt_rand(0,35)};
        }
        return $key;
    }

    /** ------------------------------------------------------------------
     * 找出字符串中的图片网址和链接网址，将其中的相对网址转换为绝对网址
     * @param string $str 原字符串
     * @param string $url 当前页面网址
     * @param bool $mao 是否保留锚点,默认不保留
     * @return string
     *---------------------------------------------------------------------*/
    public function toTrueUrl($str,$url,$mao=false){
        return preg_replace_callback('/<(img|a) ([^>]*?)(src|href)=([\'"]?)([^>\s\'"]*)\4([^>]*)>/i',function($match)use($url,$mao){
            //$match[6]=$match[6] ?? '';
            if($match[1]==='a' or $match[1]==='A'){
                return '<a '.$match[2].'href="'.$this->getTrueUrl($match[5],$url,$mao).'"'.$match[6].'>';
            }else{
                if($match[5])
                    return '<img '.$match[2].'src="'.$this->getTrueUrl($match[5],$url,$mao).'"'.$match[6].'>';
                else
                    return '';
            }
        },$str);
    }

    /** ------------------------------------------------------------------
     * 相对网址转换为绝对网址
     * @param string $srcurl 原网址
     * @param string $baseurl 当前页面网址
     * @param bool $mao:是否保留锚点,,默认不保留
     * @return string 转换为绝对网址后的网址
     *---------------------------------------------------------------------*/
    public function getTrueUrl($srcurl, $baseurl,$mao=false) {
        $srcinfo = parse_url($srcurl);
        if(isset($srcinfo['scheme'])) {
            return $srcurl;
        }
        //$srcinfo['fragment']=$srcinfo['fragment'] ?? '';
        $srcinfo['fragment']=isset($srcinfo['fragment']) ? '#'.$srcinfo['fragment'] : '';
        if(!isset($srcinfo['path'])){
            if($mao)
                return $baseurl .$srcinfo['fragment'] ;
            else
                return $baseurl;
        }
        //$srcinfo['query']=$srcinfo['query'] ?? '';
        $srcinfo['query']=isset($srcinfo['query']) ? '?'.$srcinfo['query'] : '';
        $baseinfo = parse_url($baseurl);
        $baseinfo['user']=isset($baseinfo['user']) ? $baseinfo['user'].':' : '';
        $baseinfo['pass']= isset($baseinfo['pass']) ? $baseinfo['pass'].'@' : '';
        $baseinfo['port']=isset($baseinfo['port']) ? ':'.$baseinfo['port']:'';
        $url = $baseinfo['scheme'].'://'.$baseinfo['user'].$baseinfo['pass'].$baseinfo['host'].$baseinfo['port'];
        if(!isset($baseinfo['path']) or substr($srcinfo['path'], 0, 1) == '/') {
            $path = $srcinfo['path'];
        }else{
            $filename=  basename($baseinfo['path']);
            if(strrpos($filename,'.') >0){
                //文件网址
                $path = dirname($baseinfo['path']).'/'.$srcinfo['path'];
            }else{
                //目录网址
                $path = ltrim($baseinfo['path'],'/').'/'.$srcinfo['path'];
            }
        }
        $rst = [];
        $path_array = explode('/', str_replace('\\', '/', $path));
        foreach ($path_array as $key => $dir) {
            if ($dir == '..') {
                array_pop($rst);
            }elseif($dir && $dir != '.') {
                $rst[] = $dir;
            }
        }
        $url .='/'. implode('/', $rst);
        if(end($path_array)=='')
            $url.='/';
        return $mao ? $url.$srcinfo['query'] . $srcinfo['fragment'] :$url.$srcinfo['query'];
    }

    /** ------------------------------------------------------------------
     * 过滤器
     * @param array|object $option:过滤规则集合
     * @param string $content
     * @param string $baseurl 当前页面url
     * @return string
     *---------------------------------------------------------------------*/
    protected function filter($option,$content,$baseurl){
        if($content=='')
            return $content;
        if(! is_array($option) && !($option instanceof \Traversable))
            return $content;
        foreach ($option as $v){
            $v=explode('{%|||%}',$v);
            if(!isset($v[1]))
                continue;
            switch (strtolower($v[0])){
                case 'replace'://替换
                    if(!isset($v[2]))
                        continue 2;
                    $content=str_replace($v[1],$v[2],$content);
                    break;
                case 'html'://去除html标签
                    $content=strip_tags($content,$v[1]);
                    break;
                case 'reg'://正则
                    if(!isset($v[2]))
                        continue 2;
                    $content=preg_replace($v[1],$v[2],$content);
                    break;
                case 'union'://组合
                    $content=str_replace('{%xxoo%}',$content,$v[1]);
                    break;
                case 'has'://必须包含
                    if(strpos($content,$v[1])===false){
                        $content='';
                    }
                    break;
                case 'nhas'://不能包含
                    if(strpos($content,$v[1])!==false){
                        $content='';
                    }
                    break;
                case 'trueurl'://转换为绝对网址
                    $v[1]=(int)$v[1];
                    $v[2]=(bool)($v[2] ?? false);
                    if($v[1]==0){
                        $content=$this->getTrueUrl($content,$baseurl,$v[2]);
                    }else{
                        $content=$this->toTrueUrl($content,$baseurl,$v[2]);
                    }
                    break;
            }
        }
        return $content;
    }

    /** ------------------------------------------------------------------
     * 调试模式的时候 打印变量
     * @param array $vars
     * @param string $msg
     * @param bool $exit 是否退出
     *--------------------------------------------------------------------*/
    protected function debug($vars,$msg='',$exit=true){
        if($this->debug){
            foreach ($vars as $var){
                dump($var);
            }
            if($msg)
                $msg.=PHP_EOL;
            if($exit){
                exit($msg);
            }else{
                echo $msg;
            }
        }
    }
}