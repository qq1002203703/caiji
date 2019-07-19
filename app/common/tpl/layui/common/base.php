<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?=$title?>_后台管理</title>
    <link href="/static/lib/layui/css/layui.css?v=1.02" rel="stylesheet" type="text/css">
    <link href="/static/admin/layui/css/global.css?v=1.0.1" rel="stylesheet" type="text/css">
    <!--link href="/uploadfile/images/20180626/20180626125809_78272.png" rel="shortcut icon"-->
    {%block@header%}
</head>
<body>
<div class="layui-layout layui-layout-admin">
    <!--头部菜单-->
    <div class="header" style="">
        <div class="main">
            <div class="nav-logo">
                <a href="index.php?m=site&c=manage&a=index">
                    <img src="/static/admin/layui/images/admin.png">
                </a>
            </div>
            <div class="nav">
                <ul class="layui-nav" lay-filter="nav">
                    <li  id="nav-header-home" class="layui-nav-item">
                        <a href="<?=url('admin/user/index')?>" style="font-size:16px;">管理</a>
                    </li>
                    <li id="nav-header-setting" class="layui-nav-item ">
                        <a  href="<?=url('admin/option/all')?>" style="font-size:16px;">设置</a>
                    </li>
                    <li class="layui-nav-item">
                        <a href="<?=url('/')?>" target="_blank" style="font-size:16px;">前台</a>
                    </li>
                    <!--li class="layui-nav-item" style="float: right;">
                        <a id="change_site" href="javascript:;" style="font-size:16px;" data-url="javascript://">切换站点</a>
                    </li-->
                    <li class="layui-nav-item" style="float: right;">
                        <a href="javascript:;">
                            <i class="layui-icon" style="font-size:20px; color:#FFFFFF;">&#xe612;</i>
                            admin</a>
                        <dl class="layui-nav-child">
                            <dd><a id="menu" href="<?=url('admin/option/info')?>">个人信息</a></dd>
                            <dd><a id="menu" href="<?=url('admin/option/pwd')?>">修改密码</a></dd>
                            <dd><a id="menu" href="<?=url('admin/login/logout')?>">退出</a></dd>
                        </dl>
                    </li>
                </ul>
            </div>

            <div class="nav-user">

            </div>
        </div>
    </div>
    <!--//头部菜单-->
    {%content%}
</div>
<!--公共js-->
<!--<script>
    var gid = 1;
    var site_id = 6169;
    var op_mod = 'site';
</script>-->
<script src="/static/lib/layui/layui.js?v=2.4.5" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
    layui.cache.page = 'site';
    layui.config({
        version: "1.1.22"
        ,base: '/static/admin/layui/mods/'
    }).extend({
        //fly: 'index'
        fly: 'entry'
    }).use('fly');
</script>
<!--//公共js-->

<!--独立js-->
{%block@javascript%}
<!--//独立js-->
{%block@footer%}
</body>
</html>