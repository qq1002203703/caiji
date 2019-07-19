{%extend@common/base_portal%}
{%block@title%}
<title><?php echo ($category['seo_title']?:$category['name']);if($currentPage>1){echo '_第'.$currentPage.'页';};?>_<?=$site_name?></title>
<meta name="keywords" content="<?=$category['seo_keywords']?>">
<meta name="description" content="<?=$category['seo_description']?>">
<meta name="mobile-agent" content="format=html5;url=<?=url('@video_list@',['slug'=>$category['slug']],'http://'.$mobile_domain)?>">
<link rel="alternate" media="only screen and(max-width: 750px)" href="<?=url('@video_list@',['slug'=>$category['slug']],'http://'.$mobile_domain)?>">
{%end%}

{%block@article%}
<div class="layui-container">
    <div class="fly-panel">
        <div class="layui-breadcrumb p20" lay-separator="&gt;">
            <a href="/">首页</a>
            <?=$bread?>
            <a href="#">列表</a>
        </div>
        <?php if($data):?>
        <ul class="list-movie pl20">
            <?php $type=['电影','电视']; foreach ($data as $item):?>
            <li class="item w180 mr15">
                <a class="link" href="<?=url('@video@',['id'=>$item['id']])?>" title="<?=$item['title'];?>">
                    <div class="pic h240">
                        <img <?=($item['thumb'] ?'src="'.$item['thumb'].'" alt="'.$item['title'].'"' : 'src="/uploads/images/no.gif" alt="没有缩略图"')?>>
                        <span class="icon"><?=$type[$item['type']]?></span>
                    </div>
                    <div class="text">
                        <span class="title"><?=$item['title'];?></span>
                        <span class="sub">主演:<?=$item['actor'];?></span>
                        <span class="score"><?=$item['score'];?></span>
                    </div>
                </a>
            </li>
            <?php endforeach;?>
        </ul>
        <?=$page;?>
        <?php endif;?>
    </div>
</div>
{%end%}
