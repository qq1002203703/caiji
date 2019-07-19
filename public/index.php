<?php

/* ========================================================================
 * 入口文件，用于定义常量
 * ======================================================================== */
//调试模式
define('DEBUG', true);
// 根目录
define('ROOT',realpath(__DIR__.'/../'));
//系统核心路径
define('SYSTEM', ROOT . '/system/');
//app路径
define('APP', ROOT . '/app/');
//载入composer
include ROOT . '/vendor/autoload.php';
