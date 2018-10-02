{%extend@common/base%}

{%block@main%}
<div class="path">
    <a href="<?=url('admin/index/index')?>">首页</a> > 采集设置 > <a href="<?=url('admin/caiji/handler')?>">项目管理</a> > <?=$title?>
</div>
<div class="content">
    <div class="content-card">
        <div class="content-detail">
            <div class="content-button">
                <div class="content-top">
                    总共：<?=$total?><br><br>
                </div>
            </div>
            <div class="content-item">
                <form class="pure-form search-form mb-8" method="get">
                    <div class="dh-dropdown">
                        <input type="checkbox" class="pure-checkbox js-check-all">
                        <button class="pure-button btn-sm dh-dropdown-click" type="button">选中项<span>&#9660;</span></button>
                        <ul class="dh-dropdown-content delete-dropdown-content">
                            <li class="dh-dropdown-item delete-all"><a href="<?=url('admin/caiji/shenhem_json')?>?table=<?=get('table')?>">改为垃圾</a></li>
                            <li class="dh-dropdown-item delete-all"><a href="<?=url('admin/caiji/shenhem_json')?>?table=<?=get('table')?>&laji=0">审核通过</a></li>
                        </ul>
                    </div>
                </form>
                <form class="pure-form" action="" id="myform">
                    <table class="full pure-table pure-table-bordered">
                        <thead>
                        <tr>
                            <th width="18"></th>
                            <td width="50">id</td>
                            <td>标题</td>
                            <td>创建时间</td>
                            <td width="50">完结</td>
                            <td width="120" align="center">操作</td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php  foreach ($data as $item):?>
                            <tr>
                                <td><input class="pure-checkbox" type="checkbox" value="<?=$item['id']?>" name="ids[]"></td>
                                <td><?=$item['id']?></td>
                                <td><?=$item['title']?><a class="show-detail" href="javscript:;">详情</a><div class="detail" style="display: none"><?=$item['content']?></div></td>
                                <td><?=date('Y-m-d H:i:s',$item['create_time'])?></td>
                                <td><?=$item['isend']?></td>
                                <td align="center">
                                    <a class="pure-button btn-success btn-xs edit" href="<?=url('admin/caiji/shenhe')?>?id=<?=$item['id']?>&table=<?=get('table')?>" target="_blank">编辑</a>
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
                            <li class="dh-dropdown-item delete-all"><a href="<?=url('admin/caiji/shenhem_json')?>?table=<?=get('table')?>">改为垃圾</a></li>
                            <li class="dh-dropdown-item delete-all"><a href="<?=url('admin/caiji/shenhem_json')?>?table=<?=get('table')?>&laji=0">审核通过</a></li>
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
    .show-detail{display: inline-block;margin-left: 10px;}
</style>
<script charset="UTF-8" type="text/javascript" src="/static/lib/layer/layer.js"></script>
<script charset="UTF-8" type="text/javascript">
    $(function(){
        //changeStatus();
        var someClick=new clickClass();
        //someClick.ajaxLink('.update-cache');
        someClick.ajaxLink('.upload_menu');
        someClick.checkedAll('.js-check-all','#myform td >input[type="checkbox"]');
        someClick.ajaxCheckbox('.delete-all','#myform td >input[type="checkbox"]:checked');
        /*someClick.ajaxLink('.pure-table .change',function () {
         location.reload();
         });*/
        $('.show-detail').on('click',function () {
            //var html=$(this).siblings('.detail').html();
            layer.open({
                title:'详情',
                type: 1,
                area: '600px',
                maxmin: true,
                //shadeClose: true, //点击遮罩关闭
                content: '<div style="padding:20px;">'+$(this).siblings('.detail').html()+'</div>'
            });
            //layer.full(index);
            return false;
        })
    });
    /*function  changeStatus() {
        $('.status').each(function () {
            if($(this).html()=='1')
                $(this).html('已执行');
            else
                $(this).html('未执行');
        });
    }*/
</script>
{%end%}
