<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 城市数据操作
 * ======================================*/

namespace app\admin\model;
use core\Model;

/**
 * Class City
 * @package app\admin\model
 */
class City extends Model
{
    /**
     * @var string 表名
     */
    public $table='city';
    /**
     * @var string 主键名
     */
    public $primaryKey = 'id';

    /**
     * 通过id获取对应区域数据
     * @param int $id
     * @return array
     */
    public function getById($id,$select='*'){
        return $this->select($select)->eq('id',$id)->find(null,true);
    }
    /**
     * 获取指定id下的指地区
     * @param int $id
     * @return array
     */
    public function getChildren($id,$select='*'){
        return $this->select($select)->eq('pid',$id)->limit(100)->findAll(true);
    }

    /** ------------------------------------------------------------------
     * 通过分类名获取id
     * @param string $name 分类名
     * @return int: 获取不到返回0,否则返回对应分类名的id
     *--------------------------------------------------------------------*/
    public function getIdByName($name){
        $data=$this->eq('name',$name)->find(null,true);
        return $data ? $data['id'] :0;
    }

}