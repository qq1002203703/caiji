{%extend@common/base%}

{%block@main%}
<div class="path">
    <a href="<?=url('admin/index/index')?>">首页</a>  > <?=$title?>
</div>
<div class="content">
    <div class="content-card">
        <div class="content-detail">
            <div class="content-button">
                <div class="content-top">
                    <h3>审核已经终结的</h3>
                    <p>
                        <?php foreach ($data as $item):?>
                            <a href="<?=url('admin/caiji/shenhe')?>?table=<?=$item['table']?>&name=<?=$item['name']?>" class="pure-button  btn-sm"><?=$item['name']?></a>
                        <?php endforeach;?>
                    </p>
                    <h3>批量审核终结</h3>
                    <p>
                        <?php foreach ($data as $item):?>
                            <a href="<?=url('admin/caiji/shenhem')?>?table=<?=$item['table']?>&name=<?=$item['name']?>" class="pure-button  btn-sm"><?=$item['name']?></a>
                        <?php endforeach;?>
                    </p>
                    <a href="<?=url('admin/other/queue_add')?>" class="pure-button btn-custom btn-sm">添加定时</a><br><br>
                </div>
            </div>
            <div class="content-item">

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
