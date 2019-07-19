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

class Queue extends Validate
{
    protected $rule=[
        'run_time'  => ['require','date'],
        'type'=>'integer|between:0,1',
        'del_type'=>'integer|between:0,1',
        'status'=>'integer|between:0,1',
        'callback'=>['require','checkCallback']
    ];
    protected $message=[
        'run_time.require'=>'执行时间不能为空',
        'run_time.date' => '执行时间不是一个有效的日期或时间格式',
        'type'=>'种类只能是0和1',
        'del_type'=>'删除方式只能是0和1',
        'status'=>'状态只能是0和1',
        'callback.require'     => '回调函数或方法不能为空',
        'callback.checkCallback'     => '回调函数或方法不可回调，请检查格式是否正确和此函数或方法是否存在',
    ];
    protected function checkCallback($name){
        return is_callable($name.'::create');
    }
}