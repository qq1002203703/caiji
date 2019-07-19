<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 *
 * QQ 46502166
 *
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ==========================================
 * PHP原生模板引擎：在模板中支持继承
 * =========================================*/
namespace core\lib\view;
use core\Container;

class Php
{
    /**
     * 模板变量
     * @var array
     */
    protected $data = [];

    // 模板引擎参数
    protected $config = [
        // 模板的路径
        'path'   => '',
        //模板缓存文件保存路径
        'cache_path'=>'',
        //是否开启模板缓存
        'is_cache'=>false,
        //缓存时间（单位：秒），0为永久
        'cache_time'=>0,
        // 模板渲染缓存路径(经PHP编译后的纯字符串)
        //'display_cache_path'      => '',
        // 是否开启模板渲染缓存
        //'is_display_cache'      => false,
        // 后缀
        'suffix' => 'php',
        //编译时是否去除标签之间的空格\空行\换行等空白
        'strip_space' =>true,
        //是否是调试模式，调试模式时不缓存、不去除标签空白，并且输出更详细的错误信息
        'debug' =>true,
    ];
    /**
     * 模板包含信息
     * @var array
     */
    private $includeFile = [];
    /**
     * 模板布局标签对应正则
     * @var array
     */
    protected $regex=[
        'content'=>'/\{%content@(.+?)%\}/i',
        'extend'=>'/\{%extend@(.+?)%\}/i',
        'block'=>'/\{%block@(.+?)%\}/i',
        'include'=>'/\{%include@(.+?)%\}/i',
    ];

    /**
     * 构造函数
     * @param array $config
     */
    public function __construct($config = [])
    {
        if(!empty($config))
            $this->config = array_merge($this->config, (array) $config);
        if($this->config['debug'])
            $this->config['strip_space']=false;
    }

    /**
     * 模板引擎配置项
     * @access public
     * @param  array|string $config
     * @return array|string|null
     */
    public function config($config){
        if(!empty($config)){
            if (is_array($config)) {
                $this->config = array_merge($this->config, $config);
            } elseif (isset($this->config[$config])) {
                return $this->config[$config];
            }
        }
        return null;
    }
    /**
     * 魔术函数,设置模板参数
     * @param  string $name
     * @param mixed $value
     * @return void
     */
    public function __set($name, $value)
    {
        $this->config[$name]=$value;
    }

    /**
     * 模板变量赋值
     * @access public
     * @param  string|array $name
     * @param  mixed $value
     * @return void
     */
    public function assign($name, $value = '')
    {
        if (is_array($name)) {
            $this->data = array_merge($this->data, $name);
        } else {
            $this->data[$name] = $value;
        }
    }
    /**
     * 检测是否存在模板文件
     * @access public
     * @param  string $template 模板文件或者模板规则
     * @return bool
     */
    public function exists($template )
    {
        return is_file($this->getPath($template));
    }

    /**
     * 获取模板完整路径
     * @access public
     * @param $template：模板名
     * @return string
     */
    public function getPath($template )
    {
        return $this->config['path'].$template .'.'.$this->config['suffix'];
    }
    /**
     * 渲染模板文件
     * @access public
     * @param  string    $template 模板文件
     * @param  array     $data 模板变量
     * @return string
     */
    public function fetch($template, $data = [])
    {
        //加载模板的函数
        $functionFile=$this->config['path'].'common/function.php';
        if(is_file($functionFile)){
            include_once $functionFile;
        }
        unset($functionFile);
        if ($data && is_array($data)) {
            $this->data = array_merge($this->data,$data);
        }
        $template=$this->getPath($template);
        // 模板不存在 抛出异常
        if (!is_file($template)) {
            $this->error(2,$template);
        }
        $cacheFile = $this->config['cache_path'] . md5($template) . '.php';
        if (!$this->checkCache($cacheFile)) {
            // 缓存无效 重新编译模板
            $content = file_get_contents($template);
            $this->includeFile[$template] = filemtime($template);
            $this->compiler($content, $cacheFile);
        }
        // 页面缓存
        ob_start();
        ob_implicit_flush(0);
        // 读取php编译存储
        $this->readFile($cacheFile, $this->data);
        // 获取并清空缓存
        return ob_get_clean();
    }

