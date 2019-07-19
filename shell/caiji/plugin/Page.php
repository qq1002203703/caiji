<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 *
 * ======================================*/


namespace shell\caiji\plugin;


use extend\Curl;
use extend\Selector;

class Page
{
    static public function hacpaiBeforeSelector($html,$rule,$option,$curl){
        return Selector::find($html,['json','single'],'contentHTML','')[0];
    }

    /** ------------------------------------------------------------------
     * 豆瓣列表页采集插件
     * @param string $url
     * @param string $html
     * @param array $rule
     * @param array $options
     * @param Curl $curl
     *---------------------------------------------------------------------*/
    static public function douban_single($url,$html,$rule,&$options,&$curl){


    }
}