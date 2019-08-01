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
use shell\tools\Sitemap;

class ApiPortalCtrl extends ApiAdminCtrl
{
    protected $cs=[];//当前app的配置currentSetting
    protected $pindao=[];//所有频道$cs中的pindao的keys
    protected $type='';

    //初始化
    protected function _init(){
        parent::_init();
        //读取设置
        $this->cs=Conf::all('portal');
        $this->pindao=array_keys($this->cs['pindao']);
        $this->getType();
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
/**内页------------------------------------------------------------------------------------------------------------------*/
    /** ------------------------------------------------------------------
     * 内页添加
     *--------------------------------------------------------------------*/
    public function post_add(){
        if(!$_POST)
            return  json(['code'=>1,'msg'=>'没有数据']);
        $validate=app('\app\portal\validate\Post');
        if($validate->check($_POST)){
            $model=app('\app\portal\model\PortalPost');
            $id=$model->add($_POST);
            if($id){
                if(isset($_POST['status']) && $_POST['status']=='2') //存为草稿时
                    return json(['code'=>0,'msg'=>'成功保存为草稿','action'=>url('admin/portal/post_edit').'?type='.$this->type.'&id='.$id]);
                else{
                    //提交到搜索引擎
                    Sitemap::submitMulti(url('@'.$this->type.'@',['id'=>$id]),false);
                    //ping提交
                    if(Conf::get('ping_baidu','site'))
                        Sitemap::pingBaidu(url('@'.$this->type.'@',['id'=>$id]));
                    return json(['code'=>0,'msg'=>'成功添加,新id为:'.$id.'，你可以继续添加下一个了','action'=>url('admin/portal/post_add').'?type='.$this->type]);
                }

            }else{
                return json(['code'=>3,'msg'=>'验证通过，但入库失败']);
            }
        }else{
            return json(['code'=>2,'msg'=>$validate->getError()]);
        }
    }

    /** ------------------------------------------------------------------
     * 内页编辑  必须的post参数 id
     *--------------------------------------------------------------------*/
    public function post_edit(){
        if(!$_POST)
            return  json(['code'=>1,'msg'=>'没有数据']);
        $_POST['id']=(int)post('id','int',0);
        if(! $_POST['id'])
            return  json(['code'=>2,'msg'=>$this->cs['pindao'][$this->type].'id不能为空']);
        $validate=app('\app\portal\validate\Post');
        $model=app('\app\portal\model\PortalPost');
        if($validate->check($_POST)){
            if($model->edit($_POST)){
                return json(['code'=>0,'msg'=>'成功更新','action'=>url('admin/portal/post').'?type='.$this->type]);
            }else{
                return json(['code'=>4,'msg'=>'入库失败']);
            }
        }else
            return  json(['code'=>3,'msg'=>$validate->getError()]);
    }

    /**
     * 内页批量操作
     */
    public function post_multi_action(){
        $ac=get('ac');
        switch ($ac){
            case 'del':
                $this->post_del();
                break;
            default:
                json(['code'=>2,'msg'=>'ac不符']);
        }
    }

    /** ------------------------------------------------------------------
     * 删除文章，必须的post或get参数 id
     *--------------------------------------------------------------------*/
    public function post_del(){
        $model=app('\app\portal\model\PortalPost');
        $id=post('id')?:get('id');
        $ret=$model->del($id,$this->type);
        if($ret===true){
            json(['code'=>0,'msg'=>'成功删除','action'=>url('admin/portal/post').'?type='.$this->type]);
        }else{
            json(['code'=>1,'msg'=>$ret]);
        }
    }

    /** ------------------------------------------------------------------
     * 聚合时的搜索 必须的post参数：key,可选的post参数：id,pid
     *---------------------------------------------------------------------*/
    public function search_juhe(){
        $keyword=post('key');
        $id=(int)post('id','int',0);
        $pid=(int)post('pid','int',0);
        if(!$keyword){
            json(['status'=>1,'msg'=>'关键词不能为空']);
            return;
        }
        $model=app('\app\portal\model\PortalPost');
        $model->select('id,title');
        //添加父id时，不能是自己，不能是自己的子孙
        if($id >0){
            $model->ne('id',$id)->ne('pid',$id);
        }
        //排除掉自己上次的父id
        if($pid>0){
            $model->ne('id',$pid);
        }
        $data=$model->eq('type',$this->type)->like('title','%'.$keyword.'%')->limit(20)->findAll(true);
        //var_dump($model->getSql());
        if(!$data){
            json(['status'=>3,'msg'=>'搜索结果为空，请更换其他关键词']);
            return;
        }
        json(['status'=>0,'msg'=>$keyword,'data'=>$data]);
    }

    /** ------------------------------------------------------------------
     * 设置父id为0 ,必须的get参数：id
     *--------------------------------------------------------------------*/
    public function del_pid(){
        $id=(int)get('id','int',0);
        if($id<0){
            json(['status'=>1,'msg'=>'id格式不符']);
            return;
        }
        $model=app('\app\portal\model\PortalPost');
        if($model->eq('id',$id)->update(['pid'=>0])){
            json(['status'=>0,'msg'=>'成功删除']);
        }else{
            json(['status'=>2,'msg'=>'删除失败']);
        }
    }

    /** ------------------------------------------------------------------
     * 更新标签
     *---------------------------------------------------------------------*/
    public function tags_edit(){
        $id=post('id','int',0);
        if($id<1)
            return json(['code'=>1,'msg'=>'id不对']);
        $tags=post('tags');
        if(! is_array($tags)){
            return json(['code'=>1,'msg'=>'数据格式不正确']);
        }
        $model=app('\app\portal\model\PortalPost');
        $model->addTagMap($tags,$id,$this->type,true);
        return json(['code'=>0,'msg'=>'成功更新tag']);
    }
/**分类-------------------------------------------------------------------------------------------------------------------*/
    /** ------------------------------------------------------------------
     * 分类删除
     *---------------------------------------------------------------------*/
    public function category_del(){
        $id=post('id')?:get('id');
        if(!$id) {
            json(['code'=>1,'msg'=>'id不能为空']);
            return ;
        }
        $model=app('\app\portal\model\PortalCategory');
        $model->setType('portal_'.$this->type);
        $ret=$model->del($id,'portal_post');
        if($ret===true){
            json(['code'=>0,'msg'=>'成功删除']);
        }else{
            json(['code'=>2,'msg'=>$ret]);
        }
    }

    /** ------------------------------------------------------------------
     * 分类添加
     *--------------------------------------------------------------------*/
    public function category_add(){
        if($_POST){
            $validate=app('\app\admin\validate\Category');
            if($validate->check($_POST)){
                $model=app('\app\portal\model\PortalCategory');
                $model->setType('portal_'.$this->type);
                if($id=$model->add($_POST)){
                    //var_dump($this->type);
                    json(['code'=>0,'msg'=>'成功添加，分类名: '.$_POST['name'].'，分类id:'.$id,'action'=>url('admin/portal/category_add').'?type='.$this->type.'&sss='.time()]);
                }else{
                    json(['code'=>3,'msg'=>'入库失败']);
                }
            }else{
                json(['code'=>2,'msg'=>$validate->getError()]);
            }
        }else{
            json(['code'=>1,'msg'=>'提交的数据为空']);
        }
    }

    /** ------------------------------------------------------------------
     * 分类编辑
     *---------------------------------------------------------------------*/
    public function category_edit(){
        if($_POST){
            $_POST['id']=(int)post('id','int',0);
            if($_POST['id']<1){
                json(['code'=>1,'msg'=>'id格式不符']);
                return;
            }
            $validate=app('\app\admin\validate\Category');
            if($validate->check($_POST)){
                $model=app('\app\portal\model\PortalCategory');
                $model->setType('portal_'.$this->type);
                if($model->edit($_POST)){
                    json(['code'=>0,'msg'=>'成功更新','action'=>url('admin/portal/category').'?type='.$this->type]);
                }else{
                    json(['code'=>2,'msg'=>'入库失败']);
                }
            }else{
                json(['code'=>3,'msg'=>$validate->getError()]);
            }
        }else{
            json(['code'=>4,'msg'=>'提交的数据为空']);
        }
    }

    /**
     * 更新分类缓存
     */
    public function category_cache(){
        $cateModel=app('\app\portal\model\PortalCategory');
        $cateModel->setType('portal_'.$this->type)->updateCache();
        json(['code'=>0,'msg'=>'已成功更新分类缓存']);
    }



}