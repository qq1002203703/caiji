<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 *
 * QQ 46502166
 *
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 *========================================================================================
 * Api的基础控制器，所有和Api相关的Ctrl，都直接或间接继承它
 *========================================================================================*/

namespace app\common\ctrl;
use core\Session;
class ApiCtrl
{
    public function __call($name, $arguments)
    {
		show_error('类"'.__CLASS__.'"中不存在"'.$name.'()"方法');
    }

    public function __construct(){
        //初始化
        if(method_exists($this,'_init')) {
            $this->_init();
        }
    }

    //必须登陆才可以访问后台
    protected function _must_login(){
        if($this->_is_login()){
            return true;
        }
        $this->_logout();
        json(['status'=>10,'msg'=>'你还没有登陆']);
        die();
    }

    /** ------------------------------------------------------------------
     * 检测当前用户是否是管理员
     * @return bool
     *--------------------------------------------------------------------*/
    protected function _checkIsAdmin(){
        $gid=Session::get('user.gid',false);
        return ($gid && $gid <10);
    }

    /**
     * 判断是否已经登陆
     * @return bool
     */
    protected function _is_login(){
        return (isset($_SESSION['islogin']) && $_SESSION['islogin']);
    }

    //退出登陆
    protected function _logout(){
        $_SESSION=array();
        if(isset($_COOKIE[session_name()])){
            setcookie(session_name(),'',time()-3600,'/');
        }
        session_destroy();
    }

    protected function _getQuery($data,$method='get'){
        $where=[];
        $map=[];
        foreach ($data as $k =>$item){
            $current=$method($k,$item['f'],$item['d']);
            if($item['w']){
                $where[]=[ $item['fi'] ?? $k,$item['w'],$current];
            }
            $map[$k]=$current;
        }
        return [$where,$map,http_build_query($map)];
    }
}