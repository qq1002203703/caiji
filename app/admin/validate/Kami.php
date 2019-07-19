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
class Kami extends Validate
{
    protected $rule=[
        'name'  => ['require','regex' => '/^[a-zA-Z][A-Za-z0-9_]*$/','checkName'],
        'value'=>'require|number|gt:0',
        'currency'=>'integer|between:0,1',
        'text'=>'require',
        'type'=>'require|integer|between:0,1'
    ];
    protected $field=[
        'name'=>'名称',
        'value'=>'值',
        'currency'=>'币种',
        'text'=>'描述',
        'type'=>'卡密类型'
    ];
    protected $message  =   [
        'name.checkName'     => '此名称已经存在',
        'name.regex'=>'名称只能是字母、数字和下划线的组合，且开头必须是字母',
    ];

    protected function checkName($name,$rule,$data){
        unset($rule);
        $id=$data['id'] ?? 0;
        return   app('\app\admin\model\Kami')->typeCheckName($name,$id);
    }

}