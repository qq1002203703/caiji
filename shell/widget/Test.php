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
 * 工具测试，具体干什么每次都重写
 * ======================================*/


namespace shell\widget;
use shell\BaseShell;
class Test extends BaseShell
{
    protected function _init()
    {
        parent::_init();
        $this->model=app('\app\weixinqun\model\Weixinqun');
        $this->prefix=\app\weixinqun\model\Weixinqun::$prefix;
    }
    public function outPut($msg, $important)
    {
        echo $msg;
    }

    //入口方法
    public function run(){
            //$start=(int)($this->param[1] ?? 0);
            $this->doLoop([
                //create_time > 1483200000
                'sql'=>'select * from '.$this->prefix.$this->model->table.' where caiji_isdone=0',
                'params'=>[]
            ],function($v){
                if($v['thumb'] !='' || $v['qrcode'] !='' || $v['qun_qrcode'] !=''){
                    $this->model->eq('id',$v['id'])->update(['caiji_isdone'=>1,'have_img'=>1]);
                    echo 'code 100: img is exists!'.PHP_EOL;
                    return 100;
                }else{
                    $this->model->eq('id',$v['id'])->update(['caiji_isdone'=>1]);
                    echo 'do well'.PHP_EOL;
                    return 0;
                }
            },[
                'from'=>$this->model->table,
                'where'=>[['caiji_isdone','eq',0]]
            ]);

    }
}