    /**
     * 编译模板
     * @param  string $content：读取模板的内容
     * @param string $cacheFile ：模板缓存（完整路径）
     */
    protected function compiler(&$content ,$cacheFile)
    {
        $layout='';
        $content=$this->parseContent($content,$layout);
        $content=$this->parseLayout($content,$layout);
        $content=$this->parseInclude($content);
        if ($this->config['strip_space']) {
            /* 去除html空格与换行 */
            $find    = ['~>\s+<~', '~>(\s+\n|\r)~'];
            $replace = ['><', '>'];
            $content = preg_replace($find, $replace, $content);
        }
        // 优化生成的php代码
        $content = preg_replace('/\?>\s*<\?php\s(?!echo\b)/s', '', $content);
        // 模板过滤输出
        //$replace = $this->config['tpl_replace_string'];
        //$content = str_replace(array_keys($replace), array_values($replace), $content);

        // 添加安全代码及模板引用记录
        $content = '<?php /*' . serialize($this->includeFile) . '*/ ?>' . "\n" . $content;
        // 编译存储
        $this->writeFile($cacheFile, $content);
        $this->includeFile = [];
    }

    /**
     * 解析第一层模板中的 {%content@name%} 和{%content%}标签
     * @param string $content :模板的内容
     * @param string $layoutContent:布局模板的内容
     * @return string
     */
    protected function parseContent($content , & $layoutContent){
        $matches=[];
        if(preg_match($this->regex['content'],$content,$matches) >0 ){
            $content=str_replace($matches[0],'',$content);
            $template=$this->getPath($matches[1]);
            $this->includeFile[$template] = filemtime($template);
            $layout=file_get_contents($template);
            $content=str_ireplace('{%content%}',$content,$layout);
            $content=$this->parseContentRe($content);
        }elseif (  preg_match($this->regex['extend'],$content,$matches) >0 ){
            $template=$this->getPath($matches[1]);
            $this->includeFile[$template] = filemtime($template);
            $layoutContent=file_get_contents($template);
            $layoutContent=$this->parseContentRe($layoutContent);
        }
        return $content;
    }

    /**
     * 递归解析多层 {%content@name%}和{%content%}
     * @param string $content :模板的内容
     * @return string
     */
    protected function parseContentRe($content){
        $matches=[];
        if(preg_match($this->regex['content'],$content,$matches) ==0 ){
            return $content;
        }
        $content=str_replace($matches[0],'',$content);
        $template=$this->getPath($matches[1]);
        $this->includeFile[$template] = filemtime($template);
        $layout=file_get_contents($template);
        $content=str_ireplace('{%content%}',$content,$layout);
        return $this->parseContentRe($content);
    }

    /**
     *  解析模板中的 {%extend@name%} 和  {%block@name%}标签
     * @param string $content :模板的内容
     * @param string $layout:布局模板的内容
     * @return mixed|string
     */
    protected function parseLayout($content,$layout)
    {
        $matches=[];
        if(preg_match($this->regex['extend'],$content,$matches) ==0 ){
            return $content;
        }
        $content=str_replace($matches[0],'',$content);
        if($layout ===''){
            $template=$this->getPath($matches[1]);
            $this->includeFile[$template] = filemtime($template);
            $layout=file_get_contents($template);
        }
        $find=[];
        if(preg_match_all($this->regex['block'],$layout,$find) == 0 ){
            return $layout;
        }
        $matches=[];
        if(preg_match_all($this->regex['block'],$content,$matches) == 0 ){
            return preg_replace($this->regex['block'],'',$layout);
            //return $layout;
        }
        $matches=array_combine($matches[1],$matches[0]);
        $replace=[];
        $cout=count($find[0]);
        for($i=0;$i<$cout;$i++){
            //echo "\n{$i}=>{$find[0][$i]}-----------------------------------------\n";
            if(isset($matches[$find[1][$i]])){
                $replace[$find[1][$i]]=(Container::get('\extend\Helper'))::strCut($content,$matches[$find[1][$i]],'{%end%}');
                if($replace[$find[1][$i]] == ''){
                    unset($find[0][$i]);
                }
            }else{
                unset($find[0][$i]);
            }
        }
        unset($find[1]);
        $find[0]=array_values($find[0]);
        $replace=array_values($replace);
        $content= str_replace($find[0],$replace,$layout);
        return preg_replace($this->regex['block'],'',$content);
    }

