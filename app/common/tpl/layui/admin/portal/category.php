{%extend@common/menu_main%}
{%block@main%}
<div class="fly-panel fly-panel-user" pad20>
    <div class="layui-tab layui-tab-brief" lay-filter="user">
        <div class="pull-right help-control">
            <a href="<?=url('admin/portal/setting')?>" title="设置"><i class="layui-icon">&#xe614;</i></a>
            <a href="javascript:;" title="帮助"><i class="layui-icon">&#xe607;</i></a>
        </div>
        <ul class="layui-tab-title" id="LAY_mine">
            <li class="layui-this" lay-id="list"><?=$title?></li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
            <div class="layui-form">
                <p style="font-size:13px;">
                    <a class="layui-btn" href="<?=url('admin/portal/category_add')?>?type=<?=$type?>">添加分类</a>
                    <a id="update-cache" class="layui-btn" href="javascript:;" ac="<?=url('admin/api_portal/category_cache')?>?type=<?=$type?>" confirm="false">更新缓存</a>
                </p>

                <form method="post" action="<?=url('admin/api_portal/category_multi_action')?>?type=<?=$type?>" name="form2" id="form2">
                    <div class="wordpress-select" style="width:150px;float:left;margin-top:10px;">
                        <select name="batch" id="batch" lay-filter="send_time">
                            <option value="">批量操作</option>
                            <!--option value="order">批量排序</option>
                            <option value="move_article">批量转移</option-->
                            <!--option value="del">批量删除</option-->
                            <!--option value="tj1">批量推荐</option>
                            <option value="tj2">批量特荐</option>
                            <option value="tj3">批量头条</option>
                            <option value="tj_cancel">取消推荐</option-->
                        </select>
                    </div>
                    <div style="float:left;margin-left:10px;margin-top:10px;margin-bottom:10px;">
                        <button class="layui-btn layui-btn-primary layui-btn-small" lay-filter="multi" lay-submit alert="1" batch="1">应用</button>
                    </div>
                    <table class="layui-table">
                        <colgroup>
                            <col width="3%">
                            <col width="10%">
                            <col width="33%">
                            <col width="15%">
                            <col width="12%">
                            <col width="20%">
                            <col width="7%">
                        </colgroup>
                        <thead>
                        <tr style="color: #009688">
                            <th><input type="checkbox" class="layui-btn" style="width:80px;" value="全选" lay-filter="checkall"></th>
                            <th>id</th>
                            <th>名称</th>
                            <th>seo标题</th>
                            <th>seo关键词</th>
                            <th>seo描述</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody id='find_ckeck'>
                        <?=$category;?>
                        </tbody>
                        <tfoot>
                        <tr style="color: #009688;background-color: #F2F2F2">
                            <th><input type="checkbox" class="layui-btn" style="width:80px;" value="全选" lay-filter="checkall"></th>
                            <th>id</th>
                            <th>名称</th>
                            <th>seo标题</th>
                            <th>seo关键词</th>
                            <th>seo描述</th>
                            <th>操作</th>
                        </tr>
                        </tfoot>
                    </table>
                </form>
            </div>
        </div>
        </div>
    </div>
</div>
    <script type="text/javascript">
        var navSidebar="portal-category-<?=$type?>";
        var currentData={id:0,type:"<?=$type?>"};
    </script>
{%end%}

{%block@javascript%}
    <script type="text/javascript">
        layui.use(['layer','form','fly'], function() {
            var $ = layui.jquery,
             form = layui.form,
            fly=layui.fly;
            //删除一条
            $("a[id^='deletea']").on('click', function(){
                var ac=this.id;
                ac=ac.replace('deletea_','');
                $(this).attr('ac',"<?=url('admin/api_portal/category_del')?>?type="+currentData.type+'&id='+ac);
                fly.ajaxClick(this);
                return false;
            });
            //编辑一条
            $("a[id^='edita_']").on('click', function(){
                var ac=this.id;
                ac=ac.replace('edita_','');
                $(this).attr('href',"<?=url('admin/portal/category_edit')?>?type="+currentData.type+'&id='+ac);
                //fly.ajaxClick(this);
                return true;
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