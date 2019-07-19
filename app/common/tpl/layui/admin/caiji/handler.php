{%extend@common/menu_main%}
{%block@main%}
<div class="fly-panel fly-panel-user" pad20>
    <div class="layui-tab layui-tab-brief" lay-filter="user">
        <ul class="layui-tab-title" id="LAY_mine">
            <li><a href="<?=url('admin/caiji/list')?>">采集任务管理</a></li>
            <li class="layui-this" lay-id="list"><?=$title?></li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <div class="layui-form">
                    <?php if(!$get['name']):?>
                    <p class="red">请先选择一个采集项目：</p>
                    <?php endif;?>
                    <p>
                    <?php if($projects): foreach ($projects as $project):?>
                        <a href="<?=url('admin/caiji/handler')?>?table=<?=$project['table']?>&name=<?=$project['name']?>" class="layui-btn"><?=$project['name']?></a>
                    <?php endforeach;endif;?>
                </p>
                    <?php if($data): ?>
                    <form method="post" action="<?=url('api/caiji_admin/action_multi')?>" name="form2" id="form2">
                    <div class="wordpress-select" style="width:150px;float:left;margin-top:10px;">
                        <select name="batch" id="batch" lay-filter="send_time">
                            <option value="">批量操作</option>
                            <!--option value="order">批量排序</option>
                            <option value="move_article">批量转移</option-->
                            <option value="laji">设为垃圾</option>
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
                    <!--div class="wordpress-select" style="width:150px;float:left;margin-left:10px;margin-top:10px;margin-bottom:10px;">
                        <select id="category_id" name="category_id">
                            <option value="0">所有分类</option>
                            <//=$categorys>
                        </select>
                    </div-->
                    <div class="wordpress-select">
                        <select id="isend" name="isend">
                            <option  value="1"<?=echo_select($get['isend'],1)?>>状态:完结</option>
                            <option  value="0"<?=echo_select($get['isend'],0)?>>状态:未结</option>
                        </select>
                    </div>
                    <div class="wordpress-select">
                        <select id="isshenhe" name="isshenhe">
                            <option  value="1"<?=echo_select($get['isshenhe'],1)?>>审核:是</option>
                            <option  value="0"<?=echo_select($get['isshenhe'],0)?>>审核:否</option>
                        </select>
                    </div>
                    <div class="wordpress-select">
                        <select id="islaji" name="islaji">
                            <option  value="1"<?=echo_select($get['islaji'],1)?>>垃圾:是</option>
                            <option  value="0"<?=echo_select($get['islaji'],0)?>>垃圾:否</option>
                        </select>
                    </div>
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
                            <th width="80">id</th>
                            <th><a href=''>标题</a></th>
                            <th>标签</th>
                            <th>创建时间</th>
                            <th width="50">完结</th>
                            <th width="100" style="text-align: center">操作</th>
                        </tr>
                        </thead>
                        <tbody id='find_ckeck'>
                        <?php /*dump($data);*/ $thumbFormat=getThumbFormat();$model=app('app\admin\model\Tag');foreach ($data as $item): $tags_name=$model->getName($item['id'],'bbs_normal');?>
                            <tr id="c<?=$item['id']?>">
                                <td>
                                    <input name="id[<?=$item['id']?>]" id="id_<?=$item['id']?>" type="checkbox" value="<?=$item['id']?>">
                                </td>
                                <td><?=$item['id']?></td>
                                <td>
                                    <a id="add-tag-<?=$item['id']?>" title="添加tag标签" data-id="<?=$item['id']?>" data-tag="<?=$item['tag'];?>"><?=$item['title']?></a>
                                    <div class="layui-hide" id="content-<?=$item['id']?>"><?=$item['content']?></div>
                                </td>
                                <td id="tag-<?=$item['id']?>"><?=$item['tag']?></td>
                                <td><span class="green"><?=date('Y-m-d',$item['create_time'])?></span></td>
                                <td><?=$item['isend']?></td>
                                <td style="text-align: center;">
                                    <i class="layui-icon" style="font-size: 20px;">
                                        <a title="修改" href="<?=url('admin/caiji/edit',['table'=>$get['table'],'id'=>$item['id']])?>">&#xe642;</a>
                                    </i>
                                    <i class="layui-icon" style="font-size: 20px;">
                                        <a title="设为垃圾" id="deletea_<?=$item['id']?>" href="javascript:;" ac="<?=url('api/caiji_admin/set_laji')?>?table=<?=$get['table']?>&id=<?=$item['id']?>">
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
                            <th width="50">id</th>
                            <th><a href=''>标题</a></th>
                            <th>标签</th>
                            <th>创建时间</th>
                            <th>完结</th>
                            <th style="text-align: center">操作</th>
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
                                <option value="laji">设为垃圾</option>
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
        var navSidebar="caiji-handler";
        var currentData={id:0,type:""};
    </script>
{%end%}

