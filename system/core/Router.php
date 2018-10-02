<?php 
/**
 * @author Lloyd Zhou (lloydzhou@qq.com)
 * A barebones router for PHP. It matches urls and executes PHP functions.
 * automatic get variable based on handler function parameter list.
 */
namespace core;

class Router {
    protected $prefix = '';
    protected $prefix_hook = array();
    protected $_tree = array();
    protected $_events = array();
    protected $_ctypes = array('A' => 'alnum', 'a' => 'alpha', 'd' => 'digit', 'x' => 'xdigit', 'l' => 'lower', 'u' => 'upper');
    protected $_default_node = array(self::COLON => array());
    const COLON = ':';
    const SEPARATOR = '/';
    const LEAF = 'LEAF';
	static public $module='';
	static public $ctrl='';
	static public $action='';
	//路由名，储存路由规则
	static public $router_name=[];
	//路由默认设置
	static protected $router_setting=[];
    public function __construct($tree=array(), $events=array(),$router_name=[]){
        $this->_tree = $tree;
        $this->_events = $events;
		self::$router_name=$router_name;
		self::$router_setting=Conf::get('router','system');
    }
    /* helper function to create the tree based on urls, handlers will stored to leaf. */
    protected function match_one_path(&$node, $tokens, $cb, $hook){
        $token = array_shift($tokens);
        $is_token = ($token && self::COLON == $token[0]);
        $real_token = $is_token ? substr($token, 1) : $token;
        if ($is_token) $node = &$node[self::COLON];
        if ($real_token && !array_key_exists($real_token, $node))
            $node[$real_token] = $this->_default_node;
        if ($real_token)
            return $this->match_one_path($node[$real_token], $tokens, $cb, $hook);
        $node[self::LEAF] = array($cb, (array)($hook));
    }
    /* helper function to find handler by $path. */
    protected function _resolve($node, $tokens, $params, $depth=0){
        $depth = ($depth == 0 && !$tokens[0]) ? 1 : $depth;
        $current_token = isset($tokens[$depth])?$tokens[$depth]:'';
        if (!$current_token && array_key_exists(self::LEAF, $node))
            return array($node[self::LEAF][0], $node[self::LEAF][1], $params);
        if (array_key_exists($current_token, $node))
            return $this->_resolve($node[$current_token], $tokens, $params, $depth+1);
        foreach($node[self::COLON] as $child_token=>$child_node){
            /**
             * if defined ctype validate function, for the current params, call the ctype function to validate $current_token
             * example: "/hello/:name:a.json", and url "/hello/lloyd.json" will call "ctype_alpha" to validate "lloyd"
             */
            if ($pos = stripos($child_token, self::COLON)){
                if (($m=substr($child_token, $pos+1)) && isset($this->_ctypes[$m]) && !call_user_func('ctype_'.$this->_ctypes[$m], $current_token))
                    continue;
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
    public function resolve($method, $path, $params){
        //$tokens = explode(self::SEPARATOR, str_replace('.', self::SEPARATOR, $path));
		$tokens = explode(self::SEPARATOR,  $path);
        return $this->_resolve(array_key_exists($method, $this->_tree) ? $this->_tree[$method] : $this->_default_node, $tokens, $params);
    }
    /* API to find handler and execute it by parameters. */
    public function run($params=array(), $method=null, $path=null){
        $method = $method ? $method : $_SERVER['REQUEST_METHOD'];
        $path = $path ? $path : parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		if($path !='/' && (($suffix = self::$router_setting['suffix'])) !=='' ){
			if(stripos($path,$suffix) < 1 ) return $this->error(405, 'url后缀不正确');
            if( $path=='/'.$suffix) return $this->error(405, 'url地址出错');
			$path=str_ireplace($suffix ,'',$path);
		}
        list($cb, $hook, $params) = $this->resolve($method, $path, $params);
		if($cb===false) return $this->defaultCall($path);
		$cb=$this->getUserFunc($cb);
        if (!is_callable($cb)) return $this->error(405, "不存在的方法或函数，路由无法回调,请检查路由表：[$method] $path");
        /**
         * merge the $roter and all $request values into $params.
         * auto call the "before" hook before execute the callback handler, and call "after" hook with return value of handler.
         * need define the hook with @param $params, and @return $params, so can change it in the hook handler.
         * if the hook return false, will trigger 406 error handler.
         */
        $input = ((isset($_SERVER['HTTP_CONTENT_TYPE']) && 'application/json' == $_SERVER['HTTP_CONTENT_TYPE'])
            || (isset($_SERVER['CONTENT_TYPE']) && 'application/json' == $_SERVER['CONTENT_TYPE']))
            ? (array)json_decode(file_get_contents('php://input'), true) : array();
        $this->params = array_merge($params, $_SERVER, $_REQUEST, $input, $_FILES, $_COOKIE, isset($_SESSION)?$_SESSION:array(), array('router'=>$this));
        foreach(array_merge(array('before'), $hook) as $i=>$h){
            if (false === $this->hook($h, $this)) return $this->error(406, "路由hook出错，无法执行一个勾子函数，hook: $h");
        }
        /**
         * auto get the variable list based on the callback handler parameter list.
         * if the named parameter set in user defined $params or in request, get the value.
         * if the named parameter not set, get the default value in callback handler.
         */
        $ref = is_array($cb) && isset($cb[1]) ? new \ReflectionMethod($cb[0], $cb[1]) : new \ReflectionFunction($cb);
        $args = $ref->getParameters();
		if( !empty($args)){
			array_walk($args, function(&$p, $i, $params){
				$p = isset($params[$p->getName()]) ? $params[$p->getName()] : ($p->isOptional() ? $p->getDefaultValue() : null);
			}, $this->params);
		}
        /* execute the callback handler and pass the result into "after" hook handler.*/
        return $this->hook('after', call_user_func_array($cb, $args), $this);
    }

    public function match($method,$name, $path, $cb, $hook=null){
		$method=strtoupper($method);
		$name=strtolower($name);
		self::$router_name[$name]=$path;
		//$tokens = explode(self::SEPARATOR, str_replace('.', self::SEPARATOR, trim($this->prefix.$path, self::SEPARATOR)));
		$tokens = explode(self::SEPARATOR,trim($this->prefix.$path, self::SEPARATOR));
		if (!array_key_exists($method, $this->_tree)) $this->_tree[$method] = $this->_default_node;
		$this->match_one_path($this->_tree[$method], $tokens, $cb, array_merge($this->prefix_hook, (array)$hook));
        return $this;
    }
    /* register api based on request method. also register "error" and "hook" API. */
    public function __call($name, $args){
        if (in_array($name, array('get', 'post', 'put', 'patch', 'delete', 'trace', 'connect', 'options', 'head'))
            && array_unshift($args, $name))
            return call_user_func_array(array($this, 'match'), $args);
        if (in_array($name, array('group', 'prefix'))){
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
	* @param $path string: $_SERVER['REQUEST_URI']中的PHP_URL_PATH
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
            show_error($ctrlClass . '是一个不存在的控制器');
        }
		call_user_func_array([new $ctrlClass(),$action], []);
	}

	static public function url($url,$params='')
	{
	    $url=trim($url);
		$domain=(Conf::get('is_realurl','site')=='1') ? Conf::get('site_url','site').'/' :'/';
		if($url=='/' || $url=='') return $domain;
		$suffix = self::$router_setting['suffix'];
		if(is_string($params) && $params !=='' ){
			parse_str($params,$params);
		}
		//对应已命名的路由--------------------------------------
		if(strpos($url,'@')!==false){
			$url=trim($url,'@');
			if(isset(self::$router_name[$url])){
				$url=self::$router_name[$url];
				if(empty($params)) return $domain.$url.$suffix;
				$needs=[];
				$replace=[];
				foreach($params as $k => $v){
					$needs[]=':'.$k;
					$replace[]=$v;
				}
				return $domain.preg_replace ('/:[aAdxlu]/','',str_replace($needs,$replace,$url)).$suffix;
			}else
				return '没找到对应的路由名：'.$url;
		}
		//输出系统对应url 格式'模块/控制器/方法'--------------------------------------
		$url=self::getRealUrl($url);
		if(empty($params)) return $domain.$url.$suffix;
		$str='';
		foreach($params as $k => $v){
			$str.='/'.$k.'/'.$v;
		}
		return $domain.$url.$str.$suffix;
	}

	static protected function getRealUrl($url)
	{
		$url=explode('/',$url);
		if(isset($url[1]))
			$url[1]=preg_replace_callback('/([A-Z])/',function($matches){return '_'.$matches[1];},lcfirst($url[1]));
		return strtolower(implode('/',$url));
	}
}