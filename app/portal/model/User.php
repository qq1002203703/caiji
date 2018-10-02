<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 *
 * QQ 46502166
 *
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 *
 * ======================================*/


namespace app\portal\model;
use core\Model;

class User extends Model
{
    public $table='user';
    public $primaryKey = 'id';
    public $msg='';

    /**
     * 检查用户名在数据库中是否已经存在
     * @param $username string
     * @return bool：存在返回false,不存在返回真
     */
    public function checkUsername($username){
        return ($this->eq('username',$username)  ->find()===false)?true:false;
    }
    /**
     * 检查email在数据库中是否已经存在
     * @param $email string
     * @return bool：存在返回false,不存在返回true
     */
    public function checkEmail($email){
        return ($this->eq('email',$email)  ->find()===false) ? true: false;
    }

    /**
     * 添加用户到数据中
     * @param $data：已经经过验证的数据
     * @return bool|int:成功添加返回新增的id,失败返回false
     */
    public function addUser($data){
        $this->username=$data['username'];
        $this->email=$data['email'];
        $this->password=password_hash($data['password'], PASSWORD_BCRYPT, array("cost" => 9));
        $this->gid=10;
        $this->last_login_time=time();
        $this->create_time=time();
        $this->avatar=(isset($data['avatar']) && $data['avatar'] !='')?$data['avatar']:'uploads/user/default.png';
        return $this->insert()===false? false:$this->id;
    }
}