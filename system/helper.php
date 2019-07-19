<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 *
 * QQ 46502166
 *
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
* ========================================================================
 * 全局函数
 * ======================================================================== */
/**
 * 更漂亮的变量展现方式
 * @param $var mixed:变量名
 * @param $is_echo bool:是否打印变量
 * @param $style string:样式
 * @return string|null
 */
if(!function_exists('dump')){
    function dump($var, $is_echo = true,$style=' style="padding:5px;border:1px solid #aaa;"')
    {
        ob_start();
        var_dump($var);
        $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', ob_get_clean());
        if (defined('IS_CLI') && IS_CLI ) {
            $output = PHP_EOL . $output . PHP_EOL;
        } else {
            if (!extension_loaded('xdebug')) {
                $output = htmlspecialchars($output, ENT_SUBSTITUTE);
            }
            $output = "\n<pre".$style.">\n". $output . "</pre>\n";
        }
        if ($is_echo) {
            echo($output);
            return null;
        }
        return $output;
    }
}


/**
 * 获取get数据
 * @param string $str 变量名
 * @param string $filter 过滤方式 int为只支持int类型
 * @param mixed $default 默认值 当获取不到值时,所返回的默认值
 * @return mixed
 */
function get($str = 'false', $filter = '', $default = false)
{
    if ($str !== false) {
        $return = isset($_GET[$str]) &&(!empty($_GET[$str]) || '0'==$_GET[$str]) ? $_GET[$str] : false;
        if ($return !==false) {
            switch ($filter) {
                case 'int':
                    if (!is_numeric($return)) {
                        return $default;
                    }
                    break;
                case 'array':
                    if(!is_array($return))
                        return $default;
                    break;
                default:
                    if(is_array($return)){
                        foreach ($return as $k =>$item){
                            $return[$k]=htmlspecialchars($item);
                        }
                    }else
                        $return=htmlspecialchars($return);
            }
            return $return;
        } else {
            return $default;
        }
    } else {
        return $_GET;
    }
}

/**
 * 获取post数据
 * @param string|bool $str 变量名
 * @param string $filter 过滤方式 int为只支持int类型
 * @param mixed $default 默认值 当获取不到值时,所返回的默认值
 * @return mixed
 */
function post($str = false, $filter = '', $default = false)
{
    if ($str !== false) {
        $return = isset($_POST[$str]) && (!empty($_POST[$str]) || '0'==$_POST[$str]) ? $_POST[$str] : false;
        if ($return !== false) {
            switch ($filter) {
                case 'int':
                    if (!is_numeric($return)) {
                        return $default;
                    }
                    break;
                case 'array':
                    if(!is_array($return))
                        return $default;
                    break;
                default:
                    if(is_array($return)){
                        foreach ($return as $k =>$item){
                            $return[$k]=htmlspecialchars($item);
                        }
                    }else
                        $return=htmlspecialchars($return);
            }
            return $return;
        } else {
            return $default;
        }
    } else {
        return $_POST;
    }
}


function http_method()
{
    if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
        return 'POST';
    } else {
        return 'GET';
    }
}

function json($array)
{
    header('Content-Type:application/json; charset=utf-8');
    echo json_encode($array);
}

function show404(){
    app('\app\portal\ctrl\IndexCtrl')->xx4000000004();
}
function show_error($msg){
	if (DEBUG){
        header('HTTP/1.1 404 Not Found');
        header("status: 404 Not Found");
		header('Content-Type:text/html;charset=utf-8');
		die($msg);
	}else{
		show404();
	}
}
function show_error2($msg,$path=''){
    if (DEBUG){
        header('HTTP/1.1 404 Not Found');
        header("status: 404 Not Found");
        header('Content-Type:text/html;charset=utf-8');
        die($path.' : '.$msg);
    }else{
        die($msg);
    }
}

