{%extend@common/menu_setting%}
{%block@main%}
<div class="fly-panel fly-panel-user" pad20>
    <div class="layui-tab layui-tab-brief" lay-filter="user">
        <ul class="layui-tab-title" id="LAY_mine">
            <li class="layui-this" lay-id="list"><?=$title;?></li>
        </ul>
        <div class="layui-tab-content" style="padding: 20px 0;">
            <div class="layui-tab-item layui-show">
                <p style="font-size:13px;">
                    <a class="layui-btn" href="<?=url('admin/option/add')?>?type=<?=$type?>">添加变量</a>
                    <a id="update-cache" class="layui-btn" href="javascript:;" ac="<?=url('admin/api_option/option_cache')?>?type=<?=$type?>" confirm="false">更新缓存</a>
                </p>

                <form class="layui-form" method="post" action="<?=url('admin/api_option/option_all');?>?type=<?=$type?>" id="form-main">
                    <table class="layui-table">
                        <thead>
                        <tr>
                            <th width="80">名称</th>
                            <th width="110">说明</th>
                            <th width="50">格式</th>
                            <th>值</th>
                            <th width="50">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($data as $k =>$v): ?>
                            <tr>
                                <td><?=$v['name']?></td>
                                <td><?=$v['description']?></td>
                                <td><?=($v['json']?'json':'string')?></td>
                                <td><textarea class="layui-textarea" name="<?=$v['name']?>" style="min-height: 50px"><?=$v['value']?></textarea></td>
                                <td>
                                    <a class="layui-icon" title="删除" id="deletea_<?=$type.'_'.$v['name'];?>" href="javascript:;" ac="<?=url('admin/api_option/option_del')?>?name=<?=$v['name']?>&type=<?=$type?>">
                                        &#xe640;
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach;?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="5" align="center">
                                <input type="hidden" name="type" value="<?=$type?>">
                                <input class="layui-btn" type="submit" value="提交更改"  lay-filter="ajax" lay-submit>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
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
        //删除
        $("a[id^='deletea']").on('click', function(){
            fly.ajaxClick(this,function (res,_this) {
                _this.parent().parent().remove();
            });
            return false;
        });
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