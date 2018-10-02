{%extend@common/base%}

{%block@main%}
<div class="path">
    <a href="<?=url('admin/index/index')?>">首页</a> >  其它设置</a> > <?=$title?>
</div>
<div class="content">
    <div class="content-card">
        <div class="content-detail">
            <div class="content-button">
                <div class="content-top">
                    <a href="<?=url('admin/other/queue_add')?>" class="pure-button btn-custom btn-sm">添加定时</a><br><br>
                </div>
            </div>
            <div class="content-item">
                <form class="pure-form search-form mb-8" method="get">
                    <div class="dh-dropdown">
                        <input type="checkbox" class="pure-checkbox js-check-all">
                        <button class="pure-button btn-sm dh-dropdown-click" type="button">选中项<span>&#9660;</span></button>
                        <ul class="dh-dropdown-content delete-dropdown-content">
                            <li class="dh-dropdown-item delete-all"><a href="<?=url('admin/other/queue_del')?>">删除</a></li>
                        </ul>
                    </div>
                </form>
                <form class="pure-form" action="" id="myform">
                    <table class="full pure-table pure-table-bordered">
                        <thead>
                        <tr>
                            <th width="18"></th>
                            <td width="50">id</td>
                            <td>回调函数</td>
                            <td>执行时间</td>
                            <td>类参数</td>
                            <td>函数参数</td>
                            <td width="50">状态</td>
                            <td width="50">种类</td>
                            <td>删除方式</td>
                            <td width="120" align="center">操作</td>

                        </tr>
                        </thead>
                        <tbody>
                        <?php  $status=['未执行','已执行'];foreach ($data as $item):?>
                            <tr>
                                <td><input class="pure-checkbox" type="checkbox" value="<?=$item['id']?>" name="ids[]"></td>
                                <td><?=$item['id']?></td>
                                <td><?=$item['callable']?></td>
                                <td><?=date('Y-m-d H:i:s',$item['run_time'])?></td>
                                <td><?=$item['class_param']?></td>
                                <td><?=$item['method_param']?></td>
                                <td class="status"><?=$item['status']?></td>
                                <td class="type"><?=$item['type']?></td>
                                <td class="del_type"><?=$item['del_type']?></td>
                                <td align="center">
                                    <a class="pure-button btn-success btn-xs edit" href="/admin/other/queue_edit.html?id=<?=$item['id']?>">编辑</a>
                                </td>
                            </tr>
                        <?php endforeach;?>
                        </tbody>
                        <?php if(isset($page) && $page): ?>
                            <tfoot>
                            <tr>
                                <td colspan="7"><?=$page?></td>
                            </tr>
                            </tfoot>
                        <?php endif; ?>
                    </table>
                </form>
                <form class="pure-form search-form mt-8" method="get">
                    <div class="dh-dropdown">
                        <input type="checkbox" class="pure-checkbox js-check-all">
                        <button class="pure-button btn-sm dh-dropdown-click" type="button">选中项<span>&#9660;</span></button>
                        <ul class="dh-dropdown-content delete-dropdown-content">
                            <li class="dh-dropdown-item delete-all"><a href="<?=url('admin/other/queue_del')?>">删除</a></li>
                        </ul>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
{%end%}

{%block@javascript%}
<style>
    #jtab label{margin-top:16px !important;margin-bottom:8px !important;}
</style>
<script charset="UTF-8" type="text/javascript">
    $(function(){
        changeStatus();
        var someClick=new clickClass();
        //someClick.ajaxLink('.update-cache');
        someClick.ajaxLink('.upload_menu');
        someClick.checkedAll('.js-check-all','#myform td >input[type="checkbox"]');
        someClick.ajaxCheckbox('.delete-all','#myform td >input[type="checkbox"]:checked');
        /*someClick.ajaxLink('.pure-table .change',function () {
            location.reload();
        });*/
    });
    function  changeStatus() {
        $('.status').each(function () {
            if($(this).html()=='1')
                $(this).html('已执行');
            else
                $(this).html('未执行');
        });
    }
</script>
{%end%}
