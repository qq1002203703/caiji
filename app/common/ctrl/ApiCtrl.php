<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 *
 * QQ 46502166
 *
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 *========================================================================================
 * Api的基础控制器，所有和Api相关的Ctrl，都直接或间接继承它
 *========================================================================================*/

namespace app\common\ctrl;

class ApiCtrl
{
    public function __call($name, $arguments)
    {
		show_error('类"'.__CLASS__.'"中不存在"'.$name.'()"方法');
    }

    public function __construct()
    {
        //初始化
        if(method_exists($this,'_init'))
        {
            $this->_init();
        }
    }

}