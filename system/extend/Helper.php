<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 *
 * QQ 46502166
 *
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ==========================================
 * 常用函数助手类：大多是一些字符串和数组的
 * =========================================*/
namespace extend;
class Helper
{
    /**
     * 截取两个字符串中间的内容
     * @param string $str : 原字符串
     * @param string $begin : 开始字符串
     * @param string $end ：结尾字符串
     * @param bool $is_all : 查找不到开始字符串或结尾字符串时，将由此参数指定处理方式：为false时,直接结果返回空字符串；为true时，查找不到开始字符串，开始截取位置指定为0（即取原字符串的开始位置），查找不到结尾字符串时，结尾截取位置取原字符串最后的位置;
     * @return string 当$is_all为false时，如果开始或结尾字符串有一个找不到就返回空字符串,否则返回开始和结尾字符串中间包含的字符串。
     *                         当$is_all为true时，如果查找不到开始字符串，开始截取位置指定为0（即取原字符串的开始位置）,如果查找不到结尾字符串，结尾截取位置将取原字符串最后的位置，最后返回开始和结尾字符串中间包含的字符串。
     */
    static public function strCut($str,$begin,$end,$is_all=false){
        if(($pos1 = mb_strpos($str,$begin))!==false){
            $pos1 += mb_strlen($begin);
        }else{
            if(!$is_all)
                return '';
            else
                $pos1=0;
        }
        if(($pos2=mb_strpos($str,$end,$pos1)) <= $pos1){
            if(!$is_all)
                return '';
            else
                $pos2=mb_strlen($str)-1;
        }
        return mb_substr($str, $pos1, $pos2-$pos1 );
    }
    /**
     * 检测一个变量的数组类型
     * @param $var mixed:待检测的变量
     * @return int:不是数组和空数组返回0，索引数组返回1，关联数组返回2，混合数组返回3
     */
    static public function check_array_type($var){
        //不是数组
        if(!$var || !is_array($var))
            return 0;
        $c = count($var);
        $in = array_intersect_key($var,range(0,$c-1));
        if(count($in) == $c) {
            //索引数组
            return 1;
        }else if(empty($in)) {
            //关联数组
            return 2;
        }else{
            //混合数组
           return 3;
        }
    }

    /**
     * 字符串只进行一次替换
     * @param  string $needle:要匹配的字符串
     * @param string $replace:匹配后要替换成的字符串
     * @param string $haystack：原字符串
     * @return string
     */
    static public function str_replace_once($needle, $replace, $haystack) {
        $pos = strpos($haystack, $needle);
        if ($pos === false) {
            return $haystack;
        }
        return substr_replace($haystack, $replace, $pos, strlen($needle));
    }

    /**
     * 把字符串中的外链替换为内链
     * @param string $text：原字符串
     * @param string $mylink：本地解析外链的url
     * @return string
     */
    static public function replace_outlink($text,$mylink){
        $myhost=parse_url($mylink,PHP_URL_HOST);
        if($myhost=='')
            $myhost= $_SERVER['HTTP_HOST'];
        $myhost=explode('.',$myhost);
        $num=count($myhost);
        $reg='/<a ([^<>]*?)href=["\']?(https?:\/\/(?!([a-z0-9]+\.)*?'.$myhost[$num-2].'\.'.$myhost[$num-1].').*?)[\'"]?(\s[^<>]*?)*?>/i';
        return preg_replace_callback($reg,function($match) use($mylink){
            //dump($match);
            return '<a '.'href="'.$mylink.'?url='.urlencode($match[2]).'" target="_bank">';
        },$text);
    }

    /**
     * 正则测试
     * @param string $reg
     * @param string $str
     * @return string
     */
    static public function test_regx($reg,$str){
        return preg_replace_callback($reg,function($match) {
            dump($match);
            return '';
        },$str);
    }
    /** ------------------------------------------------------------------
     * 截取去掉html标签后的，指定长度的的字符串
     * @param string $str
     * @param int $length
     * @param string $chatset
     * @return string
     *--------------------------------------------------------------------*/
    static public function text_cut($str,$length,$chatset='UTF-8'){
        return mb_substr(strip_tags($str),0,$length,$chatset);
    }

