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
 * 最基础的控制器：除Api相关的控制器外，其他的所有控制器都直接或间接继承它
 *========================================================================================*/

namespace core;
class Ctrl
{
    protected $view;

    public function __construct()
    {
        $this->view=app('view',[['debug'=>DEBUG]]);
        //默认初始化设置
        $this->_config();
		//额外初始化
        if(method_exists($this,'_init')) {
            $this->_init();
        }
		//向模板提交必要的变量
        $this->_assign(app('config')::all('site'));
        $this->_assign([
            'isLogin'=>$this->_is_login(),
            'isAdmin'=>$this->_checkIsAdmin(),
			'host'=>$this->_getHost(),
            'router'=>[
                'module'=>Router::$module,
                'ctrl'=>Router::$ctrl,
                'action'=>Router::$action,
            ]
        ]);
    }

    protected function _config(){
        //获取配置
        $template=Conf::get('template','config');
        $siteConfig=Conf::all('site');
        //自动检测是否移动客户端
        $is_mobile_domain=($siteConfig['mobile_domain']===$_SERVER['HTTP_HOST']);
        if($siteConfig['is_detect_mobile']){
            unset($_SESSION['is_mobile']);
            $is_mobile=isset($_SESSION['is_mobile']) ? $_SESSION['is_mobile'] : $this->_mobile_detect(true);
            if($is_mobile && (!$is_mobile_domain)){
                header('Location: '.$_SERVER['REQUEST_SCHEME'].'://'.$siteConfig['mobile_domain'].($_SERVER['SERVER_NAME']==80 ? '' : ':'.$_SERVER['SERVER_PORT'] ).$_SERVER['REQUEST_URI']);
                exit();
            }elseif(!$is_mobile && $is_mobile_domain){
                header('Location: '.$siteConfig['site_url'].($_SERVER['SERVER_NAME']=='80' ? '' : ':'.$_SERVER['SERVER_PORT'] ).$_SERVER['REQUEST_URI']);
                exit();
            }
        }
        //根据域名不同对应不同的模板
        if($is_mobile_domain){
            $this->view->config([
                'path' => ROOT .  DIRECTORY_SEPARATOR .trim($template['mobile_path'],'/'). DIRECTORY_SEPARATOR. $siteConfig['template_mobile']. DIRECTORY_SEPARATOR ,
                'cache_path' => ROOT .  DIRECTORY_SEPARATOR .trim($template['cache_path_mobile'],'/'). DIRECTORY_SEPARATOR,
            ] );
        }else{
            $this->view->config([
                'path' => ROOT .  DIRECTORY_SEPARATOR .trim($template['view_path'],'/'). DIRECTORY_SEPARATOR. $siteConfig['template']. DIRECTORY_SEPARATOR ,
                'cache_path' => ROOT .  DIRECTORY_SEPARATOR .trim($template['cache_path'],'/').  DIRECTORY_SEPARATOR ,
            ] );
        }
    }

    public function __call($name, $arguments){
        if(DEBUG){
            header('HTTP/1.1 404 Not Found');
            header("status: 404 Not Found");
            header('Content-Type:text/html;charset=utf-8');
            die('不存在的方法');
        }
        $this->show404();
    }
    protected function show404(){
        header('HTTP/1.1 404 Not Found');
        header("status: 404 Not Found");
        $this->_display('common/404',[],false);
        exit();
    }
    /**
     * 跳转
     * @param string $url 跳转目标地址
     * @param string $msg 显示信息
     * @param int $code 展示样式，1为绿色，2为红色，其他数字蓝色，默认为1绿色,
     * @param int $wait 等待多少秒后才跳转，默认3秒
     */
    protected function _redirect($url,$msg,$code=1,$wait=3)
    {
        if(strpos($url,'http')===false){
            $url=explode('?',$url);
            $url=(count($url)>1) ? url($url[0],$url[1]) : url($url[0]);
        }
        $this->_display('common/redirect',[
            'msg'=>$msg,
            'url'=>$url,
            'code'=>$code,
            'wait'=>$wait
        ],false);
        exit();
    }

    /**
     * 模板变量赋值
     * @access protected
     * @param  string|array $name  要显示的模板变量
     * @param  mixed $value 变量的值
     * @return $this
     */
    protected function _assign($name, $value = '')
    {
        $this->view->assign($name, $value);
        return $this;
    }

