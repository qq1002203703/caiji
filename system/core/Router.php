<?php
/**
 * @author Lloyd Zhou (lloydzhou@qq.com)
 * A barebones router for PHP. It matches urls and executes PHP functions.
 * automatic get variable based on handler function parameter list.
 */
namespace core;
/**
 * Class Router
 * @package core
 * @method  Router error(int $status,callable $func);
 * @method Router get(string $name,string $rule,string $suffix=null,callable $callback=null)
 * @method hook(callable $callback, ...$params)
 */
class Router {
    protected $prefix = '';
    protected $prefix_hook = array();
    protected $_tree = array();
    protected $_events = array();
    protected $_cTypes = array('A' => 'alnum', 'a' => 'alpha', 'd' => 'digit', 'x' => 'xdigit', 'l' => 'lower', 'u' => 'upper');
    protected $_default_node = array(self::COLON => array());
    const COLON = ':';
    const SEPARATOR = '/';
    const LEAF = 'LEAF';
    static public $module='';
    static public $ctrl='';
    static public $action='';
    static public $siteUrl='';
    //路由名，储存路由规则
    static public $router_name=[];
    //路由默认设置
    static protected $router_setting=[];
    static protected $router_suffix=[];
    protected $params;


    public function __construct($tree=array(), $events=array(),$router_name=[]){
        $this->_tree = $tree;
        $this->_events = $events;
        self::$router_name=$router_name;
        self::$router_setting=Conf::get('router','system');
    }

