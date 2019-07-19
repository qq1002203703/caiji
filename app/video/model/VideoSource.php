<?php
namespace app\video\model;
use core\Model;

class VideoSource extends Model
{
    public $table='video_source';
    public $primaryKey = 'id';

    /**-------------------------------------------------------
     * 添加
     * @param array $data: 已经验证的数据
     * @return bool|int:成功返回插入的id，否则返回false
     ---------------------------------------------------------*/
    public function add($data){
        $data=$this->filter($data);
        $id=$this->insert($data);
        return $id;
    }
    /**--------------------------------------------------------------
     * 编辑
     * @param array $data
     * @return bool|int
     ----------------------------------------------------------------*/
    public function edit($data){
        $data=$this->filter($data,true);
        $primaryKey=$data[$this->primaryKey];
        unset($data[$this->primaryKey]);
        return $this->eq($this->primaryKey,$primaryKey)->update($data);
    }

    /**-----------------------------------------------------------
     * 数据入库前过滤和处理
     * @param array $data
     * @param bool $isEdit 是否是编辑， true时表示是编辑 要进行的是更新；false时表示不是编辑，要进行的是插入
     * @return array
     --------------------------------------------------------------*/
    public function filter($data,$isEdit=false){
        $data['url']=trim($data['url']);
        if($isEdit)
            $data=$this->_filterDataE($data);
        else{
            $data=$this->_filterData($data);
        }
        $this->setDefaultValue($data);
        return $data;
    }

    /** ------------------------------------------------------------------
     * 默认值设置
     * @param array $data
     * @param bool $isEdit
     *--------------------------------------------------------------------*/
    public function setDefaultValue(&$data){
        if(isset($data['status']) && $data['status']==='')
            $data['status']=1;
        if(isset($data['isend']) && $data['isend']==='')
            $data['isend']=1;
        if(isset($data['iscaiji']) && $data['iscaiji']==='')
            $data['iscaiji']=1;
        $data['update_time']=isset($data['create_time']) && $data['create_time'] ? strtotime($data['create_time']): time();
    }

    /** ------------------------------------------------------------------
     * 删除文档 支持单篇和批量
     * @param string|array|int $id
     * @return bool|string 成功返回true 失败返回错误信息
     *--------------------------------------------------------------------*/
    public function del($id){
        if(!$id)
            return 'id不能为空';
        if(is_string($id)|| is_int($id)){
            $id=(string)$id;
            if(preg_match('/^(\d[\d,]*)*\d$/',$id)==0)
                return 'id格式不符';
            else
                $id=explode(',',$id);
        }
        $ret=$this->in('id',$id)->delete();
        if($ret){
            return true;
        }else{
            return '删除失败,可能是提交了多次删除，已经在上一次删除！';
        }
    }

}