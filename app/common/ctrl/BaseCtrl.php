<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 *
 * QQ 46502166
 *
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
========================================================================================
 * 前台需要登陆访问的控制器，它继承了\system\Ctrl，并且被前台需要登陆访问的所有控制器继承
 * ========================================================================================*/
namespace app\common\ctrl;

class BaseCtrl extends \core\Ctrl
{
	protected function _init(){
        //1.必须登陆
       $this->_must_login();
        //提交用户
        $this->assign([
            'username'=>$_SESSION['username'],
        ]);
    }

	//不登陆 不让访问，直接退出登陆 并转到登陆页
    protected function _must_login()
    {
        if($this->_is_login()){
            return true;
        }
        $this->logout();
        $this->_redirect('index/login','你还没登陆，或登陆超时');
        exit();
    }

}