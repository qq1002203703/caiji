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

class OtherCtrl extends AdminCtrl
{
    //定时任务管理
    public function queue(){
        $model=app('\app\admin\model\Crontab');
        if($_POST){
            //$model->update_option($_POST);
            //$this->_redirect('admin/option/all','成功更新');
        }
        $data=$model->findAll(true);
        $this->_assign([
            'title'=>'定时任务管理',
            'data'=>$data,
        ]);
        $this->_display();
    }

    //添加定时任务
    public function queue_add(){
        $msg='';
        $model=app('\app\admin\model\Crontab');
        if($_POST){
            $valide=app('\app\admin\validate\Crontab');
            if($valide->check($_POST)){
                if($ret=$model->add($_POST)){
                    $msg='成功添加 “'.$_POST['callable'].',“，你可以继续添加下一个变量';
                }else{
                    $msg='添加失败';
                }
            }else{
                $msg=$valide->getError();
            }
        }
        $this->_assign([
            'title'=>'添加定时任务',
            'msg'=>$msg
        ]);
        $this->_display();
    }

    //更改定时任务
    public function queue_edit(){
        $id=get('id','int',0);
        if($id<1) {
            $this->_redirect('admin/other/queue','定时任务id不能为空');
        }
        $msg='';
        $model=app('\app\admin\model\Crontab');
        if($_POST){
            $valide= app('\app\admin\validate\Crontab');
            $_POST['id']=$id;
            if($valide->check($_POST)){
                if($model->edit($_POST)){
                    $this->_redirect('admin/other/queue','成功更新');
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
        $this->_assign([
            'title'=>'编辑定时任务',
            'data'=>$data,
            'msg'=>$msg
        ]);
        unset($data,$_POST);
        $this->_display();
    }

    //删除定时任务
    public function queue_del(){
        $id=get('id');
        if(!$id || preg_match('/^(\d[\d,]*)*\d$/',$id)==0){
            json(['code'=>1,'msg'=>'id不能为空']);
            return ;
        }
        $model=app('\app\admin\model\Crontab');
        if($model->del($id)){
            json(['code'=>0,'msg'=>'成功删除']);
        }else{
            json(['code'=>2,'msg'=>'删除失败']);
        }
    }

    /** ------------------------------------------------------------------
     * 锚文本管理
     *---------------------------------------------------------------------*/
    public function links(){
        $currentPage = (int)get('page','int',1);
        $perPage=20;
        $url='';
        $where=[];
        if(isset($_GET['status'])){
            $status=(int)$_GET['status'];
            $url='?status='.$status;
            $where[]=['status','eq',$status];
        }
        $model=app('\app\admin\model\KeywordLink');
        $total = $model->count(['where'=>$where]);
        $data=[];
        if($total>0)
            $data=$model->_where($where)->order('weight desc,id')->limit(($currentPage-1)*$perPage,$perPage)->findAll(true);
        $url = url('admin/portal/post').$url.'&page=(:num)';
        $this->_display('',[
            'title'=>'自动锚文本',
            'data'=>$data,
            'page'=>(string)new Paginator($total,$perPage,$currentPage,$url),
            'total'=>$data===false ? [] : $data,
        ]);
    }

    /** ------------------------------------------------------------------
     * 锚文本添加
     *--------------------------------------------------------------------*/
    public function links_add(){
        $this->_display('',[
            'title'=>'锚文本添加',
        ]);
    }

    /** ------------------------------------------------------------------
     * 锚文本修改
     *--------------------------------------------------------------------*/
    public function links_edit(){
        $id=get('id');
        if($id<1) {
            $this->_redirect('admin/other/links','id格式不符');
        }
        $model=app('\app\admin\model\KeywordLink');
        $data=$model->eq('id',$id)->find(null,true);
        if(!$data)
            $this->_redirect('admin/other/links','不存在id为'.$id.'的锚文本');
        $this->_display('',[
            'title'=>'锚文本添加',
            'data'=>$model->eq('id',$id)->find(null,true),
        ]);
    }

}