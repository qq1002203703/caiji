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

use app\common\ctrl\ApiAdminCtrl;
use core\Session;

class ApiOptionCtrl extends ApiAdminCtrl
{
    protected $type='site';
    protected $typeMap=['site'=>'网站','portal'=>'门户','bbs'=>'论坛'];
    protected $allType;

    protected function _init(){
        parent::_init();
        $this->getType();
    }
    /** ------------------------------------------------------------------
     * 获取种类
     *--------------------------------------------------------------------*/
    protected function getType(){
        $this->allType=array_keys($this->typeMap);
        if(($type=get('type')) && in_array($type,$this->allType)){
            $this->type=$type;
        }
    }
    /** ------------------------------------------------------------------
     * 全局变量设置
     *---------------------------------------------------------------------*/
    public function option_all(){
        if($_POST){
            $model=app('\app\admin\model\Option');
            $model->setType($this->type)->update_option($_POST);
            return json(['code'=>0,'msg'=>'成功更新','action'=>url('admin/option/all')]);
        }
        return json(['code'=>1,'msg'=>'数据为空']);
    }

    /** ------------------------------------------------------------------
     * 网站全局变量添加
     *--------------------------------------------------------------------*/
    public function option_add(){
        if($_POST){
            $validate=app('\app\admin\validate\Option');
            if($validate->check($_POST)){
                $model=app('\app\admin\model\Option');
                if($ret=$model->add($_POST)){
                    return json(['code'=>0,'msg'=>'成功添加“'.$_POST['name'].',“，你可以继续添加下一个变量','action'=>url('admin/option/add')]);
                }else{
                    return  json(['code'=>2,'msg'=>'入库失败']);
                }
            }else{
                return json(['code'=>3,'msg'=>$validate->getError()]);
            }
        }
        return json(['code'=>1,'msg'=>'数据为空']);
    }

    /** ------------------------------------------------------------------
     * 网站全局设置缓存更新
     *--------------------------------------------------------------------*/
    public function option_cache(){
        $model=app('\app\admin\model\Option');
        $model->setType($this->type)->update_cache();
        json(['code'=>0,'msg'=>'缓存成功更新']);
    }

    /** ------------------------------------------------------------------
     * 网站全局变量删除
     *---------------------------------------------------------------------*/
    public function option_del(){
        $name=get('name');
        if(!$name || is_array($name)){
            return  json(['code'=>1,'msg'=>'变量名格式不符']);
        }
        $model=app('\app\admin\model\Option');
        if($model->setType($this->type)->del($name) >0){
            return  json(['code'=>0,'msg'=>'成功删除变量','action'=>url('admin/option/all').'?type='.$this->type]);
        }
        return  json(['code'=>1,'msg'=>'不存在的变量名']);
    }

    /** ------------------------------------------------------------------
     * 修改密码
     *--------------------------------------------------------------------*/
    public function pwd(){
        $currentPwd=post('current_pwd');
        $password=post('password');
        $repassword=post('repassword');
        $username=post('username');
        if(!$currentPwd){
            return json(['code'=>1,'msg'=>'原密码不能为空']);
        }
        if(!$username && !$password){
            return json(['code'=>1,'msg'=>'没有修改，因为新用户名和新密码都为空']);
        }
        if($password && ($repassword !== $password))
            return json(['code'=>2,'msg'=>'新密码和重复密码两次输入不相同']);
        $m=app('app\portal\model\User');
        $uid=Session::get('uid');
        $ret=$m->select('password')->eq('id',$uid)->find(null,true);
        if(!password_verify($currentPwd, $ret['password']))
            return json(['code'=>3,'msg'=>'原密码不正确']);
        $data=[];
        if($password)  $data['password']=password_hash($password, PASSWORD_BCRYPT, array("cost" => 9));
        if($username) $data['username']=$username;
        if($m->eq('id',$uid)->update($data))
            return json(['code'=>0,'msg'=>'成功修改,请使用新用户名或密码重新登陆','action'=>url('admin/login/logout')]);
       return json(['code'=>4,'msg'=>'入库失败']);
    }
    /** ------------------------------------------------------------------
     * 个人信息
     *--------------------------------------------------------------------*/
    public function info(){
        $m=app('app\portal\model\User');
        $uid=Session::get('uid');
        $birthday=strtotime(post('birthday','',''));
        if($birthday===false)
            return json(['code'=>1,'msg'=>'生日不是一个有效的日期']);
        $m->eq('id',$uid)->update([
            'email'=>post('email','',''),
            'birthday'=>$birthday,
            'nickname'=>post('nickname','',''),
            'website'=>post('website','',''),
            'signature'=>post('signature','',''),
        ]);
        return json(['code'=>0,'msg'=>'成功修改']);
    }


}