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
                    <a class="layui-btn" id="thumb-del" href="javascript:;" ac="<?=url('admin/api_upload/thumb_del')?>">删除缩略图</a>
                    <a class="layui-btn" id="thumb-create" href="javascript:;" ac="<?=url('admin/api_upload/thumb_create')?>">生成缩略图</a>&nbsp;&nbsp;
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
                    <?php if(isset($data) && $data): foreach ($data as $item): ?>
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
                        <?//=$page?>
                    </div>
                </form>
            </div>
        </div>
        </div>
    </div>
</div>
    <script type="text/javascript">
        var navSidebar="upload-manage";
    </script>
{%end%}

{%block@javascript%}
    <script type="text/javascript">
        layui.use(['layer','fly','form'], function() {
            var $ = layui.jquery,
             layer = layui.layer,
            fly=layui.fly;
            //删除一条
            $("a[id^='deletea']").on('click', function(){
                fly.ajaxClick(this);
                return false;
            });
            var  layerBox=function(t) {
                var msg=(t==='del' ? '删除':'生成');
                var all=(t==='del' ? '，注意"all"表示全部规格':'');
                return '<div id="layer-box" style="padding:20px;">\n' +
                    '    <div class="layui-form">\n' +
                    '        <div class="layui-form-item">\n' +
                    '            <label class="layui-form-label">'+msg+'的规格</label>\n' +
                    '            <div class="layui-input-block">\n' +
                    '                <input class="layui-input" type="text" name="format" value="">\n' +
                    '                <div class="layui-input-mid layui-word-aux">填写你需要'+msg+'的缩略图规格，如："150x150"'+all+' (不含双引号)</div>\n' +
                    '            </div>\n' +
                    '        </div>\n' +
                    '        <div class="layui-form-item">\n' +
                    '            <label class="layui-form-label">是否重设isdo</label>\n' +
                    '            <div class="layui-input-block">\n' +
                    '                <input class="layui-input" type="text" name="isdo" value="0">\n' +
                    '                <div class="layui-input-mid layui-word-aux">设置为1时,每次运行前都先重置数据表中的isdo字段</div>\n' +
                    '            </div>\n' +
                    '        </div>\n' +
                    '    </div>\n' +
                    '</div>';
            };
            //缩略图删除和生成
            $("#thumb-del,#thumb-create").on('click',function () {
                var _this=$(this);
                var t=this.id.replace('thumb-','');
                layer.open({
                    type:1,
                    area: ['500px', '300px'],
                    title:_this.html(),
                    content:layerBox(t),
                    btn:["马上提交",'取消'],
                    yes:function (index, layero) {
                        var format=layero.find("input[name='format']").val();
                        var isdo=layero.find("input[name='isdo']").val();
                        if(format===''){
                            layer.confirm("规格不能为空");
                            return false;
                        }
                        fly.json(_this.attr('ac'),{format:format,isdo:isdo},function (res) {
                            layer.msg(res.msg);
                            layer.close(index);
                        });
                    }
                });
            });
        });

    </script>
{%end%}