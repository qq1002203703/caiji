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
use core\Conf;
use extend\Helper;

class KamiAdminCtrl extends ApiAdminCtrl
{
    /** ------------------------------------------------------------------
     * 添加卡密种类接口：提交方式 post
     *---------------------------------------------------------------------*/
    public function type_add(){
        if(!$_POST)
            return json(['code'=>1,'msg'=>'数据不能为空']);
        $validate=app('\app\admin\validate\Kami');
        if(!$validate->check($_POST))
            return json(['code'=>2,'msg'=>$validate->getError()]);
        $model=app('\app\admin\model\Kami');
        if(!$model->typeAdd($_POST))
            return  json(['code'=>2,'msg'=>'写入数据库失败']);
        return  json(['code'=>0,'msg'=>'成功添加卡密种类','action'=>url('admin/kami/setting')]);
    }

    /** ------------------------------------------------------------------
     * 删除卡密种类接口：提交方式 get,参数如下
     * id number 卡密种类的id 必须
     *---------------------------------------------------------------------*/
    public function type_del(){
        $id=(int)trim(get('id','int',0));
        if($id<=0 ){
            return json(['code'=>1,'msg'=>'id格式不符']);
        }
        $model=app('\app\admin\model\Kami');
        if($model->from('kami_type')->eq('id',$id)->delete())
            return  json(['code'=>0,'msg'=>'成功删除id为'.(int)$id.'的卡密种类','action'=>url('admin/kami/setting')]);
        else
            return  json(['code'=>2,'msg'=>'不存在的卡密id']);
    }

    /** ------------------------------------------------------------------
     * 修改卡密种类接口：提交方式 post,参数如下
     *---------------------------------------------------------------------*/
    public function type_edit(){
        if(!$_POST)
            return json(['code'=>1,'msg'=>'数据不能为空']);
        $id=(int)post('id','int',0);
        if($id<=0)
            return json(['code'=>2,'msg'=>'id格式不符']);
        $validate=app('\app\admin\validate\Kami');
        if(!$validate->check($_POST))
            return json(['code'=>3,'msg'=>$validate->getError()]);
        $model=app('\app\admin\model\Kami');
        if(!$model->typeUpdate($_POST,$id))
            return  json(['code'=>4,'msg'=>'修改失败，id不存在或数据没有变化']);
        return  json(['code'=>0,'msg'=>'成功修改','action'=>url('admin/kami/setting')]);
    }

    /** ------------------------------------------------------------------
     * 生成卡密  提交方式 post,参数如下
     * type number 卡密种类的id 必须
     * number number 生成卡密的数量 必须
     *--------------------------------------------------------------------*/
    public function make(){
        $id=(int)post('type','int',0);
        if($id<= 0)
            return json(['code'=>1,'msg'=>'请选择正确的种类']);
        $number=(int)post('number','int',0);
        if($number<=0)
            return json(['code'=>2,'msg'=>'生成个数必须大于0']);
        $model=app('\app\admin\model\Kami');
        if(!$model->from('kami_type')->eq('id',$id)->find(null,true))
            return json(['code'=>3,'msg'=>'不存在卡密种类']);
        $sql='INSERT INTO `table`( `ka`, `type`, `status`) VALUES ';
        set_time_limit(0);
        for ($i=0;$i<$number;$i++)
            $sql.='(\''.Helper::uuid(32).'\','.$id.',0),';
        $sql=rtrim($sql,',');
        $model->_exec($sql,[],true);
        return json(['code'=>0,'msg'=>'成功生成','action'=>url('admin/kami/list')]);
    }
    /** ------------------------------------------------------------------
     * 设置卡密状态变成出售中  提交方式 post,参数如下
     * large_id number 最大的id 必须
     * type number 卡密种类 必须
     *--------------------------------------------------------------------*/
    public function type2sale(){
        $big=(int)post('large_id','int',0);
        if($big<=0)
            return json(['code'=>1,'msg'=>'最大id格式不符']);
        $type=(int)post('type','int',0);
        if($type<=0)
            return json(['code'=>2,'msg'=>'type格式不符']);
        $model=app('\app\admin\model\Kami');
        if($model->lte('id',$big)->eq('status',0)->eq('type',$type)->update(['status'=>1]))
            return json(['code'=>0,'msg'=>'成功更新']);
        else
            return json(['code'=>3,'msg'=>'更新失败，不存在对应的数据']);
    }

    //删除完结的卡密
    public function del_end(){
        $model=app('\app\admin\model\Kami');
        $model->eq('status',2)->delete();
        return json(['code'=>0,'msg'=>'成功删除','action'=>url('admin/kami/list')]);
    }
}