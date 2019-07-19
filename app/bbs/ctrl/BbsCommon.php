<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * Bbs模块公共操作类:主要是权限控制
 * ======================================*/

namespace app\bbs\ctrl;
use core\Ctrl;
class BbsCommon extends Ctrl
{
    /** ------------------------------------------------------------------
     * 权限检测
     * @param string $type
     * @param array $data
     * @return bool
     *--------------------------------------------------------------------*/
    protected function _checkPermissions($type,$data){
        if($this->_checkIsAdmin())
            return true;
        switch ($type){
            case 'show'://帖子阅读
                return $this->_checkShow($data);
            case 'show_list'://板块阅读
                return $this->_checkShow($data);
            case 'post_add'://发贴
                break;
            case 'post_add_m'://群发
                return false;
            case 'comment_add'://发评论
                break;
            case 'post_edit'://修改帖子
                break;
            case 'comment_edit'://修改评论
                break;
            case 'post_delete'://删除帖子
                break;
            case 'comment_delete'://删除评论
                break;
        }
        return true;
    }

    protected function _checkPostPermissions($data){
        //隐藏内容//附件权限 //收费内容
        if($this->_checkIsAdmin())
            return true;

        if(!$this->_is_login()) return false;
        if($data['buy_uid']){
            return in_array($_SESSION['uid'],explode(',',$data['buy_uid']));
        }
        return false;
    }

    protected function _checkShow($data){
        $data['post']=$data['post'] ?? '';
        $data['category']=$data['category'] ?? '';
        $postOption=json_decode($data['post']);
        $categoryOption=json_decode($data['category']);
        if($postOption || $categoryOption){
            if(!$this->_is_login())
                return false;
        }
        return true;
    }

    /** ------------------------------------------------------------------
     * 检测是否是管理员
     * @return bool
     *--------------------------------------------------------------------*/
    protected function _checkIsAdmin(){
        if(isset($_SESSION['user']['gid']) && $_SESSION['user']['gid']<10)
            return true;
        return false;
    }
}