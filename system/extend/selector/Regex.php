<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 正则选择器
 * ======================================*/

namespace extend\selector;
use extend\Helper;
class Regex {
    static protected $error='';
    static protected $html;
    /** ------------------------------------------------------------------
     * 统一接口，在原字符串中，按一定规则找出匹配的内容
     * 注意
     * （1）$method为cut时，由于cut方法只是对原字符截取两字符串间的内容，所以此时的$selector不是正则，具体参看cut方法的注释；
     * （2）多项选择时用multi，还是map？虽然很多时候不同的正则可以匹配相同的内容，但是一条正则的质量对效率的影响是很大的，怎么才能写出高质量的正则，这需要不断摸索；低质量的正则很容易造成灾难性回溯，特别是原内容比较长或正则比较复杂时，所以为了减少回溯，尽量用map方法，map方法先把原内容切成多个小块再在每个小块中对具体标签进行捕获
     * @param string $html 原内容
     * @param string $method 对应的方法有： single、multi、cut 和 map
     * @param string $selector 具体参看对应方法的注释
     * @param array|string $tags 同上
     * @param string $cut 同上
     * @return bool|string|array 同上
     *--------------------------------------------------------------------*/
    public static function find($html,$method,$selector,$tags, $cut=''){
        self::$html=$html;
        $method=(string)$method;
        if(!in_array($method,['single','multi','cut','map'],true)){
            self::$error='$method不正确，只能是single、multi、cut 和 map中的一个';
            return false;
        }
        return self::$method($selector,$tags,$cut);
    }

    /** ------------------------------------------------------------------
     * 正则单项选择器
     * @param string $reg :正则 '#<a href="(?P<url>[^>"]+)" title="(?P<title>[^>"]+)">#is'
     * @param string|array $tags 结果对应到的标签名列表,如 ['url','title'],字符串时多个标签用英文逗号','分隔，如'url,title'
     * @param string|null $html: 原字符串，被map()方法使用时才需要提供此项，find()时此项必须为空
     * @return bool|string|array 正则出错或正则结果选取数少于标签数或者tags为空时，返回false;没有匹配到时,返回空字符串;tags只有一项时，返回对应这项捕获到的字符串，tags个数大于1时返回一维数组（对应各标签捕获的结果集）
     *--------------------------------------------------------------------*/
    static public function single($reg,$tags='url',$html=null){
        if(($m=@preg_match($reg,($html ? $html : self::$html),$out))=== false) {
            self::$error = 'The regex("'.$reg.'") syntax errors!';
            return false;
        }
        if($m===0){
            self::$error = '没有匹配到结果!';
            return '';
        }
        $count1=count($out);
        if(empty($tags)){
            $tags=['url'];
        }
        if(is_string($tags)){
            $tags=explode(',',$tags);
        }
        $count2=count($tags);
        if($count1 <3 || $count1<($count2*2+1) ){
            self::$error='没有捕获到对应标签个数的结果,很可能是正则格式书写不正确，注意：捕获项正确正则格式类似这样 (?P<标签名>[\s\S]*?)';
            return false;
        }
        if($count2==1){
            if(!isset($out[$tags[0]])){
                self::$error='标签"'.$tags[0].'"没有捕获到结果,请检查正则是否正确，捕获项正确格式类似这样 (?P<'.$tags[0].'>[\s\S]*?)';
                return false;
            }
            return $out[$tags[0]];
        }
        $ret=[];
        foreach ($tags as $k=>$v){
            if(!isset($out[$v])){
                self::$error='标签"'.$v.'"没有捕获到结果,请检查正则是否正确，捕获项正确格式类似这样 (?P<'.$v.'>[\s\S]*?)';
                return false;
            }
            $ret[$v]=$out[$v];
        }
        return $ret;
    }
    /**----------------------------------------------------------------------------
     * 正则多项选择器
     * @param string $tag_reg 正则 '#<a href="(?P<url>[^>"]+)" title="(?P<title>[^>"]+)">#is'
     * @param string|array $tags：结果对应到的标签名列表，格式：数组时，如['url','title']，字符串时多个标签用英文逗号','分隔，如'url,title'
     * @param string|array $tag_cut ：截取规则，同cut()方法的$rule参数，提供此项会先对原字符串进行一次截取，从而减少原字符串的长度；本项为空时将不进行截取
     * @return bool|string|array 返回的结果情况如下：
     *  正则出错,或正则结果选取数少于标签数，或标签为空时,返回false;
     *  匹配不到时，有两种情况 : 第一种用$reg_cut截取匹配不到时返回空字符串，第二种情况preg_match_all匹配不到时，返回空数组;
     *  匹配到时，也有两种情况：当$tags只有一个时，返回一维数组结果集;否则返回对应到标签的二维数组结果集
     *---------------------------------------------------------------------------------*/
    static public function multi($tag_reg,$tags, $tag_cut=''){
        //先按reg_cut对原内容进行一次匹配，相当于对原字符串进行一次中间内容截取，去掉不必要的干扰
        if($tag_cut){
            //$html=self::regexSingle($html,$reg_cut,'cut');
            $content=self::cut($tag_cut);
            if($content===false || $content===''){
                self::$error='tag_cut 截取规则出错，无法匹配';
                return $content;
            }
        }else{
            $content=self::$html;
        }
        $res=@preg_match_all($tag_reg, $content, $out);
        unset($content);
        if($res === false) {
            self::$error = 'The multi selector regex "'.$tag_reg.'" syntax errors';
            return false;
        }
        if($res===0){
            self::$error = '没有匹配到结果!';
            return [];
        }
        $count1=count($out);
        if(empty($tags)){
            self::$error = '标签tags不能为空!';
            return false;
        }
        if(is_string($tags)){
            $tags=explode(',',$tags);
        }
        $count2=count($tags);
        if($count1 <3 || $count1<($count2*2+1) ){
            self::$error='没有捕获到对应标签个数的结果,很可能是正则格式书写不正确，注意：捕获项正确正则格式类似这样 (?P<标签名>[\s\S]*?)';
            return false;
        }
        $ret=[];
        //单个tag时 返回一维数组结果集
        if($count2==1){
            if(!isset($out[$tags[0]])){
                self::$error='标签"'.$tags[0].'"没有捕获到结果,请检查正则是否正确，捕获项正确格式类似这样 (?P<'.$tags[0].'>[\s\S]*?)';
                return false;
            }
            for ($i=0;$i<$res;$i++){
                $ret[$i]=$out[$tags[0]][$i];
            }
            return $ret;
        }
        //多个tag时 返回二维数组结果集
        for ($i=0;$i<$res;$i++){
            foreach ($tags as $k=>$v){
                if(!isset($out[$v])){
                    self::$error='标签"'.$v.'"没有捕获到结果,请检查正则是否正确，捕获项正确格式类似这样 (?P<'.$v.'>[\s\S]*?)';
                    return false;
                }
                $ret[$i][$v]=$out[$v][$i];
            }
        }
        return $ret;
    }

