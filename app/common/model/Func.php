<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 数据库处理常用函数集
 * ======================================*/
namespace app\common\model;
class Func
{
    /** ------------------------------------------------------------------
     * 为where条件查询时没有添加表前缀的字段加上表前缀，已有表前缀不会再添加
     * @param array $where 二维数组 格式如 [['name','like','xxxx'],['id','gt',20]]
     * @param string $table 表名
     * @return  array  [['name','like','xxxx'],['id','gt',20]] 处理后会变成 [['table.name','like','xxxx'],['table.id','gt',20]]
     *--------------------------------------------------------------------*/
    static public function whereAddTable($where,$table){
        if(empty($where)) return $where;
        array_walk($where,function(&$item,$key,$table){
            if(strpos($item[0],'.')===false)
                $item[0]=$table.'.'.$item[0];
        },$table);
        return $where;
    }
    /** ------------------------------------------------------------------
     * 为order语句的字段添加表前缀，已有表前缀的不会再添加
     * @param string $order order语句
     * @param string $table 表名
     * @return  string
     *--------------------------------------------------------------------*/
    static public function orderAddTable($order,$table){
        if(!$order)
            return $order;
        $order=preg_replace('/ {2,}/',' ',trim($order));
        $arr=explode(',',$order);
        array_walk($arr,function(&$item,$key,$table){
            $tmp=explode(' ',$item);
            if(strpos($tmp[0],'.')===false)
                $tmp[0]=$table.'.'.$tmp[0];
            $item=implode(' ',$tmp);
        },$table);
        return implode(',',$arr);
    }
}