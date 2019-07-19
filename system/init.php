<?php
/* ====================================================================
 * 框架初始化文件，用于引导框架启动
 *=====================================================================*/
namespace core;
define('SYSTEM_VERSION','4.0.0');
define('IS_CLI', PHP_SAPI=='cli');
if(DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors','On');
} else {
    ini_set('display_errors','Off');
}
Container :: getInstance()->bind([
	'router'				=> Router::class,
	'crouter'		    => lib\router\CRouter::class,
	'config'             => Conf::class,
	'validate'           => Validate::class,
	'db'					=> Db::class,
    'view'				=> View::class,
    'tree'                 =>Tree::class,
    'cache'              =>Cache::class,
    'session'            =>Session::class,
]);
//设置默认时区
date_default_timezone_set(\app('config')::get('timezone','system'));
define('TIME',time());
if( IS_CLI) {
    Cli::run();
} else {
    session_start();
    include_once(ROOT.'/config/route.php');
}
