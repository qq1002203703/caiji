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


namespace app\api\ctrl;

use app\common\ctrl\ApiCtrl;

class CommentCtrl extends ApiCtrl
{
    protected $error='';
    protected function checkPermissions($type){
        if($this->_checkIsAdmin())
            return true;
        $type=(string)$type;
        switch ($type){
            case 'reply': //回贴
                return $this->checkReply();
            case 'admin':
                $this->error='权限不足';
                return false;
            case 'add':
                return $this->_is_login();
        }
       return false;
    }
    protected function checkReply(){
        if(!$this->_is_login()){
            $this->error='请先登陆';
            return false;
        }
        return true;
    }

    /** ------------------------------------------------------------------
     * 评论：必须post参数：id,table_name,__token__,content; 可选 pid
     *--------------------------------------------------------------------*/
    public function reply(){
        if(!$this->checkPermissions('reply')){
            return json(['code'=>1,'msg'=>$this->error]);
        }
        $data=[];
        $data['token']=post('__token__','','');
        if(!$data['token'])
            return json(['code'=>2,'msg'=>'令牌不能为空']);
        $data['oid']=(int)post('id','int',0);
        $data['table_name']=post('table_name','','');
        $data['pid']=(int)post('pid','int',0);
        $data['content']=post('content','','');
        if(!$this->checkContent($data['content'])){
            return json(['code'=>4,'msg'=>$this->error]);
        }
        $validate=app('\app\admin\validate\Comment');
        if($validate->check($data)){
            //验证内容是否是垃圾评论
            $model=app('\app\admin\model\Comment');
            if($this->_is_login()){
                $data['uid']=$_SESSION['uid'];
                $data['username']=$_SESSION['username'];
            }else{
                $data['uid']=0;
                $data['username']='游客';
            }
            $data['create_time']=time();
            $id=$model->add($data);
            if($id>0)
                return json(['code'=>0,'msg'=>'成功评论']);
            else
                return json(['code'=>99,'msg'=>'入库失败']);
        }else{
            return json(['code'=>3,'msg'=>$validate->getError()]);
        }
    }
    //获取token
    public function token(){
        json(['code'=>0,'msg'=>\app\common\ctrl\Func::token()]);
    }

    /** ------------------------------------------------------------------
     * 检测内容是否是垃圾回复
     * @param string $str
     * @return bool
     *--------------------------------------------------------------------*/
    protected function checkContent($str){
        if(mb_strlen($str) <6){
            $this->error='评论内容不能太短，必须大于5个字';
            return false;
        }
       /* if(preg_match('/[\x{4e00}-\x{9fa5}]+/u',$str) < 1){
            $this->error='评论必须带汉字，防止垃圾评论';
            return false;
        }*/
        return true;
    }

