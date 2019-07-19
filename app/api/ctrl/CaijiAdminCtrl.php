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

class CaijiAdminCtrl extends ApiAdminCtrl
{
    //批量操作入口
    public function action_multi(){
        json(['code'=>1,'这个方法还没写，待完善！']);
    }
    //采集项目添加
    public function page_add(){
        if(!$_POST)
            return json(['code'=>1,'msg'=>'没有数据']);
        $validate=app('\app\caiji\validate\CaijiPage');
        if($validate->check($_POST)){
            $model=app('\app\caiji\model\Caiji');
            if($ret=$model->page_add($_POST)){
                return json(['code'=>0,'msg'=>'成功添加 '.$ret.'，你可以继续添加下一个','action'=>url('admin/caiji/page_add')]);
            }else{
                return json(['code'=>2,'msg'=>'入库失败']);
            }
        }else{
            return json(['code'=>1,'msg'=>$validate->getError()]);
        }
    }
    //采集项目修改
    public function page_edit(){
        $id=post('id','int',0);
        if(!$id)
            return json(['code'=>1,'msg'=>'id格式不符']);
        $validate=app('\app\caiji\validate\CaijiPage');
        if($validate->check($_POST)){
            $model=app('\app\caiji\model\Caiji');
            if($ret=$model->page_edit($_POST)){
                return json(['code'=>0,'msg'=>'成功更新','action'=>url('admin/caiji/list')]);
            }else{
                return json(['code'=>2,'msg'=>'更新失败，不存在id,或数据没有改变']);
            }
        }else{
            return json(['code'=>1,'msg'=>$validate->getError()]);
        }
    }
    //采集项目删除
    public function page_del(){
        $id=get('id','int',0);
        if(!$id)
            return json(['code'=>1,'msg'=>'id格式不符']);
        $model=app('\app\caiji\model\Caiji');
        if($ret=$model->from('caiji_page')->eq('id',$id)->delete()){
            return json(['code'=>0,'msg'=>'成功删除']);
        }else{
            return json(['code'=>1,'msg'=>'删除失败，不存在对应的id']);
        }
    }

    /** ------------------------------------------------------------------
     * 主要是修改标签，如果标题和内容也提交过来，也会同时修改
     *--------------------------------------------------------------------*/
    public function tag_edit(){
        list($where,$post)=$this->_getQuery([
            'table'=>['d'=>false,'w'=>'','f'=>''],
            'id'=>['d'=>0,'f'=>'int','w'=>'eq'],
            'tag'=>['d'=>false,'f'=>'','w'=>''],
            'title'=>['d'=>'','w'=>'','f'=>''],
            'content'=>['d'=>'','w'=>'','f'=>'']
        ],'post');
        if(!$post['table']){
            return json(['code'=>1,'msg'=>'没有指定数据表']);
        }
        if(!$post['tag'] || !$post['id'])
            return json(['code'=>1,'msg'=>'数据格式不符']);
        $data=['tag'=>str_replace('，',',',$post['tag']),'isend'=>1,'isshenhe'=>1];
        if($post['title'])
            $data['title']=$post['title'];
        if($post['content'])
            $data['content']=htmlspecialchars_decode($post['content']);
        $model=app('\app\caiji\model\Caiji');
        if($model->from($post['table'])->_where($where)->update($data))
            return json(['code'=>0,'msg'=>'成功更新']);
        return json(['code'=>1,'msg'=>'入库失败']);
    }

    /** ------------------------------------------------------------------
     * 设置为垃圾
     * get参数：string table 必须
     *                int id 必须
     *---------------------------------------------------------------------*/
    public function set_laji(){
        $id=get('id','int',0);
        if(!$id)
            return json(['code'=>1,'msg'=>'id格式不符']);
        $table=get('table','',false);
        if(!$table)
            return json(['code'=>1,'msg'=>'没有指定数据表']);
        $model=app('\app\caiji\model\Caiji');
        if($model->from($table)->eq('id',$id)->update(['islaji'=>1,'content'=>'','title'=>'','isshenhe'=>1,'isend'=>1])){
            return  json(['code'=>0,'msg'=>'成功更新']);
        }
        return json(['code'=>1,'msg'=>'入库失败']);
    }
/**定时队列*******************************************************************************************************************/

    /** ------------------------------------------------------------------
     * 采集队列添加
     *--------------------------------------------------------------------*/
    public function queue_add(){
        if(!$_POST)
            return json(['code'=>1,'msg'=>'没有数据']);
        $validate=app('\app\caiji\validate\Queue');
        if($validate->check($_POST)){
            $model=app('\app\caiji\model\CaijiQueue');
            if($ret=$model->add($_POST)){
                return json(['code'=>0,'msg'=>'成功添加 '.$ret.'，你可以继续添加下一个','action'=>url('admin/caiji/queue_add')]);
            }else{
                return json(['code'=>2,'msg'=>'入库失败']);
            }
        }else{
            return json(['code'=>1,'msg'=>$validate->getError()]);
        }
    }
    /** ------------------------------------------------------------------
     * 采集队列修改
     *--------------------------------------------------------------------*/
    public function queue_edit(){
        $id=post('id','int',0);
        if(!$id || !$_POST)
            return json(['code'=>1,'msg'=>'id格式不符']);
        $validate= app('\app\caiji\validate\Queue');
        if($validate->check($_POST)){
            $model=app('\app\caiji\model\CaijiQueue');
            if($model->edit($_POST)){
                return json(['code'=>0,'msg'=>'成功更新','action'=>url('admin/caiji/queue')]);
            }else{
                return json(['code'=>2,'msg'=>'入库失败']);
            }
        }else{
            return json(['code'=>1,'msg'=>$validate->getError()]);
        }
    }

    //删除采集队列
    public function queue_del(){
        $id=get('id');
        if(!$id || preg_match('/^(\d[\d,]*)*\d$/',$id)==0){
            json(['code'=>1,'msg'=>'id不能为空']);
            return ;
        }
        $model=app('\app\caiji\model\CaijiQueue');
        if($model->del($id)){
            json(['code'=>0,'msg'=>'成功删除']);
        }else{
            json(['code'=>2,'msg'=>'删除失败']);
        }
    }


}