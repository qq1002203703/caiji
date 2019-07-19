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
                <p>
                    <!--a class="layui-btn" href="<!?=url('admin/portal/post_add')?>">发布</a-->
                </p>
                <form method="post" action="<?=url('api/bbs_admin/action_multi')?>?aa=" name="form2" id="form2">
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
                </form>
                <form class="layui-form" name="form1" id="form1" method="get" action="" lay-filter="shaixuan">
                    <div class="wordpress-select" style="width:150px;float:left;margin-left:10px;margin-top:10px;margin-bottom:10px;">
                        <select id="category_id" name="category_id">
                            <option value="0">所有分类</option>
                            <?=$category?>
                        </select>
                    </div>
                    <div class="wordpress-select">
                        <select id="cat_id" name="batch_1">
                            <option  value="">所有属性</option>
                            <option  value="is_top"<?=echo_select($get['is_top'],1)?>>置顶</option>
                            <option  value="recommended"<?=echo_select($get['recommended'],1)?>>推荐</option>
                        </select>
                    </div>
                    <div class="wordpress-select">
                        <select id="shuxuan-type" name="type">
                            <option  value="0">所有频道</option>
                            <option  value="1"<?=echo_select($get['type'],1)?>>讨论</option>
                            <option  value="2"<?=echo_select($get['type'],2)?>>问答</option>
                        </select>
                    </div>
                    <div class="wordpress-select">
                        <select id="shuxuan-isend" name="isend">
                            <option  value="2"<?=echo_select($get['isend'],2)?>>结贴状态</option>
                            <option  value="0"<?=echo_select($get['isend'],0)?>>未结贴</option>
                            <option  value="1"<?=echo_select($get['isend'],1)?>>已结贴</option>
                        </select>
                    </div>
                    <div class="wordpress-select">
                        <input style="display: inline-block;" value="<?=$get['keywords']?>" class="layui-input" autocomplete="off" placeholder="搜索关键词" type="text" name="keywords">
                    </div>
                    <div style="float:left;margin-left:10px;margin-top:10px;">
                        <a class="layui-btn layui-btn-primary layui-btn-small" id="shaixuan">筛选</a>&nbsp;
                        共<?=$total?>条
                    </div>
                </form>
                <form class="layui-form" method="post" action="<?=url('api/bbs_admin/action_multi')?>?aa=" name="form-main" id="form-main">
                    <table class="layui-table">
                        <colgroup>
                            <col width="3%">
                            <col width="5%">
                            <col width="26%">
                            <col width="10%">
                            <col width="7%">
                            <col width="15%">
                            <col width="12%">
                            <col width="8%">
                            <col width="30%">
                            <col>
                        </colgroup>
                        <thead>
                        <tr>
                            <th><input type="checkbox" class="layui-btn" style="width:80px;" value="全选" lay-filter="checkall"></th>
                            <th><a href='#'>排序</a></th>
                            <th><a href=''>标题</a></th>
                            <th>标签</th>
                            <th>发布人</th>
                            <th>分类</th>
                            <th>
                                <div>
                                    <a>创建</a>
                                    /
                                    <a>更新</a>
                                </div>
                            </th>
                            <th>访问/下载</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody id='find_ckeck'>
                        <?php /*dump($data);*/ if($data): $bbsRouter=[1=>'bbs_post','bbs_show'];$thumbFormat=getThumbFormat();$model=app('app\admin\model\Tag');foreach ($data as $item): $tags_name=$model->getName($item['id'],'bbs_normal');?>
                            <tr id="c<?=$item['id']?>">
                                <td>
                                    <input name="id[<?=$item['id']?>]" id="id_<?=$item['id']?>" type="checkbox" value="<?=$item['id']?>">
                                </td>
                                <td>
                                    <input name="order[<?=$item['id']?>]" type="text" class="layui-input" style="width:50px;" value="0">
                                </td>
                                <td>
                                    <?=$item['id']?>.&nbsp;<a id="add-tag-<?=$item['id']?>" title="添加tag标签" data-id="<?=$item['id']?>" data-tag="<?=$tags_name;?>"><?=$item['title']?></a>
                                    <a href="<?=url('@'.$bbsRouter[$item['type']].'@',['id'=>$item['id']])?>" class="gray yang-icon"  target="_blank" title="去前台浏览"><i class="layui-icon layui-icon-link"></i></a>
                                    <div class="layui-hide" id="content-<?=$item['id']?>"><?=$item['content']?></div>
                                </td>
                                <td align="center" id="tags-<?=$item['id']?>"><?=$tags_name;?></td>
                                <td><?=$item['username']?></td>
                                <td><?=$item['category_id']?>.<?=$item['category_name']?></td>
                                <td>
                                    <span class="block green"><?=date('Y-m-d',$item['create_time'])?></span>
                                    <span class="block blue"><?=date('Y-m-d',$item['update_time'])?></span>
                                </td>
                                <td>
                                    <span class="block green"><?=$item['views']?></span>
                                    <span class="block blue"><?=$item['downloads']?></span>
                                </td>
                                <td style="text-align: center;">
                                    <i class="layui-icon" style="font-size: 20px;">
                                        <a title="修改" href="<?=url('api/bbs_admin/edit',['id'=>$item['id']])?>">&#xe642;</a>
                                    </i>
                                    <i class="layui-icon" style="font-size: 20px;">
                                        <a title="删除" id="deletea_<?=$item['id']?>" href="javascript:;" ac="<?=url('api/bbs_admin/del')?>?id=<?=$item['id']?>">
                                            &#xe640;
                                        </a>
                                    </i>
                                </td>
                            </tr>
                        <?php endforeach;endif;?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th><input type="checkbox" class="layui-btn" style="width:80px;" value="全选" lay-filter="checkall"></th>
                            <th><a href='#'>排序</a></th>
                            <th><a href=''>标题</a></th>
                            <th>标签</th>
                            <th>发布人</th>
                            <th>分类</th>
                            <th>
                                <div>
                                    <a>创建</a>
                                    /
                                    <a>更新</a>
                                </div>
                            </th>
                            <th>访问/下载</th>
                            <th>操作</th>
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
                        <div class="wordpress-select">
                            <button class="layui-btn layui-btn-primary layui-btn-small" lay-filter="multi" lay-submit alert="1" batch="1">应用</button>
                        </div>
                        <div class="layui-clear"></div>
                    </div>
                </form>
                <?=$page?>
            </div>
        </div>
        </div>
    </div>
