<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 *
 * ======================================*/

namespace app\api\ctrl;

use core\Conf;

class BilibiliCtrl
{
    //接口访问密码
    protected $pwd='';
    protected function _init(){
        $this->pwd=Conf::get('pwd','config');
        $this->checkPermissions();
    }

    /** ------------------------------------------------------------------
     * 接口权限检测
     *--------------------------------------------------------------------*/
    protected function checkPermissions(){
        if (trim(post('pwd')) !== $this->pwd){
            json(['code'=>100,'msg'=>'Fuck You!']);
            exit();
        }
    }
    /** ------------------------------------------------------------------
     * 采集搜索，需要提供
     * 'app_name'=>'caiji.my',
     * 'app_keywords'=>'恒耀平台,恒耀娱乐',
     *  'search_type'=>'video',
     * 'search_keyword'=>'吃货',
     * 'search_max_page'=>4,
     * 'title_keywords'=>'视频,视觉,xx0oo,恒耀平台,恒耀娱乐,恒耀',
     * 'is_comment'=>0 #是否必须带评论
     *---------------------------------------------------------------------*/
    public function spider_search(){


    }

    /** ------------------------------------------------------------------
     * 采集视频，需要提供
     * 'app_name'=>'caiji.my',
     * 'app_keywords'=>'恒耀平台,恒耀娱乐',
     * 'aid'=>456566,
     * 'title_keywords'=>'视频,视觉,xx0oo,恒耀平台,恒耀娱乐,恒耀',
     * 'comment_min'=>0
     *---------------------------------------------------------------------*/
    public function spider_video(){
        if(!$this->check([
            'app_name'=>true,
            'app_keywords'=>true,
            'aid'=>true,
            'title_keywords'=>true,
            'comment_min'=>false
        ],$msg)){
            json(['code'=>1,'msg'=>$msg]);
            return false;
        }
        if(!$this->saveOption([
            'app_name'=>$_POST['app_name'],
            'app_keywords'=>explode(',',$_POST['app_keywords']),
            'aid'=>$_POST['aid'],
            'title_keywords'=>$_POST['title_keywords'],
            'is_comment'=>$_POST['is_comment']
        ],$_POST['app_name'])){
            json(['code'=>2,'msg'=>'配置文件无法写入']);
            return false;
        }
        $ret=$this->shell('getVideo -n '.$_POST['app_name']);
        if($ret!=='0'){
            json(['code'=>3,'msg'=>$ret]);
            return false;
        }
        json(['code'=>0,'msg'=>'susses']);
        return true;
    }
    /** ------------------------------------------------------------------
     * 采集评论，需要提供
     * 'aid'=>123456,
     * 'app_keywords'=>'恒耀平台,恒耀娱乐',
     * 'app_name'=>0 #是否必须带评论
     *---------------------------------------------------------------------*/
    public function spider_comment(){
        //from_id 即aid
        //comment_max_page

    }
    /** ------------------------------------------------------------------
     * 需要提供
     * 'app_name'=>'caiji.my',
     * 'publish_max_num'=>10,
     *---------------------------------------------------------------------*/
    public function publish_all(){

    }
    /** ------------------------------------------------------------------
     * 需要提供
     * 'app_name'=>'caiji.my',
     * aid=>123456
     *---------------------------------------------------------------------*/
    public function publish_video(){

    }

    /** ------------------------------------------------------------------
     * 发布提供aid的评论
     * 需要提供
     * aid=>123456,
     * publish_num=>10 #发布数量
     *---------------------------------------------------------------------*/
    public function publish_comment_by_aid(){

    }

    public function publish_comment(){

    }

    /** ------------------------------------------------------------------
     * 用php命令行执行脚本
     * @param string $param
     * @return string
     *---------------------------------------------------------------------*/
    protected function shell($param){
        ignore_user_abort(true);
        set_time_limit(0);
        if (intval(ini_get("memory_limit")) < 512) {
            ini_set('memory_limit', '512');
        }
        $cmd='php '.ROOT.'/cmd caiji/bilibili '.$param;
        return exec($cmd);
    }

    /** ------------------------------------------------------------------
     * 保存配置
     * @param array $options
     * @param string $fileName
     * @return int|bool
     *---------------------------------------------------------------------*/
    protected function saveOption($options,$fileName){
        return Conf::write($options,$fileName,'config/bilibili/');
    }

    protected function check($options,&$msg){
        foreach ($options as $key =>$value){
            if(!isset($_POST[$key])){
                $msg=$key.':为必须参数';
                return false;
            }
            $_POST[$key]=trim($_POST[$key]);
            if($value && !$_POST[$key]){
                $msg=$key.':不能为空包括0';
                return false;
            }elseif (!$value and  $_POST[$key]===''){
                $msg=$key.':不能为空字符串';
                return false;
            }
        }
        return true;
    }

    public function test(){
        $_POST['a']='1';
        $_POST['b']=0;
        $_POST['c']='';
        dump($this->check([
            'a'=>true,
            'b'=>false,
            'c'=>true
        ],$msg));
        dump($msg);
    }
}