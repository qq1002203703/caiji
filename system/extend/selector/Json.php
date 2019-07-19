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
class Json
{
    static protected $error='';
    static protected $html;
    /** ------------------------------------------------------------------
     * 唯一对外公开的方法，在原字符串中，按一定规则找出匹配的内容
     * @param string $html 原内容
     * @param string $method 对应的方法有： single、multi
     * @param string $selector 具体参看对应方法的注释
     * @param array|string $tags 同上
     * @return bool|string|array 同上
     *--------------------------------------------------------------------*/
    public static function find($html,$method,$selector,$tags){
        $method=(string)$method;
        if(!in_array($method,['single','multi','list'],true)){
            self::$error='$method不正确，只能是single、multi、cut 和 map中的一个';
            return false;
        }
        self::$html=$html=self::json2Array($html);
        if(self::$html===false)
            return false;
        return self::$method($selector,$tags);
    }
    /** ------------------------------------------------------------------
     * 单项选择器
     * @param string $selector  选择器 格式 'aaa.bb.ccc'
     * @param array $map 结果对应到的标签名列表,如 ['url','title'],字符串时多个标签用英文逗号','分隔，如'url,title'
     * @param string|null $html: 原字符串，被map()方法使用时才需要提供此项，find()时此项必须为空
     * @return bool|string|array 正则出错或正则结果选取数少于标签数或者tags为空时，返回false;没有匹配到时,返回空字符串;tags只有一项时，返回对应这项捕获到的字符串，tags个数大于1时返回一维数组（对应各标签捕获的结果集）
     *--------------------------------------------------------------------*/
    static protected function single($selector){
        if(is_string($selector))
            $selector=explode('.',$selector);
        $ret=self::$html;
        foreach ($selector as $item){
            $ret=$ret[$item] ?? false;
            if($ret ===false){
                self::$error='选择器中的'.$item.'出错，json中不存在此键名';
                return false;
            }
        }
       /* foreach ($selector as $k=>$item){
            $ret[$k]=self::getArrayValue(self::$html,$item);
            if($ret[$k] ===false)
                $ret[$k]='';
        }*/
        return (string)$ret;
    }

    /** ------------------------------------------------------------------
     * 多项选择器
     * @param string $selector  选择器 格式 'aaa.bb.ccc'
     * @param string|array $map 在区域内每个标签对应的键名，正则的捕获名必须与标签对应 如
    [
    'url'=>'aaa',
    'title'=>'bbb'
    ]
     * @return array|bool|string
     *--------------------------------------------------------------------*/
    static public function multi($selector,$map){
        if(is_string($selector))
            $selector=explode('.',$selector);
        $html=self::$html;
        foreach ($selector as $item){
            if(isset($html[$item]))
                $html=$html[$item];
            else{
                self::$error='选择器中的'.$item.'出错，json中不存在此键名';
                return false;
            }
        }
        if(!$map)
            return $html;
        $ret=[];
        $isWrong=false;
        array_walk($html,function($item,$index,$map)use(&$ret,&$isWrong){
            foreach ($map as $k=> $v){
                if(isset($item[$v]))
                    $ret[$index][$k]=$item[$v];
                else
                    $ret[$index][$k]=false;
                if($ret[$index][$k]===false){
                    unset($ret[$index]);
                    $isWrong=true;
                    break;
                }
            }
        },$map);
        return $isWrong ? array_values($ret) : $ret;
    }

    static public function list($selector){
        if(is_string($selector))
            $selector=explode('.',$selector);
        $count=count($selector);
        if($count==1)
            return implode('$$$',self::$html[$selector[0]]);
        if($count > 2){
            $html=self::$html;
            for ($i=0;$i<$count-1;$i++){
                if(isset($html[$selector[$i]]))
                    $html=$html[$selector[$i]];
                else{
                    self::$error='选择器中的'.$selector[$i].'出错，json中不存在此键名';
                    return false;
                }
            }
        }else{
            $html=self::$html[$selector[0]];
        }
        $arr=[];
        foreach ($html as $item){
            $arr[]=$item[$selector[$count-1]];
        }
       return implode('$$$',$arr);
    }

    /** ------------------------------------------------------------------
     * 获取错误信息
     * @return string
     *--------------------------------------------------------------------*/
    static public function getError(){
        return self::$error;
    }

    static protected function json2Array($html){
        $html=json_decode($html,true);
        if($html===null){
            self::$error='内容不是有效的json字符串';
            return false;
        }
        return $html;
    }

    static protected function getArrayValue($array,$keys){
        if(is_string($keys))
            $keys=explode('.',$keys);
        foreach ($keys as $key){
            if(isset($array[$key]))
                $array=$array[$key];
            else
                return false;
        }
        return $array;
    }

}
