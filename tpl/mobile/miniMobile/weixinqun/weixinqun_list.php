{%extend@common/main%}
{%block@title%}
<title><?=$seo_title?>_<?=$site_name?></title>
<meta name="keywords" content="<?=$title?>">
<meta name="description" content="<?=$seo_description?>">

{%end%}

{%block@article%}
<h1 class="t-c f40 color2 mt4 mb2 pl3 pr3"><?=$title?>下微信群</h1>
<?php if($data):?>
    <ul class="yang-list">
        <?php  foreach ($data as $article):?>
            <li>
                <a class="w75 grid mb2 mt2 clearfix pr2 pl2" href="<?=url('@weixinqun@',['id'=>$article['id']])?>">
                    <div class="box col-3"><img <?=($article['qun_qrcode'] ?'src="'.$article['qun_qrcode'].'_200x200.jpg" alt="'.$article['title'].'"' : 'src="/uploads/images/no.gif" alt="没有缩略图"')?>></div>
                    <div class="box col-9">
                        <h3 class="f34"><?=$article['title']?></h3>
                    </div>
                </a>
            </li>
        <?php endforeach;?>
    </ul>
    <?=$page;?>
<?php endif;?>
{%end%}
