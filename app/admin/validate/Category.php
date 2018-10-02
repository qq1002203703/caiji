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
 *通用分类验证
 * ======================================*/
namespace app\admin\validate;
use core\Validate;

class Category extends Validate
{
    protected $rule=[
        'type'=>['require','min'=>1,'max'=>20],
        'name'  => ['require','min'=>2,'max' => 100,'checkName'],
        'pid'=>'require|checkPid',
    ];
    protected $message  =   [
        'type'=>'种类不能为空，且字数在1~20',
        'name.require'=>'分类名不能为空',
        'name.min'=>'分类名长度2~100',
        'name.max'=>'分类名长度2~100',
        'name.checkName'     => '此分类名已经存在',
        'pid.checkPid'=>'父id不存在',
        'pid.require'=>'父id不能为空'
    ];
    protected function checkName($name,$rule,$data){
        //unset($rule);
        $id=$data['id'] ?? 0;
        return   app('\app\admin\model\Category',['type'=>$data['type']])->checkName($name,$id);
    }
    protected function checkPid($pid,$rule,$data){
        //unset($rule);
        $id=$data['id'] ?? 0;
        return app('\app\admin\model\Category',['type'=>$data['type']]) ->checkPid($pid,$id);
    }
}