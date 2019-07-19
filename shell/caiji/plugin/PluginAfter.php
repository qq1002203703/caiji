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


namespace shell\caiji\plugin;


class PluginAfter
{
    /** ------------------------------------------------------------------
     * 微主播的采集结果处理插件，获取分类名（取面包屑导航的倒数第二项）
     * @param array $data
     * @return array
     *--------------------------------------------------------------------*/
    static public function wezhubo($data){
        //echo '插件：'.PHP_EOL;
        //dump($data);
        if($data['category']){
            $tmp=explode('>',$data['category']);
            $num=count($tmp);
            if($num>2){
                $data['category']=$tmp[$num-2];
            }else{
                $data['category']='其他';
            }
        }else{
            $data['category']='其他';
        }
        //dump($data);
        return $data;
    }

}