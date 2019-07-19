<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 后台portal的api类
 * ======================================*/

namespace app\admin\ctrl;

use app\common\ctrl\ApiAdminCtrl;
use core\Conf;

class ApiOtherCtrl extends ApiAdminCtrl
{
    /** ------------------------------------------------------------------
     * 锚文本添加
     *---------------------------------------------------------------------*/
    public function links_add(){
        if($_POST){
            $validate=app('\app\admin\validate\KeywordLink');
            if($validate->check($_POST)){
                $model=app('\app\admin\model\KeywordLink');
                if($model->add($_POST))
                    return json(['code'=>0,'msg'=>'成功添加','action'=>url('admin/other/links_add')]);
                else
                    return  json(['code'=>2,'msg'=>'入库失败']);
            }else
                return json(['code'=>3,'msg'=>$validate->getError()]);
        }
        return json(['code'=>1,'msg'=>'数据为空']);
    }

    /** ------------------------------------------------------------------
     * 锚文本添加
     *---------------------------------------------------------------------*/
    public function links_edit(){
        if($_POST){
            $validate=app('\app\admin\validate\KeywordLink');
            if($validate->check($_POST)){
                $model=app('\app\admin\model\KeywordLink');
                if($model->edit($_POST))
                    return json(['code'=>0,'msg'=>'成功修改','action'=>url('admin/other/links')]);
                else
                    return  json(['code'=>2,'msg'=>'入库失败']);
            }else
                return json(['code'=>3,'msg'=>$validate->getError()]);
        }
        return json(['code'=>1,'msg'=>'数据为空']);
    }
    /** ------------------------------------------------------------------
     * 删除锚文本，必须的post或get参数 id
     *--------------------------------------------------------------------*/
    public function links_del(){
        $model=app('\app\admin\model\KeywordLink');
        $id=post('id')?:get('id');
        if(!$id)
            return json(['code'=>1,'msg'=>'id不能为空']);
        $ret=$model->del($id);
        if($ret===true)
            return json(['code' =>0, 'msg' => '成功删除', 'action' => url('admin/other/links')]);
        else
            return json(['code'=>1,'msg'=>$ret]);
    }

    /** ------------------------------------------------------------------
     * 模板编辑
     *--------------------------------------------------------------------*/
    public function tpl_edit(){
        $content=post('content','',false);
        if($content===false)
            return json(['code'=>1,'msg'=>'没有提交内容']);
        $file=post('save_path');
        if(!is_file($file))
            return json(['code'=>2,'msg'=>'不是有效的文件']);
        $content=htmlspecialchars_decode((string)$content);
        $res=file_put_contents($file,$content);
        return ($res===false) ?  json(['code'=>3,'msg'=>'写入失败']) :  json(['code'=>0,'msg'=>'成功更新']);
    }

}