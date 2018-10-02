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
 *
 * ======================================*/


namespace app\weixinqun\validate;
use core\Validate;

class Weinxinqun extends Validate
{
    protected $rule=[
        'title'  => 'require',
        'category_id'=>'require|checkCateid',
        'city_id'=>'require|checkCityid',
        'excerpt'=>'require',
        'type'=>'integer|between:1,3',
        'status'=>'integer|between:0,1',
        'is_top'=>'integer|between:0,1',
        'recommended'=>'integer|between:0,1',
        'allow_comment'=>'integer|between:0,1',
        'create_time'=>'date',
        'published_time'=>'date',
        'views'=>'integer|egt:0',
        'likes'=>'integer|egt:0',
        'weixinhao'=>'require'
    ];
    protected $message  =   [
        'title.require'=>'标题不能为空',
        'excerpt.require'=>'简介不能为空',
        'cid.require'=>'分类不能为空',
        'cid.checkCid'=>'所填分类不存在',
        'type'=>'种类只能是1、2和3',
        'status'=>'状态只能是0和1',
        'is_top'=>'是否置项只能是0和1',
        'recommended'=>'是否推荐只能是0和1',
        'allow_comment'=>'允许评论只能是0和1',
        'create_time'=>'创建时间不是一个有效的日期或时间格式',
        'published_time'=>'发布时间不是一个有效的日期或时间格式',
        'views'=>'查看次数必须是大于或等于0的整数',
        'likes'=>'点赞次数必须是大于或等于0的整数',
        'weixinhao'=>'微信号不能为空',
    ];
    public function checkCateid($cid){
        $cateModel=app('\app\admin\model\Category',['type'=>'weixinqun']);
        //$result=$cateModel->eq('type','weixinqun')->find($cid,true);
        if($cateModel->eq('type','weixinqun')->find($cid,true)){
            return true;
        }
        return false;
    }
}