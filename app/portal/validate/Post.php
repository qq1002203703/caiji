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
 *portal_post表验证规则
 * ======================================*/


namespace app\portal\validate;
use core\Validate;

class Post extends Validate
{
    protected $rule=[
        'title'  => 'require',
        'content'=>'require',
        'cateid'=>'require|checkCateid',
        'type'=>'integer|between:1,2',
        'status'=>'integer|between:0,1',
        'is_top'=>'integer|between:0,1',
        'recommended'=>'integer|between:0,1',
        'allow_comment'=>'integer|between:0,1',
        'create_time'=>'date',
        'published_time'=>'date',
        'views'=>'integer|egt:0',
        'likes'=>'integer|egt:0',
        'downloads'=>'integer|egt:0',
        'pay_type'=>'integer|between:0,2',
        'money'=>'float|egt:0',
        'coin'=>'integer|egt:0'

    ];
    protected $message  =   [
        'title.require'=>'标题不能为空',
        'content.require'=>'内容不能为空',
        'cid.require'=>'分类不能为空',
        'cid.checkCid'=>'所填分类不存在',
        'type'=>'种类只能是1和2',
        'status'=>'状态只能是0和1',
        'is_top'=>'是否置项只能是0和1',
        'recommended'=>'是否推荐只能是0和1',
        'allow_comment'=>'允许评论只能是0和1',
        'create_time'=>'创建时间不是一个有效的日期或时间格式',
        'published_time'=>'发布时间不是一个有效的日期或时间格式',
        'views'=>'查看次数必须是大于或等于0的整数',
        'likes'=>'点赞次数必须是大于或等于0的整数',
        'downloads'=>'下载次数必须是大于或等于0的整数',
        '支付种类'=>' 支付种类只能是0、1和2',
        'coin'=>'金币数只能是大于或等于0的整数',
        'money'=>'金钱数只能是大于或等于0的数字',
    ];

    public function checkCateid($cid){
        $arr=explode(',',$cid);
        $cateModel=app('\app\portal\model\PortalCategory');
        $result=$cateModel->in('id', $arr)->findAll(true);
        if(count($arr) == count($result)){
            return true;
        }
        return false;
    }
}