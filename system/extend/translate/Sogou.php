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


namespace extend\translate;


use extend\HttpClient;

class Sogou
{
    static public $url='https://translate.sogou.com/reventondc/translateV2';

    static public function post($text){
        $http=New HttpClient(['opt'=>[
            CURLOPT_REFERER=>'https://translate.sogou.com/'
        ]]);
        $http->httpSetting([
            'isRandomUserAgent'=>true,

        ]);
        return $http->http(self::$url,'post',[
            'from'=>'auto',
            'to'=>'zh-CHS',
            'text'=>$text,
            'client'=>'pc',
            'fr'=>'browser_pc',
            'pid'=>'sogou-dict-vr',
            'dict'=>true,
            'word_group'=>true,
            'second_query'=>true,
            'needQc'=>1,
            'uuid'=>'',
            's'=>''
        ]);
    }

    static protected function sign($from,$content){
        #构建加密算法
        $sign = '' . 'auto' . $from . $content . 'fb3eeb5c203d77031d19ad06a6a0da30';
        return md5($sign);
    }

}