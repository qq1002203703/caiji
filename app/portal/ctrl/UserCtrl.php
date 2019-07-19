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


namespace app\portal\ctrl;
use app\common\ctrl\BaseCtrl;
use core\Session;

class UserCtrl extends BaseCtrl
{
    public function info(){
        $model=app('\app\portal\model\User');
        $this->_display('user/info',[
            'title'=>'我的资料',
            'data'=>$model->getOne($_SESSION['uid'])
        ],false);
    }

    /**------------------------------------------------------------------
     * 邮箱修改：必须post参数：email,vercode,imagecode
     *---------------------------------------------------------------------*/
    public function email_mod(){
        $this->phone_email_mod(post('email','',''),post('vercode','',''),post('imagecode','',''),'email');
    }
    /**------------------------------------------------------------------
     * 电话修改：必须post参数：phone,vercode,imagecode
     *---------------------------------------------------------------------*/
    public function phone_mod(){
        $this->phone_email_mod(post('phone','',''),post('vercode','',''),post('imagecode','',''),'phone');
    }

    /**------------------------------------------------------------------
     * 手机或邮箱修改:修改前必须发送验证码
     * @param string $name  新的手机或邮箱
     * @param string $vercode  手机或邮箱接收到的验证码
     * @param string $imagecode 图形验证码
     * @param $type 'email' 或 'phone'
     *--------------------------------------------------------------------*/
    protected function phone_email_mod($name,$vercode,$imagecode,$type){
        $id=Session::get('uid',0);
        if(!$id){
            json(['status'=>1,'msg'=>'你还没有登陆']);
            $this->_logout();
            return;
        }
        //检测验证方式
        if(!$type || !in_array($type,['email','phone'])){
            json(['status'=>1,'msg'=>'验证方式错误']);
            return ;
        }
        //检测验证码
        if(!$imagecode || strtolower($_SESSION['captch']) !== strtolower($imagecode)){
            json(['status'=>1,'msg'=>'图形验证码错误']);
            return ;
        }
        $typeMap=['email'=>'邮箱','phone'=>'电话'];
        //检测phone_email是否为空
        if(!$name){
            json(['status'=>1,'msg'=>$typeMap[$type].'不能为空']);
            return ;
        }
        //检测接收的验证码是否正确
        $user=app('\app\portal\model\User');
        if(! $user->checkReceiptCode($name,$vercode,$type)){
            json(['status'=>1,'msg'=>$typeMap[$type].'接收的验证码不正确']);
            return ;
        }
        $user->eq('id',$id)->update([$type,$name]);
        json(['status'=>0,'msg'=>'成功更改'.$typeMap[$type],'action'=>url('portal/user/info')]);
    }
    public function dizhi(){

    }
    /*public function info(){

    }*/

    //我的购物车
    public function shopcart(){
        echo '我的购物车';
    }

    public function myorder(){
        echo url('portal/shop/order').'<br>';
        echo url('portal/user/myorder').'<br>';
    }
}