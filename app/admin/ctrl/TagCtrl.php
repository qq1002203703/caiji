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


use app\common\ctrl\AdminCtrl;
use extend\Paginator;

class TagCtrl extends AdminCtrl
{
    /*protected $type='portal_article';
    protected $allType=['portal_article','portal_goods','bbs_normal'];
    protected $typeMap=['portal_article'=>'文章','portal_goods'=>'商品','bbs_normal'=>'论坛'];

    protected function getType(){
        if(($type=get('type')) && in_array($type,$this->allType)){
            $this->type=$type;
        }
        $this->_assign([
            'type'=>$this->type,
            'typeMap'=>$this->typeMap,
            'allType'=>$this->allType
        ]);
    }
    protected function _init(){
        parent::_init();
        $this->getType();
    }*/
    /** ------------------------------------------------------------------
     * 标签管理
     *---------------------------------------------------------------------*/
    public function manage(){
        $perPage=20;
        $currentPage=get('page','int',1);
        $data=[];
        $page='';
        $model=app('\app\admin\model\Tag');
        $where=[];
        $url='';
        $get['status']=(int)get('status','int',2);
        if($get['status'] <=1 && $get['status']>=0){
            $where[]=['status','eq',$get['status']];
            $url.='&status='.$get['status'];
        }
        if($get['keywords']=get('keywords','','')){
            $url.='&keywords='.$get['keywords'];
            $get['keywords']=urldecode($get['keywords']);
            $where[]=['name','like','%'.$get['keywords'].'%'];
        }
        $total=$model->count(['where'=>$where]);
        if($total>0){
            //$data=$model->getAll($where,[($currentPage-1)*$perPage,$perPage],'t.create_time desc,id desc','id,name,seo_title,create_time,slug');
            $data=$model->select('id,name,seo_title,create_time,slug,status')->_where($where)->order('create_time desc,id desc')->limit(($currentPage-1)*$perPage,$perPage)->findAll(true);
            $url=url('admin/tag/manage').'?page=(:num)'.$url;
            $page=(string)new Paginator($total,$perPage,$currentPage,$url);
        }
        $this->_display('',[
           'title'=> '标签管理',
            'data'=>$data,
            'total'=>$total,
            'page'=>$page,
            'get'=>$get,
        ]);
    }
    /** ------------------------------------------------------------------
     * 标签编辑
     *---------------------------------------------------------------------*/
    public function edit(){
        $id=get('id','int',0);
        if($id <1)
            $this->_redirect('admin/tag/manage','id格式不符');
        $model=app('\app\admin\model\Tag');
        $data=$model->eq('id',$id)->find(null,true);
        if(!$data)
            $this->_redirect('admin/tag/manage','不存在的id');
        $this->_display('',[
            'title'=>'标签编辑',
            'data'=>$data
        ]);
    }
}