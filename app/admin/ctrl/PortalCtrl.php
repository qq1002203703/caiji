<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 *
 * QQ 46502166
 *
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 门户管理
 * ======================================*/

namespace app\admin\ctrl;
use app\portal\model\PortalCategory;
use app\portal\model\PortalPost;

class PortalCtrl extends \app\common\ctrl\AdminCtrl
{
    //分类管理
    public function category(){
        $model=new PortalCategory();
        $category=$model->getTreeTable();
        $this->_assign([
            'title'=>'分类管理',
            'category'=>$category
        ]);
        $this->_display();
    }
    //添加分类
    public function add_category(){
        $msg='';
        if($_POST){
            $valide=new \app\portal\validate\Category();
            if($valide->check($_POST)){
                $model=new PortalCategory();
                if($id=$model->add($_POST)){
                    $msg='成功添加 “'.$_POST['name'].',分类id:'.$id.'“，你可以继续添加下一个分类';
                }else{
                    $msg='添加失败';
                }
            }else{
                $msg=$valide->getError();
            }
        }
        $model=new PortalCategory();
        $select=$model->getTreeSelect();
        $this->_assign([
            'title'=>'添加文章分类',
            'msg'=>$msg,
            'select'=>$select
        ]);
        $this->_display();
    }

    //删除分类
    public function delete_category(){
        $id=get('id');
        if(!$id) {
            json(['code'=>1,'msg'=>'id不能为空']);
            return ;
        }
        $model=new PortalCategory();
        if($model->hasChildren($id)) {
            json(['code'=>2,'msg'=>'存在子分类不能删除']);
            return ;
        }
        if($model->del($id)){
            json(['code'=>0,'msg'=>'成功删除']);
        }else{
            json(['code'=>3,'msg'=>'删除失败']);
        }
    }
    //编辑分类
    public function edit_category(){
        $id=get('id','int',0);
        if($id<1) {
            $this->_redirect('admin/portal/category','分类id不能为空');
        }
        $msg='';
        $model=new PortalCategory();
        if($_POST){
            $valide=new \app\portal\validate\Category();
            $_POST['id']=$id;
            if($valide->check($_POST)){
                if($model->edit($_POST)){
                    $msg='成功更新';
                }else{
                    $msg='更新失败';
                }
            }else{
                $msg=$valide->getError();
            }
            $data=$_POST;
        }else{
            $data=$model->getById($id);
        }
        $select=$model->getTreeNotIn($id);
        $this->_assign([
            'title'=>'编辑分类',
            'category'=>$data,
            'select'=>$select,
            'msg'=>$msg
        ]);
        $this->_display();
    }

    /**
     * 更新分类缓存
     */
    public function update_category_cache(){
        $cateModel=new PortalCategory();
        $cateModel->update_cache();
        json(['code'=>0,'msg'=>'已成功更新分类缓存']);
    }

    //文章管理
    public function post(){
        $currentPage =  get('page','int',1);
        $perPage=10;
        $where=[];
        $url='';
        if($category_id=get('category_id','int',0)){
            $where['r.category_id']=$category_id;
            $url.='category_id='.$category_id.'&';
        }
        if($keywords=get('keywords','','')){
            $where['p.keywords']=['title','like','%'.$keywords.'%'];
            $url.='keywords='.$keywords.'&';
        }
        $cateModel=new PortalCategory();
        $model=new PortalPost();
        //getPost()是三表联合查询，字段前要加表前缀（post表是p,category表是c,relation表是r）
        $posts=$model->getPost('p.title,p.id,p.create_time,p.status',$where,[($currentPage-1)*$perPage,$perPage]);
        $total = $model->getPostCout($where);
        $url = url('admin/portal/post').'?'.$url.'page=(:num)';

        $page=new \extend\Paginator($total,$perPage,$currentPage,$url);
        $this->_assign([
            'title'=>'文章管理',
            'posts'=>$posts,
            'page'=>(string)$page,
            'categorys'=>$cateModel->getTree(0,true,['id','name'],'<option value="%id%">%name%</option>','')
        ]);
        $this->_display();
    }

    /**
     * 添加文章
     */
    public function add_post(){
        $msg='';
        if($_POST){
            $valide=new \app\portal\validate\Post();
            if($valide->check($_POST)){
                $model=new PortalPost();
                if($id=$model->add($_POST)){
                    $msg='成功添加文章id:'.$id.'，你可以继续添加下一篇文章';
                }else{
                    $msg='添加失败';
                }
            }else{
                $msg=$valide->getError();
            }
        }
        $catModel=app('\app\portal\model\PortalCategory');
        $category=$catModel->getTree(0,true,['id','name'],'<tr><td><input type="checkbox" value="%id%" name="ids[]" data-name="%name%"></td><td>%id%</td><td>%__repeat_content__%</td></tr>','');
        $this->_assign([
            'title'=>'添加文章',
            'category'=>$category,
            'msg'=>$msg
        ]);
        $this->_display();
    }

    /**
     * 删除文章
     */
    public function delete_post(){
        $id=get('id');
        if(!$id && preg_match('/^(\d[\d,]*)*\d$/',$id)==0){
           json(['code'=>1,'msg'=>'id不能为空']);
           return ;
       }
        $id=explode(',',$id);
        $model=new PortalPost();
        $ret=$model->reset()->in('id',$id)->delete();
        if($ret){
            //删除分类
            $model->reset()->table='portal_relation';
            $model->in('post_id',$id)->delete();
            json(['code'=>0,'msg'=>'成功删除']);
        }else{
            json(['code'=>2,'msg'=>'删除失败']);
        }
    }

    /**
     * 编辑文章
     */
    public function edit_post(){
        $id=get('id','int',0);
        if($id<1) $this->_redirect('admin/portal/post','文章id不能为空');
        $msg='';
        $model=new PortalPost();
        if($_POST){
            $valide=new \app\portal\validate\Post();
            $_POST['id']=$id;
            if($valide->check($_POST)){
                if($model->edit($_POST)){
                    $msg='成功更新';
                }else{
                    $msg='更新失败';
                }
            }else{
                $msg=$valide->getError();
            }
        }
        $data=$model->getOne('',[['p.id','eq',$id]]);
        if(!$data) $this->_redirect('admin/portal/post','数据库不存在id为'.$id.'的文章');
        $catModel=app('\app\portal\model\PortalCategory');
        $category=$catModel->getTree(0,true,['id','name'],'<tr><td><input type="checkbox" value="%id%" name="ids[]" data-name="%name%"></td><td>%id%</td><td>%__repeat_content__%</td></tr>','');
        $this->_assign([
            'title'=>'编辑文章',
            'category'=>$category,
            'msg'=>$msg,
            'data'=>$data
        ]);
        $this->_display();
    }


}