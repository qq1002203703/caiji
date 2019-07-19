{%extend@common/menu_main%}
{%block@main%}
<div class="fly-panel fly-panel-user" pad20>
    <div class="layui-tab layui-tab-brief" lay-filter="user">
        <ul class="layui-tab-title" id="LAY_mine">
            <li id="tab-pc" lay-id="list" class="layui-this"><a href="<?=url('admin/tag/manage')?>"><?=$title;?></a></li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <form class="layui-form" name="form1" id="form1" method="get" action="" lay-filter="shaixuan">
                    <p>
                        <a id="btn-sitemap" class="layui-btn" ac="<?=url('api/tag_admin/sitemap')?>">提交SiteMap</a>
                    </p>
                    <div class="wordpress-select">
                        <select id="p_status" name="status">
                            <option  value="2"<?=echo_select($get['status'],2)?>>选择状态</option>
                            <option  value="1"<?=echo_select($get['status'],1)?>>状态:显示</option>
                            <option  value="0"<?=echo_select($get['status'],0)?>>状态:隐藏</option>
                        </select>
                    </div>
                    <div class="wordpress-select" style="width:150px;float:left;margin:10px">
                        <input style="display: inline-block;width: 160px;" value="<?//=$get['keywords']?>" class="layui-input" autocomplete="off" placeholder="搜索关键词" type="text" name="keywords">
                    </div>
                    <div style="float:left;margin-left:10px;margin-top:10px;">
                        <a class="layui-btn layui-btn-primary layui-btn-small" id="shaixuan">筛选</a>&nbsp;
                        共<?=$total?>条
                    </div>
                </form>
                <div class="layui-form">
                    <table class="layui-table">
                        <colgroup>
                            <col width="8%">
                            <col width="14%">
                            <col width="8%">
                            <col width="27%">
                            <col width="12%">
                            <col width="15%">
                            <col width="8%">
                            <col width="8%">
                            <col>
                        </colgroup>
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>名称</th>
                            <th>状态</th>
                            <th>seo标题</th>
                            <th>时间</th>
                            <th>别名</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if ($data): $i=1;foreach ($data as $item) : ?>
                        <tr>
                            <td><?=$item['id']?></td>
                            <td>
                                <a href="<?=url('admin/tag/edit',['id'=>$item['id']])?>" target="_blank" title="编辑"><?=$item['name']?></a>
                                <a href="<?=url('@tag@',['slug'=>$item['slug']])?>" class="gray yang-icon"  target="_blank" title="去前台浏览"><i class="layui-icon layui-icon-link"></i></a>
                            </td>
                            <td><?=$item['status']?></td>
                            <td><?=$item['seo_title']?></td>
                            <td><?=date('Y-m-d H:i',$item['create_time'])?></td>
                            <td><?=$item['slug']?></td>
                            <td>
                                <i class="layui-icon" style="font-size: 20px;">
                                    <a title="编辑" href="<?=url('admin/tag/edit',['id'=>$item['id']])?>">&#xe642;</a>
                                </i>
                                <i class="layui-icon" style="font-size: 20px;">
                                    <a title="删除" id="deletea_<?=$item['id']?>" href="javascript:;" ac="<?=$item['id']?>">&#xe640;</a>
                                </i>

                            </td>
                        </tr>
                        <?php endforeach;endif;?>
                        </tbody>
                    </table>
                   <?=$page?>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var navSidebar="tag-manage";
    var currentData={id:0,type:""};
</script>
{%end%}
{%block@javascript%}
<script type="text/javascript">
    layui.use(['layer','fly'],function () {
        var $ = layui.jquery;
        var fly=layui.fly;
        //删除一条
        $("a[id^='deletea']").on('click', function(){
            var ac=this.id;
            ac=ac.replace('deletea_','');
            $(this).attr('ac',"<?=url('api/tag_admin/del')?>?id="+ac);
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
            window.location.href="<?=url('admin/tag/manage')?>"+url.replace('&','?');
        });
        $("#btn-sitemap").click(function () {
            var _this=$(this);
            var action=_this.attr('ac');
            layer.open({
                type:1,
                area: ['500px', '300px'],
                btn:["马上提交",'取消'],
                content:'<div id="layer-box" style="padding:20px;">\n' +
                    '    <div class="layui-form">\n' +
                    '        <div class="layui-form-item">\n' +
                    '            <label class="layui-form-label">方式</label>\n' +
                    '            <div class="layui-input-block">\n' +
                    '                <input class="layui-input" type="text" name="sitemap_type" value="1">\n' +
                    '                <div class="layui-input-mid layui-word-aux">1为当天，2为只提交额外，其他为全部</div>\n' +
                    '            </div>\n' +
                    '        </div>\n' +
                    '        <div class="layui-form-item">\n' +
                    '            <label class="layui-form-label">额外</label>\n' +
                    '            <div class="layui-input-block">\n' +
                    '                <textarea class="layui-textarea" name="sitemap_other"></textarea>\n' +
                    '                <div class="layui-input-mid layui-word-aux">另外要提交的链接，一行一个</div>\n' +
                    '            </div>\n' +
                    '        </div>\n' +
                    '    </div>\n' +
                    '</div>',
                yes:function (index, layero) {
                    var sitemap_type=layero.find("input[name='sitemap_type']").val();
                    var sitemap_other=layero.find("textarea[name='sitemap_other']").val();
                    fly.json(action,{type:sitemap_type,other:sitemap_other},function (res) {
                        var msg='';
                        layui.each(res.data,function (index,element) {
                            msg += index+':'+element.result+'<br>';
                        });
                        layer.msg(msg);
                        //console.log(res.data);
                        layer.close(index);
                    });
                }
            });
        });
    });
</script>
{%end%}