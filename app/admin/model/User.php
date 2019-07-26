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


namespace app\admin\model;
use core\Model;
class User extends Model
{
    public $table='user';
    public $primaryKey='id';

    /** ------------------------------------------------------------------
     * 检测pid是否合法：一般用于入库前的验证
     * @param int $pid
     * @param int $id
     * @return bool pid为0时直接返回true,数据库存在时返回true,不存在时返回false
     *---------------------------------------------------------------------*/
    public function checkPid($pid,$id=0){
        if($pid==0) return true;
        $this->reset();
        if($id) {
            //编辑用户时排除子孙用户的id作父id
            $current=$this->getById($id,'path');
            $this->notlike('path', $current['path']);
        }
        return ($this->eq('id',$pid)  ->find()) !== false;
    }

    /**
     * 检查用户名在数据库中是否已经存在
     * @param $username string
     * @return bool 存在返回false,不存在返回真
     */
    public function checkUsername($username){
        return ($this->eq('username',$username)  ->find()) ===false;
    }

    /**
     * 检查email在数据库中是否已经存在
     * @param $email string
     * @return bool 存在返回false,不存在返回true
     */
    public function checkEmail($email){
        return ($this->eq('email',$email)  ->find())===false;
    }

    /**
     * 检查手机号在数据库中是否已经存在
     * @param $phone string
     * @return bool 存在返回false,不存在返回true
     */
    public function checkPhone($phone){
        return ($this->eq('phone',$phone)  ->find()) ===false;
    }

    /**
     * 添加用户到数据中
     * @param $data：已经经过验证的数据
     * @return int:成功添加返回新增的id,失败返回0
     */
    public function addUser($data,$gid=0){
        $data['pid']=$data['pid']??0;
        $id=$this->insert([
            'username'=>$data['username'],
            'email'=>$data['email']??'',
            'phone'=>$data['phone']??'',
            'password'=>password_hash($data['password'], PASSWORD_BCRYPT, array("cost" => 9)),
            'gid'=>($gid>1 ? $gid :10),
            'last_login_time'=>time(),
            'create_time'=>$data['create_time'] ?? time(),
            'avatar'=> $data['avatar'] ?? '',
            'signature'=>$data['signature']??'',
            'more'=>$data['more']??'',
            'birthday'=>$data['birthday'] ?? 0,
            'city'=>$data['city'] ?? '',
            'nickname'=>$data['nickname']??'',
        ]);
        //更新pid、path和level
        if($id){
            if($data['pid'] >0){
                $parent=$this->getById($data['pid'] ,'path,level');
                if(!$parent){
                    $data['pid']=0;
                    $parent=['path'=>'','level'=>0];
                }
            }else{
                $parent=['path'=>'','level'=>0];
            }
            $update['path']=$this->getPath($id,$data['pid'],$parent['path']);
            $update['level']=$this->getLevel($data['pid'],$parent['level']);
            $update['pid']=$data['pid'];
            $this->eq('id',$id)->update($update);
            return $id;
        }
        return 0;
    }
    public function addUserEx($data){
        $data=$this->_filterData($data);
        $data['pid']=$data['pid']??0;
        $data['password']=password_hash($data['password'], PASSWORD_BCRYPT, array("cost" => 9));
        $data['gid']=$data['gid']??10;
        $data['last_login_time']=time();
        $data['create_time']=$data['create_time'] ?? time();
        $id=$this->insert($data);
        //更新pid、path和level
        if($id){
            if($data['pid'] >0){
                $parent=$this->getById($data['pid'] ,'path,level');
                if(!$parent){
                    $data['pid']=0;
                    $parent=['path'=>'','level'=>0];
                }
            }else{
                $parent=['path'=>'','level'=>0];
            }
            $update['path']=$this->getPath($id,$data['pid'],$parent['path']);
            $update['level']=$this->getLevel($data['pid'],$parent['level']);
            $update['pid']=$data['pid'];
            $this->eq('id',$id)->update($update);
            return $id;
        }
        return 0;
    }

    /** ------------------------------------------------------------------
     * 获取层级关系路径
     * @param int $id
     * @param int $pid
     * @param string $parent_path
     * @return string
     *---------------------------------------------------------------------*/
    public function getPath($id,$pid,$parent_path){
        if($pid==0) return (string)$id;
        return $parent_path.'-'.$id;
        //$parent=$this->getById($pid,'path');
        //return $parent['path'].'-'.$id;
    }

    /** ------------------------------------------------------------------
     * 获取层级
     * @param int $pid
     * @param int $parent_level
     * @return int
     *---------------------------------------------------------------------*/
    public function getLevel($pid,$parent_level){
        if($pid==0) return 1;
        return $parent_level+1;
        //$parent=$this->getById($pid,'path');
        //return $parent['level']+1;
    }

    /** ------------------------------------------------------------------
     * 通过id获取用户数据
     * @param int $id
     * @param string $select
     * @return array|bool
     *--------------------------------------------------------------------*/
    public function getById($id,$select='*'){
        return $this->select($select)->eq('id',$id)->find(null,true);
    }

    public function getRandomUser($limit,$select='*'){
        return $this->_sql('SELECT '.$select.' FROM `'.self::$prefix.$this->table.'` WHERE id >= (SELECT floor(RAND() * (SELECT MAX(id) FROM `'.self::$prefix.$this->table.'` where status=1))) and status=1 ORDER BY id LIMIT '.$limit,[],false);
    }

    /** ------------------------------------------------------------------
     * 通过用户名获取用户id
     * @param string $username
     * @return int
     *--------------------------------------------------------------------*/
    public function getIdByName($username){
        $data=$this->select('id')->eq('username',$username)->find(null,true);
        return $data ? (int)$data['id'] : 0;
    }


}