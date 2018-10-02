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
 * 微信菜单模型
 * ======================================*/


namespace app\wechat\model;
use core\Model;

class WechatMenu extends Model
{
    use \app\common\model\CategoryCommon;
    use \app\common\model\RecycleCommon;
    /**
     * @var string 表名
     */
    public $table='wechat_menu';
    /**
     * @var string 主键名
     */
    public $primaryKey = 'id';

    /** ------------------------------------------------------------------
     * 添加菜单
     * @param array $data
     * @return bool|int
     *--------------------------------------------------------------------*/
    public function add($data){
        $data['level']=$this->getLevel($data['pid']);
        $id=$this->insert($data);
        if($id){
            //更新path
            $path=$this->getPath($id,$data['pid']);
            if($path !== '')
                $this->update(['path'=>$path,'id'=>$id]);
            $this->reset();
            $this->cache($this->findAll(true));
            return $id;
        }
        return false;
    }

    /** ------------------------------------------------------------------
     * getTreeTable
     * @return string
     *--------------------------------------------------------------------*/
    public function getTreeTable(){
        return $this->getTree(['id','name','type','text','status'],
            '<tr><td><input class="pure-checkbox" type="checkbox" value="%id%" name="ids[]"></td><td>%id%</td><td>%__repeat_content__%</td><td>%type%</td><td>%text%</td><td class="status">%status%</td><td align="center"><a class="pure-button btn-success btn-xs edit" href="'.url('admin/wechat/edit_menu').'?id=%id%">编辑</a> <a class="pure-button btn-error btn-xs change" href="'.url('admin/wechat/change_status').'?id=%id%">改状态</a></td></tr>','');
    }

    /*------------------------------------------------------------------
     * 更改状态
     * @param int $id
     * @return int
     *--------------------------------------------------------------------*/
    public function changeStatus($id){
        $menu=$this->getById($id);
        if(!$menu) return 1;
        $new_status=1;
        if($menu['status']==1){
            $new_status=0;
        }
        if($this->update(['id'=>$id, 'status'=>$new_status]) > 0){
            //当不使用时，要把子孙后代也改为不使用
            if($new_status==0 && $menu['pid']==0){
                $this->reset();
                $this->like('path',$menu['path'].'-%')->update(['status'=>0]);
            }
            $this->updateCache();
            return 0;
        }else{
            return 2;
        }
    }

    /** ------------------------------------------------------------------
     * 编辑微信菜单
     * @param array $data
     * @return bool
     *-------------------------------------------------------------------*/
    public function edit($data){
        $data['level']=$this->getLevel($data['pid']);
        $data['path']=$this->getPath($data['id'],$data['pid']);
        $old_data=$this->getById($data['id']);
        $data=$this->_filterData($data);
        if($this->update($data)){
            $this->childrenChange($data,$old_data);
            $this->updateCache();
            return true;
        }
        return false;
    }

    /** ------------------------------------------------------------------
     * 菜单同步到微信服务器前的验证
     * @param array $data:待检测的数据
     * @return string：验证通过返回空字符串，否则返回带错误提示的字符串
     *---------------------------------------------------------------------*/
    public function uploadValidate($data){
        $msg='';
        if(!$data){
            $msg.='还没有菜单请先添加菜单';
            return $msg;
        }
        $tree=$this->newTree($data);
        $rootMenu=$tree->getRootNodes(true);
        if(count($rootMenu) >3 ){
            $msg.='一级菜单不能超过3个；';
        }
        foreach ($rootMenu as $v){
            $node=$tree->getNodeById($v['id']);
            if($node->countChildren() >5){
                $msg.=$v['name'].' 菜单下的子菜单不能超过5个；';
            }
        }
        return $msg;
    }

    /** ------------------------------------------------------------------
     * uploadFormat
     * @param null|\core\lib\tree\Node $node
     * @return array
     *-------------------------------------------------------------------*/
    public function uploadFormat($node=null){
        $nodes=\is_object($node) ? $node->getChildren() : $this->newTree()->getRootNodes();
        $ret=[];
        foreach ($nodes as $key =>$value){
            $ret[$key]['name']=$value->get('name');
            if(!$value->haschildren()){
                $type=$value->get('type');
                $ret[$key]['type']=$type;
                if($type=='click'){
                    $ret[$key]['key']=$value->get('text');
                }elseif ($type=='view'){
                    $ret[$key]['url']=$value->get('text');
                }
            }else{
                $ret[$key]['sub_button']=$this->uploadFormat($value);
            }
        }
        return $ret;
    }

}