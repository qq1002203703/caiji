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
use core\Model;
class Tag extends Model
{
    public $table='category';
    /**
     * @var string 主键名
     */
    public $primaryKey = 'id';
    /**
     * @var string 种类
     */
    public $type='weixinqun';
    /**
     * @var string 验证错误提示消息
     */
    protected $validateMsg='';
    /**------------------------------------------------------------------
     * 检查一个名字是否合法：用于入库前的验证
     * @param  string $name
     * @param string $type
     * @param int $id
     * @return bool 数据库中已经存在返回false,不存在返回true
     *---------------------------------------------------------------------*/
    public function checkName($name,$type,$id=0){
        //编辑时排除自身的name
        $this->reset();
        if($id) $this->ne('id',$id);
        return ($this->eq('name',$name) ->eq('type',$type) ->find()===false)?true:false;
    }
    /**
     * 添加标签
     * @param $data:已经验证过的数据
     * @return bool|int:添加成功返回插入id,失败返回false
     */
    public function add($data){
        return $this->insert($data);
    }

    /** ------------------------------------------------------------------
     * 验证数据
     * @param $data
     * @param int $id
     * @return bool
     *---------------------------------------------------------------------*/
    public function checkData($data,$id=0){
        if(! isset($data['name']) || !$data['name']){
            $this->validateMsg='标签名不能为空';
            return false;
        }
        if(! isset($data['type']) || !$data['type']){
            $this->validateMsg='种类不能为空';
            return false;
        }
        if($this->checkName($data['name'],$data['type'],$id)){
            $this->validateMsg='标签名已经存在';
            return false;
        }
        return true;
    }

    /* ------------------------------------------------------------------
     * 获取验证错误时的消息
     * @return string
     *--------------------------------------------------------------------*/
    public function getValidateMsg(){
        return $this->validateMsg;
    }
}