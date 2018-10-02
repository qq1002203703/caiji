<?php
/**--------------------------------------------------
 * Created on Nov 26, 2013
 * @author Lloyd Zhou
 * @email lloydzhou@qq.com
 *------------------------------------------------/
/**
 * Simple implement of active record in PHP.
 * Using magic function to implement more smarty functions.
 * Can using chain method calls, to build concise and compactness program.
 */
namespace core;
use core\lib\ar\Base;
use core\lib\ar\Expressions;
use core\lib\ar\WrapExpressions;
use extend\Helper;
/**
 * Class AR
 * @package core
 * @method AR from(string $table)
 * @method AR select(string ... $fiel)
 * @method AR where(string $str)
 * @method AR eq(string $fiel,string|int $value)
 * @method  AR ne(string $fiel,string|int $value)
 * @method  AR gt(string $fiel,string|int $value)
 * @method  AR lt(string $fiel,string|int $value)
 * @method AR in(string $fiel,array $value)
 * @method AR like(string $fiel,string $value)
 * @method AR notlike(string $fiel,string $value)
 * @method  AR limit(int $start,int $num=0) 只传一个参数时，是查询多少条，传两个参数时是查询从$start开始返回$num条
 * @method AR order(string $order)
 * @method AR group(string $group)
 */
abstract class AR extends Base {

	public $data = array();
    /**
     * @var \PDO  static property to connect database.
     */
    public static $db;
    /**
     * @var array maping the function name and the operator, to build Expressions in WHERE condition.
     * <pre>user can call it like this: 
     *      $user->isnotnull()->eq('id', 1); 
     * will create Expressions can explain to SQL: 
     *      WHERE user.id IS NOT NULL AND user.id = :ph1</pre>
     */
    public static $operators = array(
        'equal' => '=', 'eq' => '=',
        'notequal' => '<>', 'ne' => '<>',
        'greaterthan' => '>', 'gt' => '>',
        'lessthan' => '<', 'lt' => '<',
        'greaterthanorequal' => '>=', 'ge' => '>=','gte' => '>=',
        'lessthanorequal' => '<=', 'le' => '<=','lte' => '<=',
        'between' => 'BETWEEN',
        'like' => 'LIKE',
        'notlike'=>'NOT LIKE',
        'in' => 'IN',
        'notin' => 'NOT IN',
        'isnull' => 'IS NULL',
        'isnotnull' => 'IS NOT NULL', 'notnull' => 'IS NOT NULL', 
    );
    /**
     * @var array Part of SQL, maping the function name and the operator to build SQL Part.
     * <pre>call function like this: 
     *      $user->order('id desc', 'name asc')->limit(2,1);
     *  can explain to SQL:
     *      ORDER BY id desc, name asc limit 2,1</pre>
     */
    public static $sqlParts = array(
        'select' => 'SELECT',
        'from' => 'FROM',
        'set' => 'SET',
        'where' => 'WHERE',
        'group' => 'GROUP BY','groupby' => 'GROUP BY',
        'having' => 'HAVING',
        'order' => 'ORDER BY','orderby' => 'ORDER BY',
        'limit' => 'limit',
        'top' => 'TOP',
    );
    /**
     * @var array Static property to stored the default Sql Expressions values.
     */
    public static $defaultSqlExpressions = array('expressions' => array(), 'wrap' => false,
        'select'=>null, 'insert'=>null, 'update'=>null, 'set' => null, 'delete'=>'DELETE ', 'join' => null,
        'from'=>null, 'values' => null, 'where'=>null, 'having'=>null, 'limit'=>null, 'order'=>null, 'group' => null);
    /**
     * @var array Stored the Expressions of the SQL. 
     */
    protected $sqlExpressions = array();
	/**
	* @var bool: 是否开启调试模式
	*/
	protected $debug=false;
	/**
	* @var array 开启Debug时，每条sql查询都会被储存到这里
	*/
	public $sqlCache=[];
    /**
     * @var string  The table name in database.
     */
    public $table;
	/**
     * @var string  数据表前缀.
     */
	static public $prefix='';
    /**
     * @var string  The primary key of this ActiveRecord, just suport single primary key.
     */
    public $primaryKey = 'id';
    /**
     * @var array Stored the drity data of this object, when call "insert" or "update" function, will write this data into database. 
     */
    public $dirty = array();
    /**
     * @var array Stored the params will bind to SQL when call PDOStatement::execute(), 
     */
    public $params = array();
    const BELONGS_TO = 'belongs_to';
    const HAS_MANY = 'has_many';
    const HAS_ONE = 'has_one';
    /**
     * @var array，是一个二维的关联数组，储存与本数据表关联的其它数据表的信息，进而通过getRelation()方法建立自动的影射
     * 书写格式：每一个关联表最多有6个选项，最少需要提供前面4项，后面2个选项是可选项
     * 第一项 string：self::HAS_ONE，self::HAS_MANY，self::BELONGS_TO中的一种
     * 第二项 string：数据表名
     * 第三项 string：BELONGS_TO时是对方的primaryKey在本表对应的字段名，HAS_MANY和HAS_ONE时是本表的primaryKey在对方表对应的字段名
     * 第四项 string：module name 模块名
     * 第五项 string：如果提供此项，会把此项作为一个属性保存到后面的对象中，一般传递$this过去
     * 第六项 array：二维数组，预先执行的方法，键名是本类中的一个方法名，值是此方法的参数（是数组，可以传递多个参数）；求影射表的值前会先执行这些方法
     * 例：本表是一个文章内容页，他的relations如下
     * $relations=[
     *       //和用户表的对应关系
     *      'user'=>['self::BELONGS_TO','user','uid','portal',$this,['methed1'=>['p1','p2'],'methed2'=>['x1',x2,x3]],
     *      //和分类关系表的对应关系
     *      'relation'=>['self::HAS_MANY','relation','post_id','portal','',['methed1'=>['p1']],
     *      //与评论表的对应关系
     *       'comment'=>['self::HAS_MANY','comment','post_id','portal'],
     *  ]
     */
    public $relations = array();
    /**
     * @var int The count of bind params, using this count and const "PREFIX" (:ph) to generate place holder in SQL. 
     */
    public static $count = 0;
    const PH = ':ph';


