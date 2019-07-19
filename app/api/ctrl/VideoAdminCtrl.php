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


namespace app\api\ctrl;

use app\common\ctrl\ApiAdminCtrl;
use shell\tools\Sitemap;

class VideoAdminCtrl extends ApiAdminCtrl
{
    /**视频------------------------------------------------------------------------------------------------------------------*/
    /** ------------------------------------------------------------------
     * 视频添加
     *--------------------------------------------------------------------*/
    public function post_add(){
        if(!$_POST)
            return  json(['code'=>1,'msg'=>'没有数据']);
        $validate=app('\app\video\validate\Post');
        if($validate->check($_POST)){
            $model=app('\app\video\model\Post');
            $id=$model->add($_POST);
            if($id){
                if(isset($_POST['status']) && $_POST['status']=='2') //存为草稿时
                    return json(['code'=>0,'msg'=>'成功保存为草稿','action'=>url('admin/video/post_edit').'?id='.$id]);
                else{
                    //提交到搜索引擎
                    Sitemap::submitMulti(url('@video@',['id'=>$id]),false);
                    return json(['code'=>0,'msg'=>'成功添加,新id为:'.$id.'，现在你可以添加资源了','action'=>url('admin/video/post_edit',['id'=>$id])]);
                }

            }else{
                return json(['code'=>3,'msg'=>'验证通过，但入库失败']);
            }
        }else{
            return json(['code'=>2,'msg'=>$validate->getError()]);
        }
    }

    /** ------------------------------------------------------------------
     * 视频编辑  必须的post参数 id
     *--------------------------------------------------------------------*/
    public function post_edit(){
        if(!$_POST)
            return  json(['code'=>1,'msg'=>'没有数据']);
        $_POST['id']=(int)post('id','int',0);
        if(! $_POST['id'])
            return  json(['code'=>2,'msg'=>'视频id不能为空']);
        $validate=app('\app\video\validate\Post');
        $model=app('\app\video\model\Post');
        if($validate->check($_POST)){
            if($model->edit($_POST)){
                return json(['code'=>0,'msg'=>'成功更新','action'=>url('admin/video/post')]);
            }else{
                return json(['code'=>4,'msg'=>'入库失败']);
            }
        }else
            return  json(['code'=>3,'msg'=>$validate->getError()]);
    }

    /**分类-------------------------------------------------------------------------------------------------------------------*/

    /**----------------------------------------------------------------
     * 更新分类缓存
    ------------------------------------------------------------------*/
    public function category_cache(){
        $cateModel=app('\app\video\model\Category');
        $cateModel->updateCache();
        json(['code'=>0,'msg'=>'已成功更新分类缓存']);
    }

    /** ------------------------------------------------------------------
     * 分类添加
     *--------------------------------------------------------------------*/
    public function category_add(){
        if($_POST){
            $validate=app('\app\admin\validate\Category');
            if($validate->check($_POST)){
                $model=app('\app\video\model\Category');
                if($id=$model->add($_POST)){
                    json(['code'=>0,'msg'=>'成功添加，分类名: '.$_POST['name'].'，分类id:'.$id,'action'=>url('admin/video/category_add').'?sss='.time()]);
                }else{
                    json(['code'=>3,'msg'=>'入库失败']);
                }
            }else{
                json(['code'=>2,'msg'=>$validate->getError()]);
            }
        }else{
            json(['code'=>1,'msg'=>'提交的数据为空']);
        }
    }
    /** ------------------------------------------------------------------
     * 分类删除
     *---------------------------------------------------------------------*/
    public function category_del(){
        $id=post('id')?:get('id');
        if(!$id) {
            json(['code'=>1,'msg'=>'id不能为空']);
            return ;
        }
        $model=app('\app\video\model\Category');
        $ret=$model->del($id,'video');
        if($ret===true){
            json(['code'=>0,'msg'=>'成功删除']);
        }else{
            json(['code'=>2,'msg'=>$ret]);
        }
    }

    /** ------------------------------------------------------------------
     * 分类编辑
     *---------------------------------------------------------------------*/
    public function category_edit(){
        if($_POST){
            $_POST['id']=(int)post('id','int',0);
            if($_POST['id']<1){
                json(['code'=>1,'msg'=>'id格式不符']);
                return;
            }
            $validate=app('\app\admin\validate\Category');
            if($validate->check($_POST)){
                $model=app('\app\video\model\Category');
                if($model->edit($_POST)){
                    json(['code'=>0,'msg'=>'成功更新','action'=>url('admin/video/category')]);
                }else{
                    json(['code'=>2,'msg'=>'入库失败']);
                }
            }else{
                json(['code'=>3,'msg'=>$validate->getError()]);
            }
        }else{
            json(['code'=>4,'msg'=>'提交的数据为空']);
        }
    }

