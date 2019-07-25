<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 给别的网站调用的接口
 * ======================================*/


namespace app\api\ctrl;

use core\Conf;
use app\common\ctrl\ApiCtrl;

class DataCtrl extends ApiCtrl
{
    //接口访问密码
    protected $pwd='';
    protected function _init(){
        $this->pwd=Conf::get('pwd','config');
        $this->checkPermissions();
    }

    /** ------------------------------------------------------------------
     * 接口权限检测
     *--------------------------------------------------------------------*/
    protected function checkPermissions(){
        if (trim(get('pwd')) !== $this->pwd){
            json(['code'=>100,'msg'=>'Fuck You!']);
            exit();
        }
    }
    /** ------------------------------------------------------------------
     * 获取一个用户的信息
     *---------------------------------------------------------------------*/
    public function user(){
        $data=app('\core\Model')->select('id,name,username,text,signature,city,birthday')->from('caiji_renren_name')->ne('md5','')->eq('isfabu',0)->find(null,true);
        if(!$data){
            json(['code'=>1,'msg'=>'没有数据了','data'=>[]]);
            return;
        }
        if(get('rn','int',0)){
            if(!app('\core\Model')->from('caiji_renren_name')->eq('name',$data['name'])->update(['isfabu'=>1]))
                json(['code'=>2,'msg'=>'无法更新数据','data'=>[]]);
        }
        json(['code'=>0,'msg'=>'success','data'=>$data]);
    }

    /** ------------------------------------------------------------------
     * 设置用户的发布状态为已发布
     * 参数 name : string
     *---------------------------------------------------------------------*/
    public function set_fabu(){
        $name=(string)get('name');
        if(!$name){
            json(['code'=>1,'msg'=>'name不能为空']);
            return;
        }
        if(app('\core\Model')->from('caiji_renren_name')->eq('name',$name)->update(['isfabu'=>1]))
            json(['code'=>0,'msg'=>'success']);
        else
            json(['code'=>2,'msg'=>'更新失败']);
    }

    public function test(){
        $user='';
        app('\app\portal\model\User')->randomUserEx($user);
    }
}