    /** ------------------------------------------------------------------
     * curl访问网址
     * @param $url：网址
     * @param array $option
     * @param bool $stauts:引用传值，请求是否成功
     * @param array $post：post数据(不填则为GET)
     * @param array $cookie：
     *          string $cookie['file']:cookie存放文件,
     *          bool $cookie['save']:是否每次访问后都自动更新cookie
     * @return string：正确返回请求网址的回应内容，失败返回错误信息
     *--------------------------------------------------------------------*/
   static public function curl_request($url,&$stauts,$option=array(),$post=array(),$cookie=array()){
        $curl = curl_init();
        $default_option=[
            //目标网址
            CURLOPT_URL => $url,
            //浏览器USER AGENT
            CURLOPT_USERAGENT=>'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36',
            //是否跟踪重定向
            CURLOPT_FOLLOWLOCATION =>1,
            //来路url
            //CURLOPT_REFERER=>'',
            //是否返回头部
            CURLOPT_HEADER=>false,
            //超时时间
            CURLOPT_TIMEOUT=>10,
            //不直接输出结果
            CURLOPT_RETURNTRANSFER=> 1,
        ];
        if($post) {
            //POST方式
            $default_option[CURLOPT_POST]=1;
            //POST数据
            $default_option[CURLOPT_POSTFIELDS]=http_build_query($post);
        }
        if($cookie) {
            //cookie存放文件
            $default_option[CURLOPT_COOKIE]=$cookie['file'];
            //访问后获取的新cookie再覆盖原来的
            if(isset($cookie['save']) && $cookie['save'])
                $default_option[ CURLOPT_COOKIEJAR]=$cookie['file'];
        }
        curl_setopt_array($curl,$option+$default_option);
        $data = curl_exec($curl);
        $msg=curl_error($curl);
        curl_close($curl);
        //如果获取失败
        if ($data===false) {
            $stauts=false;
            return $msg;
        }
       $stauts=true;
        return $data;
    }

    /** ------------------------------------------------------------------
     * 数组删除重复项，并找出重复项被最后合并到哪里了，暂时用于下载文件的去重复
     * @param $var：关联数组，必须是关联数组
     * @return array：包含两部分，array ['unique']: 去除重复后的数组，array ['change']:键名是重复的删除项，键值对应的是最后留下的键名
     *---------------------------------------------------------------------
     */
    static public function  array_delet_repeat($var){
        $tmp=array_filter($var);
        $count1=count( $tmp);
        $tmp=array_combine($tmp, array_fill(0, $count1, ''));
        $count2=count($tmp);
        if($count1==$count2)
            return [
            'unique'=>$var,
            'change'=>[],
        ];
        $change=[];
        foreach ($tmp as  $k1 =>$v1){
            $i=0;
            foreach ($var as $k2 => $v2){
                if($i==0 && $k1===$v2){
                    $tmp[$k1]=$k2;
                    $i++;
                }elseif($i>0 && $k1==$v2){
                    $change[$k2]=$tmp[$k1];
                }
            }
        }
        return [
            'unique'=>array_flip($tmp),
            'change'=>$change,
        ];
    }

    static public function isUrl($url){
        if(preg_match('#^(http|https|ftp|magnet|thunder)://#i',$url) == 1){
            return true;
        }
        return false;
    }

    /** ------------------------------------------------------------------
     * 对象转数组
     * @param object|array $array
     * @return array
     *---------------------------------------------------------------------*/
    static public function  object2array($array) {
        if(is_object($array)) {
            $array = (array)$array;
        }
        if(is_array($array)) {
            foreach($array as $key=>$value) {
                $array[$key] = self::object2array($value);
            }
        }
        return $array;
    }

    /** ------------------------------------------------------------------
     * 回调一个函数，本函数是call_user_func_array的增强版，除了支持call_user_func_array一切用法外，还支持用'类名@方法名'来回调一个非静态方法
     * @param string|\Closure|array $func 函数，可以是闭包，还可以是用字符串或数组形式表示的一个已经存在函数或方法，支持下面几种格式
     *      函数时，直接传字符串形式的函数名;
     *      非静态方法时,字符串方式用'类名@方法名'，数组方式用[new 类名,'方法名'];
     *      静态方法时，字符串方式用'类名::静态方法名',数组方式用['类名','方法名']
     * @param array $params 函数/方法的参数,用法同call_user_func_array的第二个参数
     * @param mixed $class_params 实例化类时类的构造函数的参数（$class_params为可变数量参数，不限个数）
     * @return mixed：成功回调时，返回回调函数的值，回调失败或回调出错时，返回false。
     *  注意： 为了知道回调是否成功，那么回调函数的返回值就不要返回false，返回除false外一切值就可以了
     *---------------------------------------------------------------------*/
    static public function callback($func,$params, ...$class_params){
        $callable=$func;
        $is_obj=false;
        if(is_string($func)){
            if(strpos($func,'@')>0){
                $callable=explode('@',$func);
                $is_obj=true;
            }
        }
        if(is_callable($callable)){
            //try{
                if($is_obj){
                    return call_user_func_array([new $callable[0](...$class_params),$callable[1]],$params);
                }else{
                    return call_user_func_array($callable,$params);
                }
            //}catch (\ArgumentCountError $e){
                //echo $e->getMessage(); //参数个数不足时，会抛出的错误消息，可以把此消息定入日志或直接输出
                //return false;
           // }
        }
        return false;
    }

}
