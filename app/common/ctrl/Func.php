<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 控制器公用函数 全部为静态方法
 * ======================================*/

namespace app\common\ctrl;
use core\Session;
class  Func
{
    /** ------------------------------------------------------------------
     * 设置后台模板
     * @return array
     *--------------------------------------------------------------------*/
    static public function _setAdminTplPath(){
        return [
            'path' => ROOT .  DIRECTORY_SEPARATOR .trim(app('config')::get('template','config')['admin_path'],'/'). DIRECTORY_SEPARATOR ,
            'cache_path' => ROOT .  DIRECTORY_SEPARATOR .trim(app('config')::get('template','config')['admin_cache_path'],'/'). DIRECTORY_SEPARATOR  ,
        ];
    }

    /** ------------------------------------------------------------------
     * 设置用户的session
     * @param $ret
     *---------------------------------------------------------------------*/
    static public function _setUserSession($ret){
        Session::set('utime',time());
        Session::set('islogin',true);
        Session::set('username',$ret['username']);
        Session::set('uid',$ret['id']);
        //if(isset($ret['isAdmin']))
            //Session::set('isAdmin',$ret['isAdmin']);
        Session::set('user',[
            'id'=>$ret['id'],
            'username'=>$ret['username'],
            'gid'=>$ret['gid'],
            'score'=>$ret['score'],
            'coin'=>$ret['coin'],
            'balance'=>$ret['balance'],
            'nickname'=>$ret['nickname'],
            'status'=>$ret['status'],
            'email'=>$ret['email'],
            'avatar'=>$ret['avatar'],
            'phone'=>$ret['phone']
        ]);
    }

    /** ------------------------------------------------------------------
     * 设置token
     * @return string
     *--------------------------------------------------------------------*/
    public static function token(){
        $token=sha1(uniqid(mt_rand(),1));
        Session::set('__token__',$token);
        return $token;
    }

    /** ------------------------------------------------------------------
     * 调用一个shell脚本
     * @param string $cmd 完整shell命令
     * @return int 返回命令输出的第一行结果的整数，如果命令没有输出就直接返回-999
     *--------------------------------------------------------------------*/
    public static function callShell($cmd){
        ignore_user_abort(true);
        set_time_limit(0);
        if (intval(ini_get("memory_limit")) < 512) {
            ini_set('memory_limit', '512');
        }
        exec($cmd,$outPut);
        //dump($outPut);
        return isset($outPut[0])? (int)$outPut[0] : -999;
    }

}