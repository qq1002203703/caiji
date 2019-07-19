<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * session类
 * ======================================*/
namespace core;
class Session
{

    /** ------------------------------------------------------------------
     * 在session中设置一个名为$sessionName的值
     * @param $sessionName
     * @param $value
     *--------------------------------------------------------------------*/
	static public function set($sessionName,$value){
		$_SESSION[$sessionName] = $value;
	}

	/**
	 * 根据sessionName获取对应在session中的值
	 * @param string $sessionName
     * @param mixed $defalut
	 * @return mixed session的值如果没有此session，返回默认值
	 */
	static public function get($sessionName,$defalut=''){
        if(strpos($sessionName,'.')===false){
            return $_SESSION[$sessionName] ?? $defalut;
        }else{
            list($a,$b)=explode('.',$sessionName);
            return $_SESSION[$a][$b] ??  $defalut;
        }
	}

	/**
	 * 删除session中的一个值
	 * @param string $sessionName
     * @return bool
	 */
	static public function del($sessionName)
	{
        if(strpos($sessionName,'.')===false){
            if(isset($sessionName)){
                unset($_SESSION[$sessionName]);
                return true;
            }
        }else{
            list($a,$b)=explode('.',$sessionName);
            if(isset($_SESSION[$a][$b])){
                unset($_SESSION[$a][$b]);
                return true;
            }
        }
        return false;
	}

    /** ------------------------------------------------------------------
     * 清空全部session
     *--------------------------------------------------------------------*/
	static public function clear(){
		if(isset($_SESSION)) {
            session_unset();
		}
	}
}