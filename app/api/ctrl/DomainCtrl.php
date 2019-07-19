<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 *  二级域名添加接口
 * ======================================*/

namespace app\api\ctrl;

use app\common\ctrl\ApiCtrl;
use core\Conf;

class DomainCtrl extends ApiCtrl
{
    //接口访问密码
    protected $pwd='';

    protected function _init(){
        $this->pwd=Conf::get('pwd','config');
        $this->checkPermissions();
    }
    /** ------------------------------------------------------------------
     * 二级域名是否被使用检测接口，method方式为:post,参数如下：
     *  sub_domain  string  二级域名    必须
     *  pwd string 接口访问密码   必须
     *---------------------------------------------------------------------*/
    public function sub_domain_query(){
        $sub_domain=trim(post('sub_domain'));
        if(!$sub_domain){
            json(['code'=>1,'msg'=>'不能为空']);
            return ;
        }
        $model=new \core\Model();
        if($model->from('domain')->eq('sub_domain',$sub_domain)->find(null,true)){
            json(['code'=>2,'msg'=>'"'.$sub_domain.'"已经被使用']);
            return ;
        }
        json(['code'=>0,'msg'=>'"'.$sub_domain.'"未被使用']);
    }
    /** ------------------------------------------------------------------
     * 二级域名入库接口，method方式为:post,参数如下：
     *  sub_domain  string  二级域名    必须
     *  type  string  种类    必须
     *  pwd string 接口访问密码   必须
     *---------------------------------------------------------------------*/
    public function sub_domain_insert(){
        $sub_domain=trim(post('sub_domain'));
        $type=trim(post('type'));
        if(!$sub_domain || !$type){
            json(['code'=>1,'msg'=>'不能为空']);
            return ;
        }
        $model=new \core\Model();
        $model->table='domain';
        $n=$model->_exec('INSERT ignore INTO `table`( `sub_domain`,type) VALUES (?,?)',[$sub_domain,$type],true);
        if($n>0)
            json(['code'=>0,'msg'=>'成功入库']);
        else
            json(['code'=>2,'msg'=>'"'.$sub_domain.'"已经被使用']);
    }

    /** ------------------------------------------------------------------
     * 接口权限检测
     *--------------------------------------------------------------------*/
    protected function checkPermissions(){
        if (trim(post('pwd')) !== $this->pwd){
            json(['code'=>100,'msg'=>'Fuck You!']);
            exit();
        }
    }

}