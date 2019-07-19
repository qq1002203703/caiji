{%extend@common/menu_main%}
{%block@main%}
<div class="fly-panel fly-panel-user" pad20>
    <div class="layui-tab layui-tab-brief" lay-filter="user">
        <ul class="layui-tab-title" id="LAY_mine">
            <li id="tab-pc" lay-id="list" class="layui-this"><?=$title;?></li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <p>
                    <a class="layui-btn" href="<?=url('admin/caiji/queue_add')?>">添加定时</a>
                </p>
                <div class="layui-form">
                    <table class="layui-table">
                        <thead>
                        <tr>
                            <th width="50">ID</th>
                            <th>回调函数</th>
                            <th width="140">执行时间</th>
                            <th>类参数</th>
                            <th>函数参数</th>
                            <th width="50">状态</th>
                            <th width="50">种类</th>
                            <th width="60">删除方式</th>
                            <th width="100">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if ($data): $i=1;foreach ($data as $item) : ?>
                        <tr>
                            <td><?=$item['id']?></td>
                            <td><?=$item['callback']?></td>
                            <td><?=date('Y-m-d H:i:s',$item['run_time'])?></td>
                            <td><?=$item['class_param']?></td>
                            <td><?=$item['method_param']?></td>
                            <td class="status"><?=$item['status']?></td>
                            <td class="type"><?=$item['type']?></td>
                            <td class="del_type"><?=$item['del_type']?></td>
                            <td>
                                <i class="layui-icon" style="font-size: 20px;">
                                    <a title="编辑" href="<?=url('admin/caiji/queue_edit',['id'=>$item['id']])?>">&#xe642;</a>
                                </i>
                                <i class="layui-icon" style="font-size: 20px;">
                                    <a title="删除" id="deletea_<?=$item['id']?>" href="javascript:;" ac="<?=url('api/caiji_admin/queue_del')?>?id=<?=$item['id']?>">
                                        &#xe640;
                                    </a>
                                </i>
                            </td>
                        </tr>
                        <?php endforeach;endif;?>
                        </tbody>
                    </table>
                   <!--?//=$page?-->
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var navSidebar="caiji-queue";
</script>
{%end%}
{%block@javascript%}
<script type="text/javascript">
    layui.use(['layer','fly'],function () {
        var $ = layui.jquery;
        var fly=layui.fly;
        layer.ready(function () {
           changeStatus();
        });
        //删除一条
       $("a[id^='deletea']").on('click', function(){
            var ac=this.id;
            ac=ac.replace('deletea_','');
            $(this).attr('ac',"<?=url('api/caiji_admin/queue_del')?>?id="+ac);
            fly.ajaxClick(this);
            return false;
        });
        //筛选
        /*$("#shaixuan").click(function () {
            var allValue= $("#form1").serializeArray();
            var url='';
            layui.each(allValue,function () {
                url+='&'+this.name+'='+this.value;
            });
            window.location.href="<!--?//=url('admin/tag/manage')?>?type="+url;
        });*/
        function  changeStatus() {
            $('.status').each(function () {
                if($(this).html()==='1')
                    $(this).html('已执行');
                else
                    $(this).html('<span class="red">未执行</span>');
            });
        }
    });
</script>
{%end%}