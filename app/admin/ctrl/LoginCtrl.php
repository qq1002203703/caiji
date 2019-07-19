<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 *
 * ======================================*/
namespace app\admin\ctrl;
use  core\Ctrl;
use app\common\ctrl\Func;
use app\portal\model\User;
class LoginCtrl extends Ctrl
{
    //默认初始化，覆盖了父类的同名方法
    protected function _config(){
        //设置后台模板路径
        $this->view->config(Func::_setAdminTplPath());
    }
    /**--------------------------------------------------------
     * 登陆页
     *--------------------------------------------------------*/
    public function login(){
        if($this->_is_login()){
            if($this->_checkIsAdmin())
                $this->_redirect('admin/user/index','你已经登陆',2);
            else
                $this->_redirect('admin/login/logout','你的权限不足',2);
        }
        $title='后台登陆';
        $this->_display('index/login',['title'=>$title]);
    }

    /** ------------------------------------------------------------------
     * 接收登陆页提交过来的数据，判断是否可以登陆
     *--------------------------------------------------------------------*/
    public function login_verify(){
        $username=post('username');
        $pwd=post('password');
        $imagecode=post('imagecode');
        if(!$imagecode || strtolower($_SESSION['captch']) !== strtolower($imagecode)){
            //json(['status'=>1,'msg'=>'图形验证码错误']);
            $this->_redirect('admin/login/login','图形验证码错误',2,4);
            return ;
        }
        if($username && $pwd){
            $m=new User();
            $ret=$m->eq('username',$username)->find(null,true);
            if($ret  &&  password_verify($pwd, $ret['password'])){
                Func::_setUserSession($ret);
                $this->_redirect('admin/user/index','成功登陆',1,2);
            }
        }
        $this->_redirect('admin/login/login','用户名或密码错误',2,4);
    }

    /**--------------------------------------------------------
     * 退出登陆
     *---------------------------------------------------------*/
    public function logout(){
       $this->_logout();
        $this->_redirect('admin/login/login','安全退出');
    }
}