<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 分销系统
 * ======================================*/

namespace app\portal\ctrl;

use app\common\ctrl\BaseCtrl;
use core\Conf;

class DrpCtrl extends BaseCtrl
{
    protected $pwd='Djidksl$$EER4ds58^)UUsshcmO';

    /** ------------------------------------------------------------------
     * 推广链接
     *---------------------------------------------------------------------*/
    public function link(){
        $model=app('\app\portal\model\User');
        $data=$model->getOne($_SESSION['uid'],'sub_domain');
        $this->_display('user/link',[
            'title'=>'我的推广链接',
            'sub_domain'=>$data['sub_domain']
        ],false);
    }

    /** ------------------------------------------------------------------
     * 推广链接添加
     *---------------------------------------------------------------------*/
    public function link_verify(){
        $subDomain=trim(post('sub_domain'));
        //检测二级域名
        if(!$subDomain){
            json(['code'=>1,'msg'=>'二级域名不能为空']);
            return ;
        }
        $subDomain=strtolower($subDomain);
        //排除www
        if($subDomain==='www'){
            json(['code'=>2,'msg'=>'"www"已经被使用']);
            return ;
        }
        $length=strlen($subDomain);
        if($length<3){
            json(['code'=>3,'msg'=>'长度不足，必须大于2']);
            return ;
        }
        if($length>100){
            json(['code'=>4,'msg'=>'长度过长，不能大于100']);
            return ;
        }
        //正则检测
        if(preg_match('/^[0-9a-z][0-9a-z\-]{2,100}$/i',$subDomain)!==1){
            json(['code'=>5,'msg'=>'格式不正确，只能是字母、数字和“-”，且开头不能是“-”']);
            return ;
        }
        $model=app('\app\portal\model\User');
        $data=$model->select('sub_domain')->eq('id',$_SESSION['uid'])->find(null,true);
        //检测是否已经开通二级域名
        if($data['sub_domain']!==''){
            json(['code'=>6,'msg'=>'你已经开通二级域名']);
            return ;
        }
        //本地检测是否已经存在
        if($model->select('id')->eq('sub_domain',$subDomain)->find(null,true)){
            json(['code'=>7,'msg'=>'"'.$subDomain.'"已被使用，请更换其他的二级域名']);
            return ;
        }
        //远程检测是否已经存在
        if(!$this->subDomainRemote($subDomain,'query',$msg)){
            json(['code'=>8,'msg'=>$msg]);
            return ;
        }
        //远程入库
        if(!$this->subDomainRemote($subDomain,'insert',$msg)){
            json(['code'=>9,'msg'=>$msg]);
            return ;
        }
        //本地入库
        $model->eq('id',$_SESSION['uid'])->update(['sub_domain'=>$subDomain]);
        json(['code'=>0,'msg'=>'成功入库','action'=>url('portal/drp/link')]);
    }

    /** ------------------------------------------------------------------
     * 从远程服务器中查询或插入二级域名
     * @param string $sub_domain 二级域名
     * @param string $type 方式：query|insert
     * @param string $msg 信息提示
     * @return bool
     *---------------------------------------------------------------------*/
    protected function subDomainRemote($sub_domain,$type,&$msg=''){
        $ex_domain=Conf::get('ex_domain','site');
        if(!$ex_domain){
            $msg='还没有设置远程服务的域名，请联系管理员';
            return false;
        }
        $url='http://www.'.$ex_domain.'/api/domain/';
        if($type=='query'){
            $url.='sub_domain_query';
        }else{
            $url.='sub_domain_insert';
        }
        $ret= $this->curl($url,['sub_domain'=>$sub_domain,'pwd'=>$this->pwd,'type'=>'wezhubo'],$msg);
        if(isset($ret['code']) && $ret['code']===0){
            $msg='';
            return true;
        }
        $msg=$ret['msg'] ?:'没有msg';
        return false;
    }

    /** ------------------------------------------------------------------
     * curl的post访问
     * @param string $url
     * @param array $data
     * @param string $msg
     * @return bool|array
     *---------------------------------------------------------------------*/
    protected function curl($url,$data,&$msg){
        $curl=app('\extend\Curl');
        $ret=$curl->post($url,$data);
        if($ret===false){
            $msg='远程服务器无法访问';
            return false;
        }
        $ret=json_decode ($ret,true);
        if($ret===null){
            $msg='远程服务器无法返回正确的回应';
            return false;
        }
        return $ret;
    }

    /** ------------------------------------------------------------------
     * 下线管理
     *---------------------------------------------------------------------*/
    public function manage(){
        $model=app('\app\portal\model\User');
        $data=$model->getOne($_SESSION['uid'],'id,path,level,pid');
        $fenxiao=Conf::get('fenxiao','site');
        for ($i=0;$i<$fenxiao['level'];$i++){
            $count=$model->count(['where'=>[['path','like',$_SESSION['uid'].'-%'],'level'=>$data['level']+1+$i]]);
            $this->_assign('count'.$i,$count);
            if($count>0)
                $this->_assign('data'.$i,$model->select('username,id')->like('path',$_SESSION['uid'].'-%')->eq('level',$data['level']+1+$i)->order('id')->limit(10)->findAll(true));
            else
                $this->_assign('data'.$i,[]);
        }
        $this->_display('user/manage',[
            'title'=>'下线管理',
            //'data'=>$data
        ],false);
    }


}