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


namespace app\common\ctrl;


trait Wechat
{
    /* ------------------------------------------------------------------
     * 实例化一个EasyWeChat的公众号类
     * @return \EasyWeChat\OfficialAccount\Application
     *---------------------------------------------------------------------
     */
    protected function officialAccount(){
        return \EasyWeChat\Factory::officialAccount(app('config')::get('wechat','system'));
    }
}