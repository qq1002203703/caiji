{%extend@common/menu_main%}
{%block@main%}
<link href="/static/admin/layui/css/post.css?v=1.0.1" type="text/css" rel="stylesheet">
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
            <div class="layui-form layui-tab-item layui-show">
                <form class="content-form" method="post" action="<?=url('admin/api_portal/post_edit')?>?type=<?=$type?>" style="min-height: 600px;overflow:hidden;" id="content_form" lay-filter="post">
                    <input type="hidden" name="timesend" value="2018-10-25 15:54:36" id="time-send-input">
                    <div class="content-left">
                        <div class="layui-tab">
                            <ul class="layui-tab-title">
                                <li class="layui-this">基本</li>
                                <li>网盘</li>
                                <li>聚合</li>
                                <li>扩展</li>
                                <li>其他</li>
                            </ul>
                            <div class="layui-tab-content">
                                <div class="layui-tab-item layui-show">
                                    <input type="hidden" name="__token__" value="<?=\app\common\ctrl\Func::token()?>">
                                    <input type="hidden" name="id" value="<?=$data['id']?>">
                                    <div class="layui-form-item">
                                        <label class="layui-form-label"></label>
                                        <div class="layui-input-block">
                                            <div class="layui-input-inline" style="width: 220px;">
                                                <label class="layui-form-label">权限</label>
                                                <div class="layui-input-block">
                                                    <select name="permissions" lay-verify="required" required>

                                                        <?php foreach ($allow as $i =>$item):?>
                                                            <option value="<?=$i?>"<?=echo_select($data['permissions'],$i)?>><?=$item?></option>
                                                        <?php endforeach;?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="layui-input-inline" style="width: 400px;">
                                                <label class="layui-form-label">分类</label>
                                                <div class="layui-input-block">
                                                    <select name="category_id" lay-verify="required" required>
                                                        <?=$category?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">标题<span class="red">（必须）</span></label>
                                        <div class="layui-input-block">
                                            <input type="text" name="title" id="title" required lay-verify="required" value="<?=$data['title']?>" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">SEO标题</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="seo_title" value="<?=$data['seo_title']?>" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">关键词</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="keywords" id="keywords" value="<?=$data['keywords']?>" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">摘要</label>
                                        <div class="layui-input-block">
                                            <textarea name="excerpt" class="layui-textarea"><?=$data['excerpt']?></textarea>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">来源</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="source" id="source" value="<?=$data['source']?>" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">售价</label>
                                        <div class="layui-input-block">
                                            <label class="layui-form-label">金钱</label>
                                            <div class="layui-input-inline" style="width: 130px;height: 36px;">
                                                <input id="money" class="layui-input" type="text" name="money" value="<?=$data['money']?>"> &nbsp;
                                            </div>
                                            <label class="layui-form-label">金币</label>
                                            <div class="layui-input-inline" style="width: 130px;height: 36px;">
                                                <input id="coin" class="layui-input" type="text" name="coin" value="<?=$data['coin']?>">&nbsp; &nbsp;
                                            </div>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        内容<span class="red">（必须）</span>
                                        <div class="layui-input-inline" style="width:100%;margin-left: 0;">
                                            <!-- <textarea id="content" name="content"  lay-verify="required" style="display: none"></textarea>-->
                                            <script id="content" name="content" type="text/plain"><?=$data['content']?></script>
                                        </div>
                                    </div>

                                </div>
                                <div class="layui-tab-item">
                                    <table class="layui-table" id="table_file">
                                        <thead>
                                        <tr>
                                            <th width="120">名称</th>
                                            <th width="80">种类</th>
                                            <th>网址</th>
                                            <th>备注</th>
                                            <th width="120" align="center">操作</th>
                                        </tr>
                                        </thead>

                                        <?php $files=json_decode($data['files']);$num=1;if($files): $num=count($files);foreach($files as $k => $item):?>
                                                <tr>
                                                    <td><input class="layui-input" type="text" name="files[<?=$k?>][name]" value="<?=$item->name?>"></td>
                                                    <td><input class="layui-input" type="text" name="files[<?=$k?>][type]" value="<?=$item->type?>"></td>
                                                    <td><input class="layui-input" type="text" name="files[<?=$k?>][url]" value="<?=$item->url?>"></td>
                                                    <td><input class="layui-input" type="text" name="files[<?=$k?>][remark]" value="<?=$item->remark?>"></td>
                                                    <td><a href="javascript:;" class="layui-btn layui-btn-normal layui-btn-sm file_add">增加</a><a href="javascript:;" class="layui-btn layui-btn-normal layui-btn-sm file_cancel">取消</a></td>
                                                </tr>
                                            <?php endforeach;?>
                                        <?php else:?>
                                            <tr>
                                                <td><input class="layui-input" type="text" name="files[0][name]"></td>
                                                <td><input class="layui-input" value="百度网盘" type="text" name="files[0][type]"></td>
                                                <td><input class="layui-input" type="text" name="files[0][url]"></td>
                                                <td><input class="layui-input" value="提取密码:" type="text" name="files[0][remark]"></td>
                                                <td><a href="javascript:;" class="layui-btn layui-btn-normal layui-btn-sm file_add">增加</a></td>
                                            </tr>
                                        <?php endif;?>
                                    </table>
                                </div>
                                <div class="layui-tab-item">
                                    <div class="layui-form-item">
                                        <label class="layui-form-label"></label>
                                        <div class="layui-input-block">
                                            <button type="button" class="layui-btn layui-btn-primary" id="juhe-parent-select" data="<?=url('admin/api_portal/search_juhe')?>?type=<?=$type?>">添加上级</button>
                                            <span id="juhe-parent-container" class="juhe-container"><?=($parent?($parent['id'].'. '.$parent['title']).'<i class="juhe-cancel">&#215;</i>' :'无')?></span>
                                            <input id="pid" type="hidden" value="<?=$data['pid']?>" name="pid">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label"></label>
                                        <div class="layui-input-block">
                                            <p><button type="button" class="layui-btn layui-btn-primary" id="juhe-children-select" data="<?=url('admin/api_portal/search_juhe')?>?type=<?=$type?>">添加下级</button></p>
                                            <ul class="juhe-container" id="juhe-children-container">
                                            </ul>
                                            <input id="children_id" type="hidden" name="children_id" value="">
                                            <?php if($children):?>
                                                <ul id="juhe-children-existing" data="<?=url('admin/api_portal/del_pid')?>">
                                                    <h3 class="juhe-title">已存在的下级</h3>
                                                    <?php foreach ($children as $child):?>
                                                        <li id="children-data-<?=$child['id']?>"><span><?=$child['id']?></span>.<?=$child['title']?><i class="juhe-cancel" data="<?=$child['id']?>">&#215;</i><?php $children_id[]=$child['id'];?></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="layui-tab-item">
                                    <table class="layui-table" id="table_more">
                                        <thead>
                                        <tr>
                                            <th width="220">变量名</th>
                                            <th>变量值</th>
                                            <th width="120" align="center">操作</th>
                                        </tr>
                                        </thead>
                                        <?php $more=$data['more'] ? json_decode($data['more']) : '';$num_more=1;if($more):?>
                                        <?php $num=count($more);foreach($more as $key => $value):?>
                                        <tr>
                                            <td><input class="layui-input" type="text" name="more[<?=$key?>][name]" value="<?=$value->name?>"></td>
                                            <td><input class="layui-input" type="text" name="more[<?=$key?>][value]" value="<?=$value->value?>"></td>
                                            <td><a href="javascript:;" class="layui-btn layui-btn-normal layui-btn-sm more_add">增加</a><a href="javascript:;" class="layui-btn layui-btn-danger layui-btn-sm more_cancel">取消</a></td>
                                        </tr>
                                            <?php endforeach;?>
                                        <?php else:?>
                                            <tr>
                                                <td><input class="layui-input" type="text" name="more[0][name]"></td>
                                                <td><input class="layui-input" type="text" name="more[0][value]"></td>
                                                <td><a href="javascript:;" class="layui-btn layui-btn-normal layui-btn-sm more_add">增加</a></td>
                                            </tr>
                                        <?php endif;?>
                                    </table>

                                </div>
                                <div class="layui-tab-item">
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">状态</label>
                                        <div class="layui-input-inline">
                                            <select id="status" name="status" class="" lay-filter="select1">
                                                <option value="0"<?=echo_select($data['status'],0)?>>禁止</option>
                                                <option value="1"<?=echo_select($data['status'],1)?>>正常</option>
                                                <option value="2"<?=echo_select($data['status'],2)?>>草稿</option>
                                                <option value="3"<?=echo_select($data['status'],3)?>>预发布</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="layui-form-item">
                                        <label class="layui-form-label">是否置顶</label>
                                        <div class="layui-input-inline">
                                            <select id="is_top" name="is_top">
                                                <option value="0"<?=echo_select($data['is_top'],0)?>>否</option>
                                                <option value="1"<?=echo_select($data['is_top'],1)?>>是</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">是否推荐</label>
                                        <div class="layui-input-inline">
                                            <select id="recommended" name="recommended">
                                                <option value="0"<?=echo_select($data['recommended'],0)?>>否</option>
                                                <option value="1"<?=echo_select($data['recommended'],1)?>>是</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="layui-form-item">
                                        <label class="layui-form-label">允许评论</label>
                                        <div class="layui-input-inline">
                                            <select id="allow_comment" name="allow_comment">
                                                <option value="0"<?=echo_select($data['allow_comment'],0)?>>不允许</option>
                                                <option value="1"<?=echo_select($data['allow_comment'],1)?>>允许</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">创建时间</label>
                                        <div class="layui-input-inline" style="width: 200px">
                                            <input id="create_time" class="layui-input datetime-input" type="text" name="create_time" placeholder="创建时间" value="<?=date('Y-m-d H:i:s',$data['create_time'])?>">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label"></label>
                                        <div class="layui-input-block">
                                            <label class="layui-form-label">查看次数:</label>
                                            <div class="layui-input-inline" style="width: 130px">
                                                <input id="views" class="layui-input" type="text" name="views" value="<?=$data['views']?>">
                                            </div>
                                            <label class="layui-form-label">点赞次数:</label>
                                            <div class="layui-input-inline" style="width: 130px">
                                                <input id="likes" type="text" name="likes" placeholder="点赞次数" class="layui-input" value="<?=$data['likes']?>">
                                            </div>
                                            <label class="layui-form-label">下载次数:</label>
                                            <div class="layui-input-inline" style="width: 130px">
                                                <input id="downloads" type="text" name="downloads" placeholder="下载次数" class="layui-input" value="<?=$data['downloads']?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--发布产品注入点-->
                        <div class="layui-form-item" style="margin-top:10px;">
                            <button class="layui-btn"  id="submitp" type="submit" lay-filter="ajax" lay-submit>确认提交</button>
                            <button class="layui-btn layui-btn-primary layui-btn-small" type="button"  id="caogaop">存入草稿</button>
                            <button class="layui-btn layui-btn-primary layui-btn-small" type="button" id="yuyue">定时发布</button>
                            <input type="hidden"  id="published_time" name="published_time" value="<?=date('Y-m-d H:i:s',$data['published_time'])?>">
                            <button type="button" id="preview" class="layui-btn layui-btn-primary layui-btn-small">预览</button>
                            <input type="hidden" id="type" name="type" value="<?=$type?>">
                        </div>
                    </div>

                    <div class="layui-collapse content-right">
                        <!-- 1 -->
                        <div class="layui-colla-item">
                            <!--
                            <h2 class="layui-colla-title">操作</h2>
                            -->
                            <div class="layui-colla-content layui-show" style='border-top-width: 0;'>
                                <div class="layui-form-item">
                                    <button class="layui-btn"  value="ok" name="submit1"  id="submitp_a"  lay-filter="ajax" lay-submit>确认提交</button>
                                    <button class="layui-btn layui-btn-primary layui-btn-small"  value="caogao" name="submit1" id="caogaop_a">存入草稿</button>
                                    <input type="button" class="layui-btn layui-btn-primary layui-btn-small" style="margin-top:10px;margin-left:0px;" id="yuyue_a" value="定时发布">
                                    <a id="preview_a" style="margin-top:10px; margin-left:10px;width:93px;" class="layui-btn layui-btn-primary layui-btn-small" href="javascript:;">预览</a>
                                </div>
                            </div>
                        </div>
                        <!--div class="layui-colla-item">
                            <h2 class="layui-colla-title">分类</h2>
                            <div class="layui-colla-content layui-show">
                                <div class="layui-form-item">
                                    <div class="layui-input-inline" style="width:100%;">
                                        <div class="layui-form-item">
                                            <div class="layui-input-inline">
                                                <ul class="categorys">
                                                    <li>
                                                        <div class="">
                                                            <input id="cat_id" lay-skin="primary" type="checkbox"  name="cat_id[]" value="4"  title="产品中心">
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="">
                                                            └&nbsp;&nbsp;									<input id="cat_id" lay-skin="primary" type="checkbox"  name="cat_id[]" value="40"  title="二级分类1">
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div-->
                        <!-- 1 -->
                        <!-- 1 -->
                        <!--缩略图注入点-->
                        <div class="layui-colla-item">
                            <h2 class="layui-colla-title" onclick='toggle_pic_S();'>缩略图</h2>
                            <div class="image" style='min-width: 248px;' id='pic_S'>
                                <?php $count_thumb=0; if($data['thumb_ids']): $count_thumb=count($data['thumb_ids']);foreach ($data['thumb_ids'] as $thumb_item): ?>
                                <div class="image-item" style="width:61px; height:61px;" id="pic_item_<?=$thumb_item['id']?>"><div class="inner" style="width:34px; height:34px;"><a id="pic_slt_<?=$thumb_item['id']?>" data_id="<?=$thumb_item['id']?>"><img class="lazy" style="width:100%;height:100%" src="<?=$thumb_item['uri']?>" id="attachment-<?=$thumb_item['id']?>"></a><input name="images_id[]" value="<?=$thumb_item['id']?>" type="hidden"><input name="images_url[]" value="<?=$thumb_item['uri']?>" type="hidden"></div></div>
                                <?php endforeach; endif;?>
                            </div>
                            <div class="layui-colla-content layui-show">
                                <div class="layui-form-item">
                                    <div class="layui-input-inline" style="width:100%;">
                                        <div class="layui-form-item">
                                            <input type='hidden' name='copy_id' value=''>
                                            <div class="layui-input-inline">
                                                <div class="image-upload" style="height: 50px;">
                                                    <div class="image-uploader">
                                                        <a href="javascript:;" class="layui-btn" id="thumbnail">
                                                            <i class="layui-icon">&#xe67c;</i>缩略图管理
                                                        </a>
                                                    </div>
                                                    <div class="layui-progress">
                                                        <div class="layui-progress-bar" lay-percent="10%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="layui-form-mid layui-word-aux" style="margin-left: 0;">
                                                <p class="upload-count text-center">已上传 <span id="upload-count"><?=$count_thumb?></span> 张图片</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--发布Tag注入点-->
                        <input type="hidden" name="tag_content" id="tag_content" >
                        <div class="layui-colla-item">
                            <h2 class="layui-colla-title">Tag设置</h2>
                            <div class="layui-colla-content layui-show">
                                <div class="layui-form-item">
                                    <div class="layui-input-inline" style="width:100%;">
                                        <div class="layui-tab-item layui-show">
                                            <div class="layui-form-item">
                                                <div class="layui-input-inline">
                                                    多个标签用中/英文逗号分隔
                                                </div>
                                            </div>
                                            <div class="layui-form-item">
                                                <div class="layui-input-inline" style="width:250px;">
                                                    <div class="layui-input-inline" style="width:146px;margin-right: 4px"><input class="layui-input" type="text" id="tag_str"></div>
                                                    <input type="button"  class="layui-btn layui-btn-primary" id="tagsend" value="添加">
                                                </div>
                                            </div>
                                            <div class="layui-form-item">
                                                <div class="layui-input-inline" id="tag_sel" style="padding-top:10px;">
                                                    <?php $model=app('\app\portal\model\PortalPost');echo $model->tagsHtml($data['id'],$type,'','<span class="tag"><a class="tag-del" href="javascript:;"><i class="layui-icon">&#x1007;</i></a>{%name%}<input type="hidden" name="tags[]" value="{%name%}"></span></span>')?>
                                                </div>
                                            </div>
                                            <div class="layui-form-item">
                                                <div class="layui-input-inline">
                                                    <a id="tags-update-help" class="red" href="javascript:;" title="编辑页要对标签修改/新添加，确定好是哪几个标签后，还需要点击下面的更新标签修改才会生效">如何正确更新标签？</a>
                                                </div>
                                                <button class="layui-btn layui-btn-danger" type="button" id="tags-update">更新标签</button>
                                            </div>
                                            <!--div class="layui-form-item">
                                                <div class="layui-input-inline" style="padding-top:10px;">
                                                    <a href="javascript:;" status="off" id="tag_lib">从常用标签中选择</a>
                                                </div>
                                            </div>
                                            <div class="layui-form-item" id="tag_libs" style="margin-top:10px;border:1px solid #e6e6e6;padding-top:5px;padding-left:10px;">
                                                <div class="layui-input-inline">
                                                    <a  href="javascript:;" id="tag_all_1" tagid="1" tagname="式工">式工</a>
                                                    <a  href="javascript:;" id="tag_all_2" tagid="2" tagname="呵哥哥">呵哥哥</a>
                                                </div-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- 1 -->
            </form>
        </div>
    </div>
