<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 *portal_post表验证规则
 * ======================================*/

namespace app\video\validate;
use core\Validate;

class Post extends Validate
{
    protected $rule=[
        'title'  => 'require',
        'category_id'=>'require|checkCategoryId',
        'status'=>'integer|between:0,3',
        'is_top'=>'integer|between:0,1',
        'type'=>'integer|between:0,1',
        'recommended'=>'integer|between:0,1',
        'allow_comment'=>'integer|between:0,1',
        'create_time'=>'date',
        'date_published'=>'date',
        'views'=>'integer|egt:0',
        'likes'=>'integer|egt:0',
        'downloads'=>'integer|egt:0',
        'permissions'=>'integer|gt:0',
        'money'=>'float|egt:0',
        'coin'=>'integer|egt:0',
        'score'=>'float|egt:0',
    ];
    protected $field=[
        'type'=>'种类',
        'title'=>'标题',
        'category_id'=>'分类',
        'content'=>'简介',
        'status'=>'状态',
        'is_top'=>'是否置顶',
        'recommended'=>'是否推荐',
        'allow_comment'=>'允许评论',
        'create_time'=>'创建时间',
        'views'=>'查看次数',
        'likes'=>'点赞次数',
        'coin'=>'金币数',
        'money'=>'金钱数',
        'score'=>'评分',
        'date_published'=>'发行日期'
    ];
    protected $message  =   [
        'category_id.checkCategoryId'=>'所填分类不存在',
    ];
    public function checkCategoryId($cid,$rule,$data){
        $cateModel=app('\app\video\model\Category');
        $result=$cateModel->getById($cid);
        return $result ? true :false;
    }

}