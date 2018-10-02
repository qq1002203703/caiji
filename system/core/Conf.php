<?php
/* ========================================================================
 * 加载系统配置类,可以防止重复引入文件
 * ======================================================================== */
namespace core;
class Conf
{
    /**
     * 用来存储已经加载过的配置
     *
     * @var array
     */
    static protected $conf = [];
    
    /**
     * 加载系统配置,如果之前已经加载过,那么就直接返回
     * @param string $name 配置名
     * @param string $file 文件名
	 * @param mixed $default 默认值，读取不到时返回的值
	 * @param string $path 文件夹相对于根目录的路径
     * @return null|array|string
     */
    static public function get($name, $file, $default = null, $path='config/')
    {
        if(isset(self::$conf[$path.$file][$name])) {
            return self::$conf[$path.$file][$name];
        } else { 
            $conf = ROOT.'/'.$path.$file.'.php';
            if(is_file($conf)) {
                self::$conf[$path.$file] = include $conf;
                return isset(self::$conf[$path.$file][$name]) ? self::$conf[$path.$file][$name]: $default;
            } else {
                return $default;
            }
        }
        
    }
    
    /**
     * 加载系统配置文件(直接加载整个配置文件),如果之前已经加载过,那么就直接返回
     * @param string $file 文件名
	 * @param mix $default 默认值，读取不到时返回的值
	 * @param string $path 文件夹相对于根目录的路径
     * @return null|array|string
     */
    static public function all($file,$default = null,$path= 'config/')
    {
        if(isset(self::$conf[$path.$file])) {
            return self::$conf[$path.$file];
        } else {
            $conf = ROOT.'/'.$path.$file.'.php';
            if(is_file($conf)) {
                self::$conf[$path.$file] = include $conf;
                return self::$conf[$path.$file];
            } else {
                return $default;
            }
        }
        
    }
}