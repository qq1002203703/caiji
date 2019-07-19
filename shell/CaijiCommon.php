<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 *采集公共类
 * ======================================*/
namespace shell;
use core\Conf;
use extend\Curl;
use extend\Helper;
abstract  class  CaijiCommon extends BaseCommon
{
    /** ------------------------------------------------------------------
     * 简单的http请求器
     * @param $url
     * @param array $data
     * @return mixed|string
     *---------------------------------------------------------------------*/
    protected function http($url,$data=[],$encoding='GBK'){
        $curl=new Curl();
        $res=$curl->get($url);
        if(!$res){
            echo '采集网页失败:'.PHP_EOL;
            dump($data);
            dump($curl->getLastInfo());
            return false;
            //exit();
        }
        $res=$curl->encoding($res,$encoding);
        return $res;
    }

    /** ------------------------------------------------------------------
     * 读取采集规则
     * @param string $name 采集规则名
     * @param string $type  种类：page/content/download/fabu
     * @param null|string $customOpt 自定义项 json格式字符串
     * @return array|bool
     *--------------------------------------------------------------------*/
    protected function getCaijiRules($name,$type,$customOpt=null){
        $type=strtolower($type);
        $caijiRule=Conf::get($type,$name,null,'config/caiji/');
        if(!$caijiRule){
            return false;
        }
        if($customOpt){
            $item_options=json_decode($customOpt,true);
            if(isset($item_options[$type])){
                $caijiRule=array_merge($caijiRule,$item_options[$type]);
            }
            unset($item_options);
        }
        $options=Conf::get('options',$name,null,'config/caiji/');
        if($options){
            $caijiRule=array_merge($options,$caijiRule);
            unset($options);
        }
        $caijiRule['name']=$name;
        $caijiRule['callback'].=ucfirst($type);
        $caijiRule['outType']=$this->outType;
        $caijiRule['runOnce']=$this->runOnce;
        $caijiRule['debug']=$this->debug;
        $caijiRule['startId']=$this->startId;
        $caijiRule['maxId']=$this->maxId;
        return $caijiRule;
    }

    /** ------------------------------------------------------------------
     * 采集处理
     * @param string $config 规则名
     * @param string $type 种类 包括 content,download,fabu
     * @param array $data 测试时的数据，提供此项表示进行的是测试，最少要提供url项的值,使用了插件的可能还要其他数据，如下面
     * [
    'url'=>'http://xuexiao.51sxue.com/detail/id_77973.html',
    'from_id'=>8314
    ]
     *---------------------------------------------------------------------*/
    protected function caiji($config,$type='content',$data=[]){
        $caijiRule=$this->getCaijiRules($config,$type,'');
        $this->dieEcho($caijiRule===false,'规则名不正确'.PHP_EOL);
        $callback=Helper::callback($caijiRule['callback'].'::create',[$caijiRule]);
        if(is_object($callback)) {
            if($data){
                echo '测试'.PHP_EOL;
                $callback->doTest($data);
            }else{
                $callback->start();
            }
        }else{
            echo '回调失败'.PHP_EOL;
        }
        echo '采集完成'.PHP_EOL;
    }


    protected function pageTest($caijiRule,$url){
        $caijiRule['url']=$url;
        $caijiRule['debug']=true;
        $callback=Helper::callback($caijiRule['callback'].'::create',[$caijiRule]);
        if(is_object($callback)) {
            $callback->start();
            exit();
        }else{
            echo '回调页面[page]采集类失败'.PHP_EOL;
            exit();
        }
    }
}