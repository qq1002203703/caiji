<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 *
 * QQ 46502166
 *
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 *========================================================================================
 * 视图类
 *========================================================================================*/

namespace core;
use core\lib\view\Php;
class View
{
    /**
     * 模板变量
     * @var array
     */
    protected $data = [];
    /**
     * 静态方式的模板变量
     * @var array
     */
    protected static $var = [];

    // 模板引擎实例
    private $template;

    // 模板引擎参数
    protected $config = [
        //分隔符
        'separator'=>DIRECTORY_SEPARATOR,
        'path'=>ROOT .  DIRECTORY_SEPARATOR .'tpl'. DIRECTORY_SEPARATOR,
        'cache_path'=>ROOT. DIRECTORY_SEPARATOR.'cache'. DIRECTORY_SEPARATOR .'tpl'. DIRECTORY_SEPARATOR ,
        'is_cache'=>! DEBUG ,
        'debug'=> DEBUG ,
        'strip_space'=>! DEBUG ,
    ];
    public function __construct($config, Php $engine)
    {
        if(! empty($config))
            $this->config = array_merge($this->config, (array) $config);
        $this->template = $engine;
        $this->template->config($this->config);
    }

    public function display($file='',$data=[],$is_auto=true)
    {
        echo $this->fetch($file,$data,$is_auto);
    }

    public function fetch($file='',$data=[],$is_auto=true){
        $file=$this->getTplFile($file,$is_auto);
        $data=array_merge(self::$var,$this->data,$data);
        return $this->template->fetch($file, $data);
    }

    /**
     * 模板参数设置
     * @param string|array $name
     * @param mixed $value
     * @return  void
     */
    public function config($name,$value=''){
        if( ! $name )
            return;
        if( is_array($name))
            $this->template->config($name);
        else
            $this->template->__set( (string) $name,$value);
    }

    public function exists($file=''){
        return $this->template->exists($file);
    }

    public function assign( $name,$value='')
    {
        //count(array_filter(array_keys($name), 'is_string'))>0
        if( is_array($name ) ){
            $this->data = array_merge($this->data, $name);
        }else if (is_string($name)){
            $this->data[$name] = $value;
        }
        return $this;
    }

    /**
     * 模板变量静态赋值
     * @access public
     * @param  mixed $name  变量名
     * @param  mixed $value 变量值
     * @return $this
     */
    public function share($name, $value = '')
    {
        if (is_array($name)) {
            self::$var = array_merge(self::$var, $name);
        } else {
            self::$var[$name] = $value;
        }
        return $this;
    }

    /**
     * 获取模板文件名
     * @param string $file：'模块/控制器/方法' 或 '控制器/方法' 或 '方法' 或为空
     * @param bool $is_auto: 是否自动获取文件名，true时自动按一规则获取，false时直接返回$file
     * @return string
     */
    protected function getTplFile($file = '', $is_auto=true){
        $router=app('router');
        if($file == '')  $file=$router :: $action;
        $file=strtolower(trim($file,'/'));
        if( ! $is_auto) return '/'== DIRECTORY_SEPARATOR ? $file : str_replace('/',DIRECTORY_SEPARATOR,$file);
        $file=explode('/',$file);
        switch (count($file)){
            case 1:
                $str=$router :: $module. $this->config['separator'] .strtolower($router::$ctrl). $this->config['separator'].$file[0];
                break;
            case 2:
                $str=$router :: $module. $this->config['separator'] .$file[0]. $this->config['separator'] .$file[1];
                break;
            case 3:
                $str=$file[0]. $this->config['separator'] .$file[1]. $this->config['separator'] .$file[2];
                break;
            default:
                $str=$router :: $module.  $this->config['separator'] .strtolower($router::$ctrl). $this->config['separator']  . $router :: $action;
        }
       return $str;
    }

    public function getConfig(){
        return $this->config;
    }

}
