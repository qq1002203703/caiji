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
//最新标签
function listNewestTags($limit){
    //$model=app('\app\admin\model\Tag');
    $data=app('\app\admin\model\Tag')->select('name,id,slug')->eq('status',1)->limit($limit)->order('id desc')->findAll(true);
    if($data){
        $html='';
        foreach ($data as $item){
            $html.='<a href="'.url('@tag@',['slug'=>$item['slug']]).'">'.$item['name'].'</a>';
        }
        return $html;
    }else{
        return '';
    }
}