/**-----------------------------------------------------------------
 * 网址解析
 * @param string $url 第一种情况传入一个路由名，格式'@router_name'或'@router_name'或 '@router_name@'，都可以；
 *                               第二种情况传入'模块名/控制器/方法名'
 * @param array|string $params 额外参数，格式 'x=10&y=20&z=30'或 array('x'=>10,'y'=>20,'z'=>30)
 * @param string $siteUrl 主页的url 如 ：http://www.dahulu.cc，后面不带'/'
 *  url('wechat/order/items','uid=100&order_id=2')
 * @return string
 */
function url($url,$params='',$siteUrl=''){
    return (\core\Container::get('router'))::url($url,$params,$siteUrl);
}
/**
 * 把下划线的类名，转换为首字母大写的驼峰式类名
 * 如:post_tag 变成 PostTag
 */
function get_real_class($str){
	$str=preg_replace_callback('/_([a-z])/',function($matches){
		return strtoupper($matches[1]);
	},strtolower($str));
	return  ucfirst($str);	
}
/**
 * 快速获取容器中的实例 支持依赖注入
 * @param string    $name 类名或标识 默认获取当前应用实例
 * @param array     $args 参数
 * @param bool      $newInstance    是否每次创建新的实例
 * @return object
 */
function app($name , $args = [], $newInstance = false)
{
    return \core\Container::get($name, $args, $newInstance);
}
/**
 * 快速载入路由
 * @param string $file：路由缓存文件，相对于ROOT的文件路径
 * @return \core\Router|\core\lib\router\CRouter
 */
function router($file = '/cache/config/router.php')
{
    $file = ROOT.$file;
    return ( ! DEBUG && is_file($file)) ? include_once($file) : (app('crouter',[$file,true]));
}

/** ------------------------------------------------------------------
 * echo_select
 * @param int|string $var1
 * @param int|string $var2
 * @param string $tag
 * @return string
 *--------------------------------------------------------------------*/
function echo_select($var1,$var2,$tag='selected'){
    return ($var1==$var2)? ' '.$tag:'';
}

/**
 * 获取客户端IP地址
 * @param  integer   $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param  boolean   $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function ip($type = 0, $adv = true)
{
    $type      = $type ? 1 : 0;
    $ip = null;
    if ($adv) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos) {
                unset($arr[$pos]);
            }
            $ip = trim(current($arr));
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u", ip2long($ip));
    $ip   = $long ? [$ip, $long] : ['0.0.0.0', 0];
    return $ip[$type];
}

/**
 * 排除0和'0'去检测一个变量是否为空
 * @param mixed $var
 * @return bool 为空返回true,不为空返回false
 */
if(!function_exists('checkIsEmpty')){
    function checkIsEmpty($var){
        return (empty($var) && '0' != $var);
    }
}

if(!function_exists('msleep')){
    /** ------------------------------------------------------------------
     * 以毫秒为单位的休眠函数
     * @param int $n 单位毫秒
     * @param int $random 取随机数(单位毫秒)
     * @param bool $isEcho 是否要输出提示信息
	 * @return int|bool 
     *---------------------------------------------------------------------*/
    function msleep($n,$random=0,$isEcho=true){
        if($n<=0){
            echo '休眠时间必须大于0'.PHP_EOL;
            return false;
        }
        if($random > $n)
            $time=mt_rand($n,$random);
        elseif($random >10 && $random <= $n)
            $time=$n+mt_rand(10,$random);
        else
            $time=$n;
        if($isEcho)
            echo '休眠：'.($time/1000).'秒..............'.PHP_EOL;
        usleep($time*1000);
		return 0;
    }
}

if(!function_exists('get_path_from_id')){
    /** ------------------------------------------------------------------
     * 从id获取对应的路径
     * @param int $id
     * @param int $num 每个文件夹下文件数
     * @return string
     *---------------------------------------------------------------------*/
    function get_path_from_id($id,$num=500){
        $len=strlen((string)$num);
        $path1=ceil($id/($num*$num));
        $path2=ceil(($id%($num*$num))/$num);
        return str_pad($path1,$len,'0',STR_PAD_LEFT).'/'.str_pad($path2,$len,'0',STR_PAD_LEFT);
    }
}

