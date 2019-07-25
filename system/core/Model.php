<?php
/* ========================================================================
 * 模型基类,当前继承于medoo
 * 主要用于连接数据库,并封装了四个常用操作
 * ======================================================================== */
namespace core;
use extend\Helper;
class Model extends AR
{
	
	public function __construct( $config=[])
	{
		parent::__construct($config);
		$this->debug = DEBUG;
        $db=Container::get('db');
		self::setDb($db->getPdo());
        self::$prefix=$db->options['prefix'];
	}

    /**
     * 原生sql查询
     * @param string $sql：原生sql
     * @param  array $params:要绑定的参数
     * @param bool $table:是否要替换sql语句中的table
     * @param bool $single 单篇还是多篇
     * @return array|bool
     */
    public function _sql($sql,$params,$table=true,$single=false){
        if($table)
            $sql=str_ireplace('table',self::$prefix.$this->table,$sql);
        return self::_query($sql, $params, $this, $single,true);
    }
    /**
     * 原生sql查询字段值
     * @param string $field:字段名
     * @param string $sql：原生sql
     * @param  array $params:要绑定的参数
     * @param bool $table:是否要替换sql语句中的table
     * @return string|bool
     */
    public function _sqlField($field,$sql,$params,$table=true){
        if($table)
            $sql=str_ireplace('table',self::$prefix.$this->table,$sql);
        $ret= self::_query($sql, $params, $this, true,true);
        if($ret)
            return $ret[$field];
        else
            return false;
    }

    /**
     * 执行原生DELETE、 INSERT、或 UPDATE sql语句
     * @param $sql
     * @param $params
     * @param bool $table
     * @return int:受影响行数
     */
    public function _exec($sql,$params,$table=true){
        if($table)
            $sql=str_ireplace('table',self::$prefix.$this->table,$sql);
        return self::execute($sql, $params,$this);
    }

    /**
     * 数据字段过滤，把表中没有的字段从原数据中去掉
     * @param $data：原数据
     * @param string $table
     * @return array
     */
    public function _filterData($data,$table=''){
        $fields=$this->getFieldsName($table);
        $ret=[];
        foreach ($fields as $field){
            if(isset($data[$field])){
                $ret[$field]=$data[$field];
            }
        }
        return $ret;
    }

    /** ------------------------------------------------------------------
     * 数据字段过滤扩展，把表中没有的字段从原数据中去掉，同时把没有输入的项删除
     * @param array $data
     * @param string $table
     * @return array
     *--------------------------------------------------------------------*/
    public function _filterDataE($data,$table=''){
        $fields=$this->getFieldsName($table);
        $ret=[];
        foreach ($fields as $field){
            if(isset($data[$field]) && $data[$field] !==''){
                $ret[$field]=$data[$field];
            }
        }
        return $ret;
    }

    /**
     * where条件查询的增强版
     * @param array $where:两种格式： ['id'=>10,'status'=>1] 和 [ ['id','gt',1],['status','eq',1] ]
     * @return static $this
     */
    public function _where($where=array()){
        if(!empty($where)){
            foreach ($where as $k =>$v){
                if(is_array($v)){
                    call_user_func_array([$this,$v[1]],[$v[0],$v[2]]);
                }else{
                    $this->eq($k,$v);
                }
            }
        }
        return $this;
    }

    /**
     * 添加limit语句的增强版
     * @param int|string|array $limit 支持整数、数组和字符串格式，如 100、'0,10'、'10' 或 [0,10]、[10]
     * @return static $this
     */
    public function _limit($limit){
        if(!$limit) return $this;
        if(is_int($limit)){
            $this->limit($limit);
            return $this;
        }
        if(is_string($limit))
            $limit=explode(',',$limit);
        if(count($limit)==2){
            $this->limit($limit[0],$limit[1]);
        }else{
            $this->limit($limit[0]);
        }
        return $this;
    }

    /**
     * 一次性绑定多条sql语句
     * @param array $exp：sql表达式，例 ['select'=>'id,title','where'=>[['id','gte',1],'status'=>1],'limit'=>'0,10']
     * @return static $this
     */
    public function _exeExp($exp=array()){
        if(isset($exp['select']))
            $this->select($exp['select']);
        if(isset($exp['from']))
            $this->from($exp['from']);
        if(isset($exp['where']))
            $this->_where($exp['where']);
        if(isset($exp['groupList']))
            $this->group($exp['groupList']);
        if(isset($exp['order']))
            $this->order($exp['order']);
        if(isset($exp['limit']))
            $this->_limit($exp['limit']);
        return $this;
    }

    /**
     * 查询总数
     * @param array $exp : 同$this->_exeExp()
     * @return int
     */
    public function count(array $exp=array()){
        if($exp)
            $this->_exeExp($exp);
        $result=$this->select('count(1) as count')->find(null,true);
        return $result ? $result['count'] : 0;
    }

    /**------------------------------------------------------------------
     * 字段值设置
     * @param string $field 字段名
     * @param int|string $value
     * @param array $where
     * @param string $table 数据库的表名
     * @param string $operator 运算符 不带运算符会直接设置字段等于值，
     *          否则设置字段等于（原值与新值经运算符计算后的值）
     * @return int 返回影响的行数
     *--------------------------------------------------------------------*/
    public function setField($field,$value,$where,$table='',$operator='+'){
        if(!$table)
            $table=$this->table;
        $sql=$this->_where($where)->_buildSql(['where']);
        $param=$this->params;
        if($operator)
            $sql='update '.self::$prefix.$table.' set `'.$field.'`=`'.$field.'`'.$operator.'(:ph0)'.$sql;
        else
            $sql='update '.self::$prefix.$table.' set `'.$field.'`= :ph0'.$sql;
        $param[':ph0']=$value;
        $this->reset(false);
        return $this->_exec($sql,$param,false);
    }

    /** ------------------------------------------------------------------
     * 随机条数的获取
     * @param int $limit
     * @param array $where
     * @param string $select
     * @param string $table
     * @return array
     *---------------------------------------------------------------------*/
    public function _random($limit=10,$where=[],$select='*',$table=''){
        if(!$table)
            $table=$this->table;
        $counter=$this->select('Max(id) as max,Min(id) as min,Count(*) as count')->from($table)->find(null,true);
        $limitCount = $counter['max'] - $counter['count'] + $limit;
        $inArr = Helper::rand_number($counter['min'], $counter['max'], $limitCount,false);
        $order=implode(',',$inArr);
        return $this->select($select)->from($table)->in('id',$inArr)->_where($where)->order("field(id,{$order})")->limit($limit)->findAll(true);
    }
}