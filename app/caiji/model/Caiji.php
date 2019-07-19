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

namespace app\caiji\model;

use core\Conf;
use core\Model;

class Caiji extends Model
{
    public $table='caiji';
    public $primaryKey='id';

    public function checkName($name,$id=0){
        //编辑时排除自身的name
        if($id) $this->ne('id',$id);
        return ($this->eq('name',$name) ->find()===false) ? true : false;
    }

    /** ------------------------------------------------------------------
 * 获取采集系统下所有项目
 * @return array 没有项目时返回空数组，否则返回二维数组结果集
 *-------------------------------------------------------------------*/
    public function getCaijiProject($select='id,name'){
        $data= $this->select($select)->from('caiji_page')->order('id desc')->group('name')->findAll(true);
        if(!$data)
            return [];
        foreach ($data as $key=>$item){
            $options=Conf::get('options',$item['name'],false,'config/caiji/');
            $data[$key]['table']=$options['table'];
        }
        return $data;
    }

    /** ------------------------------------------------------------------
     * 审核一个数据时需要更改的字段值
     * @param string|array $id
     * @param string $table
     * @param int $laji : 0或1
     * @return int
     *---------------------------------------------------------------------*/
    public function shenhe($id,$table,$laji=1){
        if(is_string($id))
            $id=explode(',',$id);
        $data=['isshenhe'=>1,'isend'=>1,'islaji'=>$laji];
        if($laji==1){
            $data['content']='';
        }
        $ret=$this->from($table)->in('id',$id)->update($data);
        return $ret;
    }

    /** ------------------------------------------------------------------
     * 添加项目
     * @param array $data
     *--------------------------------------------------------------------*/
    public function page_add($data){
        $tmp=$this->table;
        $this->table='caiji_page';
        $data=$this->_filterData($data);
        $data['update_time']=time();
        $data['url']=preg_replace('/\r?\n/','{%|||%}',$data['url']);
        $data['url_md5']=md5($data['url']);
        $ret=$this->insert($data);
        $this->table=$tmp;
        return $ret;
    }

    public function page_edit($data){
        $tmp=$this->table;
        $this->table='caiji_page';
        $data=$this->_filterDataE($data);
        $data['url']=preg_replace('/\r?\n/','{%|||%}',trim($data['url']));
        $data['url_md5']=md5($data['url']);
        $ret=$this->update($data);
        $this->table=$tmp;
        return $ret;
    }
}