    /**-------------------------------------------------------------------------------
     *
     * function to reset the $params and $sqlExpressions.
     * @param bool $is_reset_data
     * @return AR return $this, can using chain method calls.
     */
    public function reset($is_reset_data=true) {
        $this->sqlExpressions = $this->params = array();
		$is_reset_data && $this->data = $this->dirty= array();
		//$this->sqlCache;
        return $this;
    }
    /**
     * function to SET or RESET the dirty data.
     * @param array $dirty The dirty data will be set, or empty array to reset the dirty data. 
     * @return AR return $this, can using chain method calls.
     */
    public function dirty($dirty = array()){
		$this->dirty = $dirty;
		if(!empty($this->dirty))
			$this->data = array_merge($this->data, $this->dirty);
        return $this;
    }
    /**
     * set the DB connection.
     * @param \PDO $db
     */
	public static function setDb(\PDO $db) {
		self::$db=$db;
    }
    /**
     * function to find one record and assign in to current object.
     * @param int $id If call this function using this param, will find record by using this id. If not set, just find the first record in database.
     * @param $return_array bool
     * @return bool|AR if find record, assign in to current object and return it, other wise return "false".
     */
    public function find($id = null ,$return_array=false) {
        if ($id) $this->reset(false)->eq($this->primaryKey, $id);
        return self::_query($this->limit(1)->_buildSql(array('select', 'from', 'join', 'where', 'group', 'having', 'order', 'limit')), $this->params, $this->reset(false), true,$return_array);
    }
    /**
     * function to find all records in database.
     * @param bool $return_array
     * @return array return array of ActiveRecord
     */
    public function findAll($return_array=false) {
        return self::_query($this->_buildSql(array('select', 'from', 'join', 'where', 'group', 'having', 'order', 'limit')), $this->params, $this->reset(false),false,$return_array);
    }

