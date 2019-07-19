<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 *  通用分类验证
 * ======================================*/
namespace app\admin\validate;
use core\Validate;
class Order extends Validate
{
    protected $rule=[
        //'type'=>['require','min'=>1,'max'=>20],//必须先检测这项
        'email'=>'email',
        'buy_num'=>'require|integer|gt:0',
        'oid'=>'require|integer|gt:0',
        'token'=>'token'
    ];
    protected $field=[
        'buy_num'=>'购买数',
        'email'=>'邮箱',
        'token'=>'令牌',
        'oid'=>'商品id'
    ];
    protected $message  =   [
        //'type'=>'种类不能为空，且字数在1~20',
        //'buy_num'=>'购买数必须是大于0的整数',
        //'price'=>'单价只能是大于或等于0的数字',
        //'uid'     => '此uid不存在',
    ];

}