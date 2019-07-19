<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 卡密模块
 * ======================================*/


namespace app\admin\ctrl;

use app\common\ctrl\AdminCtrl;
use core\Conf;
use extend\Paginator;

class KamiCtrl extends AdminCtrl
{
    //卡密种类设置
    public function setting(){
        $model=app('\app\admin\model\Kami');
        $data=$model->from('kami_type')->order('id')->limit(100)->findAll(true);
        $this->_display('',[
            'title'=> '卡密设置',
             'data'=>$data
        ]);
    }

    //卡密种类修改
    public function edit(){
        $id=get('id','int');
        $model=app('\app\admin\model\Kami');
        $data=$model->from('kami_type')->eq('id',$id)->find(null,true);
        if(!$data)
            $this->_redirect('admin/kami/setting','不存在的卡密种类');
        $this->_display('',[
            'title'=> '卡密修改',
            'data'=>$data
        ]);
    }

    //卡密列表
    public function list(){
        $perPage=(int)get('pp','int',50);
        $currentPage=(int)get('page','int',1);
        $data=[];
        $page='';
        $model=app('\app\admin\model\Kami');
        $where=[];
        $url='';
        $get['status']=(int)get('status','int',3);
        $get['type']=(int)get('type','int',0);
        $get['pp']=$perPage;
        if($get['ka']=get('ka','','')){
            $url.='&ka='.$get['ka'];
            $where[]=['ka','eq',$get['ka']];
        }else{
            if($get['status'] <3 && $get['status']>=0){
                $where[]=['status','eq',$get['status']];
                $url.='&status='.$get['status'];
            }
            if($get['type'] >0){
                $where[]=['type','eq',$get['type']];
                $url.='&type='.$get['type'];
            }
        }
        $total=$model->count(['where'=>$where]);
        if($total>0){
            $data=$model->_where($where)->order('id')->limit(($currentPage-1)*$perPage,$perPage)->findAll(true);
            $url=url('admin/kami/list').'?pp='.$perPage.'&page=(:num)'.$url;
            $page=(string)new Paginator($total,$perPage,$currentPage,$url);
        }
        $this->_display('',[
            'title'=> '卡密管理',
           'data'=>$data,
            'total'=>$total,
            'page'=>$page,
            'get'=>$get,
            'type'=>$model->from('kami_type')->order('id')->findAll(true)
        ]);
    }

    /** ------------------------------------------------------------------
     * 生成卡密
     *---------------------------------------------------------------------*/
    public function make(){
        $model=app('\app\admin\model\Kami');
        $this->_display('',[
            'title'=> '卡密生成',
            'data'=>$model->order('id')->limit(100)->findAll(true),
        ]);
    }


}