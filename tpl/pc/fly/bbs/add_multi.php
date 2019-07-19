{%extend@common/bbs%}
{%block@title%}
<title><?=$title?>_<?=$site_name?></title>
{%end%}
{%block@article%}
        <div class="layui-col-md8 content detail">
            <div class="fly-panel detail-box">
                <h1><?php echo $title;$time=date('Y-m-d H:i',time()-3600*2);?></h1>
                <div class="fly-detail-info"></div>
                <div class="detail-body photos">
                    <form class="layui-form" action="<?=url('bbs/fabu/start2')?>" method="post">
                        <div class="layui-form-item">
                            <input class="layui-input" name="title" id="L_title" value="" placeholder="标题" lay-verify="required" required>
                        </div>
                        <div class="layui-form-item">
                            <input class="layui-input" name="tag" id="L_tag" value="" placeholder="标签">
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">种类</label>
                            <div class="layui-input-inline">
                                <select name="type">
                                    <option value="1">1</option>
                                    <option value="2" selected>2</option>
                                </select>
                            </div>

                        </div>
                        <div class="layui-form-item">
                            <textarea id="L_content" name="content" class="text-edit" lay-verify="required" required style="min-height:400px;"><?=$data;?></textarea>
                        </div>
                        <button id="L_btn-submit" class="layui-btn" lay-filter="common" lay-submit alert="1" type="submit">马上提交</button>
                        <a class="layui-btn layui-btn-primary" href="javascript:;" id="go-back-btn">取消</a>
                    </form>
                </div>
            </div>
        </div>
{%end%}
{%block@javascript%}
<script type="text/javascript">
    layui.use("layer",function () {
       var $=layui.jquery;
       var text_box=$("#L_content");
        text_box.height(text_box.get(0).scrollHeight);
    });

</script>
{%end%}

