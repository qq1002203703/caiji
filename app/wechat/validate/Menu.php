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
 *
 * ======================================*/


namespace app\wechat\validate;
use core\Validate;

class Menu extends Validate
{
    protected $rule=[
        'id'=>'checkChildren',
        'pid'=>'require|checkPid',
        'name'  => ['require','checkName','checkNameSize'],
        'type'=>'require|checkType|checkText',
        'status'=>'between:0,1',
    ];
    protected $message  =   [
        'id.checkChildren'=>'上级设置出错：由于此菜单含子类菜单，只能作顶级菜单'."\n".'除非先把它的子菜单移到其他分类，才能修改为二级菜单',
        'name.require'=>'菜单名不能为空',
        'name.checkName'     => '菜单名出错，已经存在相同名字的菜单了',
        'name.checkNameSize'=>'菜单名格式正不正确（1级菜单最多4个汉字,二级菜单最多7个汉字）',
        'pid.require'=>'父id不能为空',
        'pid.checkPid'=>'父id不存在',
        'type.require'=>'类型不能为空',
        'type.checkType'=>'类型只能是这几个中的一种：click,view,scancode_push,scancode_waitmsg,pic_sysphoto,pic_photo_or_album,pic_weixin,location_select',
        'status'=>'状态只能是0和1',
        'type.checkText'=>'text格式不正确'
    ];
    //含子类菜单时验证
    protected function checkChildren($id,$rule,$data){
        //echo 1111;
        if($data['pid'] >0){
            $modle=app('\app\wechat\model\WechatMenu');
            //var_dump(! $modle->hasChildren($id));
            //exit();
            return (! $modle->hasChildren($id));
        }
        //echo 2222;
        //exit();
        return true;
    }
    /**------------------------------------------------------------------
     * 检测菜单名
     * @param string $data
     * @param null $rule
     * @param array $all_data
     * @return bool
     *---------------------------------------------------------------------*/
    protected function checkName($name,$rule,$data){
        unset($rule);
        $id=isset($data['id'])?$data['id']:0;
        return app('\app\wechat\model\WechatMenu')->checkName($name,$id);
    }
    //验证菜单字数
    protected function checkNameSize($name,$rule,$data){
        unset($rule);
        if(!isset($data['pid']))
            return false;
        if($data['pid']==0 &&mb_strlen($name)>4)
            return false;
        if($data['pid'] > 0 &&mb_strlen($name)>7)
            return false;
        return true;
    }
    //验证pid
    protected function checkPid($pid,$rule,$data){
        unset($rule);
        $id=isset($data['id'])?$data['id']:0;
        return app('\app\wechat\model\WechatMenu')->checkPid($pid,$id);
    }

    public function checkType($type){
        return in_array($type,['click','view','scancode_push','scancode_waitmsg','pic_sysphoto','pic_photo_or_album','pic_weixin','location_select']);
    }
    protected function checkText($type,$rule,$data){
        unset($rule);
        if($type=='click') {
            return !isset($data['text'])?false: $data['text'] !== '';
        }
        if($type=='view') {
            if(!isset($data['text'])) return false;
            if(!filter_var($data['text'],FILTER_VALIDATE_URL)){
                return false;
            }
        }
        return true;
    }
}