    /** ------------------------------------------------------------------
     * 批量回复，必须的post数据：multi_reply, table_name, oid,而且multi_reply的格式如下（每条用{|||}分隔，每条里面数据又用{||}分隔）
     *          时间1{||}用户名1{||}评论1{|||}时间2{||}用户名2{||}评论2{|||}时间n{||}用户名n{||}评论n
     *---------------------------------------------------------------------*/
    public function reply_m(){
        if(!$this->checkPermissions('admin')){//权限检测
            return json(['code'=>1,'msg'=>$this->error]);
        }
        $contents=post('multi_reply');//multi_reply
        if(!$contents)
            return json(['code'=>2,'msg'=>'数据不能为空']);
        $contents=htmlspecialchars_decode($contents);
        $table_name=post('table_name');//table_name
        if(!$table_name)
            return json(['code'=>3,'msg'=>'表名不能为空']);
        $oid=(int)post('oid','int',0); //oid
        if(!$oid)
            return json(['code'=>4,'msg'=>'内页oid不能为空']);
        $model=app('\app\admin\model\Comment');
        if(!$model->checkOid($oid,$table_name))
            return json(['code'=>5,'msg'=>'不存在的表名或oid']);
        $contents=explode('{|||}',$contents);
        $count=0;
        foreach ($contents as $item){
            if(!$item)
                continue;
            list($time,$user,$content)=explode('{||}',$item);
            if($model->add_multi([
                'content'=>$content,
                'create_time'=>(strtotime($time)?:time()),
                'username'=>$user,
                'table_name'=>$table_name,
                'oid'=>$oid
            ]))
                $count++;
        }
        if($count>0)
            return json(['code'=>0,'msg'=>'成功添加 '.$count.' 评论']);
        else
            return  json(['code'=>6,'msg'=>'添加 0 评论']);
    }
    //赞与踩 参数get: id,type,
    public function clickLikes(){
        if(!$this->_is_login())
            return json(['code'=>1,'msg'=>'请先登陆']);
        $id=(int)get('id','int',0);
        if($id<1)
            return json(['code'=>2,'msg'=>'id格式不符']);
        $type=(int)get('type','int',0);
        if($type!==0 && $type!==1)
            return json(['code'=>3,'msg'=>'type格式不符']);
        $model=app('\app\admin\model\Comment');
        $typeMap=['踩','赞'];
        if($model->from('comment_like')->eq('cid',$id)->eq('uid',$_SESSION['uid'])->eq('type',$type)->find(null,true)){
            return json(['code'=>4,'msg'=>'你已经'.$typeMap[$type].'过了']);
        }
        //添加到 comment_like里
        $model->from('comment_like')->insert(['cid'=>$id,'uid'=>$_SESSION['uid'],'type'=>$type]);
        //评论里
       $filed=$type==1 ? 'likes' : 'dislikes';
       $model->setField($filed,1,['id'=>$id]);
        return json(['code'=>0,'msg'=>'成功'.$typeMap[$type].'了一次']);
    }
    //参数get id
    public function del(){
        if(!$this->checkPermissions('admin')){//权限检测
            return json(['code'=>1,'msg'=>$this->error]);
        }
        $id=(int)get('id','int',0);
        if(!$id)
            return json(['code'=>2,'msg'=>'id不能为空']);
        $model=app('\app\admin\model\Comment');
        $data=$model->select('oid,table_name,likes,dislikes','pid','children')->eq('id',$id)->find(null,true);
        if(!$data)
            return json(['code'=>3,'msg'=>'不存在的评论id']);
        if($model->eq('id',$id)->delete()){
            //删除子评论
            if($data['children'] >0 )
                $model->eq('pid',$id)->delete();
            $num=$data['children']+1;
            //评论数减少
            $model->setField('comments_num',-$num,['id'=>$data['oid']],$data['table_name']);
            //删除like
            if($data['likes'] >0 || $data['dislikes'] >0 )
                $model->from('comment_like')->eq('cid',$id)->delete();
            //上级评论减少
            if($data['pid']>0){
                $model->setField('children',-1,['id'=>$data['pid']]);
            }
            return json(['code'=>0,'msg'=>'成功删除']);
        }
        return json(['code'=>4,'msg'=>'删除失败']);
    }