    /**
     * 加载模板,模板内容不直接输出
     * @access protected
     * @param  string $file 模板路径，是相对于模板目录的路径，不包括后缀名
     * @param  array  $vars   关联数组 输出到模板的变量
     * @param  boolean  $is_auto  是否启用自动解析模板路径
     * @return string
     */
    protected function _fetch($file = '', $vars = [], $is_auto=true)
    {
        return $this->view->fetch($file, $vars, $is_auto);
    }
    /**
     * 加载模板,模板内容直接输出
     * @access protected
     * @param  string $file 模板路径，是相对于模板目录的路径，不包括后缀名
     * @param  array  $vars    输出到模板的变量
     * @param  boolean  $is_auto  是否启用自动解析模板路径
     * @return void
     */
    protected function _display($file = '', $vars = [],$is_auto=true)
    {
        echo $this->_fetch($file,$vars,$is_auto);
    }
    /**------------------------------------------------------------------
     * 加载模板 把内容缓存起来 最后输出模板渲染结果
     * @param  string $cacheFile 模板缓存完整路径
     * @param  string $file 模板文件，是相对于模板目录的路径文件，不包括后缀名
     * @param  array  $vars    输出到模板的变量
     * @param  boolean  $is_auto  是否启用自动解析模板路径
     *---------------------------------------------------------------------*/
    protected function display($cacheFile,$file = '', $vars = [],$is_auto=true){
        $content=$this->_fetch($file,$vars,$is_auto);
        \core\lib\cache\File::write($cacheFile,$content);
        echo $content;
    }

    //退出登陆
    protected function _logout()
    {
        $_SESSION=array();
        if(isset($_COOKIE[session_name()])){
            setcookie(session_name(),'',time()-3600,'/');
        }
        session_destroy();
    }

    /**
     * 判断是否已经登陆
     * @return bool
     */
    protected function _is_login()
    {
        if( isset($_SESSION['islogin']) && $_SESSION['islogin']){
            return true;
        }
        return false;
    }

    /** ------------------------------------------------------------------
     * 移动设备侦测，并把结果写入$_SESSION中
     * @param bool $is_session 是否写入SESSION中
     * @return bool
     *-------------------------------------------------------------------*/
    protected function _mobile_detect($is_session){
        $mobileDetect=app('\extend\MobileDetect');
        $is_mobile=$mobileDetect->isMobile();
        if($is_session){
            if($is_mobile){
                $_SESSION['is_mobile']=true;
                if($mobileDetect->is('wechat')){
                    $_SESSION['is_wechat']=true;
                }else{
                    $_SESSION['is_wechat']=false;
                }
            } else{
                $_SESSION['is_mobile']=false;
                $_SESSION['is_wechat']=false;
            }
        }
        return $is_mobile;
    }


    /** ------------------------------------------------------------------
     * 检测当前用户是否是管理员
     * @return bool
     *--------------------------------------------------------------------*/
    protected function _checkIsAdmin(){
        $gid=Session::get('user.gid',false);
        return ($gid && $gid <10);
    }

    /** ------------------------------------------------------------------
     * 读取详情页缓存内容
     * @param string $cacheFile 缓存文件完整路径
     * @param int $cacheTime 每次缓存的时间（单位秒）
     * @return bool
     *---------------------------------------------------------------------*/
    protected function _readCache($cacheFile,$cacheTime){
        // 检测：缓存是否存在并在有效期内
        if(\core\lib\cache\File::checkFile($cacheFile,$cacheTime)==false)
            return false;
        echo file_get_contents($cacheFile);
        return true;
    }

    /** ------------------------------------------------------------------
     * 对从$_GET或$_POST中获取的值进行的前处理
     * @param array $data  每一项的前三条是必须的，'f'表示过滤规则，'d'表示获取不到时的默认值，'w'表示where条件，'fi'表示对应的字段名（此条可以不提供，不提供时默认同键名相同）
     * 格式 :
     *   [
     *      'isend'=>['f'=>'int','d'=>0,'w'=>'eq'],
     *      'name'=>['f'=>'','d'=>'','w'=>'eq','fi'=>'caiji_name']
     *  ]
     * @param string $method 'get'或'post'
     * @return array 返回三项（用list()函数可以方便地捕获结果），
     *      第一项:array,是where,
     *      第二项: array,是get/post的变量和值的对应
     *      第三项:string,是第二项经http_build_query转换后的值
     *---------------------------------------------------------------------*/
    protected function _getQuery($data,$method='get'){
        $where=[];
        $map=[];
        foreach ($data as $k =>$item){
            $current=$method($k,$item['f'],$item['d']);
            if($item['w']){
                $where[]=[ $item['fi'] ?? $k,$item['w'],$current];
            }
            $map[$k]=$current;
        }
        return [$where,$map,http_build_query($map)];
    }
    /**------------------------------------------------------------------
     * 获取当前主机的域名和子域名
     * @return array
     *---------------------------------------------------------------------*/
    protected function _getHost(){
        $arr=explode('.',$_SERVER['SERVER_NAME']);
        $ret=[];
        $ret['domain']=array_pop($arr);
        $ret['domain']=array_pop($arr).'.'.$ret['domain'];
        $ret['sub']= implode('.',$arr);
        return $ret;
    }
}