<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 内容采集后入库
 * ======================================*/

namespace shell\caiji\plugin;
use extend\Selector;

class Getlist
{
    static public function wezhubo($html,$rule,$url){
       //dump($html);
       //dump($rule);
       //dump($url);
        $url_res=substr($url,0,strrpos($url, '.'));
        //1、从$html筛选出列表的最后一条链接
        //2、获取数字
        //3、合成链接
        $res=Selector::find($html,'regex,multi','<a href="(?P<url>[^"]+)">','url','<div class="pagebar">{%|||%}</div>');
        if($res===false){//正则出错
            echo '使用插件时：正则出错：'.Selector::getError().PHP_EOL;
            return 1;
        }elseif(!$res){//正则无法匹配到结果
            echo '使用插件时：没有配置到结果：'.Selector::getError().PHP_EOL;
            return 2;
        }
        $totalPages=end($res);
        unset($res);
        $totalPages=strrchr($totalPages,'_');
        if($totalPages===false){
            $totalPages=1;
        }else{
            $totalPages=(int)str_replace(['.html','_'],'',$totalPages);
        }
        //dump($totalPages);
        //dump($url_res);
        $url_res.='_{%0,1,'.$totalPages.',1,0,0%}'.'.html';
        return $url_res;
    }
}