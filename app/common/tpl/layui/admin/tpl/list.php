{%extend@common/menu_setting%}
{%block@main%}
<div class="fly-panel fly-panel-user" pad20>
    <div class="layui-tab layui-tab-brief" lay-filter="user">
        <ul class="layui-tab-title" id="LAY_mine">
            <li id="tab-pc" lay-id="list"><a href="<?=url('admin/tpl/list')?>">pc端<?=$title?></a></li>
            <li id="tab-mb" lay-id="list"><a href="<?=url('admin/tpl/list')?>?m=1">移动端<?=$title?></a></li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <div class="layui-form">
                    <table class="layui-table">
                        <colgroup>
                            <col width="10%">
                            <col width="30%">
                            <col width="15%">
                            <col width="15%">
                            <col width="10%">
                            <col width="25%">
                            <col>
                        </colgroup>
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>名称</th>
                            <th>类型</th>
                            <th>时间</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if ($data): $i=1;foreach ($data as $item) : if($item ==='.' || $item =='..') continue; ?>
                        <tr>
                            <td><?php echo $i;$i++; ?></td>
                            <td><a href="<?=url('admin/tpl/edit')?>?tpl=<?=$item?>&m=<?=$isMobile?>"><?=$item?></a></td>
                            <td></td>
                            <td></td>
                            <td><?=($currentTpl==$item?'<span class="red">已启用</span>':'未启用')?></td>
                            <td>
                                <i class="layui-icon" style="font-size: 20px; color: #009688;">
                                    <a title="启用" href="/" target="_blank">&#xe652;</a>
                                </i>
                                <i class="layui-icon" style="font-size: 20px; color: #1E9FFF;">
                                    <a title="编辑" href="<?=url('admin/tpl/edit')?>?tpl=<?=$item?>&m=<?=$isMobile?>">&#xe642;</a>
                                </i>
                                <i class="layui-icon" style="font-size: 20px; color: #1E9FFF;">
                                    <a title="预览" href="/" target="_blank">&#xe615;</a>
                                </i>

                            </td>
                        </tr>
                        <?php endforeach;endif;?>
                        </tbody>
                    </table>
                    <div class="fly-msg" style="margin-top: 15px;">
                        <strong>使用说明：</strong><br>
                        <i class="layui-icon" style="font-size: 20px; color: #009688;">&#xe652;</i> 启用本设计作为当前界面，点击顶部主导航上的“预览”，即可查看切换后的界面。<br>
                        <i class="layui-icon" style="font-size: 20px; color: #009688;">&#xe642;</i> 对本套设计进行多项高级操作，如编辑文件名、新增文件、打包下载等。<br>
                        <i class="layui-icon" style="font-size: 20px; color: #009688;">&#xe615;</i> 提前预览该设计的效果图，自己上传的设计师包则没预览按钮。<br>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var navSidebar="tpl-list";
</script>
{%end%}
{%block@javascript%}
<script type="text/javascript">
    layui.use(['layer','fly'],function () {
        var $ = layui.jquery;
        var isMobile=<?=$isMobile?>;
        if(isMobile)
            $("#tab-mb").addClass('layui-this');
        else
            $("#tab-pc").addClass('layui-this');
    });
</script>
{%end%}