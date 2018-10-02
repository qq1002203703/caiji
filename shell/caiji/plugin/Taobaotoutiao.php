<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 淘宝头条采集插件
 * ======================================*/

namespace shell\caiji\plugin;
use core\Conf;
class Taobaotoutiao
{
    protected $prefix;

    public function __construct()
    {
        $this->prefix=Conf::get('prefix','database');
    }
    /** ------------------------------------------------------------------
     * 列表页入库插件
     * @param array $data
     * @param array $fileRule
     * @param \core\Model $model
     * @param array $option
     * @return int
     *--------------------------------------------------------------------*/
    public function pageSave($data,$fileRule,&$model,$option){
        if(!$data){
            return 0;
        }
        $ishave=false;
        foreach ($data as $item){
            //检测重复
            if(!$item['url'])
                continue;
            $item['from_id']=$this->getFromId($item['url']);
            if($item['from_id']===false)
                continue;
            if($model->eq('from_id',$item['from_id'])->find(null,true))
                continue;
            $ishave=true;
            //插入内容表
            $model->insert([
                'url'=>$item['url'],
                'from_id'=>$item['from_id']
            ]);
            //插入page表
            $model->table='caiji_page';
            $urlMd5=md5($item['pageUrl']);
            if(! $model->eq('url_md5',$urlMd5)->find(null,true)){
                $model->insert([
                    'rule_id'=>$option['id'],
                    'url_md5'=>$urlMd5,
                    'type'=>1,
                    'url'=>$item['pageUrl'],
                    'update_time'=>time()
                ]);
            }
            $model->table='taobaotoutiao';
        }
        return $ishave ? 0 : -1;
    }

    public function checkLogin($html,$cookie){

        return 0;
    }

    protected function getFromId($url){
        $parseUrl=parse_url($url);
        if($parseUrl===false || !$parseUrl['query'])
            return false;
        parse_str($parseUrl['query'],$query);
        if(!isset($query['content_id']) || !$query['content_id'])
            return false;
        return $query['content_id'];
    }
}