    /**
     * 执行delete sql语句：不提供参数，会偿试从当前AR对象中获取主键值，否则从参数中获取主键值；有主键值后就可以不用where语句了
     * @param array $data：包含主键的值
     * @return int：返回受影响的行数
     */
    public function delete($data=array()) {
        if(empty($data)){
            if(isset($this->{$this->primaryKey}))
                $this->eq($this->primaryKey, $this->{$this->primaryKey});
        }else{
            if(isset($data[$this->primaryKey]))
                $this->eq($this->primaryKey, $data[$this->primaryKey]);
        }
        $ret=self::execute($this->_buildSql(array('delete', 'from', 'where')), $this->params,$this);
        $this->reset(false);
        return $ret;
    }

    /**
     * 执行update sql语句进行数据库更新：会自动从数据中获取主键值，所以如果数据中包含了主键的值，那么前面就可以不用where语句了
     * @param array $data：数据
     * @return AR|int|bool：1、不提供参数数据时，如果原AR对象没有数据就返回false,否则返回执行update后的AR对象；2、提供参数数据时，如果参数格式不正确返回false,否则返回执行update后受影响的行数
     */
    public function update($data=array()) {
        if(empty($data)){
            if (count($this->dirty) == 0) return false;
            foreach($this->dirty as $field => $value) $this->addCondition($field, '=', $value, ',' , 'set');
            if(isset($this->{$this->primaryKey})){
                $this->eq($this->primaryKey, $this->{$this->primaryKey});
            }
            self::execute($this->_buildSql(array('update', 'set', 'where')), $this->params,$this);
            return $this->dirty()->reset(false);
        }else{
            if(Helper::check_array_type($data) !=2 )  return false;
            foreach($data as $field => $value) $this->addCondition($field, '=', $value, ',' , 'set');
            $ret= self::execute($this->_buildSql(array('update', 'set', 'where')), $this->params,$this);
            $this->reset(false);
            return $ret;
        }
    }
    /**
     * 把数据插入到数据库中，不提供参数数据时，会获取当前AR对象中的数据插入到数据库中
     * @param array $data：数据
     * @return bool|AR|int|string：插入失败时返回false,插入成功按下面处理：1、不提供参数数据时，成功返加当前AR对象，但会把最后插入行的ID或序列值保存到$this->{$this->primaryKey}中。2、提供参数时，如果存在最后插入行的ID或序列值，就返回它，否则就返回true
     */
    public function insert($data =array()) {
        if(empty($data)){
            if (count($this->dirty) == 0) return true;
            $value = $this->_filterParam($this->dirty);
            $this->insert = new Expressions(array('operator'=> 'INSERT INTO '. self::$prefix . $this->table,
                                                  'target' => new WrapExpressions(array('target' => array_keys($this->dirty)))));
            $this->values = new Expressions(array('operator'=> 'VALUES', 'target' => new WrapExpressions(array('target' => $value))));
            $result=self::execute($this->_buildSql(array('insert', 'values')), $this->params,$this);
            if($result >0 ){
                if(isset( $this->{$this->primaryKey}))
                    $this->{$this->primaryKey} = self::$db->lastInsertId();
                return $this->dirty()->reset(false);
            }
        }else{
            if(Helper::check_array_type($data) !=2 )  return false;
            $value = $this->_filterParam($data);
            $this->insert = new Expressions(array('operator'=> 'INSERT INTO '. self::$prefix . $this->table,
                                                  'target' => new WrapExpressions(array('target' => array_keys($data)))));
            $this->values = new Expressions(array('operator'=> 'VALUES', 'target' => new WrapExpressions(array('target' => $value))));
            $result=self::execute($this->_buildSql(array('insert', 'values')), $this->params,$this);

            $this->reset(false);
            if($result >0 ){
                $inserId=self::$db->lastInsertId();
               return $inserId ? $inserId: $result ;
            }
        }
        return false;
    }

