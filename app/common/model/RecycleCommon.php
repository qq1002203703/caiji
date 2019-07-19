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
 *  回收站
 * ======================================*/


namespace app\common\model;

trait RecycleCommon
{
    /** ------------------------------------------------------------------
     * 对要删除的数据进行回收处理
     * @param string|array $data:要删除数据的主键的值
     * @param int $time：一个时间戳，什么时候彻底删除，如果不提供，默认3天后彻底删除
     * @param string $primaryKey：要删除数据的主键的名，默认为'id'
     * @param string $table:要删除数据所在的数据表名（不包括表前缀），为空时默为当前数据表
     * @return bool 回收成功返回true,失败返回false
     *---------------------------------------------------------------------*/
    public function recycle($data,$time=0,$primaryKey='id',$table=''){
        if(!$time){
            $time=TIME+3600*24*3;
        }
        $table_tmp='';
        if($table) {
            $table_tmp=$this->table;
            $this->table = $table;
        }else
            $table=$this->table;
        if(is_string($data))
            $data=explode(',',$data);
        $ret=$this->reset()->_where([[$primaryKey,'in',$data]])->update(['delete_time'=>$time]);
        $this->reset();
        if($ret>0){
            $this->table='recycle';
            foreach ($data as $v){
                $this->insert([
                    'table_name'=>$table,
                    'key_name'=>$primaryKey,
                    'key_value'=>$v,
                    'create_time'=>TIME,
                    'delete_time'=>$time
                ]);
            }
            $this->reset();
            if($table_tmp)
                $this->table=$table_tmp;
            return true;
        }else{
            return false;
        }
    }
    /** ------------------------------------------------------------------
     * 不做回收直接删除一条或多条数据
     * @param string|array $data:要删除数据的主键的值
     * @param string $primaryKey：要删除数据的主键的名，默认为'id'
     * @param string $table:要删除数据所在的数据表名（不包括表前缀），为空时默为当前数据表
     * @return int:返回删除的条数
     *---------------------------------------------------------------------*/
    public function del($data,$primaryKey='id',$table=''){
        if(!$table){
            $table=$this->table;
        }
        if(is_string($data))
            $data=explode(',',$data);
        return $this->from($table)->_where([[$primaryKey,'in',$data]])->delete();
    }
}