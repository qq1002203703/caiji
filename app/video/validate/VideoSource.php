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

class VideoSource extends Validate
{
    protected $rule=[
        'name'  => 'require',
        'url'=>'require',
        'type'=>'require|in:m3u8,xigua,xunlei,xfplay,baidupan',
        'vid'=>'require|checkVid',
        'status'=>'integer|between:0,1',
        'iscaiji'=>'integer|between:0,1',
        'isend'=>'integer|between:0,1',
        'update_time'=>'date',
    ];
    protected $field=[
        'type'=>'种类',
        'name'=>'来源名称',
        'vid'=>'视频id',
        'url'=>'资源地址',
        'status'=>'状态',
        'iscaiji'=>'是否采集',
        'isend'=>'是否完结',
        'update_time'=>'更新时间',
    ];
    protected $message  =   [
        'vid.checkVid'=>'所填:attribute不存在',
    ];
    public function checkVid($vid){
        $model=app('\app\video\model\Post');
        $result=$model->eq('id',$vid)->find(null,true);
        return $result ? true :false;
    }

}