    /**----------------------------------------------------
     * 内页批量操作
     *-----------------------------------------------------*/
    public function post_multi_action(){
        $ac=get('ac');
        switch ($ac){
            case 'del':
                $this->post_del();
                break;
            default:
                json(['code'=>2,'msg'=>'ac不符']);
        }
    }

    /** ------------------------------------------------------------------
     * 删除视频，必须的post或get参数 id
     *--------------------------------------------------------------------*/
    public function post_del(){
        $model=app('\app\video\model\Post');
        $id=post('id')?:get('id');
        $ret=$model->del($id);
        if($ret===true){
            json(['code'=>0,'msg'=>'成功删除','action'=>url('admin/video/post')]);
        }else{
            json(['code'=>1,'msg'=>$ret]);
        }
    }

    /** ------------------------------------------------------------------
     * 更新标签
     *---------------------------------------------------------------------*/
    public function tags_edit(){
        $id=post('id','int',0);
        if($id<1)
            return json(['code'=>1,'msg'=>'id不对']);
        $tags=post('tags');
        if(! is_array($tags)){
            return json(['code'=>1,'msg'=>'数据格式不正确']);
        }
        app('\app\admin\model\Tag')->addTagMap($tags,$id,'video',true);
        return json(['code'=>0,'msg'=>'成功更新tag']);
    }
    /** ------------------------------------------------------------------
     * 更新人物
     *---------------------------------------------------------------------*/
    public function people_edit(){
        $id=post('id','int',0);
        if($id<1)
            return json(['code'=>1,'msg'=>'id不对']);
        $type=post('type');
        if(!in_array($type,['actor','director','producer'],true)){
            return json(['code'=>2,'msg'=>'type格式不正确']);
        }
        $people=post('people');
        if(!$people)
            return json(['code'=>3,'msg'=>'people数据不能为空']);
        $people=str_replace('，',',',$people);
        $tags=explode(',',$people);
        $tagModel=app('\app\admin\model\Tag');
        $tagModel->addTagMap($tags,$id,'video_'.$type,true,1);
        $tagModel->from('video')->eq('id',$id)->update([$type=>$people]);
        return json(['code'=>0,'msg'=>'成功更新']);
    }
/**资源-------------------------------------------------------------------------------------------------------------------*/
    /** -----------------------------------------------------------------
     * 资源添加
     *-------------------------------------------------------------------*/
    public function source_add(){
        if($_POST){
            $validate=app('\app\video\validate\VideoSource');
            if($validate->check($_POST)){
                $model=app('\app\video\model\VideoSource');
                if($id=$model->add($_POST)){
                    json(['code'=>0,'msg'=>'成功添加: id => '.$id]);
                }else{
                    json(['code'=>3,'msg'=>'入库失败']);
                }
            }else{
                json(['code'=>2,'msg'=>$validate->getError()]);
            }
        }else{
            json(['code'=>1,'msg'=>'提交的数据为空']);
        }
    }
    /** ----------------------------------------------------------------
     * 资源编辑
     *-------------------------------------------------------------------*/
    public function source_edit(){
        if($_POST){
            $validate=app('\app\video\validate\VideoSource');
            if($validate->check($_POST)){
                $model=app('\app\video\model\VideoSource');
                if($model->edit($_POST)){
                    json(['code'=>0,'msg'=>'更新成功']);
                }else{
                    json(['code'=>3,'msg'=>'更新失败']);
                }
            }else{
                json(['code'=>2,'msg'=>$validate->getError()]);
            }
        }else{
            json(['code'=>1,'msg'=>'提交的数据为空']);
        }
    }
    /** ------------------------------------------------------------------
     * 删除资源，必须的post或get参数 id
     *--------------------------------------------------------------------*/
    public function source_del(){
        $model=app('\app\video\model\VideoSource');
        $id=post('id')?:get('id');
        $ret=$model->del($id);
        if($ret===true){
            json(['code'=>0,'msg'=>'成功删除']);
        }else{
            json(['code'=>1,'msg'=>$ret]);
        }
    }
    /** ------------------------------------------------------------------
     * 资源查询 ：从vid查询所有对应的资源
     *---------------------------------------------------------------------*/
    public function source_show(){
        $vid=(int)get('vid','int',0);
        if(!$vid) {
            json(['code'=>1,'msg'=>'id不能为空','count'=>0,'data'=>[]]);
            return ;
        }
        $model=app('\app\video\model\VideoSource');
        $count=0;
        $data=$model->eq('vid',$vid)->findAll(true);
        if($data)
            $count=count($data);
        json(['code'=>0,'msg'=>'成功','count'=>$count,'data'=>$data]);
    }

}