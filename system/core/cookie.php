<?php
namespace core;

class Cookie
{
    const SECRETKEY = 'kskkd00jjmpxmss';//混淆字符串

    /**
     * 获取cookie
     * @param string $name cookie名称
     * @param bool $decrypt 是否需要解密
     * @return string
     */
    public static function get($name,$decrypt=true)
    {
        if ($name != '') {
            return empty($_COOKIE[$name]) ? '' : ($decrypt ? self::decrypt($_COOKIE[$name]) : $_COOKIE[$name]);
        } else {
            return '';
        }
    }

    /** ------------------------------------------------------------------
     * 删除cookie
     * @param string $name
     * @return bool
     *--------------------------------------------------------------------*/
    public static function del($name)
    {
        if ($name == '' || empty($_COOKIE[$name])) {
            return true;
        } else {
            return self::set($name, '', -3600,false);
        }
    }

    /** ------------------------------------------------------------------
     * 设置cookie
     * @param string $name cookie 的名称
     * @param string $value  cookie 的值
     * @param int $expire cookie 的有效期
     * @param bool $encrypt 是否需要加密
     * @param string $path  cookie 的服务器路径
     * @param string $domain cookie 的域名
     * @param int $secure 是否通过安全的 HTTPS 连接来传输 cookie
     * @return bool
     *--------------------------------------------------------------------*/
    public static function set($name, $value, $expire = 3600,$encrypt=true, $path = '/', $domain = '', $secure = 0)
    {
        if ($name != '') {
            if($encrypt)
                $value = self::encryption($value);
            $expire = time() + $expire;
            return setcookie($name, $value, $expire, $path, $domain, $secure);
        } else {
            return false;
        }
    }

    /** ------------------------------------------------------------------
     * 清空当前域所有cookie
     *--------------------------------------------------------------------*/
    public static function clear(){
        if(!isset($_COOKIE) || empty($_COOKIE))
            return;
        foreach ($_COOKIE as $key => $value) {
            self::set($key,'',-3600,false);
        }
    }

    /** ------------------------------------------------------------------
     * getJson
     * @param $name
     * @param bool $decrypt
     * @return bool|array|string
     *--------------------------------------------------------------------*/
    public static function getJson($name,$decrypt=true){
        $res=self::get($name,$decrypt);
        if($res){
            return json_decode($res,true);
        }
        return false;
    }

    /**
     * 字符串加密
     * @param string $string
     * @return string
     */
    private static function encryption($string)
    {
        $string = base64_encode($string);
        $code = '';
        $key = substr(md5(self::SECRETKEY), 8, 18);
        $strLen = strlen($string);
        $keyLen = strlen($key);
        for ($i = 0; $i < $strLen; $i++) {
            $k = $i % $keyLen;
            $code .= $string[$i] ^ $key[$k];
        }
        return $code;
    }

    /**
     * 字符串解密
     * @param string $string
     * @return string
     */
    private static function decrypt($string)
    {
        $code = '';
        $key = substr(md5(self::SECRETKEY), 8, 18);
        $strLen = strlen($string);
        $keyLen = strlen($key);
        for ($i = 0; $i < $strLen; $i++) {
            $k = $i % $keyLen;
            $code .= $string[$i] ^ $key[$k];
        }
        return base64_decode($code);
    }

}