    /**递归解析模板中的 {%include@name%}标签
     * @param $content string
     * @return string
     */
    protected function parseInclude($content){
        $matches=[];
        if(preg_match_all($this->regex['include'],$content,$matches) ==0 ){
            return $content;
        }
        //$matches=array_combine($matches[1],$matches[0]);
        $replace=[];
        foreach ($matches[1] as $v){
            $template=$this->getPath($v);
            $this->includeFile[$template] = filemtime($template);
            $replace[]=file_get_contents($template);
        }
        //exit();
        return $this->parseInclude(str_replace($matches[0],$replace,$content));
    }
    /**
     * 检查模板编译缓存是否有效
     * 如果无效则需要重新编译
     * @access private
     * @param  string $cacheFile 缓存文件名
     * @return boolean
     */
    private function checkCache($cacheFile)
    {
        if ($this->config['debug'] || !$this->config['is_cache'] || !is_file($cacheFile) || !$handle = @fopen($cacheFile, "r")) {
            return false;
        }
        // 读取第一行
        preg_match('/\/\*(.+?)\*\//', fgets($handle), $matches);
        if (!isset($matches[1])) {
            return false;
        }
        $includeFile = unserialize($matches[1]);
        if (!is_array($includeFile)) {
            return false;
        }
        // 检查模板文件是否有更新
        foreach ($includeFile as $path => $time) {
            if (is_file($path) && filemtime($path) > $time) {
                // 模板文件如果有更新,有则缓存也要更新
                return false;
            }
        }
        // 检查编译存储是否有效
        return $this->checkFile($cacheFile, $this->config['cache_time']);
    }

    /**
     * 读取文件缓存，读取时同时传入变量
     * @access private
     * @param  string  $cacheFile 缓存的文件名
     * @param  array   $vars 变量数组
     * @return void
     */
    protected function readFile($cacheFile,$vars=[])
    {
        if (!empty($vars) && is_array($vars)) {
            // 模板阵列变量分解成为独立变量
            extract($vars, EXTR_OVERWRITE);
        }
        //载入模版缓存文件
        include $cacheFile;
    }
    /**
     * 把内容写入到缓存文件中
     * @access private
     * @param  string $cacheFile 缓存的文件名
     * @param  string $content 内容
     * @return void|array
     */
    protected function writeFile($cacheFile, $content)
    {
        // 检测模板目录
        $dir = dirname($cacheFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        // 生成模板缓存文件
        if (false === file_put_contents($cacheFile, $content)) {
            $this->error(1,$cacheFile);
        }
    }
    /**
     * 检查缓存文件是否有效
     * @access private
     * @param  string  $cacheFile 缓存的文件名
     * @param  int     $cacheTime 缓存时间
     * @return boolean
     */
    private function checkFile($cacheFile, $cacheTime)
    {
        // 缓存文件不存在, 直接返回false
        if (!file_exists($cacheFile)) {
            return false;
        }
        if (0 != $cacheTime && time() > filemtime($cacheFile) + $cacheTime) {
            // 缓存是否在有效期
            return false;
        }
        return true;
    }

    /**
     * 过滤变量
     * @param string $var
     * @return string
     */
    public function e($var){
            return strip_tags ( (string) $var);
    }

    /**
     * 在模板中使用，用于插入另一个模板的内容
     * @param $template：模板名称
     */
    public function insert($template){
        include $this->getPath($template);
    }

    /**
     * 出错处理
     * @param int $code:错误编号
     * @param string $replenish:补充说明
     */
    protected function error($code=0 ,$replenish=''){
        $error=[
            0=>'',
            1=>'模板缓存文件写入失败，有可能是写入权限不足',
            2=>'模板文件不存在',
        ];
        header('Content-Type:text/html;charset=utf-8');
        die ($this->config['debug'] ? $error[$code].' : '.$replenish : $error[$code]);
    }

}