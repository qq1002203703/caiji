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
 * 微信管理
 * ======================================*/

namespace app\admin\ctrl;

class WechatCtrl extends \app\common\ctrl\AdminCtrl
{
    use \app\common\ctrl\Wechat;
    //微信菜单管理
    public function menu(){
        $model=app('\app\wechat\model\WechatMenu');
        $data=$model->getTreeTable();
        $this->_assign([
            'title'=>'菜单管理',
            'data'=>$data,
        ]);
        $this->_display();
    }
    //添加微信菜单
    public function add_menu(){
        $msg='';
        $model=app('\app\wechat\model\WechatMenu');
        if($_POST){
            $valide=app('\app\wechat\validate\Menu');
            if($valide->check($_POST)){
                if($id=$model->add($_POST)){
                    $msg='成功添加 “'.$_POST['name'].',id:'.$id.'“，你可以继续添加下一个菜单';
                }else{
                    $msg='添加失败';
                }
            }else{
                $msg=$valide->getError();
            }
        }
        $this->_assign([
            'title'=>'添加微信菜单',
            'msg'=>$msg,
            'select'=>$model->getTop()
        ]);
        $this->_display();
    }
    /**
     * 更新微信菜单缓存
     */
    public function update_menu_cache(){
        $model=app('\app\wechat\model\WechatMenu');
        $model->updateCache();
        json(['code'=>0,'msg'=>'已成功更新了缓存']);
    }
    //删除微信菜单
    public function delete_menu(){
        $id=get('id');
        if(!$id || preg_match('/^(\d[\d,]*)*\d$/',$id)==0){
            json(['code'=>1,'msg'=>'id不能为空,或格式不正确']);
            return ;
        }
        $model=app('\app\wechat\model\WechatMenu');
        if($model->del($id)){
            $model->updateCache();
            json(['code'=>0,'msg'=>'成功删除']);
        }else{
            json(['code'=>2,'msg'=>'删除失败']);
        }
    }
    //更改微信菜单状态
    public function change_status(){
        $id=get('id');
        if($id<1) { json(['code'=>1,'msg'=>'id不能为空']);return;}
        $model=app('\app\wechat\model\WechatMenu');
        $ret=$model->changeStatus($id);
        if($ret===0)
            json(['code'=>0,'msg'=>'状态成功更新']);
        else{
            $erros=[1=>'找不到此id的菜单', 2=>'更改失败'];
            json(['code'=>2,'msg'=>$erros[$ret]]);
        }
    }
    //编辑菜单
    public function edit_menu(){
        $id=get('id','int',0);
        if($id<1) {
            $this->_redirect('admin/wechat/menu','菜单id不能为空');
        }
        $msg='';
        $model=app('\app\wechat\model\WechatMenu');
        if($_POST){
            $valide= app('\app\wechat\validate\Menu');
            $_POST['id']=$id;
            if($valide->check($_POST)){
                if($model->edit($_POST)){
                    //$msg='成功更新';
                    $this->_redirect('admin/wechat/menu','成功更新');
                }else{
                    $msg='更新失败';
                }
            }else{
                $msg=$valide->getError();
            }
        }
        $this->_assign([
            'title'=>'编辑菜单',
            'data'=>$model->getById($id),
            'select'=>$model->getTop($id),
            'msg'=>$msg
        ]);
        $this->_display();
    }
    //上传微信菜单到微信服务器，实现同步
    public function upload_menu(){
        $model=app('\app\wechat\model\WechatMenu');
        $data=$model->eq('status',1)->findAll(true);
        $msg=$model->uploadValidate($data);
        if($msg!==''){
            json(['code'=>1,'msg'=>'菜单个数不符合要求，请按下面提示修改后再同步'."\n".$msg]);
            return ;
        }
        $ret= $model->uploadFormat();
        $ret=$this->officialAccount()->menu->create($ret);
        //dump($ret);
        if(isset($ret['errcode'])){
            if($ret['errcode']==0){
                json(['code'=>0,'msg'=>'成功同步到公众号']);
            }else{
                json(['code'=>3,'msg'=>$ret['errmsg']]);
            }
        }else{
            json(['code'=>2,'msg'=>'未知错误']);
        }
    }

}