<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 财务相关数据操作
 * ======================================*/

namespace app\admin\model;

use core\Conf;
use core\Model;
class Finance extends Model
{
    /*protected $type=[0=>'充值',1=>'扣款'];
    protected $currency=[0=>'金钱',1=>'金币'	];
    protected $business=[
        0=>'卡密充值',
        1=>'会员升级',
        100=>'一级提成',
        101=>'二级提成',
        102=>'三级提成'
    ];*/

    /** ------------------------------------------------------------------
     * 通过卡密升级会员组
     * @param int $uid 用户id
     * @param array $kami 卡密数据
     * @param string $msg 返回提示信息
     * @return bool
     *--------------------------------------------------------------------*/
    public function kami_update_group($uid,$kami,&$msg=''){
        //充值会员组
        $group=10+(int)str_replace('vip_','',$kami['name'])+1;
        //用户数据获取充值时间
        $userData=$this->from('user')->select('activation_time,gid')->eq('id',$uid)->find(null,true);
        if($userData){
            if($userData['gid'] >=14){
                $msg= '你已经是永久会员无需再升级会员';
                return false;
            }
            $gid=$group>$userData['gid'] ? $group : $userData['gid'];
            //每个会员组对就增加时间
            $gArr=[11=>24*3600,12=>24*3600*30,13=>24*3600*30*365,14=>24*3600*30*365*100];
            $now=time();
            //会员到期时间计算
            $activation_time=($now>$userData['activation_time']) ? ($now+$gArr[$group]) : ($userData['activation_time']+$gArr[$group]);
            if($this->from('user')->eq('id',$uid)->update(['gid'=>$gid,'activation_time'=>$activation_time])){
                $this->log($uid,$kami['value'],'卡密充值:'.$kami['text'],0,0,0,$now);
                $this->log($uid,$kami['value'],'会员升级：'.$kami['text'],1,0,1,$now);
                $this->ticheng($uid,$kami['value'],$kami['currency']);
                $msg='已通过卡密成功升级会员';
                return true;
            }
        }
        $msg = '升级失败，请输入正确的会员id';
        return false;
    }

    /** ------------------------------------------------------------------
     * 通过卡密直接充值
     * @param int $uid 用户id
     * @param array $kami 卡密数据
     * @param string $msg 返回提示信息
     * @return bool
     *--------------------------------------------------------------------*/
    public function kami_recharge($uid,$kami,&$msg=''){
        $currencyArr=['balance','coin'];
        if($this->setField($currencyArr[$kami['currency']],$kami['value'],[['id','eq',$uid]],self::$prefix.'user','+')){
            $this->log($uid,$kami['value'],'卡密充值:'.$kami['text'],0,$kami['currency'],0);
            $this->ticheng($uid,$kami['value'],$kami['currency']);
            $msg='已通过卡密成功充值';
            return true;
        }
        $msg = '充值失败，请输入正确的会员id';
        return false;
    }

    /** ------------------------------------------------------------------
     * 上级提成处理
     * @param int $uid 当前用户id
     * @param float $amount 数量
     * @param int $currency 金钱为0，金币为1
     * @return int 返回处理的个数
     *---------------------------------------------------------------------*/
    public function ticheng($uid,$amount,$currency){
        $fenxiao=Conf::get('fenxiao','site');
        $count=0;
        if($fenxiao && isset($fenxiao['level']) && $fenxiao['level']>0){
            $pid=$uid;
            $currencyArr=['balance','coin'];
            for ($i=0;$i<$fenxiao['level'];$i++){
                $pid=$this->_sqlField('pid','select pid from '.self::$prefix.'user '.' where id=?',[$pid],false);
                $ticheng=round($amount*$fenxiao['ticheng'],2);
                if($this->setField($currencyArr[$currency],$ticheng,[['id','eq',$pid]],self::$prefix.'user','+')){
                    $count++;
                    //财务变动流水账写入
                    $this->log($pid,$ticheng,($i+1).'级下线会员提成',0,$currency,100+$i);
                }
            }
        }
        return $count;
    }

    /** ------------------------------------------------------------------
     * 账户流水写入
     * @param int $uid
     * @param float $amount
     * @param string $text
     * @param int $type
     * @param int $currency
     * @param int $business
     * @param int $time
     * @param int $status
     * @return bool|int|string
     *---------------------------------------------------------------------*/
    public function log($uid,$amount,$text,$type,$currency,$business,$time=0,$status=1){
        return $this->insert([
            'uid'=>$uid,
            'amount'=>$amount,
            'type'=>$type,
            'currency'=>$currency,
            'business'=>$business,
            'status'=>$status,
            'text'=>$text,
            'create_time'=>$time?:time()
        ]);
    }

}