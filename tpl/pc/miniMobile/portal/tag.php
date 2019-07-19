{%extend@common/main%}
{%block@title%}
<title><?=$tag['seo_title']?: $tag['name'];?>_关于<?=$tag['name'];?>的讨论_<?=$site_name?></title>
<meta name="keywords" content="<?=$tag['seo_keywords']?:$tag['name']?>">
<meta name="description" content="<?=($tag['seo_description']?:'关于'.$tag['name'].'的讨论');?>">
{%end%}

{%block@article%}
<div class="w75 clearfix grid">
    <div class="yang-tag-img fl w20 pl3 mt3">
        <?php if($tag['thumb']):?>
            <img src="<?=$tag['thumb']?>_200x200.jpg" alt="<?=$tag['name']?>">
        <?php else:?>
            <img src="/uploads/images/no.gif" alt="没有图片">
        <?php endif;?>
    </div>
    <div class="fr w55">
        <h1 class="f40 color2 pl3 pr3 mt3"><?=$tag['name']?></h1>
        <div class="yang-desc pl3 pr3 color3 f30">
            <?=$tag['content']?>
        </div>
    </div>
</div>
<div  class="yang-content pl3 pr3">
    <?php if($data):?>
        <ul class="yang-tag-list">
            <h2 class="f40 color2 pl3 pr3 mt3">【<?=$tag['name']?>】话题下内容列表</h2>
            {%include@tmp/ad_article%}
            <?php foreach ($data as $item): ?>
                <li class="mb2 bb">
                    <div class="title f32 color2">[<?=$item['pindao'];?>] <a href="<?=$item['url']?>"><?=$item['title']?></a></div>
                    <div class="content color3 f32"><?=$item['content'];?></div>
                    <div class="info f28 color4 mt1 mb1">
                        <span class="date"><?=date('Y-m-d H:i',$item['create_time'])?></span>
                        <span class="comments-num"><i class="icon iconfont icon-comment" title="回答"></i> <?=$item['comments_num']?></span>
                    </div>
                </li>
            <?php endforeach;?>
        </ul>
    <?php endif;?>
</div>
{%end%}