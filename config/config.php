<?php
//普通的配置文件示例
return array(
    //模板参数设置，没有特别说明所有路径都相对于ROOT目录
    'template'=>[
        //后台模板存放目录
        'admin_path'=>'app'.DIRECTORY_SEPARATOR.'common'.DIRECTORY_SEPARATOR.'tpl',
        //后台模板缓存存放目录
        'admin_cache_path'=>'cache'. DIRECTORY_SEPARATOR .'tpl'.DIRECTORY_SEPARATOR .'admin',
        //前台模板存放目录
        'view_path'=>'tpl'.DIRECTORY_SEPARATOR.'pc',
        //前台模板缓存存放目录
        'cache_path'=>'cache'. DIRECTORY_SEPARATOR .'tpl'.DIRECTORY_SEPARATOR .'pc',
        //前台是否开启专用的移动端模板
        'open_mobile_tpl'=>false,
        //前台移动端模板存放目录
        'mobile_path'=>'tpl'.DIRECTORY_SEPARATOR.'mobile',
        //前台移动端模板缓存存放目录
        'cache_path_mobile'=>'cache'. DIRECTORY_SEPARATOR .'tpl'.DIRECTORY_SEPARATOR .'mobile',
    ],

);