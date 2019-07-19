{%extend@common/base_portal%}
{%block@title%}
<title><?=$video['title'].$play['url'][$url2play['defaultQuality']][0]?>免费在线播放_<?=$extendData['playType'][$play['type']].'_'.$play['name']?>_<?=$site_name?></title>
<meta name="keywords" content="<?=$video['title'];?>,<?=$video['title'];?><?=$play['url'][$url2play['defaultQuality']][0];?>,<?=$play['name'];?>">
<meta name="description" content="本页面提供《<?=$video['title'];?>》<?=$play['url'][$url2play['defaultQuality']][0];?>免费在线播放，资源来自<?=$play['name'];?>，播放方式为<?=$extendData['playType'][$play['type']]?>，所有资源由<?=$site_name?>网整理，还有更多其他播放方式，欢迎大家来测试观看。">
<meta name="mobile-agent" content="format=html5;url=<?=url('@video_play@',['vid'=>$video['id'],'id'=>$play['id'],'pid'=>$url2play['defaultQuality']],'http://'.$mobile_domain);?>">
<link rel="alternate" media="only screen and(max-width: 750px)" href="<?=url('@video_play@',['vid'=>$video['id'],'id'=>$play['id'],'pid'=>$url2play['defaultQuality']],'http://'.$mobile_domain);?>">
{%end%}
{%block@article%}
<div class="layui-container">
    <div class="layui-row layui-col-space15">
        <!--左边主要区域-->
        <div class="layui-col-md8 content detail">
            <div class="fly-panel detail-box">
                <div class="layui-breadcrumb" lay-separator="&gt;">
                    <a href="/">首页</a>
                    <?//=$bread?>
                    <a href="<?=url('@video@',['id'=>$video['id']])?>"><?=$video['title']?></a>
                    <a href="#"><?=$currentPlayUrl[0];?>在线播放</a>
                </div>
                <div id="dplayer"></div>
                <div class="player-info">
                    <p class="desc">当前正在在线播放《<a href="<?=url('@video@',['id'=>$video['id']])?>"><?=$video['title']?></a>》<?=$currentPlayUrl[0];?></p>
                    <p>资源播放来源：<?=$play['name']?> 资源播放方式：<?=$extendData['playType'][$play['type']]?></p>
                    <p class="xuanji">选集：
                    <?php foreach ($play['url'] as $key => $value):?>
                        <a href="<?=url('@video_play@',['vid'=>$video['id'],'id'=>$play['id'],'pid'=>$key])?>"<?php if($key==$url2play['defaultQuality']){echo ' class="active"';}?>><?=$value[0];?></a>
                    <?php endforeach;?>
                    </p>
                <?php if($video['content']):?>
                    <p>剧情：<?php echo mb_substr(strip_tags($video['content']),0,60);?>...<a href="<?=url('@video@',['id'=>$video['id']])?>"> 详情</a></p>
                <?php endif;?>
                </div>
                <?php if($otherPlay):?>
                <div class="detail-body photos">
                    <h2>《<?=$video['title'];?>》其他资源</h2>
                    <ul class="detail-video" id="video-online">
                        <?php $typeArr=[
                            'm3u8'=>'m3u8在线观看',
                            'xunlei'=>'迅雷下载',
                            'baidupan'=>'百度盘下载',
                            'xfplay'=>'先锋影音在线观看',
                            'xigua'=>'西瓜影音在线观看',
                        ]; foreach ($otherPlay as $item):?>
                            <li><h3><?=$item['name'];?>:<?=$typeArr[$item['type']];?></h3>
                                <?php $urlList=explode("\n",$item['url']);foreach ($urlList as $key => $value):$value=explode('$',$value);?>
                                    <a href="<?=url('@video_play@',['vid'=>$video['id'],'id'=>$item['id'],'pid'=>$key])?>"><?=$value[0];?></a>
                                <?php endforeach;?>
                            </li>
                        <?endforeach;?>
                    </ul>
                </div>
                <?php endif;?>
                <?php if($isAdmin):?>
                    <div class="yang-admin">
                        <a class="layui-btn layui-btn-sm" href="<?=url('bbs/post/add_multi')?>">批量发贴</a>
                        <a id="ctrl-multi-reply" class="layui-btn layui-btn-sm" ac="<?=url('bbs/post/add_multi')?>" type="button" href="javascript:;">批量回贴</a>
                    </div>
                <?php endif;?>
            </div>
        </div>
        <!--sidebar-->
        <div class="layui-col-md4">
            <div class="fly-panel">
                <div class="fly-panel-title">相关推荐</div>
                <div class="fly-panel-main">
                    <div class="layui-row layui-col-space3">
                        <?php //echo getRelatedPostsByCategory($data['category_id'],'@goods@',6,'<div class="layui-col-xs6"><a class="sidebar-img" href="{%url%}" title="{%title%}"><img src="{%thumb%}" alt="{%title%}"></a></div>');?>
                    </div>
                </div>
            </div>
            <div class="fly-panel">
                <div class="fly-panel-title">其他推荐</div>
                <div class="fly-panel-main">
                    <div class="layui-row layui-col-space3">
                        <?php //echo getRelatedPostsByCategory($data['category_id'],'@goods@',6,'<div class="layui-col-xs6"><a class="sidebar-img" href="{%url%}" title="{%title%}"><img src="{%thumb%}" alt="{%title%}"></a></div>',false);?>
                    </div>
                </div>
            </div>
            <div class="fly-panel" id="sidebar-tag">
                <div class="fly-panel-title">最新<a href="<?=url('@tag_all@')?>">话题</a></div>
                <div class="fly-panel-main">
                    <?php echo listNewestTags(20);?>
                </div>
            </div>
        </div>
    </div>
</div>
{%end%}

{%block@javascript%}
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/dplayer@1.25.0/dist/DPlayer.min.css">
<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/dplayer@1.25.0/dist/DPlayer.min.js"></script>
<script type="text/javascript" charset="utf-8">
    var dp1 = new DPlayer({
        container: document.getElementById('dplayer'),
        autoplay:false,loop:false,preload:"auto",mutex:true,iconsColor:"#31A995",
        video: <?php echo json_encode($url2play);?>
    });
    var currentData={
        id:<?=$play['id']?>,
        table:"video",
        shopCartUrl:"<?=url('portal/shop/cart')?>",
        shopCartJson:"<?=url('portal/shop/cart_json')?>",
        commentCtrlUrl:"<?=url('api/comment/ctrl')?>"
    };
    layui.config({version: "3.0.1", base: '/static/fly/mods/'}).extend({post: 'post'}).use('post');
    layui.use(['util','shorten'], function(){
        var $ = layui.jquery;
        //显示更多
        $('#jianjie').shorten({
            showChars: 80,
            moreText: ' 展开<i class="layui-icon layui-icon-down"></i>',
            lessText: ' 收起<i class="layui-icon layui-icon-up"></i>'
        });
    });
</script>

{%end%}

