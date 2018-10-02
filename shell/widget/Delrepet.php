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
 *去除重复数据
 * ======================================*/


namespace shell\tools;
class Delrepet extends Base
{
    protected function _init()
    {
        parent::_init();
        $this->model=app('\app\weixinqun\model\Weixinqun');
        $this->prefix=\app\weixinqun\model\Weixinqun::$prefix;
    }
    public function run()
    {
        $data=$this->model->_sql('select source,count(*) as num from '.$this->prefix.$this->model->table.' group by source having num>1',[],false);
        if(!$data)
            exit('no repet'.PHP_EOL);
        $path=ROOT.'/public/uploads/images/';
        foreach ($data as $v){
            for($i=1;$i<$v['num'];$i++){
                $item=$this->model->reset()->eq('source',$v['source'])->order('have_img,id')->find(null,true);
                if(!$item)
                    break;
                //删除图片
                if($item['thumb']){
                    $file=$path.ltrim($item['thumb'],'/');
                    if(is_file($file))
                        unlink($file);
                }
                if($item['qrcode']){
                    $file=$path.ltrim($item['qrcode'],'/');
                    if(is_file($file))
                        unlink($file);
                }
                if($item['qun_qrcode']){
                    $file=$path.ltrim($item['qun_qrcode'],'/');
                    if(is_file($file))
                        unlink($file);
                }
                $this->model->reset()->eq('id',$item['id'])->delete();
                echo 'delete '.$item['id'].PHP_EOL;
            }
        }
    }
}