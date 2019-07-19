<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 网站全局变量验证类
 * ======================================*/


namespace app\admin\validate;
use core\Validate;

class KeywordLink extends Validate
{
    protected $rule=[
        'keyword'  => ['require','checkKeyword'],
        'url'=>'require',
        'status'=>'integer|between:0,1',
        'weight'=>'integer'
    ];
    protected $field=[
        'keyword'=>'关键词',
        'url'=>'链接',
        'status'=>'状态',
        'weight'=>'权重'
    ];
    protected $message=[
        'keyword.checkKeyword'     => ':attribute已经存在',
    ];
    protected function checkKeyword($keyword,$rule,$data){
        $id=$data['id'] ?? 0;
        return app('\app\admin\model\KeywordLink')->checkKeyword($keyword,$id);
    }
}