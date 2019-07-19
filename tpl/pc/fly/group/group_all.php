{%extend@common/base_portal%}
{%block@title%}
<title><?=($category['seo_title']?:$category['name']);?>_<?=$site_name?></title>
<meta name="keywords" content="<?=$category['seo_keywords']?>">
<meta name="description" content="<?=$category['seo_description']?>">
<meta name="mobile-agent" content="format=html5;url=<?=url('@goods_list@',['slug'=>$category['slug']],'http://'.$mobile_domain)?>">
<link rel="alternate" media="only screen and(max-width: 750px)" href="<?=url('@goods_list@',['slug'=>$category['slug']],'http://'.$mobile_domain)?>">
{%end%}
{%block@article%}
<div class="layui-container">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md8 content detail">
            <div class="fly-panel">
                <div class="layui-breadcrumb p20" lay-separator="&gt;">
                    <a href="/">首页</a>
                    <?=$bread?>
                    <a href="#">列表</a>
                </div>
                <?php if($data):?>
                    <ul class="list-movie pl20">
                        <h1 class="title"><?=$title?></h1>
                        <?php foreach ($data as $item):?>
                            <li class="item mr15" style="width: 170px;margin-bottom: 18px;">
                                <a class="link" href="<?=url('@goods_list@',['slug'=>$item['slug']])?>" title="<?=$item['name'];?>">
                                    <div class="layui-row">
                                        <div class="layui-col-md4">
                                            <div class="pic" style="height: 58px">
                                                <img <?=($item['thumb'] ?'src="'.$item['thumb'].'" alt="'.$item['name'].'"' : 'src="'.$tuku.'/uploads/images/no.gif" alt="没有缩略图"')?>>
                                            </div>
                                        </div>
                                        <div class="layui-col-md8">
                                            <div class="text">
                                                <span class="title"><?=$item['name'];?></span>
                                                <span class="sub">小组话题 : <span class="red"><?=$item['counts']?></span></span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        <?php endforeach;?>
                    </ul>
                <?php endif;?>

                <?=$page?>
            </div>
        </div>
        <div class="layui-col-md4">
            <div class="fly-panel">
                <div class="fly-panel-title">相关推荐</div>
                <div class="fly-panel-main">
                    <div class="layui-row layui-col-space3">
                        <?php //echo getRelatedPostsByCategory($data['category_id'],'@goods@',6,'<div class="layui-col-xs6"><a class="sidebar-img" href="{%url%}" title="{%title%}"><img src="{%thumb%}" alt="{%title%}"></a></div>');?>
                    </div>
                </div>
            </div>

            <div class="fly-panel">
                <div class="fly-panel-title">其他推荐</div>
                <div class="fly-panel-main">
                    <div class="layui-row layui-col-space3">
                        <?php //echo getRelatedPostsByCategory($data['category_id'],'@goods@',6,'<div class="layui-col-xs6"><a class="sidebar-img" href="{%url%}" title="{%title%}"><img src="{%thumb%}" alt="{%title%}"></a></div>',false);?>
                    </div>
                </div>
            </div>
            <div class="fly-panel" id="sidebar-tag">
                <div class="fly-panel-title">最新<a href="<?=url('@tag_all@')?>">话题</a></div>
                <div class="fly-panel-main">
                    <?php echo listNewestTags(20);?>
                </div>
            </div>
        </div>
    </div>
</div>

{%end%}

