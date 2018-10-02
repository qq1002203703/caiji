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


namespace app\wechat\ctrl;
use EasyWeChat\Factory;
class IndexCtrl
{
    protected $config=[];
    protected $tmp=ROOT.'/cache/tmp.txt';
    public function __construct()
    {
        $this->config=app('config')::get('wechat','system');
    }

    public function wechat(){
        $app = Factory::officialAccount($this->config);
        $app->server->push(function ($message) {
            //var_export($message,true);
            file_put_contents($this->tmp,var_export($message,true));
            return '你好呀！';
        });
        $response = $app->server->serve();
        // 将响应输出
        $response->send();
    }
    
}