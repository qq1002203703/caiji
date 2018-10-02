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
class CaijiCtrl extends AdminCtrl
{
    public function setting(){
        $id=get('id','int',1);
        $model=app('\app\admin\model\Caiji');
        $data=$model->eq('id',$id)->find(null,true);
        if(!$data){
            echo '不存在id:'.$id;
            return ;
        }
        $page=var_export(json_decode($data['page'],true),true);
        echo '$page = '.stripslashes($page).";\n\n";
        echo "//---------------------------------------------------------------------------------------------- \n\n ";
        $content=var_export(json_decode($data['content'],true),true);
        echo '$content = '.stripslashes($content).";\n\n";
        echo "//---------------------------------------------------------------------------------------------- \n\n ";
        $download=var_export(json_decode($data['download'],true),true);
        echo '$download = '.stripslashes($download).";\n\n";
    }

    public function handler(){
        $model=app('\app\admin\model\Caiji');
        $data=$model->getCaijiProject();
        //dump($data);
        $this->_assign([
            'title'=>'项目管理',
            'data'=>$data
        ]);
        $this->_display();
    }

    public function shenhe(){
        $table=get('table','',false);
        if(!$table){
            $this->_redirect('admin/caiji/handler','项目数据表不正确');
        }
        $name=get('name','',false);
        $id=get('id','',false);
        if(!$id && !$name){
            $this->_redirect('admin/caiji/handler','项目名不正确');
        }
        $isend=get('isend','int',1);
        $model=app('\app\admin\model\Caiji');
        if($_POST){
            $model->table=$table;
            $_POST['create_time']=strtotime($_POST['create_time']);
            $model->eq('id',$_POST['id'])->update($_POST);
            $model->table='caiji';
            if($id)
                header('Location: '.url('admin/caiji/shenhe').'?table='.$table.'$name='.$_POST['caiji_name']);
        }
        if($id){
            $data=$model->from($table)->eq('id',$id)->find(null,true);
        }else{
            $data=$model->from($table)->eq('caiji_name',$name)->eq('isshenhe',0)->eq('isend',$isend)->eq('islaji',0)->order('create_time desc')->find(null,true);
            echo $model->getSql();
        }
        if(!$data){
            $this->_redirect('admin/caiji/handler','没有待审核的内容');
        }
        $total=$model->count([
            'from'=>$table,
            'where'=>[['caiji_name','eq',$data['caiji_name']],['isshenhe','eq',0],['isend','eq',$isend],['islaji','eq',0]],
        ]);
        $this->_assign([
            'title'=>'审核：'.$data['title'],
            'data'=>$data,
            'total'=>$total,
        ]);
        $this->_display();
    }

    /** ------------------------------------------------------------------
     * 批量审核：需要get参数: table,name,page,isend
     *---------------------------------------------------------------------*/
    public function shenhem(){
        $table=get('table','',false);
        if(!$table){
            $this->_redirect('admin/caiji/handler','项目数据表不正确');
        }
        $name=get('name','',false);
        if(!$name){
            $this->_redirect('admin/caiji/handler','项目名不正确');
        }
        $isend=get('isend','int',1);
        $url='?isend='.$isend;
        $model=app('\app\admin\model\Caiji');
        $total=$model->count([
            'from'=>$table,
            'where'=>[['caiji_name','eq',$name],['isshenhe','eq',0],['isend','eq',$isend],['islaji','eq',0]],
        ]);
        if($total==0){
            $this->_redirect('admin/caiji/handler','没有待审核的内容');
        }
        $currentPage =  get('page','int',1);
        $perPage=50;
        $data=$model->from($table)->eq('caiji_name',$name)->eq('isshenhe',0)->eq('isend',$isend)->eq('islaji',0)->order('create_time desc')->limit(($currentPage-1)*$perPage,$perPage)->findAll(true);
        $url = url('admin/caiji/shenhem').'?'.$url.'page=(:num)';
        $page=new Paginator($total,$perPage,$currentPage,$url);
        $this->_assign([
            'title'=>'批量审核',
            'data'=>$data,
            'total'=>$total,
            'page'=>(string)$page,
        ]);
        $this->_display();
    }

    /** ------------------------------------------------------------------
     * 批量审核时更改内容：需要get参数: id,laji,table
     *---------------------------------------------------------------------*/
    public function shenhem_json(){
        $id=get('id');
        $islaji=get('laji','int',1);
        $table=get('table','',false);
        if(!$table||!$id || preg_match('/^(\d[\d,]*)*\d$/',$id)==0){
            json(['code'=>1,'msg'=>'id或table不能为空']);
            return ;
        }
        $model=app('\app\admin\model\Caiji');
        if($model->shenhe($id,$table,$islaji)){
            json(['code'=>0,'msg'=>'成功操作']);
        }else{
            json(['code'=>2,'msg'=>'操作失败']);
        }
    }
}