<?php
/**
 * 后台indexr控制器，
 */
namespace app\admin\ctrl;

class UserCtrl extends \app\common\ctrl\AdminCtrl
{
    //后台首页
    public function index(){
        $this->_display('',['title'=>'首页']);
    }
}