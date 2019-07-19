{%extend@common/menu_main%}
{%block@main%}
<div class="fly-panel fly-panel-user" pad20>
    <div class="layui-tab layui-tab-brief" lay-filter="user">
        <ul class="layui-tab-title" id="LAY_mine">
            <li class="layui-this" lay-id="list"><?=$title?></li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
            <div class="layui-form">
                <p style="font-size:13px;">
                    <a class="layui-btn" href="<?=url('admin/other/links_add')?>">添加锚文本</a>&nbsp;&nbsp;&nbsp;
                </p>
                <form method="post" action="<?=url('admin/api_other/links_multi_action')?>" name="form-main" id="form-main">
                    <div class="wordpress-select" style="width:150px;float:left;margin-top:10px;">
                        <select name="batch" id="batch" lay-filter="send_time">
                            <option value="">批量操作</option>
                            <!--option value="order">批量排序</option>
                            <option value="move_article">批量转移</option-->
                            <option value="del">批量删除</option>
                            <!--option value="tj1">批量推荐</option>
                            <option value="tj2">批量特荐</option>
                            <option value="tj3">批量头条</option>
                            <option value="tj_cancel">取消推荐</option-->
                        </select>
                    </div>
                    <div style="float:left;margin-left:10px;margin-top:10px;">
                        <button class="layui-btn layui-btn-primary layui-btn-small" lay-filter="multi" lay-submit alert="1" batch="1">应用</button>
                    </div>
                    <table class="layui-table">
                    <thead>
                    <tr>
                        <th width="20"><input type="checkbox" class="layui-btn" style="width:80px;" value="全选" lay-filter="checkall"></th>
                        <th width="80">id</th>
                        <th width="100">关键词</th>
                        <th>URL</th>
                        <th>替换次数</th>
                        <th>状态</th>
                        <th>权重</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody id='find_ckeck'>
                    <?php if($data): foreach ($data as $item): ?>
                        <tr id="c<?=$item['id']?>">
                            <td>
                                <input name="id[<?=$item['id']?>]" id="id_<?=$item['id']?>" type="checkbox" value="<?=$item['id']?>">
                            </td>
                            <td><?=$item['id']?></td>
                            <td><?=$item['keyword']?></td>
                            <td><?=$item['url']?></td>
                            <td><?=$item['num']?></td>
                            <td><?=$item['status']?></td>
                            <td><?=$item['weight']?></td>
                            <td style="text-align: center;">
                                <i class="layui-icon" style="font-size: 20px;">
                                    <a title="修改" href="<?=url('admin/other/links_edit',['id'=>$item['id']])?>">&#xe642;</a>
                                </i>
                                <i class="layui-icon" style="font-size: 20px;">
                                    <a title="删除" id="deletea_<?=$item['id']?>" href="javascript:;" ac="<?=url('admin/api_other/links_del')?>?id=<?=$item['id']?>">
                                        &#xe640;
                                    </a>
                                </i>
                            </td>
                        </tr>
                    <?php endforeach;endif;?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th width="20"><input type="checkbox" class="layui-btn" style="width:80px;" value="全选" lay-filter="checkall"></th>
                        <th width="80">id</th>
                        <th width="200">关键词</th>
                        <th>URL</th>
                        <th>替换次数</th>
                        <th>状态</th>
                        <th>权重</th>
                        <th width="80">操作</th>
                    </tr>
                    </tfoot>
                </table>
                    <div>
                        <div class="wordpress-select" style="width:120px;float:left;margin-right: 10px;">
                            <select name="batch2" id="batch2">
                                <option value="">批量操作</option>
                                <!--option value="order">批量排序</option>
                                <option value="move_article">批量转移</option-->
                                <option value="del">批量删除</option>
                                <!--option value="tj1">批量推荐</option>
                                <option value="tj2">批量特荐</option>
                                <option value="tj3">批量头条</option>
                                <option value="tj_cancel">取消推荐</option-->
                            </select>
                        </div>
                        <div style="margin-left:10px;">
                            <button class="layui-btn layui-btn-primary layui-btn-small" lay-filter="multi" lay-submit alert="1" batch="1">应用</button>
                        </div>
                        <?=$page?>
                    </div>
                </form>
            </div>
        </div>
        </div>
    </div>
</div>
    <script type="text/javascript">
        var navSidebar="other-links";
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
        });

    </script>
{%end%}