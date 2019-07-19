<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * shell下载工具集
 * ======================================*/
namespace shell\caiji;
use shell\BaseCommon;

class Download extends BaseCommon
{
    protected $error;
   /*protected function _init(){
       //$this->model->table='bbs';
   }*/

    /** ------------------------------------------------------------------
     * 论坛入库接口的图片下载
     *--------------------------------------------------------------------*/
   public function bbsImg(){
       $this->model->table='bbs';
       if(isset($this->param[1])){
          parse_str (str_replace('@','&',$this->param[1]), $opt);
           $download=new \extend\Download();
          if($opt){
              $title='';
              //主贴
              if(isset($opt['z']) && $opt['z']){
                  $model=app('\app\bbs\model\Bbs');
                  $data=$model->eq('id',$opt['id'])->find(null,true);
                  if($data && $data['more']){
                      $title=$data['title'];
                      //$data['content']=Helper::addImgAlt($data['content'],$title);
                      $download->repair('bbs',$data['id'],'more');
                  }
              }
              //评论
              if(isset($opt['c']) && $opt['c']){
                  $commentIds=explode(',',$opt['c']);
                  $model=app('\app\bbs\model\BbsComment');
                /*  if($title===''){
                      $prefix=Conf::get('prefix','database');
                      $title=$model->_sqlField('title','select title from '.$prefix.'bbs WHERE id=?',[$opt['id']],false);
                  }*/
                  foreach ($commentIds as $commentId){
                      $data=$model->eq('id',$commentId)->find(null,true);
                      if($data && $data['more']){
                          //$data['content']=Helper::addImgAlt($data['content'],$title);
                          $download->repair('comment',$commentId,'more');
                      }
                  }
              }
          }
       }
   }

   public function portalImg(){
       $this->model->table='portal_post';
       if(isset($this->param[1])){
           parse_str (str_replace('@','&',$this->param[1]), $opt);
           $download=new \extend\Download();
           if($opt){
               //$title='';
               //主贴
               if(isset($opt['z']) && $opt['z']){
                   $model=app('\app\portal\model\PortalPost');
                   $data=$model->select('id,more')->eq('id',$opt['id'])->find(null,true);
                   if($data && $data['more']){
                       //$title=$data['title'];
                       //$data['content']=Helper::addImgAlt($data['content'],$title);
                       $download->repair('portal_post',$data['id'],'more');
                   }
               }

           }
       }
   }


}