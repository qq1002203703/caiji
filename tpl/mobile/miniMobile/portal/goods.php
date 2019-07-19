{%extend@common/main%}
{%block@title%}
<title><?=($data['seo_title'] ? : $title);?>_<?=$site_name?></title>
<meta name="keywords" content="<?=($data['keywords']?:$title);?>">
<meta name="description" content="<?=$data['excerpt']?>">
<link rel="canonical" href="<?=url('@goods@',['id'=>$data['id']],$site_url)?>">
{%end%}

{%block@article%}
<div class="yang-bread f30 pl1 mt1"><a href="/">首页</a>&gt<?=$bread;?>详情</div>
<h2 class="f40 color3 mt3 mb2 pl3 pr3"><?=$title?></h2>
<div class="yang-desc pl3 pr3 color4 f28">
     <?=date('Y-m-d',$data['create_time']);?> <i class="icon iconfont icon-refresh" title="人气"></i> <?=$data['views']?>
</div>
<div class="yang-content mb6 mt1 normal color3 pl3 pr3">
    {%include@tmp/ad_article%}
    <?=$data['content'];?>
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