{%extend@common/main%}
{%block@title%}
<?php $title=$data['seo_title'] ? : $title;?>
<title><?=$title;?>_<?=$site_name?></title>
<meta name="keywords" content="<?=($data['keywords']?:$title);?>">
<meta name="description" content="<?=$data['excerpt']?:($data['videos']?$site_name.'为你提供'.$title.'视频在线观看,'.$site_name.'还为你提供'.$title.'视频在线免费下载':$title)?>">
<link rel="canonical" href="<?=url('@goods@',['id'=>$data['id']],$site_url)?>">
{%end%}
{%block@article%}
<div class="yang-bread f30 pl1 mt1"><a href="/">首页</a>&gt<?=$bread;?>详情</div>
<h2 class="f40 color3 mt3 mb2 pl3 pr3 t-c"><?=$title;?></h2>
<div class="yang-desc pl3 pr3 color4 f28">
    <?=date('Y-m-d',$data['create_time']);?> <i class="icon iconfont icon-refresh" title="人气"></i> <?=$data['views']?>
</div>
<div class="yang-content mb6 mt1 normal color3 pl3 pr3">
    {%include@tmp/ad_article%}
    <?php if($data['videos']):?>
        <div id="yang-video"></div>
    <?php else:?>
    <?=$data['content'];?>
    <?php endif;?>
    <?php if($tags): ?>
        <p class="f30 color4">
            标签 :
            <?php foreach ($tags as $tag):?>
                <a href="<?=url('@tag@',['slug'=>$tag['slug']])?>"><?=$tag['name'];?></a>
            <?php endforeach;?>
        </p>
    <?php endif;?>
    <!--blockquote class="mb4 blockquote-primary normal color4">
        免责声明：本页面内容均来源于用户投稿，其内容的真实性请自行分辨，如有问题，请立即联系客服进行更改或删除。
    </blockquote-->
</div>
<h3 class="yang-title-border pl1 f32 color3 mb2 ml1">最新评论</h3>
{%include@common/comment%}
{%end%}

{%block@javascript%}
<script type="text/javascript" charset="utf-8">
    var videos=<?=$data['videos'] ? $data['videos']:'{}';?>;
    videos.aid=<?=$data['from_id'];?>;
    $(document).ready(function(){
        if(videos.aid>0 && videos.data){
            $('#yang-video').append('<div class="video-container"><iframe class="yang-iframe" src="https://www.bilibili.com/blackboard/html5player.html?cid='+videos.data[0].cid+'&aid='+videos.aid+'&page=1&as_wide=1" width="100%" frameborder="0" allowfullscreen="allowfullscreen"></iframe></div><div class="video-btn"></div>');
            iframeResize();
        }
    });
    var iframeResize=function () {
        $(".yang-iframe").height(($('.video-container').width())/1.58);
    }
</script>
{%end%}