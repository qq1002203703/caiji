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

namespace app\portal\validate;
use core\Conf;
use core\Validate;

class Post extends Validate
{
    protected $rule=[
        'type'=>'require|checkType',
        'pid'=>'integer|egt:0|checkPid',
        'title'  => 'require',
        'content'=>'require',
        'category_id'=>'require|checkCategoryId',
        'status'=>'integer|between:0,3',
        'is_top'=>'integer|between:0,1',
        'recommended'=>'integer|between:0,1',
        'allow_comment'=>'integer|between:0,1',
        'create_time'=>'date',
        'published_time'=>'date',
        'views'=>'integer|egt:0',
        'likes'=>'integer|egt:0',
        'downloads'=>'integer|egt:0',
        'permissions'=>'integer|gt:0',
        'money'=>'float|egt:0',
        'coin'=>'integer|egt:0',
        'children_id'=>['regex'=>'/^(\d[\d,]*)*\d$/'],
    ];
    protected $field=[
        'type'=>'频道',
        'title'=>'标题',
        'category_id'=>'分类',
        'content'=>'内容',
        'status'=>'状态',
        'is_top'=>'是否置顶',
        'recommended'=>'是否推荐',
        'allow_comment'=>'允许评论',
        'create_time'=>'创建时间',
        'published_time'=>'发布时间',
        'views'=>'查看次数',
        'likes'=>'点赞次数',
        'coin'=>'金币数',
        'money'=>'金钱数',
        'children_id'=>'下级id',
        'pid'=>'上级id'
    ];
    protected $message  =   [
        'category_id.checkCategoryId'=>'所填分类不存在',
        'pid.checkPid'=>'不存在的父id'
    ];
    public function checkCategoryId($cid,$rule,$data){
        $cateModel=app('\app\portal\model\PortalCategory');
        $cateModel->setType('portal_'.$data['type']);
        $result=$cateModel->getById($cid);
        return $result ? true :false;
    }
    protected function checkPid($pid,$rule,$data){
        $id=$data['id'] ?? 0;
        return app('\app\portal\model\PortalPost') ->checkPid($pid,$data['type'],$id);
    }
    public function checkType($type){
        $arr=array_keys(Conf::get('pindao','portal'));
        return in_array($type,$arr,true);
    }
}