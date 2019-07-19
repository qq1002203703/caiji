<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 网站全局变量验证类
 * ======================================*/


namespace app\admin\validate;
use core\Validate;

class Option extends Validate
{
    protected $rule=[
        'type'=>['require','regex' => '/^[a-zA-Z][A-Za-z0-9_]{0,49}$/',],//必须先检测这项
        'json'=>'integer|between:0,1',
        'name'  => ['require','regex' => '/^[a-zA-Z_][a-zA-Z0-9_]*$/','checkName'],
        'status'=>'integer|between:0,1',
        'value'=>'checkJson',
    ];
    protected $field=[
        'type'=>'种类',
        'json'=>'格式',
        'name'=>'变量名',
        'status'=>'状态',
        'value'=>'变量值'
    ];
    protected $message=[
        'type.regex'=>':attribute只能是字母、数字和下划线的组合，且开头必须是字母，长度为1~40',
        'name.regex' => ':attribute只能是字母、数字和下划线，且必须以字母或下划线开头',
        'name.checkName'     => ':attribute已经存在',
        'value'=>':attribute json格式不对'
    ];
    protected function checkName($name,$rule,$data){
        return   app('\app\admin\model\Option')->setType($data['type'])->checkName($name);
    }
    protected function checkJson($str,$rule,$data){
        if(isset($data['json']) && ((int)$data['json'])==1){
            $json=json_decode($str);
            if($json===null)
                return false;
        }
        return true;
    }
}