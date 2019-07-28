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


namespace app\portal\ctrl;
use core\Ctrl;
use extend\Paginator;

class MemberCtrl extends Ctrl
{
    //个人主页
    public function center($uid){
        $model=app('\core\Model');
        $data=[];
        //个人资料
        $data['user']=$model->select('*')->from('user')->eq('id',$uid)->find(null,true);
        if(!$data['user'])
            show_error('不存的用户id');
        //最新文章
        $data['data']=$model->select('id,title,thumb,type,views,likes,comments_num,create_time')->from('portal_post')->eq('uid',$uid)->eq('status',1)->order('create_time desc,id desc')->limit(30)->findAll(true);
        $data['title']=$data['user']['username'].'的个人主页';
        //最新portal_post的评论
        $data['comments']=$model->select('c.id,c.content,p.id as oid,table_name,title,c.create_time,p.type,c.pid')->from('comment as c')->join('portal_post as p','c.oid=p.id')->eq('c.uid',$uid)->eq('table_name','portal_post')->order('c.create_time desc,c.id desc')->limit(30)->findAll(true);
        $this->_display('member/center',$data,false);
    }
    //所有发表过的文章
    public function article($uid){
        $model=app('\core\Model');
        $data=[];
        //个人资料
        $data['user']=$model->select('*')->from('user')->eq('id',$uid)->find(null,true);
        if(!$data['user'])
            show_error('不存的用户id');
        $where=['uid'=>$uid,'status'=>1];
        $data['total']=$model->count([
            'from'=>'portal_post',
            'where'=>$where
        ]);
        $currentPage=get('page','int',1);
        $perPage=30;
        //最新文章
        if($data['total']>0){
            $url = url('@member_article@',['uid'=>$uid]).'?page=(:num)';
            $data['page']=new Paginator($data['total'],$perPage,$currentPage,$url);
            $data['data']=$model->select('id,title,thumb,type,views,likes,comments_num,create_time')->from('portal_post')->_where($where)->order('create_time desc,id desc')->limit(($currentPage-1)*$perPage,$perPage)->findAll(true);
        }else{
            $data['data']=[];
            $data['page']='';
        }
        $data['title']=$data['user']['username'].'发表的文章和主题';
        $this->_display('member/center_article',$data,false);
    }
    //所有发表过的评论
    public function comment($uid){
        $model=app('\core\Model');
        $data=[];
        //个人资料
        $data['user']=$model->select('*')->from('user')->eq('id',$uid)->find(null,true);
        if(!$data['user'])
            show_error('不存的用户id');
        $data['total']=$model->count([
            'from'=>'comment',
            'where'=>['uid'=>$uid,'table_name'=>'portal_post']
        ]);
        $currentPage=get('page','int',1);
        $perPage=30;
        if($data['total']>0){
            $url = url('@member_comment@',['uid'=>$uid]).'?page=(:num)';
            $data['page']=new Paginator($data['total'],$perPage,$currentPage,$url);
            //$data['data']=$model->select('id,title,thumb,type,views,likes,comments_num,create_time')->from('portal_post')->_where($where)->order('create_time desc,id desc')->limit(($currentPage-1)*$perPage,$perPage)->findAll(true);
            $data['comments']=$model->select('c.id,c.content,p.id as oid,table_name,title,c.create_time,p.type,c.pid')->from('comment as c')->join('portal_post as p','c.oid=p.id')->eq('c.uid',$uid)->eq('table_name','portal_post')->order('c.create_time desc,c.id desc')->limit(($currentPage-1)*$perPage,$perPage)->findAll(true);
        }else{
            $data['comments']=[];
            $data['page']='';
        }
        $data['title']=$data['user']['username'].'发表的评论|回答|点评';
        $this->_display('member/center_comment',$data,false);
    }

    public function all(){
        $model=app('\core\Model');
        $currentPage=get('page','int',1);
        $perPage=42;
        $data['total']=$model->count([
            'from'=>'user',
            'where'=>['status'=>1]
        ]);
        if($data['total']>0){
            $url = url('@member_all@').'?page=(:num)';
            $data['page']=(string)new Paginator($data['total'],$perPage,$currentPage,$url);
            $data['data']=$model->select('id,username,score,gid,level,coin,nickname,avatar,signature,balance')->from('user')->eq('status',1)->order('last_login_ip desc,id desc')->limit(($currentPage-1)*$perPage,$perPage)->findAll(true);
        }else{
            $data['comments']=[];
            $data['page']='';
        }
        $data['title']='会员中心';
        $this->_display('member/center_all',$data,false);
    }

}