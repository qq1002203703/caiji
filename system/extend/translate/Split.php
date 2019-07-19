<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 字符串分割成数组
 * ======================================*/

namespace extend\translate;

class Split
{
    /** ------------------------------------------------------------------
     * 分割字符串
     * @param string $str 原字符串
     * @param string $needle 分割器，多个用'|'分开
     * @param int $length 每次分割最大长度
     * @return array|bool
     *---------------------------------------------------------------------
     */
    static public function do($str,$needle ='.|,',$length=5000){
        return self::split_r([$str],$needle,$length);
    }

    /** ------------------------------------------------------------------
     * 递归分割
     * @param array $data
     * @param string $needle
     * @param int $length 小于2000很容易得到false
     * @param int $i
     * @return bool|array
     *---------------------------------------------------------------------*/
    static public function split_r($data,$needle ='.|,',$length=5000,$i=0){
        if(strlen($data[$i])<$length)
            return $data;
        $tmp=$data[$i];
        $data[$i]='';
        $needles=explode('|',$needle);
        $pos=self::findPos($tmp,$needles,$length);
        if($pos===false)
            return false;
        $data[$i]=substr($tmp,0,$pos+1);
        $data[$i+1]=substr($tmp,$pos+1);
        $i++;
        return self::split_r($data,$needle,$length,$i);
    }

    /** ------------------------------------------------------------------
     * 查找分割位置
     * @param string $str
     * @param array $needles
     * @param int $length
     * @return bool|int
     *---------------------------------------------------------------------*/
    static protected function findPos($str,$needles,$length){
        $pos=false;
        $currentLength=strlen($str);
        foreach ($needles as $item){
            $pos=strrpos($str,$item,-($currentLength-$length+1));
            if($pos!==false)
                break;
        }
        return $pos;
    }
}