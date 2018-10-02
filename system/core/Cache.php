<?php
/* ========================================================================
 * 缓存类
 * ======================================================================== */
namespace core;

class Cache
{
    /**
     * @var \core\lib\cache\File | \core\lib\cache\memcached
     */
    private $class;

    /** Cache constructor.
     * @param array $option
     */
    public function __construct($option=array())
    {
        $type=app('config')::get('cache_type','system');
        $class = '\\core\\lib\\cache\\'.ucfirst($type);
        $this->class = new $class($option);
    }

    /** ------------------------------------------------------------------
     * 读取缓存
     * @param string $name
     * @return mixed：存在而且缓存时间没到期时返回原数据，否则返回false
     *---------------------------------------------------------------------*/
    public function get($name)
    {
        return $this->class->get($name);
    }

    /** ------------------------------------------------------------------
     * 把数据写入到缓存中
     * @param string $name：缓存名
     * @param mixed $data :数据
     * @param int|bool $time 缓存时间，0是永久，false时是现在时间+默认，否则是$time+time()
     * @return bool:成功返回true,否则文件缓存时抛出错误“写入权限不足”
     *---------------------------------------------------------------------*/
    public function set($name, $data, $time = false)
    {
        return $this->class->set($name,$data,$time);
    }

    /** ------------------------------------------------------------------
     * 删除缓存
     * @param string $name:缓存名
     * @return bool:成功删除返回true,否则返回false
     *---------------------------------------------------------------------*/
    public function del($name)
    {
        return $this->class->del($name);
    }
    /** ------------------------------------------------------------------
     * 删除当前所有缓存
     *---------------------------------------------------------------------*/
    public function clear()
    {
        $this->class->clear();
    }
}