</div>
    <script type="text/javascript">
        var navSidebar="bbs-list";
        var currentData={id:0,type:""};
    </script>
{%end%}

{%block@javascript%}
    <script type="text/javascript">
        layui.use(['layer','fly','form','flow'], function() {
            var $ = layui.jquery,
                form = layui.form,
                flow=layui.flow,
                fly=layui.fly;
            layer.ready(function () {
                //筛选的分类默认选中项
                $('#category_id').find("option[value='<?=$get['category_id']?>']").attr('selected',true);
                form.render('select','shaixuan');
            });
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
                    if(this.value){
                        if(this.name==='batch_1'){
                            url+='&'+this.value+'=1';
                        }else {
                            url+='&'+this.name+'='+this.value;
                        }
                    }
                });
                console.log(url);
               window.location.href="<?=url('admin/bbs/list')?>?status=<?=get('status','int',1)?>"+url;
            });

            //快速编辑
            $("a[id^='add-tag-").on('click', function(){
                var _this=$(this);
                var id=_this.attr('data-id');
                var title=_this.html();
                var tags=_this.attr('data-tag');
                var details=$("#content-"+id).html();
                var tp="<?=$get['type'];?>";
                layer.open({
                    title:'快速编辑',
                    type:1,
                    area: ['810px', '620px'],
                    content:'<div id="layer-box" style="padding:20px;">\n' +
                        '    <div class="layui-form">\n' +
                        '        <div class="layui-form-item">\n' +
                        '            <label class="layui-form-label">标题</label>\n' +
                        '            <div class="layui-input-block">\n' +
                        '                <input class="layui-input" type="text" name="title" value="'+title+'">\n' +
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
                        '            <label class="layui-form-label">结贴</label>\n' +
                        '            <div class="layui-input-block">\n' +
                        '                <input class="layui-input" type="text" name="isend" value="1">\n' +
                        '                <div class="layui-input-mid layui-word-aux">1为已结贴，0为未结贴</div>\n' +
                        '            </div>\n' +
                        '        </div>\n' +
                        '        <div class="layui-form-item">\n' +
                        '            <label class="layui-form-label">频道</label>\n' +
                        '            <div class="layui-input-block">\n' +
                        '                <input class="layui-input" type="text" name="type" value="1">\n' +
                        '                <div class="layui-input-mid layui-word-aux">1为讨论，2为问答</div>\n' +
                        '            </div>\n' +
                        '        </div>\n' +
                        '        <div class="layui-form-item">\n' +
                        '            <label class="layui-form-label">主贴</label>\n' +
                        '            <div class="layui-input-block">\n' +
                        '               <textarea class="layui-text text-edit text-edit-hidden" id="layer-content">'+details+'</textarea>' +
                        '            </div>\n' +
                        '        </div>\n' +
                        '        <div class="layui-form-item">\n' +
                        '            <label class="layui-form-label">评论</label>\n' +
                        '            <div class="layui-input-block">\n' +
                        '               <div id="comments-container"><button class="layui-btn layui-btn-primary">编辑评论</button></div>'+
                        '            </div>\n' +
                        '        </div>\n' +
                        '    </div>\n' +
                        '</div>',
                    success:function(layero, index){
                        //使textarea高度自适应
                        var text_box=layero.find("textarea");
                        text_box.height(text_box.get(0).scrollHeight+10);
                        layero.on("click" ,"button",function () {
                            $("#comments-container").append('<p>格式：当前评论的id{||}推荐（1为推荐，0为不推荐）{||}评论内容  </p><p style="text-align: right;"><a href="javascript:;" id="remove-comment">取消修改</a></p><textarea class="layui-text text-edit text-edit-hidden" name="comment" id="layer-comment"></textarea>');
                            flow.load({
                                elem: '#comments-container' //指定列表容器
                                ,isAuto:false
                                ,done: function(page, next){
                                    $.get("<?=url('api/comment/flow_manage')?>"+"?id="+id+"&table=bbs&page="+page, function(res){
                                        var layerComment=layero.find("#layer-comment");
                                        var html=layerComment.val().replace(/(^\s+)|(\s+$)/g,"");
                                        if(html !==''){
                                            layerComment.val(html+"{|||}\n"+res.data);
                                        }else {
                                            layerComment.val(res.data);
                                        }
                                        next('', page < res.pages);
                                    });
                                }
                            });
                            $(this).remove();
                        });
                        layero.on("click","#remove-comment",function () {
                            //layero.find("#layer-comment").remove();
                            layero.find("#comments-container").html('<button class="layui-btn layui-btn-primary">编辑评论</button>');
                            //$(this).remove();
                        });
                    },
                    btn:["马上提交",'取消'],
                    yes:function (index, layero) {
                        var newTitle=layero.find("input[name='title']").val();
                        var newContent=layero.find("#layer-content").val();
                        if(newTitle ===title)
                            newTitle='';
                        if(newContent ===details)
                            newContent='';
                        var tags=layero.find("input[name='tags']").val();
                        var isend=layero.find("input[name='isend']").val();
                        var currentTp=layero.find("input[name='type']").val();
                        fly.json("<?=url('api/bbs_admin/quick_edit')?>",{id:id,title:newTitle,content:newContent,tag:tags,comment:layero.find("#layer-comment").val(),isend:isend,type:currentTp},function (res) {
                            layer.msg(res.msg);
                            if(isend !=="<?=$get['isend']?>" || (tp !=="0" && tp !==currentTp)){
                                $("#c"+id).remove();
                            }else {
                                $("#tags-"+id).html(tags);
                                if(newTitle !=='')
                                    $("#add-tag-"+id).html(newTitle);
                                if(newContent !=='')
                                    $("#content-"+id).html(newContent);
                            }
                            layer.close(index);
                        });
                    }
                });

            });
        });

    </script>
{%end%}