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


use app\common\model\Func;
use core\Model;

class Kami extends Model
{
    public $table='kami';
    public $type=['会员升级','充值'];

    /**------------------------------------------------------------------
     * 检查卡密种类名是否合法：用于入库前的验证
     * @param  string $name
     * @param int $id
     * @return bool 数据库中已经存在返回false,不存在返回true
     *---------------------------------------------------------------------*/
    public function typeCheckName($name,$id=0){
        $this->from('kami_type')->select('id');
        //编辑时排除自身的name
        if($id) $this->ne('id',$id);
        $ret=$this->eq('name',$name) ->find(null,true);
        return ($ret === false);
    }

    /** ------------------------------------------------------------------
     * 添加卡密种类
     * @param array $data
     * @return bool|int|string
     *---------------------------------------------------------------------*/
    public function typeAdd($data){
        $data=$this->_filterData($data,self::$prefix.'kami_type');
        return $this->from('kami_type')->insert($data);
    }

    /** ------------------------------------------------------------------
     * 修改卡密种类
     * @param array $data
     * @param int $id
     * @return bool|int
     *---------------------------------------------------------------------*/
    public function typeUpdate($data,$id){
        unset($data['id']);
        $data=$this->_filterDataE($data,self::$prefix.'kami_type');
        return $this->from('kami_type')->eq('id',$id)->update($data);
    }

    /** ------------------------------------------------------------------
     * 两表联合搜索卡密：kami和kami_type两个表
     * @param array $where：当前表字段不用加表前缀，另外一个表的字段如果有重复必须加上表名，如id  [['kami_type.type','eq',2]]
     * @param string|int|array $limit
     * @param string $order 当前表字段不用加表前缀，另外一个表的字段如果有重复必须加上表名
     * @param bool $single 是否只输出一篇
     * @param string $select
     * @return array|bool
     *--------------------------------------------------------------------*/
    public function search($where=[],$limit='0,10',$order='id desc',$single=false,$select=''){
        if(!$select)
            $select='kami.id,kami.ka,kami.type,kami.status,kami_type.name,kami_type.value,kami_type.type as type_type,kami_type.currency,kami_type.text';
        $sql='SELECT '.$select.' FROM `'.self::$prefix.$this->table.'` as '.$this->table.'  left join '.self::$prefix.'kami_type as kami_type on '.$this->table.'.type=kami_type.id ';
        $where=Func::whereAddTable($where,$this->table);
        $order=Func::orderAddTable($order,$this->table);
        $this->_where($where)->_limit($limit)->order($order);
        $sql.=$this->_buildSql(['where','order','limit']);
        $param=$this->params;
        $this->reset(false);
        return $this->_sql($sql,$param,false,$single);
    }

    /** ------------------------------------------------------------------
     * 获取一个内容
     * @param array $where  条件
     * @param string $select
     * @return bool|array
     *--------------------------------------------------------------------*/
    public function getOne($where=[],$select=''){
        return $this->search($where,1,'',true,$select);
    }
}