{%extend@common/menu_main%}
{%block@main%}
<div class="fly-panel fly-panel-user" pad20>
    <div class="layui-tab layui-tab-brief" lay-filter="user">
        <ul class="layui-tab-title" id="LAY_mine">
            <li class="layui-this" lay-id="list"><?=$title;?></li>
        </ul>
        <div class="layui-tab-content" style="padding: 20px 0;">
            <div class="layui-tab-item layui-show">
                <form class="layui-form ayui-form-pane" method="post" action="<?=url('admin/api_other/links_edit');?>" id="form-main">
                    <div class="layui-form-item">
                        <label class="layui-form-label">关键词<span class="red"> * </span></label>
                        <div class="layui-input-inline">
                            <input  id="keyword"  class="layui-input" type="text" name="keyword" value="<?=$data['keyword']?>" lay-verify="required" required>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">链接<span class="red"> * </span></label>
                        <div class="layui-input-inline">
                            <input  id="url"  class="layui-input" type="text" name="url" value="<?=$data['url']?>" lay-verify="required" required>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">权重</label>
                        <div class="layui-input-inline">
                            <input  id="weight"  class="layui-input" type="text" name="weight" value="<?=$data['weight']?>">
                        </div>
                        <div class="layui-form-mid layui-word-aux">越大越重要</div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">状态</label>
                        <div class="layui-input-inline">
                            <select id="status" name="status">
                                <option value="0"<?=echo_select(0,$data['status'])?>>禁用</option>
                                <option value="1"<?=echo_select(1,$data['status'])?>>启用</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label"></label>
                        <div class="layui-input-block">
                            <input type="hidden" name="id" value="<?=$data['id']?>">
                            <input type="button" class="layui-btn" lay-filter="ajax" lay-submit value="提交更改">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var navSidebar="other-links";
</script>
{%end%}

{%block@javascript%}
{%end%}