    /** ------------------------------------------------------------------
     * 批量回复时的内容格式化:get参数  必须：p, 可选:id和table
     *--------------------------------------------------------------------*/
    public function reply_format(){
        $perPage=(int)get('p','int',5);
        if($perPage <1){
            return json(['code'=>1,'msg'=>'每页个数不能小于1']);
        }
        $id=get('id','int',0);
        $table_name=get('table','','');
        $model=app('\app\portal\model\User');
        //获取最后发贴时间
        if($id && $table_name)
            $time=$model->_sqlField('create_time','select create_time from '.\core\AR::$prefix.'comment where table_name=? and oid= ? order by create_time desc limit 1',[$table_name,$id],false);
        else
            $time=time();
        $noneTime=false;
        if(!$time){
            $time=time();
            $noneTime=true;
        }
        $data=$model->getRandomUser($perPage,'username');
        $arr=[];
        $time+=($noneTime ? mt_rand(200,700) : mt_rand(250,1300));
        if($data){
            array_walk($data,function ($v,$i) use (&$arr,&$time,$noneTime){
                $arr[]=date('Y-m-d H:i',$time).'{||}'.$v['username'].'{||}评论';
                $time+=($noneTime ? mt_rand(200,700) : mt_rand(250,1300));
            });
            unset($data);
        }else{
            for ($i=0;$i<$perPage;$i++){
                $arr[]=date('Y-m-d H:i',$time).'{||}用户名{||}评论';
                $time+=($noneTime ? mt_rand(200,700) : mt_rand(250,1300));
            }
        }
        return json(['code'=>0,'msg'=>'成功','data'=>implode('{|||}'."\n",$arr)]);
    }
    //流式评论列表 get参数：table,id,page
    public function flow(){
        $table_name=get('table','','');
        $id=(int)get('id','int',0);
        if(!$this->checkData($id,$table_name))
            return;
        $currentPage=(int)get('page','int',0);
        $perPage=\core\Conf::get('bbs','site')['comment_perpage'];
        $model=app('\app\admin\model\Comment');
        $total=$model->count(['where'=>['status'=>1,'pid'=>0,'table_name'=>$table_name,'oid'=>$id]]);
        $pages=(int)ceil($total/$perPage)-1;
        $data=[];
        if($total>0 && $pages+1 >= $currentPage){
            $data=$model->getSome(['c.status'=>1,'pid'=>0,'table_name'=>$table_name,'oid'=>$id],[$currentPage*$perPage,$perPage]);
        }
        if($data){
            $html='';
            foreach ($data as $item){
                $html.='<li class="comment-list-item" id="comment-'.$item['id'].'">';
                $html.='<div class="comment-list-img"><a href="javascript:;"><img src="'.($item['avatar']?:'/uploads/user/default.png').'"></a></div>';
                $html.='<div class="comment-list-body"><div class="comment-list-info"><a class="comment-list-user" href="javascript:;">'.$item['username'].'</a><span class="comment-list-date">'.date('Y-m-d H:i',$item['create_time']).'</span></div></div>';
                $html.='<div class="comment-list-text">'.$item['content'].'</div>';
                if($item['children'] >0){
                    $children=$model->getSome(['pid'=>$item['id']],10);
                    if($children){
                        $html.='<div class="comment-list-children"><ul>';
                        foreach ($children as $child){
                            $html.='<li><div class="children-img"><a href="javascript:;"><img src="'.($child['avatar']?:'/uploads/user/default.png').'"></a></div>';
                            $html.='<div class="children-body"><a href="javascript:;" class="children-user">'.$child['username'].'</a><span class="children-text">'.$child['content'].'</span><span class="children-date">'.date('Y-m-d H:i',$child['create_time']).'</span></div>';
                            $html.='</li>';
                        }
                        $html.='</ul></div>';
                    }
                }
                if($this->_checkIsAdmin()){
                    $html.=' <div class="comment-list-admin">
                        <a href="javascript:;" title="回复" class="comment-list-rp" data-id="'.$item['id'].'">回复</a>
                        <a href="javascript:;" title="编辑" class="comment-list-edit" data-id="'.$item['id'].'">编辑</a>
                        <a href="javascript:;" title="删除" class="comment-list-del" data-id="'.$item['id'].'">删除</a>
                    </div>';
                }
                $html.='<div class="comment-list-tools">
                <a href="javascript:;" title="赞" onclick="clickLikes(1,'.$item['id'].',this)"><i class="layui-icon layui-icon-praise"></i><span>赞同 <cite>'.$item['likes'].'</cite></span></a>
                <a href="javascript:;" title="评论"><i class="layui-icon layui-icon-reply-fill"></i><span>评论 '.$item['children'].'</span></a>
                <a href="javascript:;" title="回复" class="comment-list-reply" data-id="'.$item['id'].'" data-do="0" data-pid="'.$item['pid'].'">回复</a>
            </div>';
                $html.='</li>';
            }
            json(['code'=>0,'msg'=>'成功','data'=>$html,'pages'=>$pages]);
        }else
            json(['code'=>2,'msg'=>'没有数据了']);
    }

    protected function checkData($id,$table,$name='table'){
        if(!$id){
            json(['code'=>1,'msg'=>'id不能为空']);
            return false;
        }
        if(!$table){
            json(['code'=>1,'msg'=>$name.'不能为空']);
            return false;
        }
        return true;
    }

    //常用操作统一入口，必须的get参数是c,额外的参数请参看对应的方法
    public function ctrl(){
        $type=get('c','','');
        switch ($type){
            /*case 'reply': //用户回复帖子：必须post参数：id,table_name,__token__,content 可选 pid
                $this->reply();
                break;*/
            case 'del'://额外参数get : id
                $this->del();
                break;
            case 'recommended'://管理员对评论推荐，额外参数get : id,v
                $this->recommend();
                break;
            case 'edit'://管理员对评论编辑，额外参数post : 必须id,content  可选username
                $this->edit();
                break;
            case 'like'://用户对评论点赞，额外参数get：必须id,type
                $this->clickLikes();
                break;
            case 'format'://管理员批量回复时，获取回复格式，额外参数get：  必须：p, 可选:id和table
                $this->reply_format();
                break;
            case 'flow'://内页流式加载评论列表， 额外参数get：table,id,page
                $this->flow();
                break;
            case 'token': //获取token 没有参数
                $this->token();
                break;
            case 'reply_m':
                /**管理员对内页进行批量回复，
                 * 必须的post数据：multi_reply, table_name, oid,而且multi_reply的格式如下（每条用{|||}分隔，每条里面数据又用{||}分隔）
                 *    时间1{||}用户名1{||}评论1{|||}时间2{||}用户名2{||}评论2{|||}时间n{||}用户名n{||}评论n
                 */
                $this->reply_m();
                break;
            case 'renew_date':
                $this->renew_date();
                break;
            default:
                json(['code'=>98,'msg'=>'不存在的操作']);
        }
    }
    //推荐和取消推荐 get参数 id,v
    public function recommend(){
        if(!$this->checkPermissions('admin')){
            json(['code'=>1,'msg'=>'权限不足']);
            return ;
        }
        $res=$this->setAttr('recommended','推荐',function (&$value,$name){
            $value=(int)$value;
            if($value!==0 && $value!==1)
                return '推荐的值只能是0和1';
            return 0;
        });
        if($res >0){
            json(['code'=>0,'msg'=>'成功修改']);
        } elseif ($res !==-1 ){
            json(['code'=>99,'msg'=>'修改失败，可能的原因有对应的id不存在，或当前评论的推荐已经是这样了']);
        }
    }