    public function resolve($method, $path, $params){
        //$tokens = explode(self::SEPARATOR, str_replace('.', self::SEPARATOR, $path));
        $tokens = explode(self::SEPARATOR,$path);
        return $this->_resolve(array_key_exists($method, $this->_tree) ? $this->_tree[$method] : $this->_default_node, $tokens, $params);
    }
    /* helper function to find handler by $path. */
    /** ------------------------------------------------------------------
     *
     * @param array $node 路由树的一个节点
     * @param array $tokens url的查询参数
     * @param $params
     * @param int $depth
     * @return array
     *--------------------------------------------------------------------*/
    protected function _resolve($node, $tokens, $params, $depth=0){
        //dump($tokens);
        $depth = ($depth == 0 && !$tokens[0]) ? 1 : $depth;
        $current_token = isset($tokens[$depth])?$tokens[$depth]:'';
        if($current_token && $depth==(count($tokens)-1)){
            $current_token=str_replace('.html','',$current_token);
        }
        if (!$current_token && array_key_exists(self::LEAF, $node)){
            if(($suffix=strrchr($tokens[$depth-1],'.'))===false){
                $suffix='';
            }
            if($node[self::LEAF][2]===$suffix)
                return array($node[self::LEAF][0], $node[self::LEAF][1], $params,$node[self::LEAF][2]);
            else
                return array(false, '', null);
        }
        if (array_key_exists($current_token, $node)){
            return $this->_resolve($node[$current_token], $tokens, $params, $depth+1);
        }
        foreach($node[self::COLON] as $child_token=>$child_node){
            /**
             * if defined ctype validate function, for the current params, call the ctype function to validate $current_token
             * example: "/hello/:name:a.json", and url "/hello/lloyd.json" will call "ctype_alpha" to validate "lloyd"
             */

            if ($pos = stripos($child_token, self::COLON)){
                $m=substr($child_token, $pos+1);
                if(isset($child_node['LEAF']['2']) && $child_node['LEAF']['2'] && $m){
                    $m=str_replace($child_node['LEAF']['2'],'',$m);
                }
                if ($m && isset($this->_cTypes[$m]) && !call_user_func('ctype_'.$this->_cTypes[$m], $current_token)){
                    continue;
                }
                $child_token = substr($child_token, 0, $pos);
            }
            /**
             * if $current_token not null, and $child_token start with ":"
             * set the parameter named $pname and resolve next $path.
             * if can not resolve with next $path, restore the parameter named $pname.
             */
            $pvalue = array_key_exists($child_token, $params) ? $params[$child_token] : null;
            $params[$child_token] = $current_token;
            list($cb, $hook, $params) = $this->_resolve($child_node, $tokens, $params, $depth+1);
            if ($cb) return array($cb, $hook, $params);
            $params[$child_token] = $pvalue;
        }
        return array(false, '', null);
    }
    /* API to find handler and execute it by parameters. */
    /** ------------------------------------------------------------------
     * run
     * @param array $params
     * @param null $method
     * @param null $path
     * @return Router|void
     *---------------------------------------------------------------------*/
    public function run($params=array(), $method=null, $path=null){
        $method = $method ? $method : $_SERVER['REQUEST_METHOD'];
        $path = $path ? $path : parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
      /*  if($path !='/' && (($suffix = self::$router_setting['suffix'])) !=='' ){
            if(stripos($path,$suffix) < 1 ) return $this->error(405, 'url后缀不正确');
            if( $path=='/'.$suffix) return $this->error(405, 'url地址出错');
            $path=str_ireplace($suffix ,'',$path);
        }*/
        list($cb, $hook, $params) = $this->resolve($method, $path, $params);
        if($cb===false) {
            $this->defaultCall($path);
            return;
        }
        $cb=$this->getUserFunc($cb);
        if (!is_callable($cb)){
            $this->error(405, "不存在的方法或函数，路由无法回调,请检查路由表：[{$method}] {$path}");
            return;
        }
        /**
         * merge the $roter and all $request values into $params.
         * auto call the "before" hook before execute the callback handler, and call "after" hook with return value of handler.
         * need define the hook with @param $params, and @return $params, so can change it in the hook handler.
         * if the hook return false, will trigger 406 error handler.
         */
        /*$input = ((isset($_SERVER['HTTP_CONTENT_TYPE']) && 'application/json' == $_SERVER['HTTP_CONTENT_TYPE'])
            || (isset($_SERVER['CONTENT_TYPE']) && 'application/json' == $_SERVER['CONTENT_TYPE']))
            ? (array)json_decode(file_get_contents('php://input'), true) : array();*/
        //$this->params = array_merge($params, $_SERVER, $_REQUEST, $input, $_FILES, $_COOKIE, isset($_SESSION)?$_SESSION:array(), array('router'=>$this));
        $this->params = $params;
        foreach(array_merge(array('before'), $hook) as $i=>$h){
            if (false === $this->hook($h, $this)){
                $this->error(406, "路由hook出错，无法执行一个勾子函数，hook: $h");
                return;
            }
        }
        /**
         * auto get the variable list based on the callback handler parameter list.
         * if the named parameter set in user defined $params or in request, get the value.
         * if the named parameter not set, get the default value in callback handler.
         */

        try{
            $ref = (is_array($cb) && isset($cb[1])) ? new \ReflectionMethod($cb[0], $cb[1]) : new \ReflectionFunction($cb);
        }catch (\Exception $e){
            echo $e->getMessage();
            return;
        }
        $args = $ref->getParameters();

        if( !empty($args)){
            array_walk($args, function(&$p, $i, $params){
                $p = isset($params[$p->getName()]) ? $params[$p->getName()] : ($p->isOptional() ? $p->getDefaultValue() : null);
            }, $this->params);
        }
        /* execute the callback handler and pass the result into "after" hook handler.*/

        $this->hook('after', call_user_func_array($cb, $args), $this);
    }

