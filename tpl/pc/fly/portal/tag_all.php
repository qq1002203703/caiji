{%extend@common/base_portal%}
{%block@title%}
<title><?=$title;?>_<?=$site_name?></title>
<meta name="keywords" content="<?=$title;?>">
<meta name="description" content="">
{%end%}
{%block@article%}
<div class="layui-container">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md8">
            <div class="fly-panel" style="margin-bottom: 0;">
                <div class="yang-tag">
                    <h1 class="yang-title"><?=$title?></h1>
                    <div class="yang-box">
                        <div class="yang-left">

                        </div>
                        <div class="yang-right">
                            <p class="yang-desc"></p>
                            <div class="yang-relation">
                                <p>
                                    <?php if($data): foreach ($data as $item):?>
                                    <span><a href="<?=url('@tag@',['slug'=>$item['slug']])?>"><?=$item['name']?></a></span>
                                    <?php endforeach;endif;?>
                                </p>
                            </div>
                        </div>
                        <div class="layui-clear"></div>
                    </div>
                    <div class="yang-content"></div>
                </div>
                <!-- <div class="fly-none">没有相关数据</div> -->
                <div style="text-align: center">
                    <!--?=$page?-->
                </div>
            </div>
        </div>
    </div>
</div>
{%end%}
{%block@javascript%}
<script type="text/javascript">
    layui.use(['jquery','layer'], function(){
        var layer = layui.layer;
        var $=layui.$;
        layer.ready(function () {
            $("#topic-img").click(function () {
                _this=$(this);
                layer.open({
                    type:1,
                    content:'<img src="'+$(this).attr('data')+'">',
                    offset: ['100px', '100px']
                });
            });
        });
    });
</script>
{%end%}