    /** ------------------------------------------------------------------
     * 设置属性
     * @param string $niceName 属性名称的说明文字
     * @param callable $checkFunc 检测属性时否合法的函数
     *--------------------------------------------------------------------*/
    protected function setAttr($attrName,$niceName,$checkFunc,$method='get',$operator=''){
        $id=(int)get('id','int',0);
        if(!$this->checkData($id,$attrName,$niceName))
            return -1;
        $attrValue=$method('v','','');
        $checkResult=call_user_func_array($checkFunc,[&$attrValue,$attrName]);
        if($checkResult===false ){
            json(['code'=>2,'msg'=>'检测函数出错']);
            return -1;
        }elseif ($checkResult !==0){
            json(['code'=>3,'msg'=>$checkResult]);
            return -1;
        }
        $model=app('\app\admin\model\Comment');
        return $model->setField($attrName,$attrValue,['id'=>$id],'',$operator);
    }

    /** ------------------------------------------------------------------
     * 管理员修改评论 参数 post：必须项id,content, 可选项 username
     *--------------------------------------------------------------------*/
    public function edit(){
        if(!$this->checkPermissions('admin')){
            return json(['code'=>1,'msg'=>'权限不足']);
        }
        $id=(int)post('id','int',0);
        if(!$id)
            return json(['code'=>2,'msg'=>'id不能为空']);
        $data['content']=post('content','',false);
        if($data['content']===false)
            return json(['code'=>3,'msg'=>'不存在内容项']);
        else
            $data['content']=htmlspecialchars_decode($data['content']);
        $username=post('username','',false);
        $model=app('\app\admin\model\Comment');
        if($username){
            $oldData=$model->select('uid,username')->eq('id',$id)->find(null,true);
            if(!$oldData)
                return json(['code'=>4,'msg'=>'不存在的id']);
            if($oldData['username'] !==$username){
                $data['uid']=app('\app\portal\model\User')->addFromName($username);
                $data['username']=$username;
            }
        }
        //dump($data);
        if($model->eq('id',$id)->update($data))
            return json(['code'=>0,'msg'=>'成功修改']);
        else
            return json(['code'=>99,'msg'=>'id不存在，或者所有项都没变动']);
    }

    public function renew_date(){
        $id=(int)get('id','int',0);
        $table_name=get('table','','');
        if(!$this->checkData($id,$table_name))
            return;
        $model=app('\app\admin\model\Comment');
        $data=$model->select('create_time,comments_num')->from($table_name)->eq('id',$id)->find(null,true);
        $model->reset();
        if(!$data){
            json(['code'=>2,'msg'=>'不存在的id']);
            return;
        }
        //每贴间隔3~25分钟 确定最早的时间 按每次20分钟算
        $total=$data['comments_num']+1;
        $first=time()-1200*$total;
        //更新主贴的时间，
        $res=$model->from($table_name)->eq('id',$id)->update(['create_time'=>$first]);
        if(!$res){
            json(['code'=>3,'msg'=>'主贴时间无法更新']);
            return;
        }
        //更新评论
        $perPage=40;
        if($total >$perPage){
            $pages=(int)ceil($total/$perPage);
            $last=$first+mt_rand(3*60,25*60);
            for ($i=0;$i<$pages;$i++){
                $last=$this->renew_date_each($id,$table_name,$perPage,$first,$model,$last);
            }
        }else{
            $this->renew_date_each($id,$table_name,$perPage,$first,$model);
        }
        json(['code'=>0,'msg'=>'成功更新时间']);
    }

