{%extend@common/menu_main%}
{%block@main%}
<link href="/static/admin/layui/css/post.css?v=1.0.1" type="text/css" rel="stylesheet">
<div class="fly-panel fly-panel-user" pad20>
    <div class="layui-tab layui-tab-brief" lay-filter="user">
        <ul class="layui-tab-title" id="LAY_mine">
            <li class="layui-this" lay-id="list"><?=$title?></li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-form layui-tab-item layui-show">
                <form class="content-form" method="post" action="<?=url('api/caiji_admin/result_edit')?>" style="min-height: 600px;overflow:hidden;" id="content_form" lay-filter="post">
                    <div class="layui-tab-item layui-show">
                        <div class="layui-form-item">
                            <label class="layui-form-label">标题<span class="red">（必须）</span></label>
                            <div class="layui-input-block">
                                <input type="text" name="title" id="title" required lay-verify="required" value="<?=$data['title']?>" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">url</label>
                            <div class="layui-input-block">
                                <div class="layui-input-mid"><?=$data['url']?></div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">采集名称</label>
                            <div class="layui-input-inline">
                                <p class="layui-input-mid layui-form-label"><?=$data['caiji_name']?></p>
                            </div>
                        </div>

                        <div class="layui-form-item">
                            <label class="layui-form-label">是否审核</label>
                            <div class="layui-input-inline">
                                <select id="isshenhe" name="isshenhe" lay-filter="select1">
                                    <option value="0">否</option>
                                    <option value="1" selected>是</option>
                                </select>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">是否结帖</label>
                            <div class="layui-input-inline">
                                <select id="isend" name="isend" lay-filter="select1">
                                    <option value="0"<?=echo_select($data['isend'],0)?>>否</option>
                                    <option value="1"<?=echo_select($data['isend'],1)?>>是</option>
                                </select>
                            </div>
                        </div>

                        <div class="layui-form-item">
                            <label class="layui-form-label">是否采集</label>
                            <div class="layui-input-inline">
                                <select id="iscaiji" name="iscaiji" lay-filter="select1">
                                    <option value="0"<?=echo_select($data['iscaiji'],0)?>>否</option>
                                    <option value="1"<?=echo_select($data['iscaiji'],1)?>>是</option>
                                </select>
                            </div>
                        </div>

                        <div class="layui-form-item">
                            <label class="layui-form-label">是否垃圾</label>
                            <div class="layui-input-inline">
                                <select id="islaji" name="islaji" lay-filter="select1">
                                    <option value="0"<?=echo_select($data['islaji'],0)?>>否</option>
                                    <option value="1"<?=echo_select($data['islaji'],1)?>>是</option>
                                </select>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">内容<span class="red">（必须）</span></label>
                            <div class="layui-input-block" style="width:100%;margin-left: 0;">
                                <script id="content" name="content" type="text/plain"><?=$data['content']?></script>
                            </div>
                        </div>
                        <div class="layui-form-item" style="margin-top:10px;">
                            <input type="hidden" name="__token__" value="<?=\app\common\ctrl\Func::token()?>">
                            <input type="hidden" name="id" value="<?=$data['id']?>">
                            <button class="layui-btn"  id="submitp" type="submit" lay-filter="ajax" lay-submit>确认提交</button>
                        </div>
                    </div>

            </form>
        </div>
    </div>
</div>

</div>
<script type="text/javascript">//向js传递的必要参数
    var navSidebar="caiji-handler"; //侧边栏被选中的id
    var currentData={
        id:<?=$data['id']?>, //当前文档的id，如果没有就传0
        //type:"", //频道种类：'article' 'soft','goods'
        ueditor:"<?=url('admin/upload/ueditor')?>" //ueditor上传文件的接口
    };
</script>
{%end%}

{%block@javascript%}
<script type="text/javascript" charset="utf-8" src="/static/lib/neditor/neditor.config.js?v=1.0.3"></script>
<script type="text/javascript" charset="utf-8" src="/static/lib/neditor/neditor.all.min.js"></script>
<script type="text/javascript" charset="utf-8" src="/static/lib/neditor/neditor.service.js?v=1.0.2"></script>
<script type="text/javascript">
    var ue = UE.getEditor('content',{
        initialFrameWidth:"100%", //初始化编辑器宽度,默认1000
        initialFrameHeight:320
    });
   /* layui.use(['form', 'element','fly','layer'], function(){

    });*/
</script>
{%end%}