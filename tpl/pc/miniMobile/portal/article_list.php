{%extend@common/main%}
{%block@title%}
<title><?=($category['seo_title']?:$category['name']);?>_<?=$site_name?></title>
<meta name="keywords" content="<?=$category['seo_keywords']?>">
<meta name="description" content="<?=$category['seo_description']?>">
{%end%}

{%block@article%}
<h1 class="t-c f40 color2 mt4 mb2 pl3 pr3"><?=$category['name']?></h1>
<?php if($data):?>
    <ul class="yang-list">
        <?php  foreach ($data as $article):?>
            <li>
                <a class="w75 grid mb2 mt2 clearfix pr2 pl2" href="<?=url('@article@',['id'=>$article['id']])?>">
                    <div class="box col-8">
                        <h3 class="f34"><?=$article['title']?></h3>
                    </div>
                    <div class="box col-4"><img <?=($article['thumb'] ?'src="'.$article['thumb'].'_200x200.jpg" alt="'.$article['title'].'"' : 'src="/uploads/images/no.gif" alt="没有缩略图"')?>></div>
                </a>
            </li>
        <?php endforeach;?>
    </ul>
    <?=$page;?>
<?php endif;?>
{%end%}
