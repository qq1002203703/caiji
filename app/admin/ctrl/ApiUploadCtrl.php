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


namespace app\admin\ctrl;


use app\common\ctrl\ApiAdminCtrl;
use app\common\ctrl\Func;

class ApiUploadCtrl extends ApiAdminCtrl
{
    /** ------------------------------------------------------------------
     * 缩略图删除
     * 必须的post参数：format  图片规格
     * 可选的post参数:  isdo 值为1时会重设isdo字段
     *--------------------------------------------------------------------*/
    public function thumb_del(){
        $format=post('format');
        $isdo=(int)post('isdo','int',0);
        $cmd='php '.ROOT.'/cmd tools/batch thumb_del '.$format.($isdo==1 ? ' isdo': '').' 2>&1';
        $outPut=Func::callShell($cmd);
        $msg=[
            0=>'没有找到对应规格的缩略图，设置isdo为1试试，如果已经设置isdo为1，那就是真的没有这个规格的缩略图了',
            -1=>'输入规格的格式不对',
            -2=>'数据库没有找到图片,设置isdo为1试试，如果已经设置isdo为1，那就是真的没有图片了',
            -999=>'脚本没有输出结果',
        ];
        if($outPut <1)
            json(['code'=>1,'msg'=>$msg[$outPut]]);
        else
            json(['code'=>0,'msg'=>'成功删除了'.$outPut.'条']);
    }

    /** ------------------------------------------------------------------
     * 缩略图生成
     * 必须的post参数：format  图片规格
     * 可选的post参数:  isdo 值为1时会重设isdo字段
     *--------------------------------------------------------------------*/
    public function thumb_create(){
        $format=post('format');
        $isdo=(int)post('isdo','int',0);
        $cmd='php '.ROOT.'/cmd tools/batch thumb_create '.$format.($isdo==1 ? ' isdo': '').' 2>&1';
        $outPut=Func::callShell($cmd);
        $msg=[
            0=>'所有图片都已经存在'.$format.'规格的缩略图，你可以设置isdo为1试试，如果已经设置isdo为1，那就真的是不需要再重复生成这个规格的缩略图了',
            -1=>'输入规格的格式不对',
            -2=>'数据库没有找到图片,设置isdo为1试试，如果已经设置isdo为1，那就是真的没有图片了',
            -999=>'脚本没有输出结果',
        ];
        if($outPut <1)
            json(['code'=>1,'msg'=>$msg[$outPut]]);
        else
            json(['code'=>0,'msg'=>'成功生成了'.$outPut.'条']);
    }
}