    /** ------------------------------------------------------------------
     * 截取内容：对原字符串进行一次截取
     * @param string|array $rule 截取规则，包含前后两个字符串，支持数组和字符串格式，字符串格式时用'{%|||%}'分隔两者
     * @return bool|string 返回原字符串在$rule提供的两个字符串间的字符串
     *--------------------------------------------------------------------*/
    static public function cut($rule){
        if(is_string($rule))
            $rule=explode('{%|||%}',$rule);
        if(!is_array($rule) || count($rule) !==2){
            self::$error='规则格式不正确';
            return false;
        }
        return Helper::strCut(self::$html,$rule[0],$rule[1],false);
    }

    /** ------------------------------------------------------------------
     * 影射式多项查找：原字符比较长或一次性写正则比较复杂时，推荐用这种方法，因为原字符比较长或太复杂的正则很容易出现灾难性回溯
     * @param string $area:分割子项区域的正则，使用时必须用'erea'作正则捕获名，如/xxxx(?P<area>[\s\S]+?)yyyy/i
     * @param array $map：在区域内每个标签及其对应的正则，正则的捕获名必须与标签对应 如
      [
           'url'=>'#<a href="(?P<url>[^"]+)">#',
           'title'=>'#<h2>(?P<title>[\s\S]+?)</h2>#'
      ]
     * @param string|array $cut：截取规则，同mutil方法的$tag_cut参数
     * @return array|bool|string
     *--------------------------------------------------------------------*/
    static public function map($area,$map,$cut=''){
        $data=self::multi($area,'area',$cut);
        if(!$data)
            return $data;
        $ret=[];
        $isWrong=false;
        array_walk($data,function($item,$index,$map)use(&$ret,&$isWrong){
            foreach ($map as $k=> $v){
                $ret[$index][$k]=self::single($v,$k,$item);
                if($ret[$index][$k]===false){
                    unset($ret[$index]);
                    $isWrong=true;
                    break;
                }
            }
        },$map);
        return $isWrong ? array_values($ret) : $ret;
    }

    /** ------------------------------------------------------------------
     * 获取错误信息
     * @return string
     *--------------------------------------------------------------------*/
    static public function getError(){
        return self::$error;
    }
}
