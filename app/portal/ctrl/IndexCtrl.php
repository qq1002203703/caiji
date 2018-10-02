<?php
/**
 * Index控制器
 */
namespace app\portal\ctrl;
use app\portal\model\User;

class IndexCtrl extends \core\Ctrl
{
    /**---------------------------------------------
     * 首页
     *----------------------------------------------*/
    public function index()
    {
        $postModel=app('\app\portal\model\PortalPost');
        $currentPage=get('page','int',1);
        $perPage=15;
        $posts=$postModel->getPost('p.*,r.category_id',[],[($currentPage-1)*$perPage,$perPage]);
        $total = $postModel->getPostCout([]);
        $url = url('admin/portal/post').'?page=(:num)';
        $page=new \extend\Paginator($total,$perPage,$currentPage,$url);
        $this->_assign([
            'title'=>'网站首页',
            'posts'=>$posts,
            'page'=>(string)$page,
        ]);
        $this->_display();
    }
    /**---------------------------------------------------------------
     *  注册页
     *--------------------------------------------------------------*/
    public function reg(){
        if($this->_is_login()){
            $this->_redirect('portal/index/index','你已经是会员',2);
        }
        $this->_assign(['title'=>'用户注册']);
        $this->_display();
    }
    /**----------------------------------------------
     * 检查用户名是否已经被注册,返回json
     *-----------------------------------------------*/
    public function check_username(){
        $user=new \app\portal\validate\User();
        if($user->only(['username'])->check(['username'=>get('username','','')])){
            json(['code' => 0, 'msg' => '用名户通过验证']);
        }else{
            json(['code' => 1, 'msg' =>$user->getError()]);
        }
    }
    //检查邮箱是否符合规则,返回json
    public function check_email(){
        //var_dump(get('email','',''));
        $user=new \app\portal\validate\User();
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
        $is_admin=get('is_admin','','');
        if($this->_is_login()){
            if($is_admin){
                $this->_redirect('admin/user/index','你已经登陆',2);
            }else
                $this->_redirect('member/info','你已经登陆',2);
        }
        $title='用户登陆';
        if($is_admin){
            $title='后台登陆';
            $is_admin='?is_admin='.$is_admin;
        }
        $this->_assign(['title'=>$title,'is_admin'=>$is_admin]);
        $this->_display('index/login');
    }

    /**-------------------------------------------------------
     * 接收登陆页提交过来的数据，判断是否可以登陆
     *--------------------------------------------------------*/
    public function canlogin()
    {
        $username=post('username');
        $pwd=post('password');
        $is_admin=get('is_admin');
        $is_admin=$is_admin?'?is_admin='.$is_admin : '';
        if($username && $pwd){
            $m=new User();
            $ret=$m->eq('username',$username)->find(null,true);
            if($ret  &&  password_verify($pwd, $ret['password'])){
                $se=new \core\Session();
                $se->set('utime',time());
                $se->set('islogin',true);
                $se->set('user',[
                    'id'=>$ret['id'],
                    'username'=>$username,
                    'gid'=>$ret['gid'],
                    'score'=>$ret['score'],
                    'coin'=>$ret['coin'],
                    'balance'=>$ret['balance'],
                    'nickname'=>$ret['nickname'],
                    'status'=>$ret['status'],
                    'email'=>$ret['email'],
                    'avatar'=>$ret['avatar'],
                    'mobile'=>$ret['mobile']
                ]);
                $se->set('username',$username);
                $se->set('uid',$ret['id']);
                if($is_admin)
                    $this->_redirect('admin/user/index','成功登陆',1,2);
                else
                    $this->_redirect('portal/index/index','成功登陆',1,2);
            }
        }
        $this->_redirect('portal/index/login'.$is_admin,'密码或用户错误',2,4);

    }

    /**-----------------------------------------------
     *  保存注册信息，输出json格式提示
     *------------------------------------------------*/
    public function regsave_json(){
        $post=json_decode( file_get_contents("php://input"),true);
        $validate=new \app\portal\validate\User;
        if(!$validate->check($post) ){
            json(['code'=>1,'msg'=>$validate->getError()]);
            return;
        }
        $model=new \app\portal\model\User();
        if($model->addUser($post)!==false)
            json(['code'=>0,'msg'=>'成功注册']);
        else
            json(['code'=>1,'msg'=>'注册失败']);
    }

