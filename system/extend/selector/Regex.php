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
     * 唯一对外公开的方法，在原字符串中，按一定规则找出匹配的内容
     * @param string $html
     * @param string $method 对应的方法有： single、multi、cut 和 map
     * @param string $selector
     * @param array|string $tags
     * @param string $cut
     * @return bool|string|array
     *--------------------------------------------------------------------*/
    public static function find($html,$method,$selector,$tags, $cut=''){
        self::$html=$html;
        $method=__CLASS__.'::'.$method;
        if(is_callable($method)){
            return call_user_func($method,$selector,$tags,$cut);
        }else{
            self::$error='$type不正确';
            return false;
        }
    }
    /** ------------------------------------------------------------------
     * 正则单项选择器
     * @param string $reg :正则 '#<a href="(?P<url>[^>"]+)" title="(?P<title>[^>"]+)">#is'
     * @param string|array $tags 结果对应到的标签名列表,如 ['url','title'],字符串时多个标签用英文逗号','分隔，如'url,title'
     * @param string|null $html
     * @return bool|string|array：正则出错或正则结果选取数少于标签数或者tags为空时，返回false;没有匹配到时,返回空字符串;tags只有一项时，返回对应这项捕获到的字符串，tags有两个及两个以上时返回一维数组（对应各标签捕获的结果集）
     *--------------------------------------------------------------------*/
    static protected function single($reg,$tags='url',$html=null){
        if(($m=@preg_match($reg,($html ? $html: self::$html),$out))=== false) {
            self::$error = 'the regex("'.$reg.'") syntax errors!';
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
     * @param string $tag_cut ：截取时的正则，可以为空，使用时必须用cut作正则捕获名，如/xxxx(?P<cut>[\s\S]+?)yyyy/i
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
                return $content;
            }
        }else{
            $content=self::$html;
        }
        $res=@preg_match_all($tag_reg, $content, $out);
        unset($content);
        //dump($res);
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
     * 截取两字符串间的内容
     * @param string|array $rule 前后两个字符串，支持数组和字符串格式，字符串格式时用'{%|||%}'分隔
     * @return bool|string
     *--------------------------------------------------------------------*/
    static public function cut($rule){
        if(is_string($rule))
            $rule=explode('{%|||%}',$rule);
        if(count($rule) !==2){
            self::$error='规则格式不正确';
            return false;
        }
        return Helper::strCut(self::$html,$rule[0],$rule[1]);
    }

    /** ------------------------------------------------------------------
     * 影射式多项查找：原字符比较长或一次性写正则比较复杂时，推荐用这种方法，因为太复杂的正则很容易出现灾难性回溯
     * @param string $area:分割子项区域的正则
     * @param array $map：在区域内每个标签对应的正则
     * @param string|array $cut：截取规则，可以截取去掉两头没用的内容，减少干扰
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
