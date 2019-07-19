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
        $model=app('\app\caiji\model\Caiji');
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

    public function page_add(){
        $this->_display('',[
            'title'=>'采集任务添加',
        ]);
    }

    public function page_edit(){
        $id=get('id','int',0);
        if($id<1) {
            $this->_redirect('admin/caiji/list','id不能为空');
        }
        $model=app('\app\caiji\model\Caiji');
        $this->_display('',[
            'title'=>'采集任务修改',
            'data'=>$model->from('caiji_page')->eq('id',$id)->find(null,true),
        ]);
    }

    /** ------------------------------------------------------------------
     * 采集管理
     *---------------------------------------------------------------------*/
    public function list(){
        $model=app('\app\caiji\model\Caiji');
        $currentPage =  get('page','int',1);
        $perPage=20;
        $page='';
        $data=[];
        $url='';
        $where=[];
        $get['keywords']='';
        if($get['keywords']=get('keywords','','')){
            $url.='&keywords='.$get['keywords'];
            $get['keywords']=urldecode($get['keywords']);
            $where[]=['name','like','%'.$get['keywords'].'%'];
        }
        $total=$model->count([
            'from'=>'caiji_page',
            'where'=>$where
        ]);
        if($total >0){
            $data=$model->select('id,name,url,update_time,status')->from('caiji_page')->_where($where)->limit(($currentPage-1)*$perPage,$perPage)->order('id desc')->findAll(true);
            $url = url('admin/caiji/list').'?'.$url.'&page=(:num)';
            $page=(string) new Paginator($total,$perPage,$currentPage,$url);
        }

        $this->_display('',[
            'title'=>'采集任务管理',
            'data'=>$data,
            'total'=>$total,
            'page'=>$page,
            'get'=>$get
        ]);
    }

    /** ------------------------------------------------------------------
     * 采集管理
     *---------------------------------------------------------------------*/
    public function handler(){
        $model=app('\app\caiji\model\Caiji');
        $projects=$model->getCaijiProject();
        $data=[];
        $page='';
        $total=0;
        list($where,$get,$url)=$this->_getQuery([
            'table'=>['d'=>'','w'=>'','f'=>''],
            'name'=>['d'=>'','w'=>'eq','f'=>'','fi'=>'caiji_name'],
            'isshenhe'=>['d'=>0,'w'=>'eq','f'=>'int'],
            'isend'=>['d'=>1,'w'=>'eq','f'=>'int'],
            'islaji'=>['d'=>0,'w'=>'eq','f'=>'int'],
        ]);
        $get['keywords']='';
        if($get['table'] && $get['name']){
            //$isUserLike=(int)get('like','int',false);
            if($get['keywords']=get('keywords','','')){
                $url.='&keywords='.$get['keywords'];
                $get['keywords']=urldecode($get['keywords']);
                $where[]=['title','like',$get['keywords'].'%'];
            }
            $total=$model->count([
                'from'=>$get['table'],
                'where'=>$where,
            ]);
            if($total>0){
                $currentPage =  get('page','int',1);
                $perPage=20;
                $data=$model->from($get['table'])->_where($where)->order('create_time desc')->limit(($currentPage-1)*$perPage,$perPage)->findAll(true);
                $url = url('admin/caiji/handler').'?'.$url.'&page=(:num)';
                $page=(string) new Paginator($total,$perPage,$currentPage,$url);
            }
        }
        $this->_display('',[
            'title'=>'采集结果管理',
            'projects'=>$projects,
            'data'=>$data,
            'page'=>$page,
            'total'=>$total,
            'get'=>$get
        ]);
    }
    public function result_edit(){
        $table=get('table','',false);
        $id=get('id','',false);
        if(!$id || !$table){
            $this->_redirect('admin/caiji/handler','数据格式不正确');
        }
        $model=app('\app\caiji\model\Caiji');
        $data=$model->from($table)->eq('id',$id)->find(null,true);
        if(!$data){
            $this->_redirect('admin/caiji/handler','数据表 '.$table.' 中没有对应 '.$id.'的id');
        }
        $this->_display('',[
            'title'=>'编辑：'.$data['title'],
            'data'=>$data,
        ]);
    }
    /** ------------------------------------------------------------------
     * 单篇审核：必需get参数: table,name, 可选get参数isend
     *---------------------------------------------------------------------*/
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
        $model=app('\app\caiji\model\Caiji');
        if($_POST){
            $model->table=$table;
            $_POST['create_time']=strtotime($_POST['create_time']);
            $model->eq('id',$_POST['id'])->update($_POST);
            $model->table='caiji';
            if($id)
                header('Location: '.url('admin/caiji/shenhe').'?table='.$table.'&name='.$_POST['caiji_name']);
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

/************采集队列*************************************************************************************************************/
    /** ------------------------------------------------------------------
     * 采集队列管理
     *--------------------------------------------------------------------*/
    public function queue(){
        $model=app('\app\caiji\model\CaijiQueue');
        $data=$model->order('id desc')->limit(50)->findAll(true);
        $this->_display('',[
            'title'=>'定时任务管理',
            'data'=>$data,
        ]);
    }
    //添加采集队列
    public function queue_add(){
        $this->_display('',[
            'title'=>'添加采集队列',
        ]);
    }

    //更改采集队列
    public function queue_edit(){
        $id=get('id','int',0);
        if($id<1) {
            $this->_redirect('admin/caiji/queue','定时任务id不能为空');
        }
        $model=app('\app\caiji\model\CaijiQueue');
        $this->_display('',[
            'title'=>'编辑队列任务',
            'data'=>$model->getById($id)
        ]);
    }


}