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
 *
 * ======================================*/


namespace app\portal\ctrl;
use core\Ctrl;
class PostCtrl extends Ctrl
{
    /**--------------------------------------
     * 文章内容页
     * @param $id
     *----------------------------------------*/
    public function post($id){
        if($id<1)
            show_error('输入不正确的文章id');
        $model=app('\app\portal\model\PortalPost');
        $post=$model->getOne('',[['p.id','eq',$id]]);
        if(!$post)
            show_error('不存在的文章id');
        $post=\extend\Helper::replace_outlink($post,url('portal/index/outlink'));
        $this->_assign([
            'title'=>$post['title'],
            'is_login'=>$this->_is_login(),
            'is_allow'=>$this->check_post_visit($post['buy_uid']),
            'post'=>$post
        ]);
        $this->_display();
        $this->views_click($id);
    }
    /**-------------------------------------------------
     * 文章权限检测
    ----------------------------------------------------*/
    protected function check_post_visit($buy_uid){
        if(!$this->_is_login()) return false;
        if($_SESSION['user']['gid']<10) return true;
        if($buy_uid){
            return in_array($_SESSION['uid'],explode(',',$buy_uid));
        }
        return false;
    }
    /**--------------------------------------------------
     * 下载次数增加1
     *---------------------------------------------------*/
    public function downloads_click(){
        $id=get('id','int',0);
        if($id<1) return;
        $model=app('\app\portal\model\PortalPost');
        $model->_exec('update table set `downloads`=`downloads`+1 where id= ?',[$id]);
    }
    /**--------------------------------------------------
     * 查看次数增加1
     *---------------------------------------------------*/
    protected function views_click($id){
        //$id=get('id','int',0);
        if($id<1) return;
        $model=app('\app\portal\model\PortalPost');
        $model->_exec('update table set `views`=`views`+1 where id= ?',[$id]);
    }
    /**--------------------------------------------------
     * 分类列表页
     *---------------------------------------------------*/
    public function category($slug){
        $catModel=app('\app\portal\model\PortalCategory');
        $category=$catModel->_sql('select * from table WHERE slug=?',[$slug],true,true);
        if(!$category) show_error('不存在的分类slug:'.$slug);
        $path=$catModel->bread($category['id']);
        $postModel=app('\app\portal\model\PortalPost');
        $currentPage=get('page','int',1);
        $perPage=15;
        $posts=$postModel->getPost('p.*,r.category_id',[['r.category_id','eq',$category['id']]],[($currentPage-1)*$perPage,$perPage]);
        $total = $postModel->getPostCout([['r.category_id','eq',$category['id']]]);
        $url = url('admin/portal/post').'?page=(:num)';
        $page=new \extend\Paginator($total,$perPage,$currentPage,$url);
        $this->_assign([
            'title'=>$category['name'],
            'category'=>$category,
            'posts'=>$posts,
            'page'=>(string)$page,
            'path'=>$path.'列表'
        ]);
        $this->_display('list');
    }





    //protected function


}