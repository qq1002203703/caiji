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

namespace shell\caiji;
use core\lib\cache\File;
use extend\Curl;
use extend\Selector;
use shell\CaijiCommon;

class Av extends CaijiCommon
{
    //豆瓣电影所在的数据表
    protected $table='caiji_av';


    //直接从豆瓣电影所有分类中采集电影链接
    public function page(){
        $caijiRule=$this->getCaijiRules('douban','page','');
        $this->dieEcho($caijiRule===false,'规则名不正确'.PHP_EOL);
        $curl=new Curl();
        $i=1;
        while(true) {
            $html=$curl->get('http://3g.81662269.cn:9327');
            echo '开始第 '.$i.' 次抓取：'.PHP_EOL;
            $i++;
            if(!$html){
                echo '  失败：抓取网页失败！'.PHP_EOL;
                continue;
            }

            $img=Selector::find($html,['regex','cut'],'<img src =\'{%|||%}\'');
            if(!$img){
                echo '  失败：html内容筛选失败！'.PHP_EOL;
                continue;
            }
            $md5=md5($img);
            //检测是否重复
            if($this->model->from('caiji_av')->eq('md5',$md5)->find(null,true)){
                echo '  失败：此图片在数据库中已经存在！'.PHP_EOL;
                continue;
            }
            //入库
            if($id=$this->model->from('caiji_av')->insert([
                'img'=>$img,
                'md5'=>$md5,
            ]))
                echo '  成功：入库,id=>'.$id.PHP_EOL;
            else
                echo '  失败：入库'.PHP_EOL;
            //msleep(400,30);
        }
    }

    public function page2(){
        //$caijiRule=$this->getCaijiRules('douban','page','');
        //$this->dieEcho($caijiRule===false,'规则名不正确'.PHP_EOL);
        $curl=new Curl();
        for ($i=100;$i<163;$i++){
            $url='http://www.lovefo.cc/dongtaitu/list_'.$i.'.html';
            echo '开始抓取：'.$url.PHP_EOL;
            $html=$curl->get($url);
            if(!$html){
                echo '  失败：抓取网页失败！'.PHP_EOL;
                continue;
            }
            $data=Selector::find($html,['regex','multi'],'#<li id="long">\s+<a [^>]+><img width="\d+" height="\d+" src=\'[^\']+\' alt=\'(?P<title>[^>\']+)\'></a>#','title','<div class="main">{%|||%}<!---翻页开始--->');
            if(!$data){
                dump($data);
                echo '  失败：html内容筛选失败！'.PHP_EOL;
                continue;
            }
            foreach ($data as $item){
                //检测是否重复
                if($this->model->from('caiji_keywords')->eq('title',$item)->find(null,true)){
                    echo '  失败："'.$item.'"在数据库中已经存在！'.PHP_EOL;
                    continue;
                }
                //入库
                if($id=$this->model->from('caiji_keywords')->insert([
                    'title'=>$item,
                ]))
                    echo '  成功：入库,id=>'.$id.PHP_EOL;
                else
                    echo '  失败：入库'.PHP_EOL;
            }
            msleep(400,30);
            //exit();
        }
    }
    //采集www.zbjuran.com网的关键词
    public function page3(){
        //$caijiRule=$this->getCaijiRules('douban','page','');
        //$this->dieEcho($caijiRule===false,'规则名不正确'.PHP_EOL);
        $curl=new Curl();
        for ($i=1;$i<100;$i++){
            $url='https://www.zbjuran.com/dongtai/list_4_'.$i.'.html';
            echo '开始抓取：'.$url.PHP_EOL;
            $html=$curl->get($url);
            if(!$html){
                echo '  失败：抓取网页失败！'.PHP_EOL;
                continue;
            }
            $html=$curl->encoding($html,'GBK');
            $data=Selector::find($html,['regex','multi'],'#<div class="item">\s+<h3><a [^>]+><b>(?P<title>[^>"]+)</b></a></h3>#','title','<div class="main">{%|||%}<div class="pages">');
            if(!$data){
                dump($data);
                echo '  失败：html内容筛选失败！'.PHP_EOL;
                continue;
            }
            foreach ($data as $item){
                //检测是否重复
                if($this->model->from('caiji_keywords')->eq('title',$item)->find(null,true)){
                    echo '  失败："'.$item.'"在数据库中已经存在！'.PHP_EOL;
                    continue;
                }
                //入库
                if($id=$this->model->from('caiji_keywords')->insert([
                    'title'=>$item,
                ]))
                    echo '  成功：入库,id=>'.$id.PHP_EOL;
                else
                    echo '  失败：入库'.PHP_EOL;
            }
            msleep(400,30);
            //exit();
        }
    }

    public function content(){
        $this->caiji('douban','content'/*,['url'=>'http://checkip.amazonaws.com/']*/);
    }
    public function download(){
        $where=[['isdo','eq',0]];
        $total=$this->model->count([
            'from'=>$this->table,
            'where'=>$where
        ]);
        $download=new \extend\Download();
        $download->setOption('savePath','public/uploads/images/av');
        $this->doLoop($total,function ($perPage,$i)use ($where){
            return $this->model->from($this->table)->_where($where)->limit($perPage)->findAll(true);
        },function ($item,$k)use ($download){
            echo '正在处理：id=>'.$item['id'].'-----------'.PHP_EOL;
            $ret=$download->down($item['img'],'{%u%}',false);
            if(is_array($ret)){
                echo '  成功：下载！'.PHP_EOL;
                if($this->model->from($this->table)->eq('id',$item['id'])->update(['isdo'=>1]))
                    echo '  成功：更新！'.PHP_EOL;
                else
                    echo '  失败：更新！'.PHP_EOL;
            }else
                echo '  失败：下载 ! '.PHP_EOL;
            msleep(200,30);
            //exit();
        });
    }

 /*---插件区--------------------------------------------------------------------------------------------------------*/



/*==后期===========================================*/
    public function test(){

    }
}