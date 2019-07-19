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

class Shopcart extends Model
{
    public $table='shopcart';
    public $primaryKey='id';
    public $type='shop';

    /** ------------------------------------------------------------------
     * 添加到购物车
     * @param int $id
     * @return bool|int
     *--------------------------------------------------------------------*/
    public function add($id){
        $data=$this->eq('oid',$id)->find(null,true);
        if($data){
            $ret=$this->eq('id',$data['id'])->update(['buy_num'=>$data['buy_num']+1]);
        }else{
            $ret=$this->insert([
                'type'=>$this->type,
                'oid'=>$id,
                'uid'=>Session::get('uid'),
                'buy_num'=>1,
            ]);
        }
        return $ret;
    }
}