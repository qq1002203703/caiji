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
 * portal_category表的验证
 * ======================================*/
namespace app\portal\validate;
use core\Validate;

class Category extends Validate
{
    protected $rule=[
        'name'  => ['require','regex' => '/^[\x{4e00}-\x{9fa5}a-zA-Z][\x{4e00}-\x{9fa5}a-zA-Z0-9_]*$/u','checkName'],
        'pid'=>'require|checkPid',
    ];
    protected $message  =   [
        'name.regex' => '分类名必须以中文/字母开头且后面只能是中文字母数字和下划线',
        'name.require'=>'分类名不能为空',
        'name.checkName'     => '此分类名已经存在',
        'pid.checkPid'=>'父id不存在',
        'pid.require'=>'父id不能为空'
    ];
    protected function checkName($data){
        return   app('\app\portal\model\PortalCategory')->checkName($data);
    }
    protected function checkPid($data){
        return app('\app\portal\model\PortalCategory') ->checkPid($data);
    }

}