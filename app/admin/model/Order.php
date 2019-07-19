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
use core\Session;

class Order extends Model
{
    public $table='order';
    public $primaryKey='id';
    public $type='shop';

    /** ------------------------------------------------------------------
     * 订单入库前验证
     * @param array $data
     * @param string $msg 错误提示
     * @param bool $login 是否是登陆状态
     * @param bool $token 是否验证token
     * @return bool
     *--------------------------------------------------------------------*/
    public function check(&$data,&$msg,$login=true,$token=true){
        $msg='';
        if($token){
            if(!isset($data['token']) || (empty($data['token']) && '0' != $data['token'])){
                $msg='令牌不能为空';
                return false;
            }
        }
        if(!$login){
            if(!isset($data['email']) || (empty($data['email']) && '0' != $data['email'])){
                $msg='email不能为空';
                return false;
            }
        }
        $validate=app('\app\admin\validate\Order');
        if(!$validate->check($data)){
            $msg=$validate->getError();
            return false;
        }
        return true;
    }

    /** ------------------------------------------------------------------
     * 添加订单
     * @param array $post
     * @param string $msg 同check()方法的$msg
     * @param string $table 数据表
     * @param bool $login 同$this->check()方法的$login
     * @param bool $token 同$this->check()方法的$token
     * @return array 添加成功返回刚刚插入的订单的数据，失败时返回空数组
     *--------------------------------------------------------------------*/
    public function add($post,&$msg,$table,$login=true,$token=true){
        $data=[];
        if($this->check($post,$msg,$login,$token)){
            //查询oid对应的数据
            $result=$this->from($table)->eq('id',$post['oid'])->find(null,true);
            if($result){
                //生成订单
                $data=[
                    'price'=>$result['money'],
                    'oid'=>$result['id'],
                    'type'=>$this->type,
                    'buy_num'=>$post['buy_num'],
                    'email'=>$post['email'] ?? '',
                    'uid'=>$login ? Session::get('uid') : 0,
                    'title'=>$result['title'],
                    'thumb'=>$result['thumb'] ?? '',
                    'total'=>round($result['money']*$post['buy_num'],2),
                    'create_time'=>time(),
                    'status'=>0,
                    'content'=>$result['content']
                ];
                $data['id']=$this->insert($data);
                if($data['id'] < 1){
                    $msg='插入订单失败，原因未明';
                    $data=[];
                }
            }else{
                $msg='不存在的商品id';
            }
        }
        return $data;
    }

    public function search(){

    }

}