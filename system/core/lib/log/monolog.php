<?php
namespace core\lib\log;

use core\Conf;
class monolog
{
    public $class;
    public function __construct()
    {
        $this->class = new \Monolog\Logger('app');
        $log_path = Conf::get('LOG_PATH','log').date('Ymd').'.log';
        $this->class->pushHandler(new \Monolog\Handler\StreamHandler($log_path));
    }
    
    public function __call($name,$args )
    {
        $this->class->$name($args[1]);
    }
    
    public function addlog($level,$message)
    {
        $this->class->log($level,json_encode($message));
    }
}