    /**
     * 通过POD预处理方式执行sql语句，本方法主要用于update、delete或insert sql语句，要想返回结果集，最好用_query()函数
     * @param string $sql The SQL need to be execute.
     * @param array $param The param will be bind to PDOStatement.
     * @param AR $obj
     * @return bool|int:语句执行失败会抛出异常，否则返回受影响的行数
     */
    public static function execute($sql, $param = array(),$obj=null) {
		if($obj && $obj->debug) $obj->sqlCache[] = $sql;
        $sth = self::$db->prepare($sql);
        if($sth && $sth->execute($param)){
            return $sth->rowCount();
        }
        throw new \Exception($sth->errorInfo()[2]);
    }
    /**
     * 通过POD预处理方式执行sql查询语句，没有结果集的sql语句最好用execute()函数
     * @param string $sql The SQL to find record.
     * @param array $param The param will be bind to PDOStatement.
     * @param AR $obj The object, if find record in database, will assign the attributes in to this object.
     * @param bool $single if set to true, will find record and fetch in current object, otherwise will find all records.
     * @return bool|AR|array
     */
    public static function _query($sql, $param = array(), $obj, $single=false,$return_array=false) {
		$obj ->debug && $obj ->sqlCache[] = $sql;
        if ($sth = self::$db->prepare($sql)) {
			//返回数组形式结果集
			if($return_array){
				$sth->execute($param);
				if ($single) {
					$result=$sth->fetch( \PDO::FETCH_ASSOC );
					$obj->reset()->data = $result ?? [];
					return $result;
				};
				$obj->reset();
				return $sth->fetchAll(\PDO::FETCH_ASSOC);
			//返回AR对象形式结果集
			}else{
				$sth->setFetchMode( \PDO::FETCH_INTO , $obj);
				$sth->execute($param);
				if ($single){
					return $sth->fetch( \PDO::FETCH_INTO ) ? $obj->dirty() : false;
				}
				$result = array();
				while ($obj = $sth->fetch( \PDO::FETCH_INTO )) $result[] = clone $obj->dirty();
				return $result;
			}
        }		
        return false;
    }

