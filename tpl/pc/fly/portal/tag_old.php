{%extend@common/base_portal%}
{%block@title%}
<title><?=$tag['seo_title']?: $tag['name'];?>_关于<?=$tag['name'];?>的讨论_<?=$site_name?></title>
<meta name="keywords" content="<?=$tag['seo_keywords']?:$tag['name']?>">
<meta name="description" content="<?=$tag['seo_description']?>">
{%end%}
{%block@article%}
<div class="layui-container">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md8">
            <div class="fly-panel" style="margin-bottom: 0;">
                <div class="yang-tag">
                    <h1 class="yang-title"><?=$tag['seo_title']?: $tag['name'];?></h1>
                    <div class="yang-box">
                        <div class="yang-left">
                            <?php if($tag['thumb']):?>
                            <img id="topic-img" class="yang-img" src="<?=$tag['thumb']?>_200x200.jpg" alt="<?=$tag['name']?>" title="点击查看大图" data="<?=$tag['thumb']?>">
                            <?php else:?>
                                <img class="yang-img" src="/uploads/images/no.gif" alt="没有图片">
                            <?php endif;?>
                        </div>
                        <div class="yang-right">
                            <p class="yang-desc"><?=$tag['seo_description']?></p>
                            <div class="yang-relation">
                                <p>相关话题</p>
                                <p>
                                    <?php if($randomTags): foreach ($randomTags as $randomTag):?>
                                    <span><a href="<?=url('@tag@',['slug'=>$randomTag['slug']])?>"><?=$randomTag['name']?></a></span>
                                    <?php endforeach;endif;?>
                                </p>
                            </div>
                        </div>
                        <div class="layui-clear"></div>
                    </div>
                    <div class="yang-content"><?=$tag['content']?></div>
                    <?php if($data2) $isPortal=true; elseif ($data1) $isPortal=false;else $isPortal=true; ?>
                    <div class="layui-tab">
                        <ul class="layui-tab-title">
                            <li<?=$isPortal?' class="layui-this"':''?>>问答与讨论</li>
                            <li<?=!$isPortal?' class="layui-this"':''?>>文章与商品</li>
                        </ul>
                        <div class="layui-tab-content">
                            <div class="layui-tab-item<?=$isPortal?' layui-show':''?>">
                                <?php if($data2):$bbsRouter=[1=>'bbs_post',2=>'bbs_show'];?>
                                    <ul class="yang-list">
                                        <h2>【<?=$tag['name']?>】 相关话题的讨论</h2>
                                        <?php foreach ($data2 as $item):?>
                                            <li>
                                                <div class="yang-list-left">
                                                    <div class="yang-list-badge">
                                                        <span class="yang-list-count"><?=$item['views']?></span>
                                                        <span class="yang-list-liulan">浏览</span>
                                                    </div>
                                                </div>
                                                <div class="yang-list-right">
                                                    <h3><a href="<?=url('@'.$bbsRouter[$item['type']].'@',['id'=>$item['id']])?>"><?=$item['title']?></a></h3>
                                                    <div class="content"><?=(\extend\Helper::text_cut($item['content'],130));?></div>
                                                    <span class="fly-list-nums"><i class="iconfont icon-pinglun1" title="回答"></i><?=$item['comments_num']?></span>
                                                    <span class="date"><?=date('Y-m-d H:i',$item['create_time'])?> , 有 <span class="red"><?=$item['comments_num']?></span> 条评论</span>
                                                </div>
                                            </li>
                                        <?php endforeach;?>
                                    </ul>
                                <?php endif;?>
                            </div>
                            <div class="layui-tab-item<?=!$isPortal?' layui-show':''?>">
                                <?php if($data1):?>
                                    <ul class="yang-list">
                                        <h2>【<?=$tag['name']?>】 相关话题的文章</h2>
                                        <?php foreach ($data1 as $item):?>
                                            <li>
                                                <div class="yang-list-left">
                                                    <div class="yang-list-badge">
                                                        <span class="yang-list-count"><?=$item['views']?></span>
                                                        <span class="yang-list-liulan">浏览</span>
                                                    </div>
                                                </div>
                                                <div class="yang-list-right">
                                                    <h3><a href="<?=url('@'.$item['type'].'@',['id'=>$item['id']])?>"><?=$item['title']?></a></h3>
                                                    <div class="content"><?=(\extend\Helper::text_cut($item['content'],130));?></div>
                                                    <span class="fly-list-nums"><i class="iconfont icon-pinglun1" title="回答"></i><?=$item['comments_num']?></span>
                                                    <span class="date"><?=date('Y-m-d H:i',$item['create_time'])?> , 有 <span class="red"><?=$item['comments_num']?></span> 条评论</span>
                                                </div>
                                            </li>
                                        <?php endforeach;?>
                                    </ul>
                                <?php endif;?>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- <div class="fly-none">没有相关数据</div> -->
                <div style="text-align: center">
                    <?php //dump($page);?>
                </div>
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
{%block@javascript%}
<script type="text/javascript">
    layui.use(['jquery','layer'], function(){
        var layer = layui.layer;
        var $=layui.$;
        layer.ready(function () {
            $("#topic-img").click(function () {
                _this=$(this);
                layer.open({
                    type:1,
                    content:'<img src="'+$(this).attr('data')+'">',
                    offset: ['100px', '100px']
                });
            });
        });
    });
</script>
{%end%}

