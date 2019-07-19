<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 论坛帖子
 * ======================================*/

namespace app\bbs\model;
use app\admin\model\Post;
use app\common\model\Func;
class Bbs extends Post
{
    public $table='bbs';
    public $primaryKey='id';
    public function filter($data,$isEdit=false){
        return $data;
    }

    public function searchTag($where=[],$limit='0,10',$order='id desc',$single=false){
        $sql='SELECT '.$this->table.'.*,user.username as username ,user.avatar as avatar,category.name as category_name,category.slug as category_slug ,group_concat(tag.name) as tag_name  FROM `'.self::$prefix.$this->table.'` as '.$this->table.'  left join '.self::$prefix.'user as user on '.$this->table.'.uid=user.id left join '.self::$prefix.'category as category on '.$this->table.'.category_id=category.id left join '.self::$prefix.'tag_relation as tag_r on tag_r.oid='.$this->table.'.id left join '.self::$prefix.'tag as tag on tag.id=rag_r.tid';
        $where=Func::whereAddTable($where,$this->table);
        $order=Func::orderAddTable($order,$this->table);
        $this->_where($where)->_limit($limit)->group('tag_r.oid')->order($order);
        $sql.=$this->_buildSql(['where', 'groupList','order','limit']);
        //dump($sql);
        $param=$this->params;
        $this->reset(false);
        return $this->_sql($sql,$param,false,$single);
    }

    public function delOne($id){
        //删除自己
        $res= $this->eq('id',$id)->delete();
        if($res){
            //删除帖子
            $this->from('comment')->eq('table_name','bbs')->eq('oid',$id)->delete();
            //删除标签
            $this->from('tag_relation')->eq('type','bbs_normal')->eq('oid',$id)->delete();
        }
        return $res;
    }

    public function delSome($ids){
        $count=0;
        foreach ($ids as $id){
            if($this->delOne($id))
                $count++;
        }
        return $count;
    }
}