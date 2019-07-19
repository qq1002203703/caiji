{%extend@common/menu_main%}
{%block@main%}
<div class="fly-panel fly-panel-user" pad20>
    <div class="layui-tab layui-tab-brief" lay-filter="user">
        <ul class="layui-tab-title" id="LAY_mine">
            <li class="layui-this" lay-id="list"><?=$title;?> &gt; <?=$data['name']?></li>
        </ul>
        <div class="layui-tab-content" style="padding: 20px 0;">
            <div class="layui-tab-item layui-show">
                <form class="layui-form ayui-form-pane" method="post" action="<?=url('api/tag_admin/edit');?>" id="form-main">
                    <div class="layui-form-item">
                        <label class="layui-form-label">标签缩略图</label>
                        <div class="layui-input-inline" style="width: 160px;">
                            <div>
                                <button type="button" class="layui-btn upload-file" id="thumb-button" lay-data="{field: 'pic', exts: 'jpg|jpeg|gif|png'}">
                                    <i class="layui-icon">&#xe67c;</i>上传缩略图
                                </button>
                            </div>
                            <div class="layui-form-mid layui-word-aux"></div>
                        </div>
                        <div class="layui-input-inline" style="width: 300px;">
                            <img id="thumb-logo" style="max-width:200px;" src="<?=$data['thumb']?:'/uploads/images/notpic.gif';?>">
                            <input id="thumb-input" type="hidden" name="thumb" value="<?=$data['thumb']?>">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">名称<span class="red"> * </span></label>
                        <div class="layui-input-inline">
                            <input  id="name"  class="layui-input" type="text" name="name" value="<?=$data['name']?>" lay-verify="required" required>
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">更新时间</label>
                        <div class="layui-input-inline">
                            <select name="update_time">
                                <option value="0">不更新</option>
                                <option value="1" selected>更新</option>
                            </select>
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">seo标题</label>
                        <div class="layui-input-inline" style="min-width: 60%">
                            <input  id="seo_title"  class="layui-input" type="text" name="seo_title" value="<?=$data['seo_title']?>">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">seo关键词</label>
                        <div class="layui-input-inline">
                            <input  id="seo_keywords"  class="layui-input" type="text" name="seo_keywords" value="<?=$data['seo_keywords']?>">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">seo描述</label>
                        <div class="layui-input-inline" style="min-width:60%">
                            <textarea  id="seo_description" class="layui-textarea" name="seo_description"><?=$data['seo_description']?></textarea>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">自定义URL</label>
                        <div class="layui-input-inline">
                            <input type="text" name="slug" id="slug" value="<?=$data['slug']?>" class="layui-input" >
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">状态</label>
                        <div class="layui-input-inline">
                            <select name="status">
                                <option value="0"<?=echo_select($data['status'],0)?>>隐藏</option>
                                <option value="1"<?=echo_select($data['status'],1)?>>显示</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">详细说明</label>
                        <div class="layui-input-block">
                            <script id="content" name="content" type="text/plain"><?=$data['content']?></script>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label"></label>
                        <div class="layui-input-block">
                            <input type="hidden" name="id" value="<?=$data['id']?>">
                            <input type="button" class="layui-btn" lay-filter="ajax" lay-submit value="提交更改">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var navSidebar="tag-manage";
    var currentData={
        id:<?=$data['id']?>, //当前文档的id，如果没有就设为0
        type:"", //
        uploader:"<?=url('admin/upload/img')?>?per=8", //缩略图上传时后端接收上传数据的接口url
        isEdit:true //是否是编辑页
    };
</script>
{%end%}

{%block@javascript%}
<script type="text/javascript" charset="utf-8" src="/static/lib/neditor/neditor.config.js?v=1.0.3"></script>
<script type="text/javascript" charset="utf-8" src="/static/lib/neditor/neditor.all.min.js"></script>
<script type="text/javascript" charset="utf-8" src="/static/lib/neditor/neditor.service.js?v=1.0.1"></script>

<script type="text/javascript">
    var ue = UE.getEditor('content',{initialFrameHeight:300});
    layui.use(['layer'], function() {
        var $=layui.jquery,
            layer=layui.layer;
        //上传缩略图
        $("#thumb-button").on('click',function() {
            layer.open({
                type: 2,
                title: '上传缩略图',
                maxmin: true,
                resize:false,
                scrollbar:false,
                shadeClose: true,
                area : ['800px' , '580px'],
                btn:['确认','取消'],
                content: currentData.uploader
                ,yes:function (index, layero) {
                    var currentTab=layer.getChildFrame('.layui-tab > .layui-tab-content >.layui-show', index);
                    var imageList=currentTab.find('.upload-list > .image-item input[type=checkbox]:checked');
                    if(imageList.length<1) {
                        layer.confirm('请先上传图片，或选中已有图片！');
                    }else if(imageList.length >1){
                        layer.confirm('只能选一张图片作缩略图！');
                    }else {
                        var uri=imageList.attr('data-uri');
                        $("#thumb-logo").attr('src',uri);
                        $("#thumb-input").val(uri);
                        layer.close(index);
                    }
                }
            });
        });
    });
</script>
{%end%}