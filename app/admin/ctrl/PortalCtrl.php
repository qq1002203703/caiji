<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 门户管理
 * ======================================*/

namespace app\admin\ctrl;
use app\common\ctrl\AdminCtrl;
use core\Conf;
use extend\Paginator;

class PortalCtrl extends AdminCtrl
{
    protected $cs=[];//当前app的配置currentSetting
    protected $pindao=[];//所有频道['article','soft','goods']
    protected $type='';
    //初始化
    protected function _init(){
        parent::_init();
        //读取设置
        $this->cs=Conf::all('portal');
        $this->pindao=array_keys($this->cs['pindao']);
        $this->getType();
        $this->_assign([
            'type'=>$this->type,
            'pindao'=>$this->pindao,
            'cs'=>$this->cs
        ]);
    }

    /** ------------------------------------------------------------------
     * 获取频道种类
     * @return mixed|string
     *--------------------------------------------------------------------*/
    protected function getType(){
        $type=get('type');
        if(!$type || !in_array($type,$this->pindao)){
            $type='article';
        }
        $this->type=$type;
        return $type;
    }

    //分类管理
    public function category(){
        $model=app('\app\portal\model\PortalCategory');
        $category=$model->setType('portal_'.$this->type)->getTreeTable();
        $this->_assign([
            'title'=>$this->cs['pindao'][$this->type].'分类管理',
            'category'=>$category
        ]);
        $this->_display();
    }
    //添加分类
    public function category_add(){
        $model=app('\app\portal\model\PortalCategory');
        $model->setType('portal_'.$this->type);
        $this->_assign([
            'title'=>'添加'.$this->cs['pindao'][$this->type].'分类',
            'select'=>$model->getTreeSelect(),
        ]);
        $this->_display();
    }


    //编辑分类
    public function category_edit(){
        $id=get('id','int',0);
        if($id<1) {
            $this->_redirect('admin/portal/category?type='.$this->type,'分类id不能为空');
        }

        $model=app('\app\portal\model\PortalCategory');
        $model->setType('portal_'.$this->type);
        $data=$model->getById($id);
        if($data['thumb_ids']){//图集
            $data['thumb_ids']=$model->select('id,uri')->from('file')->in('id',explode(',',$data['thumb_ids']))->findAll(true);
        }
        $select=$model->getTreeNotIn($id);
        $this->_assign([
            'title'=>'编辑'.$this->cs['pindao'][$this->type].'分类',
            'data'=>$data,
            'select'=>$select,
        ]);
        $this->_display();
    }

    //post管理
    public function post(){
        $currentPage =  get('page','int',1);
        $perPage=10;
        $status=(int)get('status','int',1);
        $where[]=['status','eq',$status];
        $where[]=['type','eq',$this->type];
        $url='?type='.$this->type.'$status='.$status;
        $get=[];
        if($get['category_id']=get('category_id','int',0)){
            $where[]=['category_id','eq',$get['category_id']];
            $url.='&category_id='.$get['category_id'];
        }
        if($get['keywords']=get('keywords','','')){
            $url.='&keywords='.$get['keywords'];
            $get['keywords']=urldecode($get['keywords']);
            $where[]=['title','like','%'.$get['keywords'].'%'];
        }
        if($get['is_top']=get('is_top','int',0)){
            $where[]=['is_top','eq',$get['is_top']];
            $url.='&is_top='.$get['is_top'];
        }
        if($get['recommended']=get('recommended','int',0)){
            $where[]=['recommended','eq',$get['recommended']];
            $url.='&recommended='.$get['recommended'];
        }
        $cateModel=app('\app\portal\model\PortalCategory');
        $cateModel->setType('portal_'.$this->type);
        $model=app('\app\portal\model\PortalPost');
        $total = $model->count(['where'=>$where]);
        if($total==0)
            $posts=[];
        else
            $posts=$model->search($where,[($currentPage-1)*$perPage,$perPage],'create_time desc,id');
        $url = url('admin/portal/post').$url.'&page=(:num)';
        $page=(string)new Paginator($total,$perPage,$currentPage,$url);
        $this->_display('',[
            'title'=>$this->cs['pindao'][$this->type].'管理',
            'data'=>$posts,
            'page'=>$page,
            'total'=>$total,
            'total_fabu'=>$model->count(['where'=>[['status','eq',1],['type','eq',$this->type]]]),
            'total_dingshi'=>$model->count(['where'=>['status','eq',3],['type','eq',$this->type]]),
            'categorys'=>$cateModel->getTree(['id','name'],'<option value="%id%">%name%</option>'),
            'get'=>$get,
        ]);
    }

    /**
     * 添加post
     */
    public function post_add(){
        $catModel=app('\app\portal\model\PortalCategory');
        $category=$catModel->setType('portal_'.$this->type)->getTreeSelect();
        if($category==='')//没有分类时
            $this->_redirect('admin/portal/category?type='.$this->type,'请先添加分类');
        $this->_assign([
            'title'=>'添加'.$this->cs['pindao'][$this->type],
            'category'=>$category,
            'allow'=>[1=>'完全公开',2=>'金钱购买',3=>'金币购买',4=>'积分达到',5=>'vip会员'],
            'allowDefaultSelect'=>$this->getAllowDefaultSelect()
        ]);
        $this->_display();
    }

    /**
     * 编辑文章
     */
    public function post_edit(){
        $id=get('id','int',0);
        if($id<1) {
            $this->_redirect('admin/portal/post',$this->cs['pindao'][$this->type].'id不能为空');
        }
        $model=app('\app\portal\model\PortalPost');
        $data=$model->getOne($id);
        if(!$data) $this->_redirect('admin/portal/post','不存在id为'.$id.'的'.$this->pindao[$this->type]);
        $catModel=app('\app\portal\model\PortalCategory');
        $category=$catModel->setType('portal_'.$this->type)->getTreeSelect($data['category_id']);
        unset($catModel);
        //$tagModel=app('\app\admin\model\Tag');
        //$tagModel->type='portal_'.$this->type;
        $this->_assign([
            'title'=>'编辑'.$this->cs['pindao'][$this->type],
            'category'=>$category,
            'data'=>$data,
            'allow'=>[1=>'完全公开',2=>'金钱购买',3=>'金币购买',4=>'积分达到',5=>'vip会员'],
            'parent'=>($data['id']>0) ? $model->select('id,title')->eq('id',$data['pid'])->find(null,true) : '',
            'children'=>$model->eq('pid',$data['id'])->limit(20)->findAll(true),
        ]);
        $this->_display();
    }

    /** ------------------------------------------------------------------
     * 默认权限选中项
     * @return int
     *--------------------------------------------------------------------*/
    protected function getAllowDefaultSelect(){
        switch ($this->type){
            case 'article':
            case 'soft':
                return 1;
            case 'shop':
            //case 4:
                return 2;
            default:
                return 1;
        }
    }

}