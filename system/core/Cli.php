<?php
/* ================================================
 * php cli模式核心类
 * ================================================ */
namespace core;
class Cli
{
    public static function run()
    {
        $argv = $_SERVER['argv'];
        unset($argv[0]);
        $shellName = trim(array_shift($argv),'/');
        if(!$shellName || strpos($shellName,'/')===false){
            $folder='helper';
            $class ='help';
        }else{
            list($folder,$class)=explode('/',$shellName);
        }
        unset($shellName);
        $class='\shell\\'.$folder.'\\'.ucfirst($class);
        if(is_callable([$class,'start'])){
            call_user_func_array([new $class($argv),'start'], []);
        }else{
            echo PHP_EOL.'The method "'.$class.' -> start()" can\'t be callback '.PHP_EOL;
        }
    }
}