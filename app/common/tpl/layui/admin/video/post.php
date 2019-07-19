{%extend@common/menu_main%}
{%block@main%}
<div class="fly-panel fly-panel-user" pad20>
    <div class="layui-tab layui-tab-brief" lay-filter="user">
        <div class="pull-right help-control">
            <a href="<?=url('admin/video/setting')?>" title="设置"><i class="layui-icon">&#xe614;</i></a>
            <a href="javascript:;" title="帮助"><i class="layui-icon">&#xe607;</i></a>
        </div>
        <ul class="layui-tab-title" id="LAY_mine">
            <li class="layui-this" lay-id="list"><?=$title?></li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
            <div class="layui-form">
                <p style="font-size:13px;">
                    <a class="layui-btn" href="<?=url('admin/video/post_add')?>">发布视频</a>&nbsp;&nbsp;&nbsp;
                    <a href="<?=url('admin/video/post')?>?status=1">已发布(<?=$total_fabu?>)</a>
                    | <a href="<?=url('admin/video/post')?>?status=3">定时发布(<?=$total_dingshi?>)</a>
                </p>
                <form method="post" action="<?=url('api/video_admin/post_multi_action')?>" name="form2" id="form2">
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
                            <?=$categorys?>
                        </select>
                    </div>
                    <div class="wordpress-select" style="width:150px;float:left;margin-left:10px;margin-top:10px;margin-bottom:10px;">
                        <select id="cat_id" name="batch_1">
                            <option  value="">所有属性</option>
                            <option  value="is_top"<?=echo_select($get['is_top'],1)?>>置顶</option>
                            <option  value="recommended"<?=echo_select($get['recommended'],1)?>>推荐</option>
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
                            <th>缩略图</th>
                            <th>导演</th>
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
                        <?php /*dump($data);*/ if($data): $thumbFormat=getThumbFormat('video');foreach ($data as $item): ?>
                            <tr id="c<?=$item['id']?>">
                                <td>
                                    <input name="id[<?=$item['id']?>]" id="id_<?=$item['id']?>" type="checkbox" value="<?=$item['id']?>">
                                </td>
                                <td>
                                    <input name="order[<?=$item['id']?>]" type="text" class="layui-input" style="width:50px;" value="0">
                                </td>
                                <td>
                                    <?=$item['id']?>.&nbsp;<a href="<?=url('admin/video/post_edit',['id'=>$item['id']])?>" title="编辑"><?=$item['title']?></a>
                                    <a href="<?=url('@video@',['id'=>$item['id']])?>" class="gray yang-icon"  target="_blank" title="去前台浏览"><i class="layui-icon layui-icon-link"></i></a>
                                </td>
                                <td align="center"><img src="<?=getThumb($item['thumb'],$thumbFormat)?>" style="max-height: 80px;"></td>
                                <td><?=$item['director']?></td>
                                <td><?=$item['category_id']?>.<?=$item['category_name']?></td>
                                <td>
                                    <span class="block green"><?=date('Y-m-d',$item['create_time'])?></span>
                                    <span class="block blue"><?=date('Y-m-d',$item['update_time'])?></span>
                                </td>
                                <td>
                                    <span class="block green"><?=$item['views']?></span>
                                    <span class="block blue"><?=$item['likes']?></span>
                                </td>
                                <td style="text-align: center;">
                                    <i class="layui-icon" style="font-size: 20px;">
                                        <a title="修改" href="<?=url('admin/video/post_edit',['id'=>$item['id']])?>">&#xe642;</a>
                                    </i>
                                    <i class="layui-icon" style="font-size: 20px;">
                                        <a title="删除" id="deletea_<?=$item['id']?>" href="javascript:;" ac="<?=url('api/video_admin/post_del')?>?id=<?=$item['id']?>">
                                            &#xe640;
                                        </a>
                                    </i>
                                    <i class="layui-icon" style="font-size: 20px;">
                                        <a title="复制本文新发一篇" href="<?=url('admin/video/post_copy')?>?id=<?=$post['id']?>">&#xe60a;</a>
                                    </i>
                                </td>
                            </tr>
                        <?php endforeach;endif;?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th><input type="checkbox" class="layui-btn" style="width:80px;" value="全选" lay-filter="checkall"></th>
                            <th>
                                <a href='index.php?m=site&c=content&a=article_index&status=1&order_listorder=desc'>排序</a>
                            </th>
                            <th>
                                <a href='index.php?m=site&c=content&a=article_index&status=1&order_title=desc'>标题</a>
                            </th>
                            <th>缩略图</th>
                            <th>发布人</th>
                            <th>分类</th>
                            <th style="text-align: center;">
                                    <a href='index.php?m=site&c=content&a=article_index&status=1&order_addtime=desc'>
                                        <span style='color:green;'>首发</span>
                                    </a>
                                    /
                                    <a href='index.php?m=site&c=content&a=article_index&status=1&order_updatetime=desc'>
                                        <span style='color:blue;'>更新</span>
                                    </a>
                            </th>
                            <th>访问量</th>
                            <th style="text-align: center;">操作</th>
                        </tr>
                        </tfoot>
                    </table>
                    <div>
                        <div class="wordpress-select">
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
                        <?=$page?>
                    </div>

            </div>
        </div>
        </div>
    </div>
</div>
    <script type="text/javascript">
        var navSidebar="video-post";
        var currentData={id:0,type:""};
    </script>
{%end%}

{%block@javascript%}
    <script type="text/javascript">
        layui.use(['layer','fly','form'], function() {
            var $ = layui.jquery,
             form = layui.form,
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
                    if(this.value && this.value !=='0'){
                        if(this.name==='batch_1'){
                            url+='&'+this.value+'=1';
                        }else {
                            url+='&'+this.name+'='+this.value;
                        }
                    }
                });
               window.location.href="<?=url('admin/video/post')?>?status=<?=get('status','int',1)?>"+url;
            })
        });

    </script>
{%end%}