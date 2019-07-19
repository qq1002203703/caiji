{%extend@common/main%}
{%block@title%}
<title><?php echo ($category['seo_title']?:$category['name']);if($currentPage>1){echo '_第'.$currentPage.'页';};?>_<?=$site_name?></title>
<meta name="keywords" content="<?=$category['seo_keywords']?>">
<meta name="description" content="<?=$category['seo_description']?>">
<link rel="canonical" href="<?=url('@video_list@',['slug'=>$category['slug']],$site_url)?>">
{%end%}

{%block@article%}
<div class="yang-bread f30 pl1 mt1 mb1"><a href="/">首页</a>&gt<?=$bread;?>列表</div>
<div class="pl1 pr1">
    {%include@tmp/ad_article%}
</div>
<div class="f36 picbox clearfix">
    <?php $type=['电影','电视']; foreach ($data as $item):?>
        <div class="col-4">
            <a class="link" href="<?=url('@video@',['id'=>$item['id']])?>" title="<?=$item['title'];?>">
                <img class="h34" <?=($item['thumb'] ?'src="'.$item['thumb'].'" alt="'.$item['title'].'"' : 'src="/uploads/images/no.gif" alt="没有缩略图"')?>>
                <span class="icon f28 t-c color8 pl1 pr1"><?=$type[$item['type']]?></span>
                <span class="title f30 color3"><?=$item['title'];?></span>
                <span class="sub  f28 color4 mb2"><?=$item['actor'];?></span>
            </a>
        </div>
    <?php endforeach;?>
</div>
<?=$page;?>

{%end%}

{%block@javascript%}
{%end%}