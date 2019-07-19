{%extend@common/menu_setting%}
{%block@main%}
<div class="fly-panel fly-panel-user" pad20>
    <div class="layui-tab layui-tab-brief" lay-filter="user">
        <ul class="layui-tab-title" id="LAY_mine">
            <li class="layui-this" lay-id="list"><?=$title;?></li>
        </ul>
        <div class="layui-tab-content" style="padding: 20px 0;">
            <div class="layui-tab-item layui-show">
                <form class="layui-form ayui-form-pane" method="post" action="<?=url('admin/api_option/pwd');?>" id="form-main">
                    <div class="layui-form-item">
                        <label class="layui-form-label">新用户名</label>
                        <div class="layui-input-inline">
                            <input  id="username"  class="layui-input" type="text" name="username">
                        </div>
                        <div class="layui-form-mid">用户名不修改就留空</div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">原密码<span class="red"> * </span></label>
                        <div class="layui-input-inline">
                            <input  id="current_pwd"  class="layui-input" type="password" name="current_pwd" lay-verify="required" required>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">新密码</label>
                        <div class="layui-input-inline">
                            <input  id="password"  class="layui-input" type="password" name="password">
                        </div>
                        <div class="layui-form-mid">密码不修改就留空</div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">重复密码</label>
                        <div class="layui-input-inline">
                            <input  id="repassword"  class="layui-input" type="password" name="repassword">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label"></label>
                        <div class="layui-input-block">
                            <input type="button" class="layui-btn" lay-filter="ajax" lay-submit value="提交更改">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var navSidebar="option-pwd";
</script>
{%end%}

{%block@javascript%}
{%end%}