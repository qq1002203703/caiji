{%extend@common/main%}
{%block@title%}
<title><?=$title?>_<?=$site_name?></title>
<meta name="keywords" content="<?=$title?>">
{%end%}

{%block@article%}
<?php if($cityParent): //上级城市?>
<div class="yang-bread f30 pl1"><a href="/">首页</a> &gt <a href="<?=url('@xuexiao_city@',['id'=>$cityParent['id']])?>"><?=$cityParent['name'];?></a> &gt <?=$city['name'];?></div>
<?php endif;?>
<h1 class="t-c f40 color2 mt4 mb2 pl3 pr3"><?=$city['merger_name'];?>下学校大全</h1>
<?php if($cityChildren):?>
<div class="yang-city w73 m1 color8 t-c clearfix f28">
<?php foreach ($cityChildren as $child)://下级城市?>
    <a class="w22 h8 mr1 ml1 pt2 pb2 mb1 radius0 fl bg-color-info color8" href="<?=url('@xuexiao_city@',['id'=>$child['id']])?>"><?=$child['name'];?></a>
<?php endforeach;?>
</div>
<?php endif;?>
<?php if($data):?>
    <ul class="yang-list">
        <?php  foreach ($data as $article):?>
            <li>
                <a class="w75 grid mb2 mt2 clearfix pr2 pl2" href="<?=url('@xuexiao@',['id'=>$article['id']])?>">
                    <div class="box col-3"><img <?=($article['thumb'] ?'src="'.$article['thumb'].'_320x200.jpg" alt="'.$article['title'].'"' : 'src="/uploads/images/no.gif" alt="没有缩略图"')?>></div>
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
