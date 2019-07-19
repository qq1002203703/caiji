{%extend@common/menu_main%}
{%block@main%}
<div class="fly-panel fly-panel-user" pad20>
    <div class="layui-tab layui-tab-brief" lay-filter="user">
        <ul class="layui-tab-title" id="LAY_mine">
            <li class="layui-this" lay-id="list"><?=$title;?></li>
        </ul>
        <div class="layui-tab-content" style="padding: 20px 0;">
            <div class="layui-form layui-tab-item layui-show">
                <form class="layui-form layui-form-pane" method="post" action="<?=url('admin/api_portal/category_edit');?>?type=<?=$type?>" enctype="multipart/form-data" id="content_form">
                    <input type="hidden" name="__token__" value="<?=\app\common\ctrl\Func::token()?>">
                    <input type="hidden" name="id" value="<?=$data['id']?>">
                    <div class="layui-form-item">
                        <label class="layui-form-label">上级分类</label>
                        <div class="layui-input-inline" >
                            <select name="pid">
                                <option value="0">顶级分类</option>
                                <?=$select?>
                            </select>
                        </div>
                        <div class="layui-form-mid layui-word-aux" style="margin-left:0px;">请选择分类</div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">分类名称</label>
                        <div class="layui-input-inline">
                            <input class="layui-input" type="text" name="name" id="name" required lay-verify="required" value="<?=$data['name']?>">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">seo标题</label>
                        <div class="layui-input-block">
                            <input type="text" name="seo_title" value="<?=$data['seo_title']?>" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">seo关键词</label>
                        <div class="layui-input-block">
                            <input type="text" name="seo_keywords" id="keywords" value="<?=$data['seo_keywords']?>" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">描述标签</label>
                        <div class="layui-input-block">
                            <textarea name="seo_description" class="layui-textarea"><?=$data['seo_description']?></textarea>
                        </div>
                    </div>
                    <div class="layui-form-item" pane>
                        <label class="layui-form-label">显示</label>
                        <div class="layui-input-inline">
                            <input type="radio" name="status" value="1" title="正常显示"<?=echo_select(1,$data['status'],'checked')?>>
                            <input type="radio" name="status" value="0" title="暂时隐藏"<?=echo_select(0,$data['status'],'checked')?>>
                        </div>
                        <div class="layui-form-mid layui-word-aux">选择暂时隐藏时，本分类不显示在网站前台。</div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">自定义URL</label>
                        <div class="layui-input-inline">
                            <input type="text" name="slug" id="filename" value="<?=$data['slug']?>" class="layui-input" >
                        </div>
                        <div class="layui-form-mid layui-word-aux">仅伪静态方案采用<a href="#" >方案二和方案四</a>时，才可以自定义URL。其他方案时放空。</div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">列表模板</label>
                        <div class="layui-input-inline">
                            <input type="text" name="list_tpl" autocomplete="off" value="<?=$data['list_tpl']?>" class="layui-input" >
                        </div>
                        <div class="layui-form-mid layui-word-aux">可不填（不用填写.php后缀）</div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">内页模板</label>
                        <div class="layui-input-inline">
                            <input type="text" name="content_tpl" autocomplete="off" value="<?=$data['content_tpl']?>" class="layui-input" >
                        </div>
                        <div class="layui-form-mid layui-word-aux">可不填，每个频道都有默认的模板，具体参看对应文档（不用填写.php后缀）</div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">详细说明</label>
                        <div class="layui-input-block">
                            <script id="content" name="content" type="text/plain"><?=$data['content']?></script>
                        </div>
                        <div class="layui-form-mid layui-word-aux" style="margin-left:110px;">
                            可不填，模板上调用时，才会显示，调取标签：&lt;?=$data['content']=?&gt;
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">分类缩略图</label>
                        <div class="layui-input-inline" style="width: 160px;">
                            <div>
                                <button type="button" class="layui-btn upload-file" id="thumb-button" lay-data="{field: 'pic', exts: 'jpg|jpeg|gif|png'}">
                                    <i class="layui-icon">&#xe67c;</i>上传分类缩略图
                                </button>
                            </div>
                            <div class="layui-form-mid layui-word-aux">调取标签：&lt;?=$data['thumb']=?&gt;</div>
                        </div>

                        <div class="layui-input-inline" style="width: 300px;">
                            <img id="thumb-logo" style="max-width: 100%;" src="<?=$data['thumb']?:'/uploads/images/notpic.gif';?>">
                        </div>
                        <div class="layui-input-inline" style="width: 300px;">
                            <div id="thumb-container">
                                <?php $count_thumb=0; if($data['thumb_ids']): $count_thumb=count($data['thumb_ids']);foreach ($data['thumb_ids'] as $thumb_item): ?>
                                    <div class="image-item" style="width:61px; height:61px;" id="pic_item_<?=$thumb_item['id']?>"><div class="inner" style="width:34px; height:34px;"><a id="pic_slt_<?=$thumb_item['id']?>" data_id="<?=$thumb_item['id']?>"><img class="lazy" style="width:100%;height:100%" src="<?=$thumb_item['uri']?>" id="attachment-<?=$thumb_item['id']?>"></a><input name="images_id[]" value="<?=$thumb_item['id']?>" type="hidden"><input name="images_url[]" value="<?=$thumb_item['uri']?>" type="hidden"></div></div>
                                <?php endforeach; endif;?>
                            </div>
                            <div style="clear: both;padding-left:10px;"><p class="upload-count">已上传 <span id="upload-count"><?=$count_thumb?></span> 张图片</p></div>
                        </div>
                    </div>
                    <div class="layui-form-item" style="margin-top:30px;">
                        <label class="layui-form-label" style="background: #fff;border:none">&nbsp;</label>
                        <div class="layui-input-block">
                            <button  class="layui-btn"  id="submitp" type="submit" lay-filter="ajax" lay-submit>确认提交</button>
                            <a class="layui-btn layui-btn-primary" href="javascript:;" id="go-back-btn">取消</a>
                        </div>
                    </div>
                    <input type="hidden" name="type" value="portal_<?=$type;?>">
                    <!--input type="hidden" id="content_id_field" name="id" value="">
                    <input type="hidden" name="http_referer" value=""-->
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var navSidebar="portal-category-<?=$type?>";
    var currentData={
        id:0, //当前文档的id，如果没有就设为0
        type:"<?=$type?>", //频道种类：'article' 'soft','goods'
        uploader:"<?=url('admin/upload/img')?>?per=8", //缩略图上传时后端接收上传数据的接口url
        isEdit:false //是否是编辑页
    };
</script>
{%end%}

{%block@javascript%}
<script type="text/javascript" charset="utf-8" src="/static/lib/neditor/neditor.config.js?v=1.0.3"></script>
<script type="text/javascript" charset="utf-8" src="/static/lib/neditor/neditor.all.min.js"></script>
<script type="text/javascript" charset="utf-8" src="/static/lib/neditor/neditor.service.js?v=1.0.1"></script>
<script type="text/javascript" src="/static/admin/layui/js/thumb.js?v=1.0.1"></script>
<script type="text/javascript">
    var ue = UE.getEditor('content',{initialFrameHeight:300});
   /* layui.use(['layer','form','fly'], function() {
        var $=layui.jquery,
            layer=layui.layer;
    });*/
</script>
{%end%}