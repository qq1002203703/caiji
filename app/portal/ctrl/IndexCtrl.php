<?php
/**
 * Index控制器
 */
namespace app\portal\ctrl;
use app\common\ctrl\Func;
use core\Conf;
use core\Ctrl;
use extend\Captcha;
use extend\Helper;
use extend\HttpClient;

//use extend\Paginator;

class IndexCtrl extends Ctrl
{
    /**---------------------------------------------
     * 首页
     *----------------------------------------------*/
    public function index()
    {
		if( $homeModule=Conf::get('home_module','site')){
            $this->_display($homeModule.'/index',app('\app\\'.$homeModule.'\ctrl\IndexCtrl')-> index(),false);
            return ;
        }
        $postModel=app('\app\portal\model\PortalPost');
        //$bbsModel=app('\app\bbs\model\Bbs');
        //$tagModel=app();
        $this->_display('portal/index',[
            //'title'=>'网站首页',
            'articles'=>$postModel->search([['type','eq','article'],['status','eq',1]],10,'create_time desc,id desc'),
            //'goods'=>$postModel->search([['type','eq','goods'],['status','eq',1]],10,'create_time desc,id desc'),
            //'page'=>(string)$page,
            //'bbsData'=>$bbsModel->search([['status','eq',1],['type','eq',2]],'20','create_time desc,id desc',false,'bbs.create_time,bbs.comments_num,bbs.id,bbs.category_id,bbs.title'),
            //'groups'=>$postModel->from('category')->eq('pid',1)->order('counts desc,id desc')->limit(12)->findAll(true),
            'groups'=>$postModel->select('name,slug,counts,thumb')->from('category')->eq('type','portal_group')->order('counts desc,id desc')->limit(12)->findAll(true),
            'comments'=>$postModel->select('c.id,c.content,p.id as oid,table_name,title,c.create_time,p.type,c.pid,c.is_content,c.username,c.uid')->from('comment as c')->join('portal_post as p','c.oid=p.id')->eq('table_name','portal_post')->order('c.create_time desc,c.id desc')->limit(10)->findAll(true),
            //'topics'=>$postModel->from('tag')->eq('status',1)->order('create_time desc,id desc')->limit(10)->findAll(true),
        ],false);
    }

    /**----------------------------------------------
     * 检查用户名是否已经被注册,返回json
     *-----------------------------------------------*/
    public function check_username(){
        $user=app('\app\portal\validate\User');
        if($user->only(['username'])->check(['username'=>get('username','','')])){
            json(['code' => 0, 'msg' => '用名户通过验证']);
        }else{
            json(['code' => 1, 'msg' =>$user->getError()]);
        }
    }
    //检查邮箱是否符合规则,返回json
    public function check_email(){
        //var_dump(get('email','',''));
        $user=app('\app\portal\validate\User');
        if($user->only(['email'])->check(['email'=>get('email','','')])){
            json(['code' => 0, 'msg' => '邮箱通过验证']);
        }else{
            json(['code' => 1, 'msg' =>$user->getError()]);
        }
    }
    /**--------------------------------------------------------
     * 登陆页
     *--------------------------------------------------------*/
    public function login(){
        if($this->_is_login()){
            $this->_redirect('member/info','你已经登陆',2);
        }
        $title='用户登陆';
        $this->_display('portal/login',['title'=>$title,'reg_verify'=>(int)Conf::get('reg_verify','site')],false);
    }

    /**---------------------------------------------------------------
     *  注册页
     *--------------------------------------------------------------*/
    public function reg(){
        if($this->_is_login()){
            $this->_redirect('portal/index/index','你已经是会员',2);
        }
        $this->_display('portal/reg',['title'=>'用户注册','reg_verify'=>(int)Conf::get('reg_verify','site')],false);
    }

    /**-------------------------------------------------------
     * 登陆验证 : 必需的post参数: type,imagecode,password,以及跟type对应的('email','username','phone') 中的一种
     *--------------------------------------------------------*/
    public function login_verify(){
        $type=post('type');
        if(!$type || !in_array($type,['email','username','phone'])){
            json(['code'=>1,'msg'=>'登陆方式错误']);
            return ;
        }
        $imagecode=post('imagecode','','');
        if(!$imagecode || strtolower($_SESSION['captch']) !== strtolower($imagecode)){
            json(['code'=>1,'msg'=>'图形验证码错误']);
            return ;
        }
        $key=post($type);
        if(!$key){
            json(['code'=>1,'msg'=>$type.'错误']);
            return ;
        }
        $pwd=post('password');
        $model=app('app\portal\model\User');
        $ret=$model->eq($type,$key)->find(null,true);
        if(!$ret  ||  !password_verify($pwd, $ret['password'])){
            $logType=['username'=>'用户名','email'=>'邮箱','phone'=>'手机'];
            json(['code'=>1,'msg'=>'密码或'.$logType[$type].'错误']);
            return;
        }
        Func::_setUserSession($ret);
        json(['code'=>0,'msg'=>'成功登陆','action'=>url('portal/index/index')]);
    }

