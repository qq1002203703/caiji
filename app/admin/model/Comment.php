<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 *  评论Model
 * ======================================*/

namespace app\admin\model;
use core\Model;
class Comment extends Model
{
    public $table='comment';
    public $primaryKey='id';

    /** ------------------------------------------------------------------
     * 添加评论
     * @param array $data 已经验证了的数据
     *--------------------------------------------------------------------*/
    public function add($data){
        $data=$this->_filterData($data);
        $id=$this->insert($data);
        if($id>0){
            //原内页的评论数+1
            $this->setField('comments_num',1,['id'=>$data['oid']],$data['table_name']);
            //上级的子评论+1
            if(isset($data['pid']) && $data['pid'] >0)
                $this->setField('children',1,['id'=>$data['pid']]);
        }
        return $id;
    }
    //必须 oid,username,table_name,content,create_time
    public function add_multi($data){
        if(!isset($data['uid']) || !$data['uid']){
            $data['uid']=$this->getUserId($data['username']);
        }
        return $this->add($data);
    }
    //查询用户的id
    public function getUserId($username){
        return (app('\app\portal\model\User')->addFromName($username));
    }
    //查询oid是否存在
    public function checkOid($oid,$table_name){
        if($this->select('id')->from($table_name)->eq('id',$oid)->find(null,true))
            return true;
        return false;
    }
    //查询oid下的评论
    public function list(){
        $this->select('id,username,avatar');
    }

    public function getSome($where,$limit,$order='',$select='',$single=false){
        if(!$select)
            $select='c.id,c.username,u.avatar,content,c.create_time,children,c.pid,oid,table_name,uid,likes,dislikes,recommended';
        if(!$order)
            $order='create_time desc,id desc';

        $this->select($select)->from('comment as c')->join('user as u','u.id=c.uid')->_where($where)->order($order)->_limit($limit);
        /*$aaa=$this->findAll(true);
        //echo $this->getSql();
        return $aaa;*/
        if($single)
            return $this->find(null,true);
        else
            return $this->findAll(true);
    }
}