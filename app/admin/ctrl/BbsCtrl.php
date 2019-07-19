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
use app\admin\other\Search;
use  app\common\ctrl\AdminCtrl;
class BbsCtrl extends AdminCtrl
{
    /** ------------------------------------------------------------------
     * 实例化当前的分类
     * @return \app\bbs\model\BbsCategory
     *--------------------------------------------------------------------*/
    protected function newCategory(){
        return app('app\bbs\model\BbsCategory');
    }
    //分类管理
    public function category(){
        $model=$this->newCategory();
        $category=$model->getTreeTable();
        $this->_assign([
            'title'=>'分类管理',
            'category'=>$category
        ]);
        $this->_display();
    }
    //添加分类
    public function category_add(){
        $msg='';
        $model=$this->newCategory();
        if($_POST){
            $valide=app('\app\admin\validate\Category');
            if($valide->check($_POST)){
                if($id=$model->add($_POST)){
                    $msg='成功添加 “'.$_POST['name'].',分类id:'.$id.'“，你可以继续添加下一个分类';
                }else{
                    $msg='添加失败';
                }
            }else{
                $msg=$valide->getError();
            }
        }
        $select=$model->getTreeSelect();
        $this->_assign([
            'title'=>'添加论坛分类',
            'msg'=>$msg,
            'select'=>$select
        ]);
        $this->_display();
    }

    //删除分类
    public function category_delete(){
        $id=get('id');
        if(!$id) {
            json(['code'=>1,'msg'=>'id不能为空']);
            return ;
        }
        $model=$this->newCategory();
        //判断是否有子分类
        if($model->hasChildren($id)) {
            json(['code'=>2,'msg'=>'存在子分类不能删除']);
            return ;
        }
        //判断是否有帖子
        if($model->count(['from'=>'bbs', 'where'=>[['category_id','eq',$id]]]) >0 ){
            json(['code'=>2,'msg'=>'此分类下存在帖子不能删除，请先把帖子转移到其他分类再删除！']);
            return ;
        }
        if($model->del($id)){
            json(['code'=>0,'msg'=>'成功删除']);
        }else{
            json(['code'=>3,'msg'=>'删除失败']);
        }
    }
    //编辑分类
    public function category_edit(){
        $id=get('id','int',0);
        if($id<1) {
            $this->_redirect('admin/portal/category','分类id不能为空');
        }
        $msg='';
        $model=$this->newCategory();
        if($_POST){
            $valide=app('\app\admin\validate\Category');
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
        $cateModel=$this->newCategory();
        $cateModel->updateCache();
        json(['code'=>0,'msg'=>'已成功更新分类缓存']);
    }

    /**------------------------------------------------------------------
     * 帖子管理
     *---------------------------------------------------------------------*/
    public function list(){
        $currentPage =  get('page','int',1);
        $perPage=10;
        $where=[];
        $url='';
        $get=[];
        if($get['category_id']=get('category_id','int',0)){
            $where[]=['category_id','eq',$get['category_id']];
            $url.='&category_id='.$get['category_id'];
        }
        $filters=[];
        //是否结贴
        $get['isend']=(int)get('isend','int',0);
        if($get['isend']===0 || $get['isend']===1){
            $where[]=['isend','eq',$get['isend']];
            $url.='&isend='.$get['isend'];
            $filters[]=['attr_in','isend',[$get['isend']]];
        }else{
            $get['isend']=2;
            $url.='&isend=2';
        }
        //频道
        $get['type']=(int)get('type','int',0);
        if($get['type']==1 || $get['type']==2){
            $where[]=['type','eq',$get['type']];
            $url.='&type='.$get['type'];
            $filters[]=['attr_in','myorder',[$get['type']]];
        }else{
            $get['type']=0;
        }
        //关键词
        if($get['keywords']=get('keywords','','')){
            $url.='&keywords='.$get['keywords'];
            $get['keywords']=urldecode($get['keywords']);
            $ids=$this->search($get['keywords'],$filters,300);
            if($ids)
                $where[]=['id','in',$ids];
            else
                $where[]=['id','eq',0];
            //$where[]=['title','like','%'.$get['keywords'].'%'];
        }
        if($get['is_top']=get('is_top','int',0)){
            $where[]=['is_top','eq',$get['is_top']];
            $url.='&is_top='.$get['is_top'];
        }
        if($get['recommended']=get('recommended','int',0)){
            $where[]=['recommended','eq',$get['recommended']];
            $url.='&recommended='.$get['recommended'];
        }

        $model=app('\app\bbs\model\Bbs');
        $total = $model->count(['where'=>$where]);
        $data=[];
        $page='';
        if($total>0){
            $data=$model->search($where,[($currentPage-1)*$perPage,$perPage],'id desc');
            $url = url('admin/bbs/list').'?'.$url.'&page=(:num)';
            $page=(string) new \extend\Paginator($total,$perPage,$currentPage,$url);
        }
        $cateModel=$this->newCategory();
        $this->_assign([
            'title'=>'帖子管理',
            'data'=>$data,
            'page'=>$page,
            'category'=>$cateModel->getTree(['id','name'],'<option value="%id%">%name%</option>'),
            'get'=>$get,
            'total'=>$total
        ]);
        $this->_display();
    }

    /**
     * 添加文章
     */
    public function post_add(){
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
    public function post_delete(){
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
    public function post_eidt(){
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

    protected function search($keyword,$filters=[],$limit=300){
        $filters[]=['attr_in','source_id',[0]];
        $sphinx=new Search(['maxNum'=>$limit],$filters);
        $res=$sphinx->query($keyword,'union',1,$limit,$count);
        if($res===false)
            return false;
        $ids=[];
        array_walk($res,function ($value)use (&$ids){
            $ids[]=($value['id']-1)/10;
        });
        return $ids;
    }
}