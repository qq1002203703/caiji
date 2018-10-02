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
namespace app\portal\validate;
use core\Validate;

class User extends Validate
{
    protected $rule=[
        'username'  => ['require','regex' => '/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_-]{3,20}$/u','checkUsername'],
        'password'   => ['require','regex' =>'/.{6,30}/ ','confirm'=>'repassword'],
        'email' => 'require|email|checkEmail',
    ];
    protected $message  =   [
            'username.regex' => '用户名必须是3~20位的中文/字母/数字/下划线_及破折号-',
            'username.require'=>'用户名不能为空',
            'username.checkUsername'     => '此用户名已经被使用',
            'password.require'=>'密码不能为空',
            'password.regex' => '密码必须6~30位任意字符',
            'password.confirm' => '两次密码输入不相同',
             'email.require'=>'邮箱不能为空',
            'email.email'=>'邮箱格式不符',
            'email.checkEmail'=>'此邮箱已经被使用',
        ];
    protected function checkUsername($data){
        return   (new \app\portal\model\User())->checkUsername($data);
    }
    protected function checkEmail($data){
        return   (new \app\portal\model\User()) ->checkEmail($data);
    }

}