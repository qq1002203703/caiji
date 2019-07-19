<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 模板函数
 * ======================================*/
function getThumb($thumb,$format=''){
    if(!$thumb)
        return '/uploads/images/no.gif';
    if($format)
        return $thumb.'_'.$format.'.jpg';
    return $thumb;
}

function getThumbFormat($module='portal'){
    $setting=\core\Conf::get('thumb',$module);
    $cut=strstr($setting,',',true);
    if($cut!==false)
        $setting=$cut;
    return $setting;
}

function tagTypeUnique($type){
    if(strpos($type,',') ===false)
        return $type;
    $arr=explode(',',$type);
    return implode(',',array_unique($arr));
}