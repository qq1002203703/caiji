{%extend@common/menu_main%}
{%block@main%}
<div class="fly-panel fly-panel-user" pad20>
    <div class="layui-tab layui-tab-brief" lay-filter="user">
        <ul class="layui-tab-title" id="LAY_mine">
            <li class="layui-this" lay-id="list"><?=$title?></li>
            <li><a href="<?=url('admin/caiji/handler')?>">采集结果管理</a></li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <div class="layui-form">
                    <p>
                        <a href="<?=url('admin/caiji/page_add')?>" class="layui-btn">添加采集项目</a>
                    </p>
                    <?php if($data): ?>
                    <form method="post" action="<?=url('api/caiji_admin/action_multi')?>" name="form2" id="form2">
                    <div class="wordpress-select" style="width:150px;float:left;margin-top:10px;">
                        <select name="batch" id="batch" lay-filter="send_time">
                            <option value="">批量操作</option>
                            <!--option value="order">批量排序</option>
                            <option value="move_article">批量转移</option-->
                            <option value="page_del">批量删除</option>
                            <!--option value="tj1">批量推荐</option>
                            <option value="tj2">批量特荐</option>
                            <option value="tj3">批量头条</option>
                            <option value="tj_cancel">取消推荐</option-->
                        </select>
                    </div>
                    <div style="float:left;margin-left:10px;margin-top:10px;">
                        <button class="layui-btn layui-btn-primary layui-btn-small" lay-filter="multi" lay-submit alert="1" batch="1">应用</button>
                    </div>
                </form>
                    <form class="layui-form" name="form1" id="form1" method="get" action="" lay-filter="shaixuan">
                        <div class="wordpress-select" style="width:150px;float:left;margin:10px">
                        <input style="display: inline-block;width: 160px;" value="<?=$get['keywords']?>" class="layui-input" autocomplete="off" placeholder="搜索关键词" type="text" name="keywords">
                    </div>
                        <div style="float:left;margin-left:10px;margin-top:10px;">
                        <a class="layui-btn layui-btn-primary layui-btn-small" id="shaixuan">筛选</a>&nbsp;
                        共<?=$total?>条
                    </div>
                    </form>
                    <form class="layui-form">
                        <table class="layui-table" id="form-main">
                        <thead>
                        <tr>
                            <th width="30"><input type="checkbox" class="layui-btn" style="width:80px;" value="全选" lay-filter="checkall"></th>
                            <th width="60">id</th>
                            <th>名称</th>
                            <th>目标网址</th>
                            <th width="140">时间</th>
                            <th width="50">状态</th>
                            <th width="60" style="text-align: center">操作</th>
                        </tr>
                        </thead>
                        <tbody id='find_ckeck'>
                        <?php foreach ($data as $item): ?>
                            <tr id="c<?=$item['id']?>">
                                <td>
                                    <input name="id[<?=$item['id']?>]" id="id_<?=$item['id']?>" type="checkbox" value="<?=$item['id']?>">
                                </td>
                                <td><?=$item['id']?></td>
                                <td><?=$item['name']?></td>
                                <td><?=str_replace('{%|||%}','<br>',$item['url']);?></td>
                                <td><span class="green"><?=date('Y-m-d H:i:s',$item['update_time'])?></span></td>
                                <td><?=$item['status']?></td>
                                <td style="text-align: center;">
                                    <i class="layui-icon" style="font-size: 20px;">
                                        <a title="修改" href="<?=url('admin/caiji/page_edit',['id'=>$item['id']])?>">&#xe642;</a>
                                    </i>
                                    <i class="layui-icon" style="font-size: 20px;">
                                        <a title="删除" id="deletea_<?=$item['id']?>" href="javascript:;" ac="<?=url('api/caiji_admin/page_del')?>?id=<?=$item['id']?>">
                                            &#xe640;
                                        </a>
                                    </i>
                                </td>
                            </tr>
                        <?php endforeach;?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th><input type="checkbox" class="layui-btn" style="width:80px;" value="全选" lay-filter="checkall"></th>
                            <th>id</th>
                            <th>名称</th>
                            <th>目标网址</th>
                            <th>时间</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                        </tfoot>
                    </table>
                    </form>
                    <div class="layui-clear">
                   <div class="wordpress-select">
                            <select name="batch2" id="batch2">
                                <option value="">批量操作</option>
                                <!--option value="order">批量排序</option>
                                <option value="move_article">批量转移</option-->
                                <option value="page_del">批量删除</option>
                                <!--option value="tj1">批量推荐</option>
                                <option value="tj2">批量特荐</option>
                                <option value="tj3">批量头条</option>
                                <option value="tj_cancel">取消推荐</option-->
                            </select>
                        </div>
                   <div class="wordpress-select">
                            <button class="layui-btn layui-btn-primary layui-btn-small" lay-filter="multi" lay-submit alert="1" batch="1">应用</button>
                        </div>
               </div>
                    <?=$page;?>
                    <?php endif;?>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
        var navSidebar="caiji-list";
        var currentData={id:0,type:""};
    </script>
{%end%}

{%block@javascript%}
<script type="text/javascript">
        layui.use(['layer','fly','form'], function() {
            var $ = layui.jquery,
             form = layui.form,
            fly=layui.fly;
            //删除一条
            $("a[id^='deletea']").on('click', function(){
                fly.ajaxClick(this);
                return false;
            });
            //筛选
            $("#shaixuan").click(function () {
                var allValue= $("#form1").serializeArray();
                var url='';
                layui.each(allValue,function () {
                    url+='&'+this.name+'='+this.value;
                });
               window.location.href="<?=url('admin/caiji/list')?>"+url;
            });
        });
    </script>
{%end%}