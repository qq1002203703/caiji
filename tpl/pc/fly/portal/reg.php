{%extend@common/base_portal%}
{%block@title%}
<title><?=$title?>_<?=$site_name?></title>
{%end%}
{%block@article%}
<div class="layui-container fly-marginTop">
    <div class="fly-panel fly-panel-user" pad20>
        <div class="layui-tab layui-tab-brief" lay-filter="user">
            <ul class="layui-tab-title">
                <li><a href="<?=url('portal/index/login')?>">登入</a></li>
                <li class="layui-this">注册</li>
            </ul>
            <div class="layui-form layui-tab-content" id="LAY_ucm" style="padding: 20px 0;">
                <div class="layui-tab-item layui-show">
                    <div class="layui-form layui-form-pane">
                        <form method="post" action="<?=url('portal/index/reg_verify')?>" lay-filter="common">
                            <?php $loginType=['username','email','phone'];$isVerify=false;if($loginType[$reg_verify]=='email') : $isVerify=true;?>
                                <div class="layui-form-item">
                                    <input type="hidden" name="type" value="email">
                                    <label for="L_phone_email" class="layui-form-label">邮箱</label>
                                    <div class="layui-input-inline">
                                        <input type="text" id="L_phone_email" name="phone_email" required lay-verify="required|email" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label for="L_imagecode" class="layui-form-label">图形码</label>
                                    <div class="layui-input-inline">
                                        <input type="text" id="L_imagecode" name="imagecode" required lay-verify="required" placeholder="图形验证码" autocomplete="off" class="layui-input">
                                    </div>
                                    <div class="layui-form-mid" style="padding: 0!important;">
                                        <img src="<?=url('portal/index/captcha')?>?w=140&h=36" onclick="Code()" id="img" class="fly-imagecode">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label for="L_vercode" class="layui-form-label">验证码</label>
                                    <div class="layui-input-inline"> <input type="text" id="L_vercode" name="vercode" required="" lay-verify="required" placeholder="输入邮箱收到的验证码" autocomplete="off" class="layui-input"> </div>
                                    <div class="layui-form-mid" style="padding: 0!important;"> <button type="button" class="layui-btn layui-btn-normal" id="FLY_getvercode">获取验证码</button> </div>
                                </div>
                                <?php  elseif ($loginType[$reg_verify]=='phone') : $isVerify=true;?>
                                <div class="layui-form-item">
                                    <input type="hidden" name="type" value="phone">
                                    <label for="L_phone_email" class="layui-form-label">手机</label>
                                    <div class="layui-input-inline">
                                        <input type="text" id="L_phone_email" name="phone_email" required lay-verify="required|phone" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label for="L_imagecode" class="layui-form-label">图形码</label>
                                    <div class="layui-input-inline">
                                        <input type="text" id="L_imagecode" name="imagecode" required lay-verify="required" placeholder="图形验证码" autocomplete="off" class="layui-input">
                                    </div>
                                    <div class="layui-form-mid" style="padding: 0!important;">
                                        <img src="<?=url('portal/index/captcha')?>?w=140&h=36" onclick="Code()" id="img" class="fly-imagecode">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label for="L_vercode" class="layui-form-label">验证码</label>
                                    <div class="layui-input-inline"> <input type="text" id="L_vercode" name="vercode" required="" lay-verify="required" placeholder="请输入手机短信验证码" autocomplete="off" class="layui-input"> </div>
                                    <div class="layui-form-mid" style="padding: 0!important;"> <button type="button" class="layui-btn layui-btn-normal" id="FLY_getvercode">获取验证码</button> </div>
                                </div>
                            <?php endif;?>
                            <div class="layui-form-item">
                                <label for="L_username" class="layui-form-label">用户名</label>
                                <div class="layui-input-inline">
                                    <input type="text" id="L_username" name="username" required lay-verify="username" autocomplete="off" class="layui-input">
                                </div>

                                    <div class="layui-form-mid layui-word-aux">
                                        3~20位的中文/字母/数字/下划线及小横线
                                        <?php if($isVerify):?>
                                        注册后用户名和<?=$loginType[$reg_verify]?>都可以登陆账号
                                        <?php endif;?>
                                    </div>

                            </div>
                            <div class="layui-form-item">
                                <label for="L_password" class="layui-form-label">密码</label>
                                <div class="layui-input-inline">
                                    <input type="password" id="L_password" name="password" required lay-verify="required|password" autocomplete="off" class="layui-input">
                                </div>
                                <div class="layui-form-mid layui-word-aux">6到30个字符</div>
                            </div>
                            <div class="layui-form-item">
                                <label for="L_repassword" class="layui-form-label">确认密码</label>
                                <div class="layui-input-inline">
                                    <input type="password" id="L_repassword" name="repassword" required lay-verify="required" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <?php if(!$isVerify):?>
                                <div class="layui-form-item">
                                    <label for="L_imagecode" class="layui-form-label">图形码</label>
                                    <div class="layui-input-inline">
                                        <input type="text" id="L_imagecode" name="imagecode" required lay-verify="required" placeholder="图形验证码" autocomplete="off" class="layui-input">
                                    </div>
                                    <div class="layui-form-mid" style="padding: 0!important;">
                                        <img src="<?=url('portal/index/captcha')?>?w=140&h=36" onclick="Code()" id="img" class="fly-imagecode">
                                    </div>
                                </div>
                            <?php endif;?>
                            <div class="layui-form-item">
                                <button alert="1" class="layui-btn" lay-filter="common" lay-submit>立即注册</button>
                            </div>
                            <div class="layui-form-item fly-form-app">
                                <!--span>或者直接使用社交账号快捷注册</span>
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
 /*  layui.extend({
        aliNum: '{/}/static/lib/alignment/aliNum' // {/}的意思即代表采用自有路径，即不跟随 base 路径
    });*/

   layui.use(['jquery','layer','form','fly'], function(){
       var layer = layui.layer, $=layui.$ ,form=layui.form,fly=layui.fly;
       layer.ready(function(){
           //$('.spinner-input').spinner({min:1, value:1,step:1});
           form.verify({
               username: function(value, item){ //value：表单的值、item：表单的DOM对象
                   if(!new RegExp("^[a-zA-Z0-9_\u4e00-\u9fa5][a-zA-Z0-9_\-\u4e00-\u9fa5]{2,19}$").test(value)){
                       return '用户名必须是3~20位的中文/字母/数字/下划线_及小横线- 且不能是小横线开头';
                   }
               },
               password: [
                   /^[\S]{6,30}$/,
                   '密码必须6到30位，且不能出现空格'
               ]
            });
           /*
            ,sendAuthCode: function(options){
            options = $.extend({
            seconds: 60 //多少秒后可以重发验证码
            ,type:"phone" //验证方式'phone' 或 'email'
            ,elemPhone: $('#L_phone') //手机或email对象
            ,elemVercode: $('#L_vercode') //验证码对象
            ,elemImagecode: $('#L_imagecode') //图形码对象
            ,url:"/portal/index/code" //进行验证的url
            ,elem:"" //被点击的选择器，需要定义
            }, options);
            */
           <?php if($reg_verify):?>
           fly.sendAuthCode({
               type:"<?=$loginType[$reg_verify]?>",
               url:"<?=url('portal/index/sendcode')?>",
               elem:"#FLY_getvercode",
               elemPhone: $('#L_phone_email')
           });
          <?php endif;?>
       });

   });
     function Code() {
         document.getElementById("img").src="<?=url('portal/index/captcha')?>??w=140&h=36&"+Math.random();
     }
 </script>
{%end%}

