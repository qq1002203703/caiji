<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 *采集队列验证类
 * ======================================*/

namespace app\caiji\validate;
use core\Validate;

class CaijiPage extends Validate
{
    protected $rule=[
        'name'  => 'require',
        'type'=>'integer|between:0,1',
        'url'=>'require',
        'status'=>'integer|between:0,1',
        'options'=>'checkOptions'
    ];
    protected $field=[
        'type'=>'种类',
        'url'=>'目标网址',
        'name'=>'名称',
        'status'=>'状态',
        'options'=>'额外参数'
    ];

    protected $message=[
        'options'     => ':attribute不是有效json格式字符串',
    ];

    protected function checkOptions($str){
        return (json_decode($str)===null ? false : true);
    }

   /* protected function checkName($name,$rule,$data){
        $id=$data['id'] ?? 0;
        return   app('\app\caiji\model\Caiji')->checkName($name,$id);
    }*/
}