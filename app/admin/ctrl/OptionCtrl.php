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
    //网站全局设置
    public function all(){
        $model=app('\app\admin\model\Option');
        if($_POST){
            $model->update_option($_POST);
            $this->_redirect('admin/option/all','成功更新');
        }
        $data=$model->eq('status',1)->ne('name','city')->findAll(true);
        $this->_assign([
            'title'=>'全局设置',
            'data'=>$data,
        ]);
        $this->_display();
    }
    //添加网站全局变量
    public function add_option(){
        $msg='';
        $model=app('\app\admin\model\Option');
        if($_POST){
            $valide=app('\app\admin\validate\Option');
            if($valide->check($_POST)){
                if($ret=$model->add($_POST)){
                    $msg='成功添加 “'.$_POST['name'].',“，你可以继续添加下一个变量';
                }else{
                    $msg='添加失败';
                }
            }else{
                $msg=$valide->getError();
            }
        }
        $this->_assign([
            'title'=>'添加网站全局变量',
            'msg'=>$msg
        ]);
        $this->_display();
    }
    //更新全部设置的缓存
    public function update_cache(){
        $model=app('\app\admin\model\Option');
        $model->update_cache();
        json(['code'=>0,'msg'=>'成功']);
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
    //当前设置的区域城市数据
    public function citys_current(){

    }
}