<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 *
 * QQ 46502166
 *
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 网站设置控制器
 * ======================================*/

namespace app\admin\ctrl;
use app\common\ctrl\AdminCtrl;

class OptionCtrl extends AdminCtrl
{
    protected $type='site';
    protected $typeMap=['site'=>'网站','portal'=>'门户','bbs'=>'论坛'];
    protected $allType;

    protected function _init(){
        parent::_init();
        $this->getType();
    }
    /** ------------------------------------------------------------------
     * 获取种类
     *--------------------------------------------------------------------*/
    protected function getType(){
        $this->allType=array_keys($this->typeMap);
        if(($type=get('type')) && in_array($type,$this->allType)){
            $this->type=$type;
        }
        $this->_assign([
            'type'=>$this->type,
            'map'=>$this->typeMap,
            'allType'=>$this->allType
        ]);
    }
    //网站全局设置
    public function all(){
        $model=app('\app\admin\model\Option');
        $data=$model->eq('type',$this->type)->eq('status',1)->ne('name','city')->findAll(true);
        $this->_assign([
            'title'=>$this->typeMap[$this->type].'全局设置',
            'data'=>$data,
        ]);
        $this->_display();
    }

    //网站全局变量添加
    public function add(){
        $this->_display('',[
            'title'=>$this->typeMap[$this->type].'变量添加',
        ]);
    }

    //修改密码
    public function pwd(){
        $this->_display('',[
            'title'=>'密码修改'
        ]);
    }
    //个人信息
    public function info(){
        $useModel=app('app\portal\model\User');
        $this->_display('',[
            'title'=>'个人信息',
            'data'=>$useModel->eq('id',$_SESSION['uid'])->find(null,true),
        ]);
    }

    //区域设置
    public function citys(){
        $msg='';
        if($_POST){
            $id=$_POST['city'] ?? 0;
            if(is_array($id))
                $id=implode(',',$id);
            $type=(int) post('type','int',0);
            unset($_POST);
            if(!$id && preg_match('/^(\d[\d,]*)*\d$/',$id)==0){
                $msg='所选区域不能为空，或格式不正确';
            }
            if(!in_array($type,[0,1])){
                $msg='种类不正确';
            }
            if($msg==''){
                $ret=app('\app\admin\model\Option')->set_city($id,$type);
                $msg=$ret>0?'成功更新':'更新失败';
            }
        }
        $this->_assign([
            'title'=>'城市设置',
            'msg'=>$msg
        ]);
        $this->_display();
    }
    //省市生成json数据
    public function citys_cache(){
        $type=get('type','','');
        switch ($type){
            case 'all':
                $select='id,name,pid,shortname,level,citycode,zipcode,merger_name,lng,lat,pinyin';
                $fileName='all';
                break;
            case 'normal':
                $select='id,name,pid,level';
                $fileName='normal';
                break;
            case 'easybui':
                $select='';
                $fileName='easybui';
                break;
            case 'min':
                $select='name';
                $fileName='min';
                break;
            default:
                $select='id,name';
                $fileName='small';
        }
        $fileName='citys-'.$fileName.'.js';
        $model=app('\app\portal\model\Citys');
        $model->jsonCache($select,$fileName,$type);
        json(['code'=>0,'msg'=>'ok']);
    }

    /** ------------------------------------------------------------------
     * 设置种类
     * @param string $type
     * @return static $this
     *--------------------------------------------------------------------*/
    public function setType($type){
        $this->type=$type;
        return $this;
    }
}