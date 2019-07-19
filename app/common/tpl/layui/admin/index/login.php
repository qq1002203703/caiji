<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>后台登录_<?=$site_name?></title>
    <link href="/static/lib/layui/css/layui.css?v=1.01" rel="stylesheet" type="text/css">
    <link href="/static/admin/layui/css/global.css?v=1.0.0" rel="stylesheet" type="text/css">
</head>
<body>
<style type="text/css">
    body{background: #f1f2f3;height: auto;}
    #img{cursor: pointer}
</style>
<div class="main layui-clear" style="width:450px;margin-top:100px;">
    <div class="fly-panel fly-panel-user" pad20>
        <div class="layui-tab layui-tab-brief">
            <ul class="layui-tab-title">
                <li class="layui-this" style="cursor:default;">登录</li>
            </ul>
            <div class="layui-form layui-tab-content" id="LAY_ucm" style="padding: 20px 0;">
                <div class="layui-tab-item layui-show">
                    <div class="layui-form">
                        <form method="post" action="<?=url('admin/login/login_verify')?>">
                            <div class="layui-form-item">
                                <label class="layui-form-label">用户名</label>
                                <div class="layui-input-inline">
                                    <input type="text" id="L_username" name="username" required lay-verify="required" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label for="L_pass" class="layui-form-label">密码</label>
                                <div class="layui-input-inline">
                                    <input type="password" id="L_pass" name="password" required lay-verify="required" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label for="L_pass" class="layui-form-label">验证码</label>
                                <div class="layui-input-inline">
                                    <div class="layui-row">
                                        <div class="layui-col-xs6">
                                            <input type="text" id="L_imagecode" name="imagecode" required lay-verify="required" placeholder="输入验形码" autocomplete="off" class="layui-input">
                                        </div>
                                        <div class="layui-col-xs6" style="text-align: right">
                                            <img src="<?=url('portal/index/captcha')?>?w=140&h=36" onclick="Code()" id="img" class="fly-imagecode">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label"></label>
                                <div class="layui-input-inline">
                                    <button class="layui-btn" lay-submit>立即登录</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<script src="/static/lib/layui/layui.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
    layui.cache.page = 'site';
    layui.config({
        version: "1.1.21"
        ,base: '/static/admin/layui/mods/'
    }).extend({
        fly: 'index'
    }).use('fly');

    function Code() {
        document.getElementById("img").src="<?=url('portal/index/captcha')?>?w=140&h=36&"+Math.random();
    }
</script>
</body>
</html>