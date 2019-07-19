<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 选择器，在内容/字符串中，查找指定规则的子内容/子字符串
 * 目前只有正则选择器，可以扩展加入xpath、css等选择器
 * ======================================*/
namespace extend;

class Selector {
    static protected $error='';

    /** ------------------------------------------------------------------
     * find
     * @param string $html
     * @param string|array $type
     * @param string $selector
     * @param string|array $tags
     * @param string $cut
     * @return bool|string|array
     *--------------------------------------------------------------------*/
    public static function find(&$html,$type,$selector,$tags='',$cut=''){
        if(!$type)
            $type=['regex','single'];
        if(is_string($type))
            $type=explode(',',$type);
        $type[0]='\extend\selector\\'.ucfirst($type[0]);
        if(is_callable($type[0].'::find')){
            $ret=call_user_func($type[0].'::find',$html,$type[1],$selector,$tags,$cut);
            if(!$ret)
                self::$error=call_user_func($type[0].'::getError');
            return $ret;
        }else{
            self::$error='不存在的选择器';
            return false;
        }
    }
    /** ------------------------------------------------------------------
     * 获取错误信息
     * @return string
     *--------------------------------------------------------------------*/
    static public function getError(){
        return self::$error;
    }
}
