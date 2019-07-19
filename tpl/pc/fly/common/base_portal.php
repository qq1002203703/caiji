<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    {%block@title%}
    <meta http-equiv="X-UA-Compatible" content="IE=edge,Chrome=1">
    <link rel="stylesheet" href="<?=$tuku;?>/static/lib/layui/css/layui.css">
    <link rel="stylesheet" href="<?=$tuku;?>/static/fly/css/global.css?v=1.2">
    <link href="/static/favicon.ico?v=1.1" rel="shortcut icon" type="image/x-icon">
</head>
<body>
{%include@common/header%}
<!--导航-->
<div style="margin-top: 44px"></div>
<!--//end 导航-->
{%block@article%}
<div class="fly-footer">
    <p> &copy;  2016~<?=date('Y')?> <a href="<?=$site_url?>/"><?=$site_name?></a>版权所有</p>
</div>
<!--公共js-->
<script src="<?=$tuku;?>/static/lib/layui/layui.js"></script>
<script type="text/javascript" charset="utf-8">
    layui.cache.page = 'jie';
    layui.cache.user = {
        username: '游客'
        ,uid: -1
        ,avatar: '<?=$tuku;?>/static/fly/images/avatar/00.jpg'
        ,experience: 83
        ,sex: '男'
    };
    layui.config({
        version: "3.0.0",
        base: '<?=$tuku;?>/static/fly/mods/'
    }).extend({
        fly: 'index'
    }).use('fly');
</script>
<!--公共js-->
{%block@javascript%}
</body>
</html>