{%extend@common/main%}
{%block@title%}
<title>全部小组_<?=$site_name?></title>
<meta name="keywords" content="全部小组">
<meta name="description" content="<?=$site_name?>下全部小组">
<link rel="canonical" href="<?=url('@group_all@','',$site_url)?>">
{%end%}

{%block@article%}
<div class="yang-bread f30 pl1 mt1"><a href="/">首页</a>&gt全部小组</div>
{%include@tmp/ad_article%}
<div class="f36 picbox clearfix pl1 pr1 mt1">
    <?php foreach ($data as $item):?>
        <div class="col-4">
            <a class="link" href="<?=url('@group_list@',['slug'=>$item['slug']])?>" title="<?=$item['name'];?>">
                <img class="h15" <?=($item['thumb'] ?'src="'.$item['thumb'].'" alt="'.$item['name'].'"' : 'src="'.$tuku.'/uploads/images/no.gif" alt="没有缩略图"')?>>
                <span class="title f30 color3"><?=$item['name'];?></span>
                <span class="sub  f28 color4 mb2">小组话题: <span class="red"><?=$item['counts']?></span></span>
            </a>
        </div>
    <?php endforeach;?>
</div>
<?=$page;?>
{%end%}