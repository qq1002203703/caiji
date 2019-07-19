<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 批处理工具
 * ======================================*/


namespace shell\tools;

use extend\Selector;
use shell\CaijiCommon;

class Batch extends CaijiCommon
{
    public function __construct($param=[])
    {
        parent::__construct($param);
    }



    /** ------------------------------------------------------------------
     * 缩略图批量删除
     * 必须参数 $this->param[1]  缩略图规格 如 '150x150','all'表示全部规格
     * 可选参数 $this->param[2]  是否重设isdo字段  只有值为'isdo'时才重设
     * @return int
     *--------------------------------------------------------------------*/
    public function thumb_del(){
        //验证规格格式
        if(!isset($this->param[1]) || preg_match('/^(all|\d+x\d+)$/',$this->param[1])<1){
            echo '-1'.PHP_EOL;
            return -1;
        }
        $model=app('\app\admin\model\File');
        //重设isdo字段
        if(isset($this->param[2]) && $this->param[2]=='isdo')
            $model->update(['isdo'=>0]);
        $format=$this->param[1];
        //查询所有符合条件的个数
        $total=$model->count(['where'=>[['isimg','eq',1],['isdo','eq',0]]]);
        if($total==0){
            echo '-2'.PHP_EOL;
            return -2;
        }
        $j=0;
        //开始循环处理
        $this->doLoop(
            $total,
            function($perPage,$i)use($model){
                return $model->eq('isimg',1)->limit($i*$perPage,$perPage)->order('id desc')->findAll(true);
            },
            function ($item,$i)use ($format,$model,&$j){
                if($item['thumb']){
                    $allFormat=explode(',',$item['thumb']);
                    $allFormat_tmp=$allFormat;
                    foreach ($allFormat as $key => $itemFormat){
                        if($format=='all' || $format==$itemFormat){
                            //删除
                            @unlink(ROOT.$item['savepath'].'_'.$itemFormat.'.'.$item['ext']);
                            unset($allFormat_tmp[$key]);
                        }
                    }
                    $new_thumb=implode(',',$allFormat_tmp);
                    if($new_thumb!==$item['thumb']) {
                        $j++;
                        $model->eq('id', $item['id'])->update(['thumb' => $new_thumb, 'isdo' => 1]);
                        return 0;
                    }
                }
                $model->eq('id',$item['id'])->update(['isdo'=>1]);
                return 1;
            }
        );
        echo $j.PHP_EOL;
        return $j;
    }
    /** ------------------------------------------------------------------
     * 缩略图批量生成
     * 必须参数 $this->param[1]  缩略图规格 如 '150x150'
     * 可选参数 $this->param[2]  是否重设isdo字段  只有值为'isdo'时才重设
     * @return int
     *--------------------------------------------------------------------*/
    public function thumb_create(){
        //验证规格格式
        if(!isset($this->param[1]) || preg_match('/^\d+x\d+$/',$this->param[1])<1){
            echo '-1'.PHP_EOL;
            return -1;
        }
        $model=app('\app\admin\model\File');
        //重设isdo字段
        if(isset($this->param[2]) && $this->param[2]==='isdo')
            $model->update(['isdo'=>0]);
        $format=$this->param[1];
        //查询所有符合条件的个数
        $total=$model->count(['where'=>[['isimg','eq',1],['isdo','eq',0]]]);
        if($total==0){
            echo '-2'.PHP_EOL;
            return -2;
        }
        $j=0;
        //开始循环处理
        $this->doLoop(
            $total,
            function($perPage,$i)use($model){
                return $model->eq('isimg',1)->limit($i*$perPage,$perPage)->order('id desc')->findAll(true);
            },
            function ($item,$i)use ($format,$model,&$j){
                //查询是否已经有对应规格的缩略图
                if(strpos($item['thumb'],$format)===false){
                    //生成缩略图
                    $resizeClass=app('\extend\ImageResize');
                    $savepath=ROOT.$item['savepath'];
                    if($resizeClass->checkImage($savepath)){
                        list($width,$height)=explode('x',$format);
                        try{
                            $resizeClass->add()-> crop($width,$height,true)->save($savepath.'_'.$width.'x'.$height.'.jpg',IMAGETYPE_JPEG);
                            $item['thumb']=$item['thumb'] ? $item['thumb'].','.$width.'x'.$height : $width.'x'.$height;
                            //更新数据库
                            $model->eq('id',$item['id'])->update(['thumb'=>$item['thumb'],'isdo'=>1]);
                            $j++;
                            return 0;
                        }catch (\Exception $e){

                        }
                    }
                }
                $model->eq('id',$item['id'])->update(['isdo'=>1]);
                return 1;
            }
         );
        echo $j.PHP_EOL;
        return $j;
    }

    /** ------------------------------------------------------------------
     * 批量给用户添加头像
     *--------------------------------------------------------------------*/
    public function user_img(){
        $model=app('\app\portal\model\User');
        $model->update(['isdo'=>0]);
        $total=$model->count();
        if($total==0){
            echo '没有要处理的了'.PHP_EOL;
            return;
        }
        $j=0;
        $this->doLoop($total,function ($perPage,$i)use($model){
            return $model->select('id,username,avatar')->limit($perPage*$i,$perPage)->order('id desc')->findAll(true);
        },function ($item,$i)use ($model,&$j){
            if($item['avatar'])
                return 1;
            $file='/uploads/user/'.mt_rand(0,500).'.jpg';
            if($model->eq('id',$item['id'])->update(['avatar'=>$file])){
                $j++;
                echo $item['id'].'=>'.$file.PHP_EOL;
            } else
                echo $item['id'].'=>出错了'.PHP_EOL;
            return 0;
        });
        echo '总共处理了：'.$j.PHP_EOL;
    }



}