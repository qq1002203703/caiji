{%extend@common/base_portal%}
{%block@title%}
<title><?=($category['seo_title']?:$category['name']);?>_小组_<?=$site_name?></title>
<meta name="keywords" content="<?=$category['seo_keywords']?>">
<meta name="description" content="<?=$category['seo_description']?>">
<meta name="mobile-agent" content="format=html5;url=<?=url('@group_list@',['slug'=>$category['slug']],'http://'.$mobile_domain)?>">
<link rel="alternate" media="only screen and(max-width: 750px)" href="<?=url('@group_list@',['slug'=>$category['slug']],'http://'.$mobile_domain)?>">
{%end%}
{%block@article%}
<div class="layui-container">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md8">
            <div class="fly-panel">
                <div class="layui-breadcrumb p20" lay-separator="&gt;">
                    <a href="/">首页</a>
                    <a href="/group/all">全部小组</a>
                    <?=$bread?>
                    <a href="#">列表</a>
                </div>
                <div class="yang-tag">
                    <div class="yang-box">
                        <div class="yang-left">
                            <?php if($category['thumb']):?>
                                <img id="topic-img" class="yang-img" src="<?=$category['thumb']?>" alt="<?=$category['name']?>" title="点击查看大图" data="<?=$category['thumb']?>">
                            <?php else:?>
                                <img class="yang-img" src="<?=$tuku;?>/uploads/images/no.gif" alt="没有图片">
                            <?php endif;?>
                        </div>
                        <div class="yang-right">
                            <h1 class="title"><?=$category['seo_title']?: $category['name'];?></h1>
                            <p class="yang-desc"><?=$category['seo_description']?></p>
                            <div class="yang-relation">
                                <p>相关小组</p>
                                <p>
                                    <?php if($randoms): foreach ($randoms as $item):?>
                                        <span><a href="<?=url('@group_list@',['slug'=>$item['slug']])?>"><?=$item['name']?></a></span>
                                    <?php endforeach;endif;?>
                                </p>
                            </div>
                        </div>
                        <div class="layui-clear"></div>
                    </div>
                    <div class="yang-content"><?=$category['content']?></div>
                    <?php if($data):?>
                        <ul class="fly-list">
                            <?php foreach ($data as $item):?>
                                <li>
                                    <a href="javascript:;" class="fly-avatar">
                                        <img src="<?=($item['avatar']? ($tuku.$item['avatar']) :$tuku.'/uploads/user/default.png')?>" alt="<?=$item['username']?>">
                                    </a>
                                    <div class="fly-list-title">
                                        <a href="<?=url('@group@',['id'=>$item['id']])?>"><?=$item['title']?></a>
                                    </div>
                                    <div class="fly-list-info">
                                        <a href="javascript:;"><cite><?=$item['username']?></cite></a>
                                        <span><?=date('Y-m-d H:i',$item['create_time'])?></span>
                                        <span class="fly-list-nums">
                                    <i class="iconfont icon-pinglun1" title="评论"></i> <?=$item['comments_num']?>
                                </span>
                                    </div>
                                </li>
                            <?php endforeach;?>
                        </ul>
                    <?php endif;?>
                </div>
                <?=$page?>
            </div>
        </div>
        <div class="layui-col-md4">
            <div class="fly-panel">
                <div class="fly-panel-title">相关推荐</div>
                <div class="fly-panel-main">
                    <div class="layui-row layui-col-space3">
                        <?php //echo getRelatedPostsByCategory($data['category_id'],'@group@',6,'<div class="layui-col-xs6"><a class="sidebar-img" href="{%url%}" title="{%title%}"><img src="{%thumb%}" alt="{%title%}"></a></div>');?>
                    </div>
                </div>
            </div>

            <div class="fly-panel">
                <div class="fly-panel-title">其他推荐</div>
                <div class="fly-panel-main">
                    <div class="layui-row layui-col-space3">
                        <?php //echo getRelatedPostsByCategory($data['category_id'],'@group@',6,'<div class="layui-col-xs6"><a class="sidebar-img" href="{%url%}" title="{%title%}"><img src="{%thumb%}" alt="{%title%}"></a></div>',false);?>
                    </div>
                </div>
            </div>
            <div class="fly-panel" id="sidebar-tag">
                <div class="fly-panel-title">最新话题</div>
                <div class="fly-panel-main">
                    <?php echo listNewestTags(20);?>
                </div>
            </div>
        </div>
    </div>
</div>

{%end%}