    public function match($method,$name, $path, $cb,$suffix=null,$hook=null){
        $method=strtoupper($method);
        $name=strtolower($name);
        if($suffix===null) $suffix='';
        self::$router_name[$name]=['path'=>$path,'suffix'=>$suffix];
        //$tokens = explode(self::SEPARATOR, str_replace('.', self::SEPARATOR, trim($this->prefix.$path, self::SEPARATOR)));
        $tokens = explode(self::SEPARATOR,$this->prefix.trim($path, self::SEPARATOR));
        if (!array_key_exists($method, $this->_tree))
            $this->_tree[$method] = $this->_default_node;
        $this->match_one_path($this->_tree[$method], $tokens, $cb, $suffix,array_merge($this->prefix_hook, (array)$hook));
        return $this;
    }
    /** ------------------------------------------------------------------
     * 向路由结构树添加一个节点，节点结构如下
     * [
     *          ':'=>[],
     *          'article'=>[
     *              ':'=>[
     *                  'id:d.html'=>[
     *                      ':'=>[],
     *                      'LEAF'=>[
     *                          'portal/post/article', //控制器
     *                          [], // 勾子
     *                          '.html'  //后缀
     *                  ]
     *              ]
     *          ],
     * ]
     * @param array $node
     * @param array $tokens
     * @param string $cb 控制器、函数方法
     * @param string $suffix 后缀
     * @param array $hook 勾子
     *---------------------------------------------------------------------*/
    protected function match_one_path(&$node, $tokens, $cb,$suffix, $hook){
        $token = array_shift($tokens);
        //是否是匹配规则
        $is_token = ($token && self::COLON === $token[0]);
        $real_token = $is_token ? substr($token, 1) : $token;
        if ($is_token)
            $node = &$node[self::COLON];
        if ($real_token && !array_key_exists($real_token, $node))
            $node[$real_token] = $this->_default_node;
        if ($real_token)
            $this->match_one_path($node[$real_token], $tokens, $cb,$suffix, $hook);
        else
            $node[self::LEAF] = [$cb, (array)($hook),$suffix];
    }
    /* register api based on request method. also register "error" and "hook" API. */
    public function __call($name, $args){
        if (in_array($name, array('get', 'post', 'put', 'patch', 'delete', 'trace', 'connect', 'options', 'head'))
            && array_unshift($args, $name)){
            return call_user_func_array(array($this, 'match'), $args);
        }
        if (in_array($name, array('groupList', 'prefix'))){
            $this->prefix = isset($args[0]) && is_string($args[0]) && self::SEPARATOR == $args[0][0] ? $args[0] : '';
            $this->prefix_hook = isset($args[1]) ? (array)$args[1] : array();
        }
        if (in_array($name, array('error', 'hook'))){
            $key = $name. ':'. array_shift($args);
            if (isset($args[0]) && is_callable($args[0])){
                $this->_events[$key] = $args[0];
            }else if (isset($this->_events[$key]) && is_callable($this->_events[$key])){
                return call_user_func_array($this->_events[$key], $args);
            }else {
                return ('error' == $name) ? trigger_error('"'.$key.'" 还没有定义对应错误码的回调函数（handler） error: '.$args[0]) : $args[0];
            }
        }
        return $this;
    }


