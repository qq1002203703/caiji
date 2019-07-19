{%extend@common/base_portal%}
{%block@title%}
<title><?=($category['seo_title']?:$category['name']);?>_<?=$site_name?></title>
<meta name="keywords" content="<?=$category['seo_keywords']?>">
<meta name="description" content="<?=$category['seo_description']?>">
<meta name="mobile-agent" content="format=html5;url=<?=url('@article_list@',['slug'=>$category['slug']],'http://'.$mobile_domain)?>">
<link rel="alternate" media="only screen and(max-width: 750px)" href="<?=url('@article_list@',['slug'=>$category['slug']],'http://'.$mobile_domain)?>">
{%end%}
{%block@article%}
<div class="layui-container">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md8">
            <div class="fly-panel">
                <div class="layui-breadcrumb" lay-separator="&gt;">
                    <a href="/">首页</a>
                    <?=$bread?>
                    <a href="#">详情</a>
                </div>
                <ul class="list-block">
                    <h2 class="list-block-title"><?=$title?></h2>
                    <?php if($data): foreach ($data as $article):?>
                        <li class="list-block-item">
                            <a class="list-block-pic" href="<?=url('@article@',['id'=>$article['id']])?>"><img <?=($article['thumb'] ?'src="'.$article['thumb'].'_200x200.jpg" alt="'.$article['title'].'"' : 'src="/uploads/images/no.gif" alt="没有缩略图"')?> class="item-img"></a>
                            <div class="list-block-detail">
                                <h3 class="item-title"><a href="<?=url('@article@',['id'=>$article['id']])?>" ><?=$article['title']?></a></h3>
                                <p class="item-desc layui-hide-xs"><?=($article['excerpt'] ? :\extend\Helper::text_cut($article['content'],200))?></p>
                                <p class="item-about"><a class="item-user" href="<?=url('@article_list@',['slug'=>$article['category_slug']])?>"><?=$article['category_name']?></a><span class="item-date"><?=date('Y-m-d H:i',$article['create_time'])?></span></p>
                            </div>
                        </li>
                    <?php endforeach;endif;?>
                </ul>
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

