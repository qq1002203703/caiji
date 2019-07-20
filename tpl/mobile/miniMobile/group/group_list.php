{%extend@common/main%}
{%block@title%}
<title><?=($category['seo_title']?:$category['name']);?>_小组_<?=$site_name?></title>
<meta name="keywords" content="<?=$category['seo_keywords']?>">
<meta name="description" content="<?=$category['seo_description']?>">
<link rel="canonical" href="<?=url('@group_list@',['slug'=>$category['slug']],$site_url)?>">
{%end%}

{%block@article%}
<div class="yang-bread f30 pl1 mt1"><a href="/">首页</a>&gt<a href="/group/all">小组</a>&gt<?=$bread;?></div>
<div class="w75 clearfix grid">
    <div class="yang-tag-img fl w20 pl3 mt3">
        <?php if($category['thumb']):?>
            <img src="<?=$category['thumb']?>" alt="<?=$category['name']?>">
        <?php else:?>
            <img src="<?=$tuku;?>/uploads/images/no.gif" alt="没有图片">
        <?php endif;?>
    </div>
    <div class="fr w55">
        <h2 class="f36 color2 pl3 pr3 mt3"><?=$category['seo_title']?: $category['name'];?></h2>
        <div class="yang-desc pl3 pr3 color3 f30">
            <p>小组话题：<?=$category['counts']?></p>
            <?=$category['content']?>
            <p>相关小组：<?php if($randoms): foreach ($randoms as $item):?><span class="dib mr1"><a href="<?=url('@group_list@',['slug'=>$item['slug']])?>"><?=$item['name']?></a></span><?php endforeach;endif;?></p>
        </div>
    </div>
</div>
{%include@tmp/ad_article%}
<?php if($data):?>
    <ul class="yang-list2 mt2">
        <?php  foreach ($data as $article): ?>
            <li class="item w75 h10 pl2 pr2 mb2 pb1">
                <div class="item-l w9">
                    <a href="#"><img src="<?=($article['avatar']? $tuku.$article['avatar']:$tuku.'/uploads/user/default.png')?>" alt="<?=$article['username']?>"></a>
                </div>
                <div class="item-r w57 ml1">
                    <div class="title color3 f32">
                        <a href="<?=url('@group@',['id'=>$article['id']])?>"><?=$article['title']?></a>
                    </div>
                    <div class="sub color4 f30">
                        <span class="dib"><?=date('Y-m-d H:i',$article['create_time'])?></span>
                        <i class="icon iconfont icon-comment dib pr1 pl1"></i>
                        <span class="dib"><?=$article['comments_num']?></span>
                    </div>
                </div>
            </li>
        <?php endforeach;?>
    </ul>
    <?=$page;?>
<?php endif;?>
{%end%}
