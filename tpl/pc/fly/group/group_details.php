{%extend@common/base_portal%}
{%block@title%}
<?php $title=$data['seo_title'] ? : $title;?>
<title><?=$title;?>_<?=$site_name?></title>
<meta name="keywords" content="<?=($data['keywords']?:$title);?>">
<meta name="description" content="<?=$data['excerpt']?:($data['videos']?$site_name.'为你提供'.$title.'视频在线观看,'.\extend\Helper::text_cut($data['content'],200):$title)?>">
<meta name="mobile-agent" content="format=html5;url=<?=url('@group@',['id'=>$data['id']],'http://'.$mobile_domain)?>">
<link rel="alternate" media="only screen and(max-width: 750px)" href="<?=url('@group@',['id'=>$data['id']],'http://'.$mobile_domain)?>">
<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?=$site_url?>/feed/portal">
<link rel="alternate" type="application/rss+xml" title="<?=$title?>" href="<?=url('@feed_group@',['id'=>$data['id'],'feed'=>'feed'])?>">
<style>
    .detail-box h1.video-title{font-size: 16px;color: #666;text-align: center;margin-bottom:10px; }
    .video-container{max-width: 620px;margin: 0 auto;border: 1px solid #000}
</style>
{%end%}
{%block@article%}
<div class="layui-container">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md8 content detail">
            <div class="fly-panel detail-box">
                <div class="layui-breadcrumb" lay-separator="&gt;">
                    <a href="/">首页</a>
                    <a href="/group/all">小组</a>
                    <?=$bread?>
                    <a href="#">详情</a>
                </div>
                <?php if($data['videos']):?>
                    <h1 class="video-title"><?=$data['seo_title']?$data['seo_title']:$title?></h1>
                    <div class="fly-detail-info">
                    <span class="fly-detail-column">
                        <?=date('Y-m-d H:i',$data['create_time']);?>&nbsp;&nbsp;作者:<a href="<?=url('@member@',['uid'=>$data['uid']])?>" ><?=$data['username']?></a>
                    </span>
                        <span class="fly-list-nums">
                        <i class="iconfont" title="回复">&#xe60c;</i> <?=$data['comments_num']?>
                  </span>
                    </div>
                    <div id="yang-video"></div>
                    <div class="detail-body photos">
                        <div class="item"><span>简介：</span>
                            <div id="jianjie"><?=$data['content']?></div>
                        </div>
                    <?php if($tags): ?>
                        <p class="detail-tags">
                            标签 :
                            <?php foreach ($tags as $tag):?>
                                <a href="<?=url('@tag@',['slug'=>$tag['slug']])?>"><?=$tag['name'];?></a>
                            <?php endforeach;?>
                        </p>
                    <?php endif;?>
                    </div>
                <?php else:?>
                <h1><?=$title?></h1>
                <div class="fly-detail-info">
                    <span class="fly-detail-column">
                        <?=date('Y-m-d H:i',$data['create_time']);?>
                    </span>
                    <span class="fly-list-nums">
                        <!--a href="#comment"><i class="iconfont" title="回答">&#xe60c;</i><?//=$data['comments_num']?></a-->
                        <i class="iconfont" title="人气">&#xe60b;</i> <?=$data['views']?>
                  </span>
                </div>
                <div class="detail-body photos">
                    <?=$data['content'];?>
                    <?php if($tags): ?>
                        <p class="detail-tags">
                            标签 :
                            <?php foreach ($tags as $tag):?>
                                <a href="<?=url('@tag@',['slug'=>$tag['slug']])?>"><?=$tag['name'];?></a>
                            <?php endforeach;?>
                        </p>
                    <?php endif;?>
                    <?php if($isAdmin):?>
                        <div class="yang-admin">
                            <a class="layui-btn layui-btn-sm" href="<?=url('bbs/post/add_multi')?>">批量发贴</a>
                            <a id="ctrl-multi-reply" class="layui-btn layui-btn-sm" ac="<?=url('bbs/post/add_multi')?>" type="button" href="javascript:;">批量回贴</a>
                        </div>
                    <?php endif;?>
                </div>
                <?php endif;?>
            </div>
            <div class="fly-panel detail-box" id="flyReply">
                <fieldset class="layui-elem-field layui-field-title" style="text-align: center;">
                    <legend>最新评论</legend>
                </fieldset>
                {%include@common/comment%}
            </div>
        </div>
        <div class="layui-col-md4">
            <div class="fly-panel">
                <div class="fly-panel-title">相关推荐</div>
                <div class="fly-panel-main">
                    <div class="layui-row layui-col-space3">
                        <?php //echo getRelatedPostsByCategory($data['category_id'],'@group@',6,'<div class="layui-col-xs6"><a class="sidebar-img" href="{%url%}" title="{%title%}"><img src="{%thumb%}" alt="{%title%}"></a></div>');?>
                    </div>
                </div>
            </div>
            <div class="fly-panel">
                <div class="fly-panel-title">其他推荐</div>
                <div class="fly-panel-main">
                    <div class="layui-row layui-col-space3">
                        <?php //echo getRelatedPostsByCategory($data['category_id'],'@group@',6,'<div class="layui-col-xs6"><a class="sidebar-img" href="{%url%}" title="{%title%}"><img src="{%thumb%}" alt="{%title%}"></a></div>',false);?>
                    </div>
                </div>
            </div>
            <div class="fly-panel" id="sidebar-tag">
                <div class="fly-panel-title">最新话题</div>
                <div class="fly-panel-main">
                    <?php echo listNewestTags(20);?>
                </div>
            </div>
        </div>
    </div>
</div>
{%end%}
{%block@javascript%}
<script type="text/javascript" charset="utf-8">
    var currentData={
        id:<?=$data['id']?>,
        table:"portal_post",
        shopCartUrl:"<?=url('portal/shop/cart')?>",
        shopCartJson:"<?=url('portal/shop/cart_json')?>",
        commentCtrlUrl:"<?=url('api/comment/ctrl')?>"
    };
    var videos=<?=$data['videos'] ? $data['videos']:'{}';?>;
    videos.aid=<?=$data['from_id'];?>;
    layui.config({version: "3.0.1", base: '/static/fly/mods/'}).extend({post: 'post'}).use('post');
    layui.use(['layer','shorten'], function(){
        var $=layui.jquery;
        var iframeResize=function () {
            $(".yang-iframe").height(($('.video-container').width())/1.58);
        };
        $(document).ready(function(){
            if(videos.aid>0 && videos.data){
                $('#yang-video').append('<div class="video-container"><iframe class="yang-iframe" src="https://player.bilibili.com/player.html?cid='+videos.data[0].cid+'&aid='+videos.aid+'&page=1&as_wide=1" width="100%" frameborder="0" allowfullscreen="allowfullscreen"></iframe></div><div class="video-btn"></div>');
                iframeResize();
            }
            //显示更多
            $('#jianjie').shorten({
                showChars: 200,
                moreText: ' 展开<i class="layui-icon layui-icon-down"></i>',
                lessText: ' 收起<i class="layui-icon layui-icon-up"></i>'
            });
        });

    });

</script>
{%end%}

