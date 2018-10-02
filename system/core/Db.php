<?php
/* ========================================================================
 * PDO数据库连接类
 * 支持数据库类型：mysql\sqlite\mariadb\mssql
 * ======================================================================== */
namespace core;

class Db
{
	/**
	* 数据库设置项
	* @var array
	*/
	public $options=[];
    /**
     * PDO连接句柄
     * @var \PDO
     */
	public $pdo;

    /**----------------------------------------------------------------
     * Db 构造方法.
     * @param Conf $conf
     * @param array $options
     */
	public function __construct(Conf $conf, $options=[])
	{
	    if(empty($options))
            $this->options = $conf::all('database');
        else
            $this->options = $options;
        if(!isset($this->options['prefix']))
            $this->options['prefix']='';
		$this->pdo=$this->connect();
	}

    /**----------------------------------------------------------------
     * 连接数据库
     * @return \PDO
     * @throws \Exception
     */
	public function connect()
	{
		try {
			$commands = array();
			$dsn = '';
			$options=$this->options;
			if (!is_array($options)){
				throw new \Exception('数据库配置参数出错，无法连接数据库');
			}
			if (isset($options['port']) &&is_int($options['port'] * 1)){
				$port = $options['port'];
			}
			$type = strtolower($options['database_type']);
			$is_port = isset($port);
			switch ($type){
				case 'mariadb':
					$type = 'mysql';
				case 'mysql':
					$dsn = $type . ':host=' . $options['server'] . ($is_port ? ';port=' . $port : '') . ';dbname=' . $options['database_name'];
					//$commands[] = 'SET SQL_MODE=ANSI_QUOTES';
                    $commands[]='SET SQL_MODE=STRICT_TRANS_TABLES';
					break;
				case 'mssql':
					$dsn = strstr(PHP_OS, 'WIN') ?
						'sqlsrv:server=' . $options['server'] . ($is_port ? ',' . $port : '') . ';database=' . $options['database_name'] :
						'dblib:host=' . $options['server'] . ($is_port ? ':' . $port : '') . ';dbname=' . $options['database_name'];
					$commands[] = 'SET QUOTED_IDENTIFIER ON';
					break;
				case 'sqlite':
					$dsn = $type . ':' . $options['database_file'];
					break;
			}
			if (in_array($type, array('mariadb', 'mysql', 'mssql')) && $options['charset'])
			{
				$commands[] = "SET NAMES '" . $options['charset'] . "'";
			}
			$db = new \PDO($dsn,$options['username'],$options['password']);
            $db->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
			foreach ($commands as $value){
				$db ->exec($value);
			}
			return $db;
		}
		catch (\PDOException $e) {
			throw new \Exception($e->getMessage());
		}

	}
    /**----------------------------------------------------------------
     * 获取PDO连接句柄
     * @return \PDO
     */
    public function getPdo() {
        if(!empty($this->pdo))
            return $this->pdo;
        else
            return  $this->connect();

    }
}