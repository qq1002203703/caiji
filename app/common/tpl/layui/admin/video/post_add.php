{%extend@common/menu_main%}
{%block@main%}
<link href="/static/admin/layui/css/post.css?v=1.0.1" type="text/css" rel="stylesheet">
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
            <div class="layui-form layui-tab-item layui-show">
                <form class="content-form" method="post" action="<?=url('api/video_admin/post_add')?>" style="min-height: 600px;overflow:hidden;" id="content_form" lay-filter="post">
                    <div class="content-left">
                        <div class="layui-tab">
                            <ul class="layui-tab-title">
                                <li class="layui-this">基本</li>
                                <li>seo</li>
                                <li>信息</li>
                                <li>其他</li>
                            </ul>
                            <div class="layui-tab-content">
                                <div class="layui-tab-item layui-show">
                                    <input type="hidden" name="__token__" value="<?=\app\common\ctrl\Func::token()?>">
                                    <div class="layui-form-item">
                                        <label class="layui-form-label"></label>
                                        <div class="layui-input-block">
                                            <div class="layui-input-inline" style="width: 220px;">
                                                <label class="layui-form-label">权限</label>
                                                <div class="layui-input-block">
                                                    <select name="permissions" lay-verify="required" required>
                                                        <?php foreach ($allow as $i =>$item):?>
                                                            <option value="<?=$i?>"<?=(echo_select($i,$allowDefaultSelect))?>><?=$item?></option>
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
                                            <input type="text" name="title" id="title" required lay-verify="required" value="" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">来源</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="source" id="source" value="" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">售价</label>
                                        <div class="layui-input-block">
                                            <label class="layui-form-label">金钱</label>
                                            <div class="layui-input-inline" style="width: 130px;height: 36px;">
                                                <input id="money" class="layui-input" type="text" name="money" placeholder="金钱数"> &nbsp;
                                            </div>
                                            <label class="layui-form-label">金币</label>
                                            <div class="layui-input-inline" style="width: 130px;height: 36px;">
                                                <input id="coin" class="layui-input" type="text" name="coin" placeholder="金币数">&nbsp; &nbsp;
                                            </div>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        简介：
                                        <div class="layui-input-inline" style="width:100%;margin-left: 0;">
                                           <!-- <textarea id="content" name="content"  lay-verify="required" style="display: none"></textarea>-->
                                            <script id="content" name="content" type="text/plain"></script>
                                        </div>
                                    </div>

                                </div>
                                <div class="layui-tab-item">
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">SEO标题</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="seo_title" value="" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">关键词</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="keywords" id="keywords" value="" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">摘要</label>
                                        <div class="layui-input-block">
                                            <textarea name="excerpt" class="layui-textarea"></textarea>
                                        </div>
                                    </div>

                                </div>
                                <div class="layui-tab-item">

                                    <div class="layui-form-item">
                                        <label class="layui-form-label">类型</label>
                                        <div class="layui-input-inline">
                                            <select id="type" name="type">
                                                <option value="0" selected>电影</option>
                                                <option value="1">电视</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">导演</label>
                                        <div class="layui-input-inline" style="width: 70%">
                                            <input type="text" name="director" value="" class="layui-input">
                                        </div>
                                        <div class="layui-form-mid layui-word-aux">多个用,分隔</div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">制片人</label>
                                        <div class="layui-input-inline" style="width: 70%">
                                            <input type="text" name="producer" value="" class="layui-input">
                                        </div>
                                        <div class="layui-form-mid layui-word-aux">多个用,分隔</div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">演员</label>
                                        <div class="layui-input-inline" style="width: 70%">
                                            <input type="text" name="actor" value="" class="layui-input">
                                        </div>
                                        <div class="layui-form-mid layui-word-aux">多个用,分隔</div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">别名</label>
                                        <div class="layui-input-inline" style="width: 70%">
                                            <input type="text" name="other_name" value="" class="layui-input">
                                        </div>
                                        <div class="layui-form-mid layui-word-aux">多个用$$$分隔</div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">地区</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="area" value="" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">imdb</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="imdb" value="" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">分数</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="score" value="" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">发行日期</label>
                                        <div class="layui-input-inline" style="width: 200px">
                                            <input id="date_published" class="layui-input"  type="text" name="date_published"  value="">
                                        </div>
                                        <div class="layui-form-mid layui-word-aux"> 格式：2019-07-01</div>
                                    </div>
                                </div>
                                <div class="layui-tab-item">
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">状态</label>
                                        <div class="layui-input-inline">
                                            <select id="status" name="status" class="" lay-filter="select1">
                                                <option value="0">禁止</option>
                                                <option value="1" selected>正常</option>
                                                <option value="2">草稿</option>
                                                <option value="3">预发布</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="layui-form-item">
                                        <label class="layui-form-label">是否置顶</label>
                                        <div class="layui-input-inline">
                                            <select id="is_top" name="is_top">
                                                <option value="0" selected>否</option>
                                                <option value="1">是</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">是否推荐</label>
                                        <div class="layui-input-inline">
                                            <select id="recommended" name="recommended">
                                                <option value="0" selected>否</option>
                                                <option value="1">是</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="layui-form-item">
                                        <label class="layui-form-label">允许评论</label>
                                        <div class="layui-input-inline">
                                            <select id="allow_comment" name="allow_comment">
                                                <option value="0">不允许</option>
                                                <option value="1" selected>允许</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">创建时间</label>
                                        <div class="layui-input-inline" style="width: 200px">
                                            <input id="create_time" class="layui-input datetime-input" type="text" name="create_time" placeholder="创建时间">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label"></label>
                                        <div class="layui-input-block">
                                            <label class="layui-form-label">查看次数:</label>
                                            <div class="layui-input-inline" style="width: 130px">
                                                <input id="views" class="layui-input" type="text" name="views" placeholder="查看次数">
                                            </div>
                                            <label class="layui-form-label">点赞次数:</label>
                                            <div class="layui-input-inline" style="width: 130px">
                                                <input id="likes" type="text" name="likes" placeholder="点赞次数" class="layui-input">
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
                            <input type="hidden"  id="published_time" name="published_time" value="">
                            <button type="button" id="preview" class="layui-btn layui-btn-primary layui-btn-small">预览</button>
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
                                    <button class="layui-btn"  value="ok" name="submit1"  id="submitp_a" lay-filter="ajax" lay-submit>确认提交</button>
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
                                                <p class="upload-count text-center">已上传 <span id="upload-count">0</span> 张图片</p>
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
                                                </div>
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
<script type="text/javascript">
    var navSidebar="video-post-add";
    var currentData={
        id:0, //当前文档的id，如果没有就设为0
        type:"", //频道种类：'article' 'soft','goods'
        uploader:"<?=url('admin/upload/img')?>?per=8", //缩略图上传时后端接收上传数据的接口url
        isEdit:false, //是否是编辑页
        ueditor:"<?=url('admin/upload/ueditor')?>" //ueditor上传文件的接口
    };
</script>

{%end%}

{%block@javascript%}
<script type="text/javascript" charset="utf-8" src="/static/lib/neditor/neditor.config.js?v=1.0.3"></script>
<script type="text/javascript" charset="utf-8" src="/static/lib/neditor/neditor.all.min.js"> </script>
<script type="text/javascript" charset="utf-8" src="/static/lib/neditor/neditor.service.js?v=1.0.2"></script>
<script type="text/javascript" src="/static/admin/layui/js/video.js"></script>

<div id="time_send" style="display: none;">
    <div class="layui-tab layui-tab-brief" style="margin-left:30px;">
        <ul class="layui-tab-title">
            <li class="layui-this" lay-id="pub">选择定时发送的时间</li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-form-item">
                <div class="layui-input-inline">
                    <input type="text" id="time-send-select" value="2018-10-25 15:54:36" class="layui-input datetime-input" min="2018-10-25 15:54:36" max="2019-10-25 15:54:36" placeholder="选择定时发送的时间">
                </div>
            </div>
        </div>
    </div>
</div>
{%end%}