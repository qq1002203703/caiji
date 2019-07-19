<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 后台api类 所有后台的api都继续它
 * ======================================*/


namespace app\common\ctrl;


class ApiAdminCtrl extends ApiCtrl
{
    //初始化
    protected function _init(){
        $this->_must_login();
        if (! $this->_checkIsAdmin()){
            json(['status'=>10,'msg'=>'你没有权限']);
            die();
        }
    }
}