    /** ------------------------------------------------------------------
     * renew_date_each
     * @param $perPage
     * @param \app\admin\model\Comment $model
     *--------------------------------------------------------------------*/
    protected function renew_date_each($id,$table_name,$perPage,$firstTime,&$model,$lastTime=0){
        $data=$model->select('id,create_time')->eq('oid',$id)->eq('table_name',$table_name)->lt('create_time',$firstTime)->limit($perPage)->order('create_time,id')->findAll(true);
        if($lastTime===0)
            $lastTime=$firstTime+mt_rand(3*60,25*60); //3~25分钟
        if($data){
            foreach ($data as $datum) {
                if($model->eq('id',$datum['id'])->update(['create_time'=>$lastTime]))
                    $lastTime+=mt_rand(3*60,25*60);
            }
        }
        return $lastTime;
    }
//****Tag页****************************************************************************************************************************
    /** ------------------------------------------------------------------
     * tag页时，获取讨论帖子的评论
     *--------------------------------------------------------------------*/
    public function tag(){
        $table_name=get('table','','');
        $id=(int)get('id','int',0);
        if(!$this->checkData($id,$table_name))
            return;
        $model=app('\app\admin\model\Comment');
        $content=$model->select('content')->from($table_name)->eq('id',$id)->find(null,true);
        if(!$content){
            json(['code'=>2,'msg'=>'不存在的id','data'=>'','content'=>'']);
            return;
        }
        $pageSize=get('size','int',10);
        $data=$model->getSome(['c.status'=>1,'pid'=>0,'table_name'=>$table_name,'oid'=>$id],$pageSize);
        if($data){
            $html='';
            foreach ($data as $item){
                $html.='<li class="comment-list-item" id="comment-'.$item['id'].'">';
                $html.='<div class="comment-list-img"><a href="javascript:;"><img src="'.($item['avatar']?:'/uploads/user/default.png').'"></a></div>';
                $html.='<div class="comment-list-body"><div class="comment-list-info"><a class="comment-list-user" href="javascript:;"  id="user-'.$item['id'].'">'.$item['username'].'</a><span class="comment-list-date">'.date('Y-m-d H:i',$item['create_time']).'</span></div></div>';
                $html.='<div class="comment-list-text" id="content-text-'.$item['id'].'">'.$item['content'].'</div>';
                if($item['children'] >0){
                    $children=$model->getSome(['pid'=>$item['id']],10);
                    if($children){
                        $html.='<div class="comment-list-children"><ul>';
                        foreach ($children as $child){
                            $html.='<li><div class="children-img"><a href="javascript:;"><img src="'.($child['avatar']?:'/uploads/user/default.png').'"></a></div>';
                            $html.='<div class="children-body"><a href="javascript:;" class="children-user"  id="user-'.$child['id'].'">'.$child['username'].'</a><span class="children-text" id="content-text-'.$item['id'].'">'.$child['content'].'</span><span class="children-date">'.date('Y-m-d H:i',$child['create_time']).'</span></div>';
                            $html.='</li>';
                        }
                        $html.='</ul></div>';
                    }
                }

                $html.='</li>';
            }
            json(['code'=>0,'msg'=>'成功','data'=>$html,'content'=>$content['content']]);
        }else
            json(['code'=>0,'msg'=>'没有数据了','data'=>'','content'=>$content['content']]);
    }
    /** ------------------------------------------------------------------
     * tag页获取评论
     *---------------------------------------------------------------------*/
    public function flow_manage(){
        $table_name=get('table','','');
        $id=(int)get('id','int',0);
        if(!$this->checkData($id,$table_name))
            return;
        $currentPage=(int)get('page','int',1)-1;
        $perPage=10;
        $model=app('\app\admin\model\Comment');
        $total=$model->count(['where'=>['status'=>1,'table_name'=>$table_name,'oid'=>$id]]);
        $pages=(int)ceil($total/$perPage);
        $data=[];
        if($total>0 && $pages >= $currentPage){
            $data=$model->select('id,content,recommended')->_where(['status'=>1,'table_name'=>$table_name,'oid'=>$id])->order('create_time desc,id desc')->_limit([$currentPage*$perPage,$perPage])->findAll(true);
        }
        if($data){
            $html=[];
            foreach ($data as $item){
                $html[]=$item['id'].'{||}'.$item['recommended'].'{||}'.$item['content'];
            }
            json(['code'=>0,'msg'=>'成功','data'=>implode('{|||}'."\n",$html),'pages'=>$pages]);
        }else
            json(['code'=>2,'msg'=>'没有数据了']);
    }
}