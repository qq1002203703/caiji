<?php
/* ========================================================================
 * 加载系统配置类,可以防止重复引入文件
 * ======================================================================== */
namespace core;

class Conf
{
    /**
     * @var array  用来存储已经加载过的配置
     */
    static protected $conf = [];
    
    /**
     * 加载系统配置,如果之前已经加载过,那么就直接返回
     * @param string $name 配置名
     * @param string $file 文件名
	 * @param mixed $default 默认值，读取不到时返回的值
	 * @param string $path 文件夹相对于根目录的路径
     * @param bool $isCache 是否缓存变量
     * @return null|array|string
     */
    static public function get($name, $fileName, $default = null, $folder='config/',$isCache=true){
        if($isCache && isset(self::$conf[$folder.$fileName][$name])) {
            return self::$conf[$folder.$fileName][$name];
        } else{
            $file = ROOT.'/'.$folder.$fileName.'.php';
            if(is_file($file)) {
                $config=include $file;
                if($isCache)
                    self::$conf[$folder.$fileName] = $config;
                return isset($config[$name]) ? $config[$name]: $default;
            } else {
                return $default;
            }
        }
    }
    
    /**
     * 加载系统配置文件(直接加载整个配置文件),如果之前已经加载过,那么就直接返回
     * @param string $file 文件名
	 * @param mixed $default 默认值，读取不到文件时返回的值
	 * @param string $path 文件夹相对于根目录的路径
     * @param bool $isCache 是否缓存变量
     * @return null|array|string
     */
    static public function all($fileName,$default = null,$folder= 'config/',$isCache=true){
        if($isCache && isset(self::$conf[$folder.$fileName])) {
            return self::$conf[$folder.$fileName];
        } else {
            $file = ROOT.'/'.$folder.$fileName.'.php';
            if(is_file($file)) {
                $config=include $file;
                if($isCache)
                    self::$conf[$folder.$fileName] = $config;
                return $config;
            } else {
                return $default;
            }
        }
    }

    /** ------------------------------------------------------------------
     * 向配置文件中添加项
     * @param string $name 配置项的名称
     * @param int|string|array $value  配置项的值
     * @param bool $isArray 配置项是否是数组
     * @param string $fileName 文件名
     * @param string $folder 相对于根目录的文件夹路径
     * @return bool
     *---------------------------------------------------------------------*/
    static public function add($name,$value,$isArray,$fileName,$folder='config/'){
        $all=self::all($fileName,[],$folder);
        if($isArray){
            if(!isset($all[$name]))
                $all[$name]=[];
            $all[$name][]=$value;
        }else
            $all[$name]=$value;
        return self::write($all,$fileName,$folder);
    }

    /** ------------------------------------------------------------------
     * 删除配置项
     * @param string $name 配置项的名称
     * @param int|string $key 键名
     * @param string $fileName 文件名
     * @param string $folder 相对于根目录的文件夹路径
     * @return bool
     *---------------------------------------------------------------------*/
    static public function del($name,$key,$fileName,$folder='config/'){
        $all=self::all($fileName,[],$folder);
        if($key===null)
            unset($all[$name]);
        else
            unset($all[$name][$key]);
        return self::write($all,$fileName,$folder);
    }
    /** ------------------------------------------------------------------
     * 同时修改配置项的名称和值
     * @param string $name 配置项的名称
     * @param int|string $newKey 新的key名
     * @param int|string $oldKey 原来的key名
     * @param int|string|array $value  配置项的值
     * @param string $fileName  文件名
     * @param string $folder 相对于根目录的文件夹路径
     * @return bool
     *---------------------------------------------------------------------*/
    static public function edit($name,$newKey,$oldKey,$value,$fileName,&$msg='',$folder='config/'){
        $all=self::all($fileName,[],$folder);
        if($newKey===null)
            $all[$name]=$value;
        else{
            unset($all[$name][$oldKey]);
            if(array_key_exists($newKey,$all[$name])){
                $msg='有相同的健名存在';
                return false;
            }
            $all[$name][$newKey]=$value;
        }
        return self::write($all,$fileName,$folder);
    }

    /** ------------------------------------------------------------------
     * 修改配置:只能用于修改没有数组的配置
     * @param string|array $name 配置项的名称
     * @param int|string $value  配置项的值 $name为数组时此项不起作用
     * @param string $fileName  文件名
     * @param string $folder 相对于根目录的文件夹路径
     * @return bool
     *---------------------------------------------------------------------*/
    static public function editValue($name,$value,$fileName,$folder='config/'){
        $conf = ROOT.'/'.$folder.$fileName.'.php';
        if(!is_file($conf))
            return false;
        $content=file_get_contents($conf);
        if(is_array($name)){
            $s=[];
            $r=[];
            foreach ($name as $key =>$value){
                $s[]='#\''.str_replace('\'','\\\'',$key).'\' *=> *[^\n\r]+#';
                if(is_string($value))
                    $value="'".str_replace('\'','\\\'',$value)."'";
                $r[]="'{$key}'=>{$value},";
            }
            $count=count($name);
        }else{
            if(is_string($value))
                $value="'".str_replace('\'','\\\'',$value)."'";
            $s='#\''.str_replace('\'','\\\'',$name).'\' *=> *[^\n\r]+#';
            $r="'{$name}'=>{$value},";
            $count=1;
        }
        $content=preg_replace($s,$r,$content,-1,$i);
        if($count !==$i)
            return false;
        return file_put_contents($conf,$content) ? true : false;
    }

    /** ------------------------------------------------------------------
     * 写入文件中
     * @param mixed $data 数据
     * @param string $fileName 文件名
     * @param string $folder 相对于根目录的文件夹路径
     * @return bool
     *--------------------------------------------------------------------*/
    static public function write($data,$fileName,$folder='config/'){
        $str="<?php \n return ".var_export($data,true).';';
        $path=ROOT.'/'.$folder.$fileName.'.php';
        return \core\lib\cache\File::write($path,$str);
    }

    /** ------------------------------------------------------------------
     * 清除缓存的变量
     * @param string $fileName
     * @param null|string $name
     * @param string $folder
     *---------------------------------------------------------------------*/
    static public function clear($fileName, $name=null,$folder='config/'){
        if($name)
            unset(self::$conf[$folder.$fileName][$name]);
        else
            unset(self::$conf[$folder.$fileName]);
    }

}