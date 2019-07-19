{%extend@common/menu_setting%}
{%block@main%}
<div class="fly-panel fly-panel-user" pad20>
    <div class="layui-tab layui-tab-brief" lay-filter="user">
        <ul class="layui-tab-title" id="LAY_mine">
            <li class="layui-this" lay-id="list"><?=$title;?></li>
        </ul>
        <div class="layui-tab-content" style="padding: 20px 0;">
            <div class="layui-tab-item layui-show">
                <form class="layui-form ayui-form-pane" method="post" action="<?=url('admin/api_option/option_add');?>?type=<?=$type?>" id="form-main">
                    <div class="layui-form-item">
                        <label class="layui-form-label">变量名<span class="red"> * </span></label>
                        <div class="layui-input-inline">
                            <input  id="name"  class="layui-input" type="text" name="name" lay-verify="required" required>
                        </div>
                        <div class="layui-form-mid layui-word-aux">只能是字母、下线线和数字，且开头只能是字母或下划线</div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">状态</label>
                        <div class="layui-input-inline">
                            <select id="status" name="status">
                                <option value="0">禁用</option>
                                <option value="1" selected>启用</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">格式</label>
                        <div class="layui-input-inline">
                            <select id="json" name="json">
                                <option value="0" selected>string</option>
                                <option value="1">json</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">说明</label>
                        <div class="layui-input-block">
                            <input class="layui-input" type="text" name="description" id="description">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">变量值</label>
                        <div class="layui-input-block">
                            <textarea id="value" class="layui-textarea" name="value"></textarea>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label"></label>
                        <div class="layui-input-block">
                            <input type="hidden" name="type" value="<?=$type?>">
                            <input type="button" class="layui-btn" lay-filter="ajax" lay-submit value="提交更改">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var navSidebar="option-all-<?=$type?>";
</script>
{%end%}

{%block@javascript%}

<script type="text/javascript">
    layui.use(['layer','form','fly'], function() {
        var $=layui.jquery,
            layer=layui.layer,
            fly=layui.fly;
        //更新缓存
        $("#update-cache").on('click', function(){
            fly.ajaxClick(this,function(res){
                layer.alert(res.msg, {title:'提示',time: 2000,end:function () {
                        window.location.reload();
                    }
                });
            });
            return false;
        });
    });
</script>
{%end%}