{%block@javascript%}
<script type="text/javascript">
        layui.use(['layer','fly','form'], function() {
            var $ = layui.jquery,
             layer = layui.layer,
            fly=layui.fly;
           /* layer.ready(function () {
                //筛选的分类默认选中项
                $('#category_id').find("option[value='//=$get['category_id']']").attr('selected',true);
                form.render('select','shaixuan');
            });*/
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
               window.location.href="<?=url('admin/caiji/handler')?>?table=<?=$get['table']?>&name=<?=$get['name']?>"+url;
            });

            //修改标签
            $("a[id^='add-tag-").on('click', function(){
                var _this=$(this);
                var id=_this.attr('data-id');
                var title=_this.html();
                var tags=_this.attr('data-tag');
                var details=$("#content-"+id).html();
                layer.open({
                    title:'添加标签',
                    type:1,
                    area: ['800px', '600px'],
                    content:'<div id="layer-box" style="padding:20px;">\n' +
                        '    <form class="layui-form">\n' +
                        '        <div class="layui-form-item">\n' +
                        '            <label class="layui-form-label">标题</label>\n' +
                        '            <div class="layui-input-block">\n' +
                        '                <input class="layui-input" type="text" name="title" value="'+title+'">\n' +
                        '                <div class="layui-input-mid layui-word-aux">可以同时修改标题</div>\n' +
                        '            </div>\n' +
                        '        </div>\n' +
                        '        <div class="layui-form-item">\n' +
                        '            <label class="layui-form-label">标签</label>\n' +
                        '            <div class="layui-input-block">\n' +
                        '                <input class="layui-input" type="text" name="tags" value="'+tags+'">\n' +
                        '                <div class="layui-input-mid layui-word-aux">多个用逗号分隔</div>\n' +
                        '            </div>\n' +
                        '        </div>\n' +
                        '        <div class="layui-form-item">\n' +
                        '            <label class="layui-form-label">详情</label>\n' +
                        '            <div class="layui-input-block">\n' +
                        '               <textarea class="layui-text text-edit text-edit-hidden" id="layer-content">'+details+'</textarea>' +
                        '            </div>\n' +
                        '        </div>\n' +
                        '    </form>\n' +
                        '</div>',
                    success:function(layero, index){
                        //使textarea高度自适应
                        var text_box=layero.find("textarea");
                        text_box.height(text_box.get(0).scrollHeight+10);
                    },
                    btn:["马上提交",'设为垃圾','取消'],
                    yes:function (index, layero) {
                        var newTitle=layero.find("input[name='title']").val();
                        var newContent=layero.find("#layer-content").val();
                        //console.log(newContent);
                        if(newTitle ===title)
                            newTitle='';
                        if(newContent ===details)
                            newContent='';
                        var tags=layero.find("input[name='tags']").val();
                        fly.json("<?=url('api/caiji_admin/tag_edit')?>",{id:id,title:newTitle,content:newContent,tag:tags,table:"<?=$get['table']?>"},function (res) {
                            layer.msg(res.msg);
                            _this.parent().parent().remove();
                            //$("#tag-"+id).html(tags);
                            layer.close(index);
                        });
                    },
                    btn2: function(index){
                        layer.confirm('真的要设为垃圾？',function (index2) {
                            fly.json("<?=url('api/caiji_admin/set_laji')?>",{id:id,table:"<?=$get['table']?>"},function (res) {
                                layer.msg(res.msg);
                                _this.parent().parent().remove();
                                layer.close(index2);
                                layer.close(index);
                            },{type:'get'});
                        })

                    }
                });
            });

        });
    </script>
{%end%}