    /**--------------------------------------------------------
     * 退出登陆
     *---------------------------------------------------------*/
    public function logout()
    {
        $_SESSION=array();
        if(isset($_COOKIE[session_name()])){
            setcookie(session_name(),'',time()-3600,'/');
        }
        session_destroy();
        $this->_redirect('/','安全退出');
    }

    /**----------------------------------------------------
     * 外链中转器
     *-----------------------------------------------------*/
    public function outlink(){
        $url=get('url');
        if($url){
            $this->_redirect(urldecode($url),'小心：你正在访问一个非本站的链接！',2,3600);
        }else{
            $this->_redirect('/','返回首页',1,0);
        }
    }

    /** ------------------------------------------------------------------
     * 发布接口
     *---------------------------------------------------------------------*/
    public function locoy(){
        if(trim(get('pwd')) !== 'Djidksl$$EER4ds58cmO')
            die('密码错误');
        if(($is_cate=get('is_cate','int',0))==1){
            //$cateModel=app('\app\portal\model\PortalCategory');
            //echo $cateModel->getTreeSelect();
            exit();
        }
        /**数据项
         'title'=>$v['标题'],*
        'excerpt'=>$v['内容'],
        'cate_name'=>$v['分类'],*
        'city_name'=>$v['地区'],*
        'tags'=>$v['标签'],*
        'weixinhao'=>$v['微信号'],
        'qun_qrcode'=>$v['群二维码'],
        'qrcode'=>$v['群主二维码'],
        'create_time'=>$v['时间'],*
        'source'=>$v["PageUrl"],
        'thumb'=>$v['缩略图']
         */
        $data=$this->get_post(['title','cate_name','city_name','tags','create_time','qun_qrcode','qrcode','thumb','source','uid']);
        if(!$data['title']) die('标题不能为空');
        if(!$data['source']) die('来源不能为空');
        /**默认参数设置-----------------------------------*/
        if(!$data['uid']) $data['uid']=1;
        $data['create_time']=$this->get_date($data['create_time']);
        $data['published_time']= $data['create_time'];
        $data['update_time']=$data['create_time'];
        $data['caiji_iscaiji']=1;
        $data['caiji_isfabu']=1;
        $data['caiji_isdown']=1;
        $data['caiji_isdone']=1;
        /**--------------------------------------------------*/
        $model=app('\app\weixinqun\model\Weixinqun');
        if($ret=$model->eq('source',$data['source'])->find(null,true)){
            //$this->fabu_imags($ret,$ret['id'],$model);
            //删除图片
            echo '发布成功';
        }else{
            $data['category_id']=$model->getCategoryId($data['cate_name'],1);
            $data['city_id']=$model->getCityId($data['city_name'],1);
            $id=$model->add($data);
            if($id > 0){
                //处理图片
                $this->fabu_imags($data,$id,$model);
                echo '发布成功';
            }else
                echo '插入失败';
        }

    }

    /** ------------------------------------------------------------------
     * 火车头接口：内容过虑器
     * @param $content
     * @return string
     *--------------------------------------------------------------------*/
    protected function replace_content($content){
        return preg_replace([
            '/<div[^<>]*?>([\s\S]+?)<\/div>/i',
            '/(<div[^<>]*?>)|(<\/div>)/i',
            '/<p[^<>]*?>/i'
        ],[
            '<p>$1</p>',
            '',
            '<p>'
        ],$content);
    }
    public function caiji2database($data){
        /**数据项
        'excerpt'=>$v['内容'],
        'category_id'=>$v['分类'],*
        'city_id'=>$v['地区'],*
        'tags'=>$v['标签'],*
        'weixinhao'=>$v['微信号'],
        'qun_qrcode'=>$v['群二维码'],
        'qrcode'=>$v['群主二维码'],
        'create_time'=>$v['时间'],*
        'thumb'=>$v['缩略图']
         */
        /**默认参数设置-----------------------------------*/
        $data['uid']=1;
        $data['create_time']=$this->get_date($data['create_time']);
        $data['published_time']= $data['create_time'];
        $data['update_time']=$data['create_time'];
        /**--------------------------------------------------*/
        $model=app('\app\weixinqun\model\Weixinqun');
        //添加标签
        if($data['tags']){
            $model->addTagsMap($data['tags'],$data['id']);
        }
        $data['category_id']=$model->getCategoryId($data['category_id'],1);
        $data['city_id']=$model->getCityId($data['city_id']);
        $ret=$model->eq('id',$data['id'])->update($data);
        if($ret > 0){
            return '发布成功';
        }else
            return '没有更新';
    }
    /** ------------------------------------------------------------------
     * 自动处理$_POST数据
     * @param array $require
     * @return array
     *---------------------------------------------------------------------*/
    protected function get_post($require=array()){
        $data=[];
        if($_POST){
            foreach ($_POST as $k =>$v){
                $data[$k]=trim($v);
            }
        }
        foreach ($require as $v){
            $data[$v]=$data[$v] ?? '';
        }
        unset($_POST);
        return $data;
    }

