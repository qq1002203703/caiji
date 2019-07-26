{%extend@common/main%}
{%block@title%}
<title><?=$title;?>_<?=$site_name?></title>
<meta name="keywords" content="<?=$title;?>">
<meta name="description" content="<?=$title;?>">
<link rel="canonical" href="<?=url('@comment@',['id'=>$data['id']],$site_url)?>">
{%end%}

{%block@article%}
<div class="yang-bread f30 pl1 mt1"><a href="/">首页</a>&gt;<a href="<?=url('@'.$post['type'].'@',['id'=>$post['id']])?>"><?=$post['title']?></a>&gt;详情</div>
<h2 class="f40 color3 mt3 mb2 pl3 pr3"><?=$title?></h2>
<div class="yang-desc pl3 pr3 color4 f28">
     <?=date('Y-m-d',$data['create_time']);?> <i class="icon iconfont icon-refresh" title="回复"></i>  <?=$data['children']?>
    <blockquote class="mb4 blockquote-primary normal color4 mt2">
        原文：<a href="<?=url('@'.$post['type'].'@',['id'=>$post['id']])?>" ><?=$post['title']?></a><br><?=url('@'.$post['type'].'@',['id'=>$post['id']])?>
    </blockquote>
</div>
<div class="yang-content mb6 mt1 normal color3 pl3 pr3">
    {%include@tmp/ad_article%}
    <?=$data['content'];?>
    <?php if ($comments):?>
        <h3 class="yang-title-border pl1">跟帖回复 : </h3>
        <div class="bg-color7 p1">
            <?php foreach ($comments as $comment):?>
                <p>
                    <a class="color3" href="<?=url('@member@',['uid'=>$comment['uid']])?>"><?=$comment['username']?>:</a>
                    <?=strip_tags($comment['content'])?>
                </p>
            <?php endforeach;?>
        </div>
    <?php endif;?>
</div>
{%end%}