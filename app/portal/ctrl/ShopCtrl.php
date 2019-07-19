<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 非登陆状态下的购物中心
 * ======================================*/


namespace app\portal\ctrl;

use core\Cookie;
use core\Ctrl;
use extend\Paginator;

class ShopCtrl extends Ctrl
{

    /** ------------------------------------------------------------------
     * 购物车
     *--------------------------------------------------------------------*/
    public function cart(){
        if($this->_is_login()){
            //转到portal/user/shopcart
            header(url('portal/user/shopcart'));
            return;
        }
        $result=Cookie::getJson('shop_cart',false);
        if($result){
            $ids=array_keys($result);
            $model=app('\app\portal\model\PortalPost');
            $data=$model->in('id',$ids)->findAll(true);
        }else{
            $data=false;
        }
        $this->_display('portal/shop_cart',[
            'title'=>'我的购物车',
            'data'=>$data,
            'buyNum'=>$result
        ],false);
    }

    /** ------------------------------------------------------------------
     * 添加到购物车  必须post参数 id
     *--------------------------------------------------------------------*/
    public function cart_add(){
        $id=(int)post('id','int',0);
        if($id<1){
            json(['code'=>1,'msg'=>'id格式不正确']);
            return;
        }
        $model=app('\app\portal\model\PortalPost');
        $data=$model->find($id,true);
        if(!$data){
            json(['code'=>2,'msg'=>'此id不存在']);
            return;
        }
        $this->_is_login() ? $this->cart_add_login($id) : $this->cart_add_nologin($id);
    }
    /** ------------------------------------------------------------------
     * 不登陆状态时的加入购物车
     * @param int $id
     *--------------------------------------------------------------------*/
    protected function cart_add_nologin($id){
        $cookie=Cookie::get('shop_cart',false);
        if($cookie){
            $cookie=json_decode($cookie,true);
            if($cookie==false){
                Cookie::del('shop_cart');
                $cookie=[];
                $cookie[$id]=1;
            }else{
                if(array_key_exists($id,$cookie)){ //查看是否已经有相同的产品
                    $cookie[$id]++;
                }else{
                    $cookie[$id]=1;
                }
            }
        }else{
            $cookie=[];
            $cookie[$id]=1;
        }
        Cookie::set('shop_cart',json_encode($cookie),3600*24*365,false);
        json(['code'=>0,'msg'=>'成功加入购物车']);
    }

    /** ------------------------------------------------------------------
     * 登陆状态时的加入购物车
     * @param int $id
     *--------------------------------------------------------------------*/
    protected function cart_add_login($id){
        $shopModel=app('\app\admin\model\Shopcart');
        if($shopModel->add($id))
            json(['code'=>0,'msg'=>'成功加入购物车']);
        else
            json(['code'=>0,'msg'=>'出错了']);
    }

    /** ------------------------------------------------------------------
     * 下订单
     *--------------------------------------------------------------------*/
    public function order(){
       /* dump(Func::token());
        return;*/
     /*  $post=[
            'oid'=>10,
            'email'=>'ss@aaa.com',
            'buy_num'=>10,
            'token'=>'9f3df3167f0a9c5d21f99cdcd5bf9bd4927fb735'
        ];*/
        $post=post();
        $model=app('\app\admin\model\Order');
        $data=$model->add($post,$msg,'portal_post',false,true);
        $this->_display('portal/shop_order',[
            'title'=>'支付页面',
            'data'=>$data,
            'msg'=>$msg
        ],false);
    }

    /** ------------------------------------------------------------------
     * 我的订单查询：可选get参数 email,status
     *--------------------------------------------------------------------*/
    public function myorder(){
        $email=get('email','','');
        $data=[];
        $page='';
        $totalWaiting=0;
        if($email){
            $url='?email='.$email;
            $where[]=['email','eq',$email];
            $status=(int)get('status','int',-1);
            if($status>=0){
                $where[]=['status','eq',$status];
                $url.='&status='.$status;
            }else{
                $url.='&status=-1';
            }
            $model=app('\app\admin\model\Order');
            $currentPage=get('page','int',1);
            $perPage=14;
            $url = url('portal/shop/myorder').$url.'&page=(:num)';
            $total=$model->count(['where'=>$where]);
            //待支付定单数
            $totalWaiting=$model->count([
                'where'=>[['status','eq',0],['email','eq',$email]]
            ]);
            $data=$model->_where($where)->limit(($currentPage-1)*$perPage,$perPage)->findAll(true);
            $page=(string) new Paginator($total,$perPage,$currentPage,$url,[
                'page'=>'laypage-main',
                'current'=>'laypage-curr',
                'next'=>'laypage-next',
                'disabled'=>'laypage-disabled'
            ]);
        }
        $this->_display('portal/shop_myorder',[
            'title'=>'订单查询',
            'email'=>$email,
            'totalWaiting'=>$totalWaiting,
            'data'=>$data,
            'page'=>$page
        ],false);
    }
}