{%extend@common/menu_setting%}
{%block@main%}
<div class="fly-panel fly-panel-user" pad20>
    <div class="layui-tab layui-tab-brief" lay-filter="user">
        <ul class="layui-tab-title" id="LAY_mine">
            <li class="layui-this" lay-id="list"><?=$title;?></li>
        </ul>
        <div class="layui-tab-content" style="padding: 20px 0;">
            <div class="layui-tab-item layui-show">
                <form class="layui-form ayui-form-pane" method="post" action="<?=url('admin/api_option/info');?>" id="form-main">
                    <div class="layui-form-item">
                        <label class="layui-form-label">邮箱</label>
                        <div class="layui-input-inline">
                            <input  id="email"  class="layui-input" type="text" name="email" lay-verify="email" value="<?=$data['email']?>">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">昵称</label>
                        <div class="layui-input-inline">
                            <input  id="nickname"  class="layui-input" type="text" name="nickname" value="<?=$data['nickname']?>">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">生日</label>
                        <div class="layui-input-inline">
                            <input  id="birthday"  class="layui-input" type="text" name="birthday" value="<?=date('Y-m-d',$data['birthday'])?>">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">个人网页</label>
                        <div class="layui-input-inline" style="width: 500px">
                            <input  id="website"  class="layui-input" type="text" name="website" lay-verify="url" value="<?=$data['website']?>">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">个性签名</label>
                        <div class="layui-input-inline">
                            <input  id="signature"  class="layui-input" type="text" name="signature" value="<?=$data['signature']?>">
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
    var navSidebar="option-info";
</script>
{%end%}

{%block@javascript%}
<script type="text/javascript">
    layui.use('laydate', function() {
        layui.laydate.render({
            elem: '#birthday'
            , type: 'date'
            ,min: '1920-1-1'
            ,max: "<?=date('Y-m-d')?>"
        });
    });
</script>
{%end%}