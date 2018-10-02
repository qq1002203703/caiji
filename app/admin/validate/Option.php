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


namespace app\admin\validate;
use core\Validate;

class Option extends Validate
{
    protected $rule=[
        'name'  => ['require','regex' => '/^[a-zA-Z_][a-zA-Z0-9_]*$/','checkName'],
        'status'=>'integer|between:0,1',
    ];
    protected $message=[
        'name.require'=>'名称不能为空',
        'name.regex' => '名称只能是字母、数字和下划线，且必须以字母或下划线开头',
        'name.checkName'     => '此名称已经存在',
        'status'=>'状态只能是0和1',
    ];
    protected function checkName($name){
        return   app('\app\admin\model\Option')->checkName($name);
    }
}