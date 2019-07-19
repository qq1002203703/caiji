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


namespace app\api\ctrl;
use app\common\ctrl\ApiCtrl;

class SearchCtrl extends ApiCtrl
{
    public function test(){
        $sphinx=app('\app\admin\other\Search');
        echo '
            <!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>采集结果管理_后台管理</title>
    <link href="/static/lib/layui/css/layui.css?v=1.02" rel="stylesheet" type="text/css">
    <link href="/static/admin/layui/css/global.css?v=1.0.1" rel="stylesheet" type="text/css">
    <!--link href="/uploadfile/images/20180626/20180626125809_78272.png" rel="shortcut icon"-->
    
</head>
<body><ul>';
        $data=$sphinx->query('搬砖活动','zuanke8',1,30,$total);
        echo '<strong>'.$total.'</strong><br>';
        if($data){
            foreach ($data as $item){
                //$id=($item['table_name']=='bbs' ? ($item['id']-1) :($item['id']-2))/10;
                $item['table_name']='';
                $id=$item['id'];
                echo '<li style="border-bottom: 1px solid #e2e2e2">'.$item['table_name'].$item['weight'].'|'.$id.' => '.$item['title'];
                echo '<div style="padding: 5px 20px;">'.$item['content'].'</div>';
                echo '</li>';
            }
        }else{
            echo '没有结果';
        }
        echo '</ul></body></html>';
    }
}