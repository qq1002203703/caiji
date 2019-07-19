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

class VideoCtrl extends AdminCtrl
{
    //分类管理
    public function category(){
        $model=app('\app\video\model\Category');
        $this->_assign([
            'title'=>'视频分类管理',
            'category'=>$model->getTreeTable(),
        ]);
        $this->_display();
    }
    //添加分类
    public function category_add(){
        $model=app('\app\video\model\Category');
        $this->_assign([
            'title'=>'添加视频分类',
            'select'=>$model->getTreeSelect(),
        ]);
        $this->_display();
    }
    //编辑分类
    public function category_edit(){
        $id=get('id','int',0);
        if($id<1) {
            $this->_redirect('admin/video/category','分类id不能为空');
        }

        $model=app('\app\video\model\Category');
        $data=$model->getById($id);
        if($data['thumb_ids']){//图集
            $data['thumb_ids']=$model->select('id,uri')->from('file')->in('id',explode(',',$data['thumb_ids']))->findAll(true);
        }
        $select=$model->getTreeNotIn($id);
        $this->_assign([
            'title'=>'编辑视频分类',
            'data'=>$data,
            'select'=>$select,
        ]);
        $this->_display();
    }

    //post管理
    public function post(){
        $currentPage =  get('page','int',1);
        $perPage=10;
        $status=(int)get('status','int',1);
        $where[]=['status','eq',$status];
        $url='?status='.$status;
        $get=[];
        if($get['category_id']=get('category_id','int',0)){
            $where[]=['category_id','eq',$get['category_id']];
            $url.='&category_id='.$get['category_id'];
        }
        if($get['keywords']=get('keywords','','')){
            $url.='&keywords='.$get['keywords'];
            $get['keywords']=urldecode($get['keywords']);
            $where[]=['title','like','%'.$get['keywords'].'%'];
        }
        if($get['is_top']=get('is_top','int',0)){
            $where[]=['is_top','eq',$get['is_top']];
            $url.='&is_top='.$get['is_top'];
        }
        if($get['recommended']=get('recommended','int',0)){
            $where[]=['recommended','eq',$get['recommended']];
            $url.='&recommended='.$get['recommended'];
        }
        $cateModel=app('\app\video\model\Category');
        $model=app('\app\video\model\Post');
        $total = $model->count(['where'=>$where]);
        if($total==0)
            $posts=[];
        else
            $posts=$model->search($where,[($currentPage-1)*$perPage,$perPage],'create_time desc,id');
        $url = url('admin/video/post').$url.'&page=(:num)';
        $page=(string)new Paginator($total,$perPage,$currentPage,$url);
        $this->_display('',[
            'title'=>'视频管理',
            'data'=>$posts,
            'page'=>$page,
            'total'=>$total,
            'total_fabu'=>$model->count(['where'=>[['status','eq',1]]]),
            'total_dingshi'=>$model->count(['where'=>['status','eq',3]]),
            'categorys'=>$cateModel->getTree(['id','name'],'<option value="%id%">%name%</option>'),
            'get'=>$get,
        ]);
    }

    /**
     * 添加post
     */
    public function post_add(){
        $catModel=app('\app\video\model\Category');
        $category=$catModel->getTreeSelect();
        if($category==='')//没有分类时
            $this->_redirect('admin/video/category','请先添加分类');
        $this->_assign([
            'title'=>'添加视频',
            'category'=>$category,
            'allow'=>[1=>'完全公开',2=>'金钱购买',3=>'金币购买',4=>'积分达到',5=>'vip会员'],
            'allowDefaultSelect'=>1
        ]);
        $this->_display();
    }

    /**----------------------------------------------------------------------
     * 编辑视频
     -----------------------------------------------------------------------*/
    public function post_edit(){
        $id=get('id','int',0);
        if($id<1) {
            $this->_redirect('admin/video/post','id不能为空');
        }
        $model=app('\app\video\model\Post');
        $data=$model->getOne($id);
        if(!$data) $this->_redirect('admin/portal/post','不存在id为'.$id.'的视频');
        $catModel=app('\app\video\model\Category');
        $category=$catModel->getTreeSelect($data['category_id']);
        unset($catModel);
        //$data['video_source']=$model->from('video_source')->eq('vid',$id)->findAll(true);
        $this->_assign([
            'title'=>'编辑视频',
            'category'=>$category,
            'data'=>$data,
            'allow'=>[1=>'完全公开',2=>'金钱购买',3=>'金币购买',4=>'积分达到',5=>'vip会员'],
        ]);


        $this->_display();
    }

    //资源添加
    public function source_add(){
        $this->_assign([
            'title'=>'添加视频资源'
        ]);
        $this->_display();
    }

    //资源添加
    public function source_edit(){
        $id=get('id','int',0);
        if($id<1) {
            echo 'id格式不符';
            return;
        }
        $model=app('\app\video\model\VideoSource');
        $data=$model->eq('id',$id)->find(null,true);
        if(!$data){
            echo '不存在的id';
            return;
        }
        $this->_assign([
            'title'=>'编辑视频资源',
            'data'=>$data,
        ]);
        $this->_display();
    }
}