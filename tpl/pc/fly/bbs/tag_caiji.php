{%extend@common/bbs%}
{%block@title%}
<title><?=$tag['seo_title']?: $tag['name'];?>_关于<?=$tag['name'];?>的讨论_<?=$site_name?></title>
<meta name="keywords" content="<?=$tag['seo_keywords']?>">
<meta name="description" content="<?=$tag['seo_description']?>">
{%end%}
{%block@article%}
<div class="layui-col-md8">
    <div class="fly-panel" style="margin-bottom: 0;">
        <div class="yang-tag">
            <div class="yang-box">
                <div class="yang-left">
                    <img id="topic-img" class="yang-img" src="<?=$tag['thumb']?>_200x200.jpg" alt="<?=$tag['name']?>" title="点击查看大图" data="<?=$tag['thumb']?>">
                </div>
                <div class="yang-right">
                    <h1 class="yang-title"><?=$tag['seo_title']?></h1>
                    <p class="yang-desc"><?=$tag['seo_description']?></p>
                    <div class="yang-relation">
                        <p>相关话题</p>
                        <p>
                            <?php if($randomTags): foreach ($randomTags as $randomTag):?>
                            <span><a href="<?=url('@tag_bbs_normal@',['slug'=>$randomTag['slug']])?>"><?=$randomTag['name']?></a></span>
                            <?php endforeach;endif;?>

                        </p>
                    </div>
                </div>
                <div class="layui-clear"></div>
            </div>
            <div class="yang-content"><?=$tag['content']?></div>
            <?php if($data):?>
                <ul class="yang-list">
                    <?php foreach ($data as $item):?>
                        <li>
                            <div class="yang-list-left">
                                <div class="yang-list-badge">
                                    <span class="yang-list-count">120</span>
                                    <span class="yang-list-liulan">浏览</span>
                                </div>
                            </div>
                            <div class="yang-list-right">
                                <h2><a href="<?=url('@bbs_post@',['id'=>$item['id']])?>"><?=$item['title']?></a></h2>
                                <div class="content"><?=$item['content']?></div>
                                <span class="fly-list-nums"><i class="iconfont icon-pinglun1" title="回答"></i><?=$item['comments_num']?></span>
                                <span class="date">有<?=$item['comments_num']?>人参与了讨论，<?=date('Y-m-d H:i',$item['create_time'])?></span>
                            </div>
                        </li>
                    <?php endforeach;?>
                </ul>
            <?php endif;?>
        </div>
        <!-- <div class="fly-none">没有相关数据</div> -->
        <div style="text-align: center">
            <?=$page?>
            <?php //dump($page);?>
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