    /**
     * 获取关联表的数据
     * There was three types of relations: {BELONGS_TO, HAS_ONE, HAS_MANY}
     * @param string $name string：The name of the relation,$this->relation的一个键名.
     * @return AR object|array
     */
    protected function & getRelation($name) {
        $relation = $this->relations[$name];
        if ($relation instanceof self || (is_array($relation) && $relation[0] instanceof self))
            return $relation;
		if(!isset($relation[3]))
			throw new \Exception("relation $name 缺少第4个设置项.");
		//$module=(isset($relation[3]) && $relation[3]) ? $relation[3] : (app('router')::module);
		$obj='\app\\' . $relation[3] . '\model\\'.get_real_class($relation[1]);
        $this->relations[$name] = $obj = new $obj;
        if (isset($relation[4]) && is_array($relation[4]))
            foreach((array)$relation[4] as $func => $args)
                call_user_func_array(array($obj, $func), (array)$args);
		$backref = isset($relation[5]) ? $relation[5] : '';
		switch ($relation[0]){
			case self::HAS_ONE :
				$obj->eq($relation[2], $this->{$this->primaryKey})->find() && $backref && $obj->__set($backref, $this);
				break;
			case self::HAS_MANY:
				$this->relations[$name] = $obj->eq($relation[2], $this->{$this->primaryKey})->findAll();
				if ($backref)
					foreach($this->relations[$name] as $o)
						$o->__set($backref, $this);
				break;
			case self::BELONGS_TO:
				$obj->eq($obj->primaryKey, $this->{$relation[2]})->find() && $backref && $obj->__set($backref, $this);
				break;
			default:
				throw new \Exception("relation $name 第一项not found.");
		}
        return $this->relations[$name];
    }
    /**
     * helper function to build SQL with sql parts.
     * @param string $n The SQL part will be build.
     * @param int $i The index of $n in $sqls array.
     * @param AR $o The refrence to $this
     * @return string 
     */
    private function _buildSqlCallback(&$n, $i, $o){
        //if ('select' === $n && null == $o->$n) $n = strtoupper($n). ' '. $o::$prefix . $o->table.'.*';
        if ('select' === $n && null == $o->$n) $n = strtoupper($n). ' *';
        elseif (('update' === $n||'from' === $n) && null == $o->$n) $n = strtoupper($n).' '. $o::$prefix.$o->table;
        elseif ('delete' === $n) $n = strtoupper($n). ' ';
        else $n = (null !== $o->$n) ? $o->$n. ' ' : '';
    }
    /**
     * helper function to build SQL with sql parts.
     * @param array $sqls The SQL part will be build.
     * @return string 
     */
    protected function _buildSql($sqls = array()) {
        array_walk($sqls, array($this, '_buildSqlCallback'), $this);
        return implode(' ', $sqls);
    }
    /**
     * magic function to make calls witch in function mapping stored in $operators and $sqlPart.
     * also can call function of PDO object.
     * @param string $name function name
     * @param array $args The arguments of the function.
     * @return mixed Return the result of callback or the current object to make chain method calls.
     */
    public function __call($name, $args) {

        if (is_callable($callback = array(self::$db,$name)))
            return call_user_func_array($callback, $args);
        if (in_array($name = strtolower($name), array_keys(self::$operators)))
             $this->addCondition($args[0], self::$operators[$name], isset($args[1]) ? $args[1] : null, (is_string(end($args)) && 'or' === strtolower(end($args))) ? 'OR' : 'AND');
         else if (in_array($name= str_replace('by', '', $name), array_keys(self::$sqlParts))){
             if($args=implode(', ', $args)){
                if('from'==strtolower($name)) $args=self::$prefix.$args;
                $this->$name = new Expressions(array('operator'=>self::$sqlParts[$name], 'target' => $args));
             }
         }
        else throw new \Exception("Method $name not exist.");
        return $this;
    }
    /**
     * make wrap when build the SQL expressions of WHWRE.
     * @param string $op If give this param will build one WrapExpressions include the stored expressions add into WHWRE. otherwise wil stored the expressions into array.
     * @return AR return $this, can using chain method calls.
     */
    public function wrap($op = null) {
        if (1 === func_num_args()){
            $this->wrap = false;
            if (is_array($this->expressions) && count($this->expressions) > 0)
            $this->_addCondition(new WrapExpressions(array('delimiter' => ' ','target'=>$this->expressions)), 'or' === strtolower($op) ? 'OR' : 'AND');
            $this->expressions = array();
        } else $this->wrap = true;
        return $this;
    }
    /**
     * helper function to build place holder when make SQL expressions.
     * @param mixed $value the value will bind to SQL, just store it in $this->params.
     * @return mixed $value
     */
    protected function _filterParam($value) {
        if (is_array($value)) foreach($value as $key => $val) $this->params[$value[$key] = self::PH. ++self::$count] = $val;
        else if (is_string($value)){
            $this->params[$ph = self::PH. ++self::$count] = $value;
            $value = $ph;
        }
        return $value;
    }
    /**
     * helper function to add condition into WHERE. 
     * create the SQL Expressions.
     * @param string $field The field name, the source of Expressions
     * @param string $operator 
     * @param mixed $value the target of the Expressions
     * @param string $op the operator to concat this Expressions into WHERE or SET statment.
     * @param string $name The Expression will contact to.
     */
    public function addCondition($field, $operator, $value, $op = 'AND', $name = 'where') {
        $value = $this->_filterParam($value);
        //if ($exp =  new Expressions(array('source'=>('where' == $name? self::$prefix.$this->table.'.' : '' ) .$field, 'operator'=>$operator, 'target'=>(is_array($value)
        if ($exp =  new Expressions(array(
            'source'=>$field,
            'operator'=>$operator,
            'target'=>(is_array($value) ? new WrapExpressions('between' === strtolower($operator) ? array('target' => $value, 'start' => ' ', 'end' => ' ', 'delimiter' => ' AND ') : array('target' => $value)
            ) : $value)))) {
            if (!$this->wrap)
                $this->_addCondition($exp, $op, $name);
            else
                $this->_addExpression($exp, $op);
        }
    }
    /**
     * helper function to add condition into JOIN. 
     * create the SQL Expressions.
     * @param string $table The join table name
     * @param string $on The condition of ON
     * @param string $type The join type, like "LEFT", "INNER", "OUTER"
     */
    public function join($table, $on, $type='LEFT'){
        $this->join = new Expressions(array('source' => $this->join ?: '', 'operator' => $type. ' JOIN', 'target' => new Expressions(
            array('source' => self::$prefix . $table, 'operator' => 'ON', 'target' => $on)
        )));
        return $this;
    }
    /**
     * helper function to make wrapper. Stored the expression in to array.
     * @param Expressions $exp The expression will be stored.
     * @param string $operator The operator to concat this Expressions into WHERE statment.
     */
    protected function _addExpression($exp, $operator) {
        if (!is_array($this->expressions) || count($this->expressions) == 0) 
            $this->expressions = array($exp);
        else 
            $this->expressions[] = new Expressions(array('operator'=>$operator, 'target'=>$exp));
    }
    /**
     * helper function to add condition into WHERE. 
     * @param Expressions $exp The expression will be concat into WHERE or SET statment.
     * @param string $operator the operator to concat this Expressions into WHERE or SET statment.
     * @param string $name The Expression will contact to.
     */
    protected function _addCondition($exp, $operator, $name ='where' ) {
        if (!$this->$name) 
            $this->$name = new Expressions(array('operator'=>strtoupper($name) , 'target'=>$exp));
        else 
            $this->$name->target = new Expressions(array('source'=>$this->$name->target, 'operator'=>$operator, 'target'=>$exp));
    }
    /**
     * magic function to SET values of the current object.
     */
    public function __set($var, $val) {
        if (array_key_exists($var, $this->sqlExpressions) || array_key_exists($var, self::$defaultSqlExpressions))
            $this->sqlExpressions[$var] = $val;
        else if (array_key_exists($var, $this->relations) && $val instanceof self)
            $this->relations[$var] = $val;
        else $this->dirty[$var] = $this->data[$var] = $val;
    }
    /**
     * magic function to UNSET values of the current object.
     */
    public function __unset($var) { 
        if (array_key_exists($var, $this->sqlExpressions)) unset($this->sqlExpressions[$var]);
        if(isset($this->data[$var])) unset($this->data[$var]);
        if(isset($this->dirty[$var])) unset($this->dirty[$var]);
    }
    /**
     * magic function to GET the values of current object.
     */
    public function & __get($var) {
        if (array_key_exists($var, $this->sqlExpressions)) return  $this->sqlExpressions[$var];
        else if (array_key_exists($var, $this->relations)) return $this->getRelation($var);
        else return  parent::__get($var);
    }

	/**
     * 把AR对象转化为数组.
	 * return array()
     */
	public function toArray()
	{
		return $this->data;
	}

    /**
     * 取得最执行的sql语句
     * @param bool $last: 是否只返回最后一条，默认为true
     * @return array|string
     */
	public function getSql($last=true){
	    if($last){
	        if(empty($this->sqlCache))
	            return '';
            return end($this->sqlCache);
        }

        return $this->sqlCache;
	}

    /**
     * 获取表的所有字段名
     * @param string $table
     * @return mixed
     */
	public function getFieldsName($table=''){
	    if(!$table)
	        $table=self::$prefix.$this->table;
        $sth = self::$db->prepare('DESCRIBE '.$table);
        $sth->execute();
        return $sth->fetchAll(\PDO::FETCH_COLUMN);
    }
}
