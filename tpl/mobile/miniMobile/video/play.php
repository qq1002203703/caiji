{%extend@common/main%}
{%block@title%}
<title><?=$video['title'].$play['url'][$url2play['defaultQuality']][0]?>免费在线播放_<?=$extendData['playType'][$play['type']].'_'.$play['name']?>_<?=$site_name?></title>
<meta name="keywords" content="<?=$video['title'];?>,<?=$video['title'];?><?=$play['url'][$url2play['defaultQuality']][0];?>,<?=$play['name'];?>">
<meta name="description" content="本页面提供《<?=$video['title'];?>》<?=$play['url'][$url2play['defaultQuality']][0];?>免费在线播放，资源来自<?=$play['name'];?>，播放方式为<?=$extendData['playType'][$play['type']]?>，所有资源由<?=$site_name?>网整理，还有更多其他播放方式，欢迎大家来测试观看。">
<link rel="canonical" href="<?=url('@video_play@',['vid'=>$video['id'],'id'=>$play['id'],'pid'=>$url2play['defaultQuality']],$site_url)?>">
{%end%}

{%block@article%}
<div class="p2">
    <div id="player"></div>
</div>
<div class="pl3 pr3 color4 f28">
    {%include@tmp/ad_article%}
</div>
<div class="player-info pr3 pl3 color3 f30">
    <p class="desc mb1">当前正在在线播放《<a href="<?=url('@video@',['id'=>$video['id']])?>"><?=$video['title']?></a>》<?=$currentPlayUrl[0];?></p>
    <p class="mb1">资源播放来源：<?=$play['name']?> 资源播放方式：<?=$extendData['playType'][$play['type']]?></p>
    <div class="xuanji p1">
        <p class="mb1">选播：</p>
        <p class="mb1">
        <?php foreach ($play['url'] as $key => $value):?>
            <a href="<?=url('@video_play@',['vid'=>$video['id'],'id'=>$play['id'],'pid'=>$key])?>"<?php if($key==$url2play['defaultQuality']){echo ' class="active"';}?>><?=$value[0];?></a>
        <?php endforeach;?>
    </p>
    </div>
    <?php if($video['content']):?>
        <p class="mb1">简介：<?php echo mb_substr(strip_tags($video['content']),0,50);?>...<a class="color4" href="<?=url('@video@',['id'=>$video['id']])?>">更多</a></p>
    <?php endif;?>
</div>
<?php if($otherPlay):?>
    <div class="yang-content mb6 mt1 normal color3 pl3 pr3">
        <h2 class="yang-one">其他资源</h2>
        <ul>
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
{%end%}

{%block@javascript%}
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/dplayer@1.25.0/dist/DPlayer.min.css">
<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/dplayer@1.25.0/dist/DPlayer.min.js"></script>
<script type="text/javascript">
    var dp1 = new DPlayer({
        container: document.getElementById('player'),
        autoplay:false,loop:false,preload:"auto",mutex:true,theme:"#b7daff",volume:1.0,
        video: <?php echo json_encode($url2play);?>
    });
</script>
{%end%}