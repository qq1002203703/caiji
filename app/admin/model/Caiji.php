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

namespace app\admin\model;

use core\Conf;
use core\Model;

class Caiji extends Model
{
    public $table='caiji';
    public $primaryKey='id';

    /** ------------------------------------------------------------------
     * 获取采集系统下所有项目
     * @return array 没有项目时返回空数组，否则返回二维数组结果集
     *-------------------------------------------------------------------*/
    public function getCaijiProject(){
        $data= $this->select('id,name')->from('caiji_page')->group('name')->findAll(true);
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
        $table_tmp=$this->table;
        $this->table=$table;
        if(is_string($id))
            $id=explode(',',$id);
        $data=['isshenhe'=>1,'isend'=>1,'islaji'=>$laji];
        if($laji==1){
            $data['content']='';
        }
        $ret=$this->reset()->in('id',$id)->update($data);
        $this->reset()->table=$table_tmp;
        return $ret;
    }
}