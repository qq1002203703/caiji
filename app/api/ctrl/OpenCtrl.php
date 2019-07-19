<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 任何人都可以访问的对外接口
 * ======================================*/


namespace app\api\ctrl;


use app\common\ctrl\ApiCtrl;
use extend\Helper;

class OpenCtrl extends ApiCtrl
{
    /** ------------------------------------------------------------------
    * 卡密检测接口，method方式为:post|get,参数如下：
    *  ka  string  卡密的内容    必须
    *---------------------------------------------------------------------*/
    public function kami_check(){
        $ka=trim(post('ka'))?:trim(get('ka'));
        if(!$ka)
            return json(['code'=>1,'msg'=>'不能为空']);
        $model=app('\app\admin\model\Kami');
        if($data=$model->getOne([['ka','eq',$ka],['status','lt',2]])){
            $currency=['元','金币'];
            return json(['code'=>0,'msg'=>'卡密有效，种类为“'.$data['text'].'”，价值为'.$data['value'].$currency[$data['currency']]]);
        }
        return json(['code'=>2,'msg'=>'无效的卡密！']);
    }

    /** ------------------------------------------------------------------
     * 卡密使用接口：method方式为:post|get,参数如下：
     *  ka  string  卡密的内容    必须
     *--------------------------------------------------------------------*/
    public function kami_use(){
        //判断是否已经登陆
        if(!$this->_is_login())
            return json(['code'=>1,'msg'=>'在使用卡密前，请先登陆！']);
        $ka=trim(post('ka'))?:trim(get('ka'));
        //判断是否为空
        if(checkIsEmpty($ka))
            return json(['code'=>1,'msg'=>'请输入正确的卡密']);
        $model=app('\app\admin\model\Kami');
        $where=[['ka','eq',$ka],['status','lt',2]];
        //获取卡密信息
        $data=$model->getOne($where);
        if(!$data)
            return json(['code'=>2,'msg'=>'卡密不存在或已经被使用，请更换其他卡密']);
        if(!$model->_where($where)->update(['status'=>2]))
            return json(['code'=>2,'msg'=>'卡密不存在或已经被使用，请更换其他卡密']);
        $data['type_type']=(int)$data['type_type'];
        //dump($data);
        $finance=app('\app\admin\model\Finance');
        $msg='';
        $res=false;
        if($data['type_type']==0)
            $res=$finance->kami_update_group($_SESSION['uid'],$data,$msg);
        else if($data['type_type']==1)
            $res=$finance->kami_recharge($_SESSION['uid'],$data,$msg);
        if($res)//成功
            return json(['code'=>0,'msg'=>$msg]);
        else{//失败
            //恢复卡密
            $model->eq('ka',$ka)->update(['status'=>$data['status']]);
            return json(['code'=>3,'msg'=>$msg]);
        }
    }

    public function go(){
        $url=get('l');
        if($url){
            header('Location:'.Helper::urldecode($url));
        }else{
            header('Content-Type:text/html;charset=utf-8');
            echo '链接不为空';
        }
    }

}