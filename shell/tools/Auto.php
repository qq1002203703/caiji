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


namespace shell\tools;

use core\Conf;
use shell\BaseCommon;

class Auto extends BaseCommon
{
    protected $task=[
        //['name'=>'学校发布','class'=>'\shell\caiji\Xuexiao','method'=>'start','class_param'=>['fabu'],'method_param'=>''],
        ['name'=>'知乎答案发布','class'=>'\shell\ctrl\Portal','method'=>'start','class_param'=>['crontab','-n zhihu'],'method_param'=>''],
        ['name'=>'sitemap生成','class'=>'\shell\tools\Sitemap','method'=>'start','class_param'=>['create','-a'],'method_param'=>''],
    ];
    public function __construct(array $param = [])
    {
        parent::__construct($param);
        $this->task=Conf::all('auto',[]);
    }

    public function run(){
        if(!$this->task){
            echo '没有待处理的任务'.PHP_EOL;
            return;
        }
        foreach ($this->task as $item){
            echo '正在处理任务:'.$item['name'].PHP_EOL;
            $this->callback($item['class'].'@'.$item['method'],[$item['method_param']],$item['class_param']);
        }
    }
}