</div>
<div class="layui-hide" id="num">1</div>
<div class="layui-hide" id="num_more">1</div>
</div>
<script type="text/javascript">//向js传递的必要参数
    var navSidebar="portal-post-add-<?=$type?>"; //侧边栏被选中的id
    var currentData={
        id:<?=$data['id']?>, //当前文档的id，如果没有就传0
        type:"<?=$type?>", //频道种类：'article' 'soft','goods'
        uploader:"<?=url('admin/upload/img')?>?per=8", //缩略图上传时后端接收上传数据的接口地址
        isEdit:true, //是否是编辑页
        tagsEditUrl:"<?=url('admin/api_portal/tags_edit')?>", //标签修改的url
        ueditor:"<?=url('admin/upload/ueditor')?>" //ueditor上传文件的接口
    };
</script>
{%end%}

{%block@javascript%}
<script type="text/javascript" charset="utf-8" src="/static/lib/neditor/neditor.config.js?v=1.0.3"></script>
<script type="text/javascript" charset="utf-8" src="/static/lib/neditor/neditor.all.min.js"></script>
<script type="text/javascript" charset="utf-8" src="/static/lib/neditor/neditor.service.js?v=1.0.2"></script>
<script type="text/javascript" src="/static/admin/layui/js/post.js"></script>

<div id="time_send" style="display: none;">
    <div class="layui-tab layui-tab-brief" style="margin-left:30px;">
        <ul class="layui-tab-title">
            <li class="layui-this" lay-id="pub">选择定时发送的时间</li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-form-item">
                <div class="layui-input-inline">
                    <input type="text" id="time-send-select" value="<?=date('Y-m-d H:i:s')?>" class="layui-input datetime-input" min="<?=date('Y-m-d H:i:s')?>" max="<?=date('Y-m-d H:i:s',time()+90*24*3600)?>" placeholder="选择定时发送的时间">
                </div>
            </div>
        </div>
    </div>
</div>
{%end%}