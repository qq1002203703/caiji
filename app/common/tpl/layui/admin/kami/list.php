{%extend@common/menu_main%}
{%block@main%}
<div class="fly-panel fly-panel-user" pad20>
    <div class="layui-tab layui-tab-brief" lay-filter="user">
        <ul class="layui-tab-title" id="LAY_mine">
            <li id="tab-pc" lay-id="list" class="layui-this"><a href="<?=url('admin/kami/list')?>"><?=$title;?></a></li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <form class="layui-form" name="form1" id="form1" method="get" action="" lay-filter="shaixuan">
                    <p>
                        <a class="layui-btn" href="<?=url('admin/kami/setting')?>">种类设置</a>
                        <a class="layui-btn" href="<?=url('admin/kami/make')?>">生成卡密</a>
                        <a class="layui-btn" href="javascript:;" id="delete_end"  ac="<?=url('api/kami_admin/del_end')?>">删除完结卡</a>
                    </p>
                    <div class="wordpress-select">
                        <select id="p_status" name="status">
                            <option  value="3"<?=echo_select($get['status'],2)?>>选择状态</option>
                            <option  value="0"<?=echo_select($get['status'],0)?>>刚出炉</option>
                            <option  value="1"<?=echo_select($get['status'],1)?>>出售中</option>
                            <option  value="2"<?=echo_select($get['status'],2)?>>完结了</option>
                        </select>
                    </div>
                    <div class="wordpress-select">
                        <select id="p_type" name="type">
                            <option  value="0"<?=echo_select($get['type'],0)?>>选择种类</option>
                            <?php $kami_type=[]; foreach ($type as $item): $kami_type[]=$item['text'];?>
                            <option  value="<?=$item['id']?>"<?=echo_select($get['type'],$item['id'])?>><?=$item['text']?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                    <div class="wordpress-select">
                        <input style="display: inline-block;width: 160px;" value="<?=$get['ka']?>" class="layui-input" autocomplete="off" placeholder="检索卡密" type="text" name="ka">
                    </div>
                    <div class="wordpress-select" style="width:100px;margin-left: 50px;margin-right:0;">
                        <input style="width:80px;display: inline-block;" value="<?=$get['pp']?>" class="layui-input" type="text" name="pp">
                    </div>
                     <div class="wordpress-select layui-form-mid layui-word-aux" style="width:40px;margin-left:0;margin-right: 20px;">条/页</div>

                    <div style="float:left;margin-left:10px;margin-top:10px;">
                        <a class="layui-btn layui-btn-normal layui-btn-small" id="shaixuan">筛选</a>&nbsp;
                        共<?=$total?>条
                    </div>
                </form>
                <div class="clearfix"></div>
                <?php $arr=['id'=>'','ka'=>'','status'=>'','type'=>''];$typeArr=['刚出炉','出售中','完结了']; if($data):$arr=['id'=>'<ul>','ka'=>'<ul>','status'=>'<ul>','type'=>'<ul>'];?>
                    <?php foreach ($data as $item):?>
                        <?php $arr['id'].='<li>'.$item['id'].'</li>';?>
                        <?php $arr['ka'].='<li>'.$item['ka'].'</li>';?>
                        <?php $arr['status'].='<li>'.$typeArr[$item['status']].'</li>';?>
                        <?php $arr['type'].='<li>'.$kami_type[$item['type']].'</li>';?>
                    <?php endforeach;?>
                    <?php $arr['id'].='</ul>';?>
                    <?php $arr['ka'].='</ul>';?>
                    <?php $arr['status'].='</ul>';?>
                    <?php $arr['type'].='</ul>';?>
                <?php endif;?>
                <style>
                    .layui-table li{border-bottom: 1px solid #E6E6E6;line-height: 35px;}
                    .layui-table li:last-child{border-bottom:none}
                </style>
                <table class="layui-table">
                        <colgroup>
                            <col width="13%">
                            <col width="55%">
                            <col width="17%">
                            <col width="15%">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>卡密</th>
                            <th>状态</th>
                            <th>种类</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?=$arr['id'];?></td>
                                <td><?=$arr['ka'];?></td>
                                <td><?=$arr['status'];?></td>
                                <td><?=$arr['type'];?></td>
                            </tr>
                            <?php if($get['status']===0):?>
                        <tr>
                            <td colspan="4"><a id="btn-sale" class="layui-btn" href="javascript:;">设置为出售中</a> </td>
                        </tr>
                        <?php endif;?>
                        </tbody>
                </table>
                <?=$page;?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var navSidebar="kami-list";
</script>
{%end%}
{%block@javascript%}
<script type="text/javascript">
    layui.use(['layer','fly'],function () {
        var $=layui.jquery;
        var fly=layui.fly;
        //筛选
        $("#shaixuan").click(function () {
            var allValue= $("#form1").serializeArray();
            var url='';
            layui.each(allValue,function () {
                url+='&'+this.name+'='+this.value;
            });
            window.location.href="<?=url('admin/kami/list')?>"+url.replace('&','?');
        });
        //设为出售中
        $("#btn-sale").click(function () {
            var kami_type=<?=$get['type'];?>;
            if(kami_type <=0){
                layer.msg('请先选择种类');
                return false;
            }
           fly.json("<?=url('api/kami_admin/type2sale')?>", {large_id:<?php $aa=end($data);echo $aa['id']?:0;?>,type:kami_type}, function (res) {
                    window.location.reload();
            });
            return false;
        });
        //删除完结的卡密
        $("#delete_end").on('click', function(){
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