<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 后台标签（tags）api类
 * ======================================*/

namespace app\api\ctrl;

use app\common\ctrl\ApiAdminCtrl;

class TagAdminCtrl extends ApiAdminCtrl
{
    /** ------------------------------------------------------------------
     * 标签添加
     *--------------------------------------------------------------------*/
    public function add(){
        $model=app('\app\admin\model\Tag');
        if($model->checkData($_POST)){
            if($model->add($_POST))
                return json(['code'=>0,'msg'=>'成功添加','action'=>url('admin/tag/manage')]);
            else
                return json(['code'=>1,'msg'=>'入库失败']);
        }else
            return json(['code'=>1,'msg'=>$model->getValidateMsg()]);
    }

    /** ------------------------------------------------------------------
     * 标签修改
     *--------------------------------------------------------------------*/
    public function edit(){
        $id=post('id','int',0);
        if($id <1)
            return json(['code'=>1,'msg'=>'id格式不符']);
        $update_time=(int)post('update_time','int',0);
        $model=app('\app\admin\model\Tag');
        if($model->checkData($_POST,$id)){
            if($update_time==1)
                $_POST['create_time']=time();
            if($model->edit($_POST)>0)
                return json(['code'=>0,'msg'=>'成功更新','action'=>url('admin/tag/manage')]);
            else
                return json(['code'=>1,'msg'=>'入库失败']);
        }else
            return json(['code'=>1,'msg'=>$model->getValidateMsg()]);
    }

    /** ------------------------------------------------------------------
     * 标签删除
     *--------------------------------------------------------------------*/
    public function del(){
        $id=get('id','int',0);
        if($id <1)
            return json(['code'=>1,'msg'=>'id格式不符']);
        $model=app('\app\admin\model\Tag');
        if($model->delOne($id))
           return json(['code'=>0,'msg'=>'成功删除']);
        return json(['code'=>1,'msg'=>'删除失败，找不到对应的id']);
    }
    //标签页链接提交
    public function sitemap(){
        $type=(int)post('type','int',1);
        $other=post('other','','');
        $model=app('\app\admin\model\Tag');
        if($type==1){
            $data=$model->select('id,name,slug')->eq('status',1)->gt('create_time',strtotime(date("Y-m-d"),time()))->findAll(true);
        }elseif($type==2){
            $data=[];
        }else{
            $data=$model->select('id,name,slug')->eq('status',1)->findAll(true);
        }
        $urls=[];
        if($data){
            foreach ($data as $item){
                $urls[]=url('@tag@',['slug'=>$item['slug']]);
            }
        }
        if($other){
            $other=explode("\n",$other);
            $urls=array_merge($urls,$other);
        }
        if($urls){
            $ret=\shell\tools\Sitemap::submitMulti($urls,true);
            return json(['code'=>0,'msg'=>'sitemap已经提交','data'=>$ret]);
        }
        return json(['code'=>1,'msg'=>'urls为空']);
    }

}