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
class Comment extends Validate
{
    protected $rule=[
        'oid'=>'require|integer|gt:0',
        'table_name'=>'require',
        'pid'=>'integer|egt:0',
        'content'=>'require',
        'token'=>'token'
    ];
    protected $field=[
        'table_name'=>'表名',
        'pid'=>'父id',
        'content'=>'内容',
        'token'=>'令牌',
        'oid'=>'内页oid'
    ];

}