{%extend@common/main%}
{%block@title%}
<title><?=($data['seo_title'] ? : $title);?>在线观看_<?=$site_name?></title>
<meta name="keywords" content="<?=$data['keywords']?>">
<meta name="description" content="<?=$data['excerpt']?>">
<link rel="canonical" href="<?=url('@video@',['id'=>$data['id']],$site_url)?>">
{%end%}

{%block@article%}
<div class="yang-bread f30 pl1 mt1 mb1"><a href="/">首页</a>&gt<?=$bread;?>详情</div>
<div class="video-info">
    <div class="w75 grid mb2 mt2 clearfix pr2 pl2">
        <div class="box col-4">
            <img <?=($data['thumb'] ?'src="'.$data['thumb'].'" alt="'.$data['title'].'"' : 'src="/uploads/images/nopic.gif" alt="没有缩略图"')?>>
            <div class="score f28 t-c color8 pl1 pr1 bg-color-danger"><?=$data['score']?></div>
        </div>
        <div class="box col-8">
            <h2 class="title f34 color2 mb2 pl3 pr3"><?=$title?></h2>
            <div class="item pl2 pr2 f28">
                <span class="color4">导演：</span><span class="color3"><?=$data['director']?></span>
            </div>
            <div  class="item pl2 pr2 f28">
                <span class="color4">主演：</span><span class="color3"><?=$data['actor']?></span>
            </div>
            <div class="item pl2 pr2 f28">
                <span class="color4">地区：</span><span class="color3"><?=$data['area']?></span>
            </div>
            <div class="item pl2 pr2 f28">
                <span class="color4">类型 :</span>
                <?php foreach ($tags as $tag):?>
                    <a class="color3" href="<?=url('@tag@',['slug'=>$tag['slug']])?>" ><?=$tag['name'];?></a>
                <?php endforeach;?>
            </div>
        </div>
    </div>
</div>
<div class="pl3 pr3 color4 f28">
    {%include@tmp/ad_article%}
</div>
<div class="tabBox m3">
    <section class="swiper-container">
        <div class="tabindexList f30">
            <span class="pr2 tab-title color2">在线观看</span>
            <span class="pl2 pr2 color2">信息详情</span>
            <span class="pl2 pr2 color2">讨论点评</span>
        </div>
        <div class="swiper-wrapper color3 f28">
            <div class="swiper-slide pr2 pt2 pb2">
                <ul class="video-list">
                <?php $typeArr=[
                    'm3u8'=>'m3u8在线观看',
                    'xunlei'=>'迅雷下载',
                    'baidupan'=>'百度盘下载',
                    'xfplay'=>'先锋影音在线观看',
                    'xigua'=>'西瓜影音在线观看',
                ]; foreach ($source as $item):?>
                    <li class="item mb2">
                        <h3 class="f30 mb1 yang-bgcolor-success p1 color8"><?=$item['name'];?>:<?=$typeArr[$item['type']];?></h3>
                        <div class="pl1">
                            <?php $urlList=explode("\n",$item['url']);foreach ($urlList as $key => $value):$value=explode('$',$value);?>
                                <a class="mb1" href="<?=url('@video_play@',['vid'=>$data['id'],'id'=>$item['id'],'pid'=>$key])?>"><?=$value[0];?></a>
                            <?php endforeach;?>
                        </div>
                    </li>
                <?endforeach;?>
                </ul>
            </div>
            <div class="swiper-slide pr2 pt2 pb2">
                <p><span class="color4">别名：</span><?=$data['other_name']?></p>
                <p><span class="color4">上映日期：</span><?=date('Y-m-d',$data['date_published'])?></p>
                <p><span class="color4">制片人：</span><?=$data['producer']?></p>
                <p><span class="color4">豆瓣评分：</span><span class="color1"><?=$data['score']?></span></p>
                <p><span class="color4">简介：</span></p>
                <?=$data['content']?>
            </div>
            <div class="swiper-slide pr2 pt2 pb2">
                {%include@common/comment%}
            </div>
        </div>
    </section>
</div>

{%end%}

{%block@javascript%}
<script type="text/javascript">
var swiper = new Swiper('.swiper-container', {on: {
        slideChangeTransitionStart: function() {
            $(".tabindexList span").removeClass("tab-title").eq(this.activeIndex).addClass("tab-title");
        }
    }});
$(".tabindexList span").click(function() {
    var index = $(this).index();
    $(".tabindexList span").removeClass("tab-title").eq(index).addClass("tab-title");
    swiper.slideTo(index, 300, false);
});
</script>
{%end%}