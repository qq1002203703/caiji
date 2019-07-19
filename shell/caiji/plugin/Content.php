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


class Content
{
    static public function douban_before($data){
        $data['url']='https://api.douban.com/v2/movie/subject/'.$data['from_id'];
        return $data;
    }

    static public function douban_after($html){
        if(strpos($html,'{"msg":"movie_not_found"')!==false || strpos($html,'"msg":"invalid_apikey"')!==false){
            return 102;
        }
        return $html;
    }

}