    /**
     * 从字符串得到对应的方法或函数
     */
    protected function getUserFunc($str)
    {
        $str=trim($str,'/');
        if(strrpos($str,'/')!==false){
            $arr=explode('/',$str);
            self::$module=strtolower($arr[0]);
            if(self::$module =='common') show_error('模块common禁止从url直接访问');
            self::$ctrl=ucfirst($arr[1]);
            self::$action = isset($arr[2]) ? strtolower($arr[2]) : (self::$router_setting['action']);
            $class='\\app\\'.self::$module.'\ctrl\\'.self::$ctrl.'Ctrl';
            $method=self::$action;
            if(class_exists($class))
                return [new $class,$method];
        }elseif(strpos($str,'@')!==false){
            $arr=explode('@',$str);
            if(class_exists($arr[0]))
                return [new $arr[0],$arr[1]];
        }elseif(strpos($str,'::')!==false)
            return explode('::',$str);
        return $str;
    }
    /**--------------------------------------------------------
     * 没有匹配到路由时，解析path去访问系统对应的'模块/控制器/方法'
     * @param $path string $_SERVER['REQUEST_URI']中的PHP_URL_PATH
    ---------------------------------------------------------*/
    protected function defaultCall($path)
    {
        $default = self::$router_setting;
        $path=trim($path,'/');

        if ($path !== '') {
            $path = explode('/', $path);
            //----求module-----------------
            if (isset($path[0]) && $path[0]) {
                self::$module = strtolower($path[0]);
                if(self::$module =='common') show_error('模块common禁止从url直接访问');
                unset($path[0]);
            } else {
                self::$module = strtolower($default['module']);
            }

            //----求ctrl-----------------
            if (isset($path[1]) && $path[1]) {
                self::$ctrl = get_real_class($path[1]);
                unset($path[1]);
            } else {
                self::$ctrl = ucfirst($default['ctrl']);
            }
            //----求action--------------
            if (isset($path[2]) && $path[2]) {
                self::$action = strtolower($path[2]);
                unset($path[2]);
            } else {
                self::$action = strtolower($default['action']);
            }
            //----额外参数传入$_GET中------------
            if(!empty($path)){
                $path = array_merge($path);
                $pathLenth = count($path);
                for($i = 0;$i < $pathLenth;$i=$i+2){
                    if (isset($path[$i + 1])) {
                        $_GET[$path[$i]] = $path[$i + 1];
                    }
                }
            }
        } else {
            self::$module = strtolower($default['module']);
            self::$ctrl = ucfirst($default['ctrl']);
            self::$action = strtolower($default['action']);
        }
        $ctrlClass = '\\app\\' . self::$module . '\ctrl\\' . self::$ctrl . 'Ctrl';
        $action = self::$action;
        //$ctrlFile = APP . self::$module.'/ctrl/' . self::$ctrl . 'Ctrl.php';
        if (!class_exists($ctrlClass)) {
            show_error('" '.$ctrlClass . ' "<br>是一个不存的控制器，请检查链接和路由是否正确！');
        }
        call_user_func_array([new $ctrlClass(),$action], []);
    }

    /** ------------------------------------------------------------------
     * url输出器
     * @param string $url
     * @param string|array $params
     * @param string $siteUrl
     * @return string
     *---------------------------------------------------------------------*/
    static public function url($url,$params=[],$siteUrl='')
    {
        $url=trim($url);
        if(!$siteUrl)
            $siteUrl=(self::$siteUrl) ? : self::getSiteUrl();
        else
            $siteUrl.='/';
        if($url=='/' || $url=='') return $siteUrl;
        $suffix = '';
        if(is_string($params) && $params !=='' ){
            parse_str($params,$params);
        }
        //对应已命名的路由--------------------------------------
        if(strpos($url,'@')!==false){
            $url=trim($url,'@');
            if(isset(self::$router_name[$url])){
                $url=self::$router_name[$url]['path'];
                if(empty($params)) return $siteUrl.$url;
                $needs=[];
                $replace=[];
                foreach($params as $k => $v){
                    $needs[]=':'.$k;
                    $replace[]=$v;
                }
                return $siteUrl.preg_replace ('/:[aAdxlu]/','',str_replace($needs,$replace,$url));
            }else
                return '没找到对应的路由名：'.$url;
        }
        //输出系统对应url 格式'模块/控制器/方法'--------------------------------------
        $url=self::getRealUrl($url);
        if(empty($params))
            return $siteUrl.$url.$suffix;
        $str='';
        foreach($params as $k => $v){
            $str.='/'.$k.'/'.$v;
        }
        return $siteUrl.$url.$str.$suffix;
    }

    static protected function getRealUrl($url)
    {
        $url=explode('/',$url);
        if(isset($url[1]))
            $url[1]=preg_replace_callback('/([A-Z])/',function($matches){return '_'.$matches[1];},lcfirst($url[1]));
        return strtolower(implode('/',$url));
    }

    /** ------------------------------------------------------------------
     * 获取网站的主页地址
     * @return string
     *---------------------------------------------------------------------*/
    static public function getSiteUrl(){
        $siteConfig=Conf::all('site');
        return ($siteConfig['is_realurl']=='1') ?(($_SERVER['HTTP_HOST']==$siteConfig['mobile_domain'] ? 'http://'.$siteConfig['mobile_domain'] : $siteConfig['site_url']).'/') :'/';
    }
}