    /**-----------------------------------------------
     *  注册验证，成功后会保存注册信息到数据库：必须的post参数：
     *      username,password,repassword,imagecode,
     *  如果开启了验证方式还必须有下面的post参数
     *      type,phone_email,vercode
     *------------------------------------------------*/
    public function reg_verify(){
        $post=post();
        //检测验证码
        if(!$post['imagecode'] || strtolower($_SESSION['captch']) !== strtolower($post['imagecode'])){
            json(['code'=>1,'msg'=>'图形验证码错误']);
            return ;
        }
        unset($post['imagecode']);
        //检测验证方式
        $reg_verify=(int)Conf::get('reg_verify','site'); //0,1,2
        if($reg_verify>0){
            $type=$post['type'];
            unset($post['type']);
            if(!$type || !in_array($type,['email','phone'])){
                json(['code'=>1,'msg'=>'验证方式错误']);
                return ;
            }
            $verifyType=['email'=>'邮箱','phone'=>'手机'];
            //检测phone_email是否为空
            if(!$post['phone_email']){
                json(['code'=>1,'msg'=>$verifyType[$type].'不能为空']);
                return ;
            }
            //检测接收的验证码是否正确
            $user=app('\app\portal\model\User');
            if(! $user->checkReceiptCode($post['phone_email'],$post['vercode'],$type)){
                json(['code'=>1,'msg'=>$verifyType[$type].'接收的验证码不正确']);
                return ;
            }
            $post[$type]=$post['phone_email'];
            unset($post['phone_email']);
        }
        //全部数据合法性检测
        $validate=app('\app\portal\validate\User');
        if(!$validate->check($post) ){
            json(['code'=>1,'msg'=>$validate->getError()]);
            return;
        }
        //入库
        $model=app('\app\portal\model\User');
		$post['pid']=$this->getPid($model);
        if($model->addUser($post)!==false)
            json(['code'=>0,'msg'=>'成功注册,现在可以登陆了','action'=>url('portal/index/login')]);
        else
            json(['code'=>1,'msg'=>'注册失败']);
    }

    /**------------------------------------------------------------------
     * 根据域名获取pid
     * @param \app\admin\model\User $model
     * @return int
     *---------------------------------------------------------------------*/
    protected function getPid($model){
        $host=$this->_getHost();
        if(!$host['sub'])
            return 0;
        $parent=$model->select('id')->eq('sub_domain',$host['sub'])->find(null,true);
        return $parent ? $parent['id']  : 0;
    }
    /** ------------------------------------------------------------------
     * 手机或邮箱发送验证码:必须的post参数 imagecode,type,phone_email
     *---------------------------------------------------------------------*/
    public function sendcode(){
        //检测图形码是否正确
        $imagecode=post('imagecode','','');
        if(!$imagecode || strtolower($_SESSION['captch']) !== strtolower($imagecode)){
            json(['code'=>1,'msg'=>'图形验证码错误']);
            return ;
        }
        //type检测
        $type=post('type','','');
        $verifyType=['email'=>'邮箱','phone'=>'手机'];
        if(!$type || !in_array($type,['email','phone'])){
            json(['code'=>1,'msg'=>'验证方式错误']);
            return ;
        }
        $name=post('phone_email','','');
        //检测name值是否为空
        if(!$name){
            json(['code'=>1,'msg'=>$verifyType[$type].'不能为空']);
            return ;
        }
        $user=app('\app\portal\validate\User');
        if($type=='phone'){//电话合法性检测
            if(!$user->only(['phone'])->check(['phone'=>$name])){
                json(['code' => 1, 'msg' =>$user->getError()]);
                return;
            }
        }elseif ($type=='email'){//邮箱合法性检测
            if(! $user->only(['email'])->check(['email'=>$name])){
                json(['code' => 1, 'msg' =>$user->getError()]);
                return;
            }
        }
        unset($user);
        //检测是否重复提交
        $userModel=app('\app\portal\model\User');
        if(!$userModel->checkVirefyCode($type,$name)){
            json(['code' => 1, 'msg' =>$userModel->msg]);
            return;
        }
        $userModel->sendCode($type,$name);
        json(['code'=>0,'msg'=>'验证码已成功发送,请注意查收']);
    }

    /**--------------------------------------------------------
     * 退出登陆
     *---------------------------------------------------------*/
    public function logout()
    {
        $this->_logout();
        $this->_redirect('/','安全退出');
    }

    /**----------------------------------------------------
     * 外链中转器
     *-----------------------------------------------------*/
    public function outlink(){
        $url=get('url');
        $t=(int)get('t','int',3600);
        if($url){
            if($t<1){
                header('Location:'.Helper::unescape($url));
                exit();
            }else
                $this->_redirect(urldecode($url),'小心：你正在访问一个非本站的链接！',2,$t);
        }else{
            $this->_redirect('/','返回首页',1,0);
        }
    }
    /** ------------------------------------------------------------------
     * 输出图形验证码 : 可选get参数：w,h
     *--------------------------------------------------------------------*/
    public function captcha(){
        $width=get('w','int',150);
        $height=get('h','int',50);
        $captch=new Captcha();
        $captch->initialize([
            'width' => $width,     // 宽度
            'height' => $height,     // 高度
            'line' => false,    // 直线
            'curve' => true,    // 曲线
            'noise' => 2,       // 噪点背景
            'fonts' => []       // 字体
        ]);
        try{
            $captch->create();
            $captch->output(1);
        }catch (\Exception $e){
            echo  $e->getMessage();
        }
    }

    public function test(){
    }

}


