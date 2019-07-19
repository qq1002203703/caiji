{%extend@common/base_portal%}
{%block@title%}
<title><?=$title?>_<?=$site_name?></title>
{%end%}
{%block@article%}
<div class="layui-container fly-marginTop">
    <div class="fly-panel fly-panel-user" pad20>
        <div class="layui-tab layui-tab-brief" lay-filter="user">
            <ul class="layui-tab-title">
                <li class="layui-this">登入</li>
                <li><a href="<?=url('portal/index/reg')?>">注册</a></li>
            </ul>
            <div class="layui-form layui-tab-content" id="LAY_ucm" style="padding: 20px 0;">
                <div class="layui-tab-item layui-show">
                    <div class="layui-form layui-form-pane">
                        <form method="post" action="<?=url('portal/index/login_verify')?>" lay-filter="common">
                            <div class="layui-form-item">
                                <label for="L_type" class="layui-form-label">登陆方式</label>
                                <div class="layui-input-inline">
                                    <select class="layui-select" name="type" id="L_type" lay-filter="login_type">
                                        <option value="username">用户名</option>
                                        <?php $loginType=['username','email','phone'];if($loginType[$reg_verify]=='email') : ?>
                                        <option value="phone">手机</option>
                                        <?php elseif ($loginType[$reg_verify]=='phone') : ?>
                                        <option value="email">邮箱</option>
                                        <?php endif;?>
                                    </select>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label for="L_type_content" class="layui-form-label">用户名</label>
                                <div class="layui-input-inline">
                                    <input type="text" id="L_type_content" name="username" required lay-verify="required" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label for="L_pass" class="layui-form-label">密码</label>
                                <div class="layui-input-inline">
                                    <input type="password" id="L_pass" name="password" required lay-verify="required" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label for="L_imagecode" class="layui-form-label">验证码</label>
                                <div class="layui-input-inline">
                                    <input type="text" id="L_imagecode" name="imagecode" required lay-verify="required" placeholder="图形码" autocomplete="off" class="layui-input">
                                </div>
                                <div class="layui-form-mid" style="padding: 0!important;"><img src="<?=url('portal/index/captcha')?>?w=140&h=36" onclick="Code()" id="img" class="fly-imagecode"></div>
                            </div>
                            <div class="layui-form-item">
                                <button class="layui-btn" lay-filter="common" lay-submit>立即登录</button>
                                <span style="padding-left:20px;">
                                    <a href="<?=url('portal/index/forget')?>">忘记密码？</a>
                                </span>
                            </div>
                            <div class="layui-form-item fly-form-app">
                                <!--span>或者使用社交账号登入</span>
                                <a href="" onclick="layer.msg('正在通过QQ登入', {icon:16, shade: 0.1, time:0})" class="iconfont icon-qq" title="QQ登入"></a>
                                <a href="" onclick="layer.msg('正在通过微博登入', {icon:16, shade: 0.1, time:0})" class="iconfont icon-weibo" title="微博登入"></a-->
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{%end%}

{%block@javascript%}

<script type="text/javascript" charset="utf-8">
   layui.use(['jquery','layer','form'], function(){
       var layer = layui.layer;
       var $=layui.$;
       var form=layui.form;
       layer.ready(function(){
           form.on('select(login_type)', function(data){
               var selectText=$(data.elem).find("option:selected").text();
               $("label[for='L_type_content']").text(selectText);
               $("#L_type_content").attr("name",data.value);
           });
       });
   });
     function Code() {
         document.getElementById("img").src="<?=url('portal/index/captcha')?>?w=140&h=36&"+Math.random();
     }
 </script>
{%end%}

