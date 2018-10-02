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
            'is_login'=>$this->_is_login(),
            'router'=>[
                'module'=>Router::$module,
                'ctrl'=>Router::$ctrl,
                'action'=>Router::$action,
            ]
        ]);
    }

    protected function _config(){
        //设置前台模板路径
        $template=app('config')::get('template','config');
        if($template['open_mobile_tpl'])
            $this->_mobile_detect(true);
        if(isset($_SESSION['is_mobile']) && $_SESSION['is_mobile']){
            $this->view->config([
                'path' => ROOT .  DIRECTORY_SEPARATOR .trim($template['mobile_path'],'/'). DIRECTORY_SEPARATOR. app('config')::get('template_mobile','site'). DIRECTORY_SEPARATOR ,
                'cache_path' => ROOT .  DIRECTORY_SEPARATOR .trim($template['cache_path_mobile'],'/'). DIRECTORY_SEPARATOR,
            ] );
        }else{
            $this->view->config([
                'path' => ROOT .  DIRECTORY_SEPARATOR .trim($template['view_path'],'/'). DIRECTORY_SEPARATOR. app('config')::get('template','site'). DIRECTORY_SEPARATOR ,
                'cache_path' => ROOT .  DIRECTORY_SEPARATOR .trim($template['cache_path'],'/').  DIRECTORY_SEPARATOR ,
            ] );
        }
    }

    public function __call($name, $arguments){
        show_error('类"'.__CLASS__.'"中不存在"'.$name.'()"方法');
    }
    /**
     * 跳转
     * @param $url string:跳转目标地址
     * @param $msg string：显示信息
     * @param $code int：展示样式，1为绿色，2为红色，其他数字蓝色，默认为1绿色,
     * @param $wait int:等待多少秒后才跳转，默认3秒
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
     * @param  mixed $name  要显示的模板变量
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
}