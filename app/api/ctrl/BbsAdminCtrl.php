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

class BbsAdminCtrl extends ApiAdminCtrl
{
    /** ------------------------------------------------------------------
     * 单个删除
     *--------------------------------------------------------------------*/
    public function del(){
        $id=get('id','int',0);
        if(!$id)
            return json(['code'=>1,'msg'=>'id格式不符']);
        if(app('\app\bbs\model\Bbs')->delOne($id))
            return json(['code'=>0,'msg'=>'成功删除']);
        else
            return json(['code'=>1,'msg'=>'删除失败，不存在对应的id']);
    }

    /** ------------------------------------------------------------------
     * 批量删除
     *--------------------------------------------------------------------*/
    public function del_multi(){
        $id=post('id');
        if(!is_array($id)){
            return  json(['code'=>1,'msg'=>'id格式不符']);
        }
        $res=app('\app\bbs\model\Bbs')->delSome($id);
        if($res>0)
            return json(['code'=>0,'msg'=>'成功删除'.$res.'条','action'=>url('admin/bbs/list')]);
        else
            return json(['code'=>1,'msg'=>'删除失败']);
    }

    /** ------------------------------------------------------------------
     * 批量操作统一入口
     *--------------------------------------------------------------------*/
    public function action_multi(){
        $ac=get('ac','','');
        switch ($ac){
            case 'del':
                $this->del_multi();
        }
    }

    public function get_tags(){
        $id=post('id','int',0);
        if(!$id)
            return json(['code'=>1,'msg'=>'id格式不符']);
        $type=post('type','','bbs_normal');
        $tagModel=app('\app\admin\model\Tag');
        $data=$tagModel->getName($id,$type);
        if(!$data)
            return json(['code'=>0,'msg'=>'查找不到对应的id','data'=>'']);
        return json(['code'=>0,'msg'=>'成功获取','data'=>$data]);
    }

    /** ------------------------------------------------------------------
     *  快速修改帖子，可以修改的选项有：标签（会先把帖子原来的标签删除，再添加新的标签进去）、标题、是否结贴、内容和评论
     *  post参数： int id  必须
     *                   string tag 可选 多个用逗号分隔
     *                   string pindao 可选 默认为 'bbs_normal'
     *                   string title 可选
     *                   string content  可选
     *                   int isend  可选 0或1，默认0
     *                   string comment   可选 格式：id{||}recommended{||}content{|||}id2{||}recommended{||}content2
     *---------------------------------------------------------------------*/
    public function quick_edit(){
        $id=post('id','int',0);
        if(!$id)
            return  json(['code'=>1,'msg'=>'id格式不符']);
        $tags=post('tag','','');
        $type=post('pindao','','bbs_normal');
        $tagModel=app('\app\admin\model\Tag');
        if($tags){
            $tags=str_replace('，',',',$tags);
            $tagModel->editFromOid($id,$tags,$type);
        }
        $data=[];
        if($tmp=post('content'))
            $data['content']=htmlspecialchars_decode($tmp);
        if($tmp=post('title')){
            $data['title']=$tmp;
        }
        if($tmp=post('type','int',false)){
            $data['type']=$tmp;
        }
        unset($tmp);
        $data['isend']=(int)post('isend','int',0);
        if($data['isend'] !==0 && $data['isend']!==1){
            $data['isend']=0;
        }
        //修改标题和内容
        $tagModel->from('bbs')->eq('id',$id)->update($data);
        //评论修改
        $this->comment_fast_edit(post('comment','',false),$tagModel);
        return json(['code'=>0,'msg'=>'成功修改标签']);
    }

    /** ------------------------------------------------------------------
     * 快速评论修改
     * @param string $data 格式：id{||}recommended{||}content{|||}id2{||}recommended{||}content2
     * @param \core\Model $model
     *---------------------------------------------------------------------*/
    protected function comment_fast_edit($data,&$model){
        if($data){
            $data=explode('{|||}',htmlspecialchars_decode($data));
            foreach ($data as $item){
                if(!$item)
                    continue;
                $comment=explode('{||}',$item);
                if(count($comment) !==3)
                    continue;
                $model->from('comment')->eq('id',trim($comment[0]))->update([
                    'content'=>trim($comment[2]),
                    'recommended'=>trim($comment[1])
                ]);
            }
        }
    }

}