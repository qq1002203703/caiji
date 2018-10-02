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


namespace app\admin\model;
use core\Model;
use app\common\model\RecycleCommon;
class Crontab extends Model
{
    use RecycleCommon;
    /**
     * @var string 表名
     */
    public $table='crontab';
    /**
     * @var string 主键名
     */
    public $primaryKey = 'id';

    public function add($data){
        $data=$this->_filterData($data);
        $data['status']=0;
        $data['run_time']=strtotime($data['run_time']);
        $data['name_md5']=md5($data['callable'].preg_replace('/\s\-[a-z](?:\s\d+)?/','',$data['class_param']).$data['method_param']);
        return $this->insert($data);
    }

    public function getById($id){
        return $this->eq('id',$id)->find(null,true);
    }

    public function edit($data){
        if(!isset($data['id']))
            return false;
        $data=$this->_filterData($data);
        $data['run_time']=strtotime($data['run_time']);
        //$data['name_md5']=md5($data['callable'].$data['class_param'].$data['method_param']);
        $data['name_md5']=md5($data['callable'].preg_replace('/\s\-[a-z](?:\s\d+)?/','',$data['class_param']).$data['method_param']);
        return $this->eq('id',$data['id'])->update($data);
    }
}