    /** ------------------------------------------------------------------
     * fabu_imags
     * @param $data
     * @param $id
     * @param \app\weixinqun\model\Weixinqun $model
     *---------------------------------------------------------------------
     */
    protected function fabu_imags($data,$id,$model){
        $date_path=date('Y/m/d/',strtotime($data['create_time']));
        $change=false;
        if($data['qrcode']){
            $data['qrcode']=$this->changPath($date_path,$data['qrcode']);
            $change=true;
        }
        if($data['thumb']){
            $data['thumb']=$this->changPath($date_path,$data['thumb']);
            $change=true;
        }
        if($data['qun_qrcode']){
            $data['qun_qrcode']=$this->changPath($date_path,$data['qun_qrcode']);
            $change=true;
        }
        if($change){
            //更新数据库
            $model->eq('id',$id)->update([
                'qrcode'=>$data['qrcode'],
                'thumb'=>$data['thumb'],
                'qun_qrcode'=>$data['qun_qrcode']
            ]);
        }

       /* $new_data=[ 'thumb'=>'', 'qrcode'=>'','qun_qrcode'=>''];
        //群二维码
        $arr=array_unique( ['qun_qrcode'=>$data['qun_qrcode'], 'qrcode'=>$data['qrcode']] );
        switch (count($arr)){
            case 1:
                if($data['qrcode']){
                    $new_data['qrcode']=$new_data['qun_qrcode']=$this->changPath($date_path,$data['qrcode']);
                }
                break;
            case 2:
                if($data['qrcode'])
                    $new_data['qrcode']=$this->changPath($date_path,$data['qrcode']);
                if($data['qun_qrcode'])
                    $new_data['qun_qrcode']=$this->changPath($date_path,$data['qun_qrcode']);
        }
        //缩略图
        if($data['thumb']){
            $new_data['thumb']=$this->changPath($date_path,$data['thumb']);
        }
        //更新内容
        if($new_data != ['thumb'=>$data['thumb'], 'qrcode'=>$data['qrcode'], 'qun_qrcode'=>$data['thumb']]){
            $model->eq('id',$id)->update($new_data);
        }*/
    }

    /** ------------------------------------------------------------------
     * 获取在此随机天数前的日期
     * @param $str_time
     * @return bool|string
     *---------------------------------------------------------------------*/
    protected function get_date($str_time){
        $time=strtotime($str_time);
        if($time===false){
            return date('Y-m-d H:i:s', strtotime('-'.mt_rand(3,30).' days'));
        }else{
            return date('Y-m-d H:i:s',$time-mt_rand(3600*24*3,3600*24*25));
        }
    }

    /** ------------------------------------------------------------------
     * 更改文件路径
     * @param string $date_path
     * @param string $file
     * @return string
     *--------------------------------------------------------------------*/
    protected function changPath($date_path,$file){
        $path1=ROOT.'/public/uploads/tmp/';
        $path2=ROOT.'/public/uploads/images/';
        if(is_file($path1.$file)){
            $name=basename ($file);
            if($file==$date_path.$name)
                return $file;
            $new_path=$path2.$date_path;
            if(!is_dir($new_path)){
                if(!mkdir($new_path,0755,true)){
                    dump($new_path);
                    die('文件夹没有写入权限'.PHP_EOL);
                }
            }
            $image=new \extend\ImageResize();
            if($image->checkImage($path1.$file)){
                $image->add()->resizeToBestFit(250,250)->save($path2.$date_path.$name);
                unlink($path1.$file);
                return $date_path.$name;
            }else{
                echo ' ----do fail:'.$path1.$file.' ;message: '.$image->getMsg().PHP_EOL;
                return '';
            }
           /* if(rename($path.$file,$path.$date_path.$name))
                return $date_path.$name;
            else
                return '';*/
        }else{
            return '';
        }
    }


    public function test(){
        dump(basename(str_replace('\\','/',get_class())));
    }


}


