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


namespace shell\caiji\plugin;


class PluginCommon
{
    public function task001($data){
        if($data['qrcode'] !='' ||$data['qrcode'] !='' || $data['thumb'] !='' )
            $data['have_img']=1;
        else
            $data['have_img']=0;
        if(!isset($data['content']) || !$data['content'] ){
            $model=app('\app\weixinqun\model\Weixinqun');
            $prefix=\app\weixinqun\model\Weixinqun::$prefix;
            $ret=$model->_sql('SELECT excerpt,id FROM `'.$prefix.'weixinqun` WHERE id >= (SELECT floor(RAND() * (SELECT MAX(id) FROM `'.$prefix.'weixinqun`))) ORDER BY id LIMIT 4',[],false);
            if($ret){
                $data['content']='';
                foreach ($ret as $v){
                    $data['content'].='<p>'.$v['excerpt'].'</p>';
                }
            }
        }
        return $data;
    }

    /** ------------------------------------------------------------------
     * 采集任务1的下载插件
     * @param $data
     *--------------------------------------------------------------------*/
    public function task001_down($data){
        $image=app('\extend\ImageResize');
        $image->quality_jpg=90;
        $image->quality_png=90;
        $file=ROOT.'/'.$data['save_path'];
        if($image->checkImage($file)){
            $image->add()->resizeToBestFit(250,250)->save($file);
            echo ' ----img resize success:'.$file.PHP_EOL;
        }else{
            echo ' ----img resize fail:'.$file.' ;message: '.$image->getMsg().PHP_EOL;
        }
    }
    public function task002_down($data){
        $image=app('\extend\ImageResize');
        $image->quality_jpg=90;
        $image->quality_png=90;
        $file=ROOT.'/'.$data['save_path'];
        if($image->checkImage($file)){
            $image->add()->resizeToBestFit(800,800)->save($file);
            echo ' ----img resize success:'.$file.PHP_EOL;
        }else{
            echo ' ----img resize fail:'.$file.' ;message: '.$image->getMsg().PHP_EOL;
        }
    }
    public function test(){

    }
}