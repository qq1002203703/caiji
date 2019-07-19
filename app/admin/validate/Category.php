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

class Category extends Validate
{
    protected $rule=[
        'type'=>['require','regex' => '/^[a-zA-Z][A-Za-z0-9_]{1,39}$/',],//必须先检测这项
        'name'  => ['require','min'=>2,'max' => 100,'checkName'],
        'pid'=>'require|checkPid',
        'status'=>'integer|between:0,1'
    ];
    protected $field=[
        'type'=>'分类种类',
        'name'=>'分类名称',
        'pid'=>'父id',
        'status'=>'显示'
    ];
    protected $message  =   [
        'type.regex'=>'分类种类只能是字母、数字和下划线的组合，且开头必须是字母，长度为2~40',
        'name.checkName'     => '此分类名已经存在',
        'pid.checkPid'=>'父id不存在',
    ];
    protected function checkName($name,$rule,$data){
        unset($rule);
        $id=$data['id'] ?? 0;
        return   app('\app\admin\model\Category')->setType($data['type'])->checkName($name,$id);
    }
    protected function checkPid($pid,$rule,$data){
        unset($rule);
        $id=$data['id'] ?? 0;
        return app('\app\admin\model\Category')->setType($data['type'])->checkPid($pid,$id);
    }
}