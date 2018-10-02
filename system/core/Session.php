<?php
/**
 * session 操作类
 * @author 张森  2013-08-27
 */
namespace core;

class Session
{

	public function set($sessionName,$value)
	{
		return $_SESSION[$sessionName] = $value;
	}

	/**
	 * 根据sessionName获取session值
	 * @param string $sessionName
	 * @return string session的值如果没有此session，返回空。
	 */
	public function get($sessionName)
	{
		return isset($_SESSION[$sessionName]) ? $_SESSION[$sessionName] : '';
	}

	/**
	 * 删除一个session
	 * @param string $sessionName
	 */
	public function del($sessionName)
	{
		if(isset($sessionName)) {
            unset($_SESSION[$sessionName]);
		    return true;
		}
		return false;
	}

	public function clear()
	{
		if(isset($_SESSION))
		{
            session_unset();
		}
	}
}