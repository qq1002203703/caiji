{%extend@common/base_portal%}
{%block@title%}
<title><?=$tag['seo_title']?: $tag['name'];?>_关于<?=$tag['name'];?>的讨论_<?=$site_name?></title>
<meta name="keywords" content="<?=$tag['seo_keywords']?:$tag['name']?>">
<meta name="description" content="<?=($tag['seo_description']?:'关于'.$tag['name'].'的讨论');?>">
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
                            <img id="topic-img" class="yang-img" src="<?=$tag['thumb']?>" alt="<?=$tag['name']?>" title="点击查看大图" data="<?=$tag['thumb']?>">
                            <?php else:?>
                                <img class="yang-img" src="<?=$tuku?>/uploads/images/no.gif" alt="没有图片">
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

                    <div class="layui-tab">

                        <div class="layui-tab-content">
                            <div class="layui-tab-item layui-show">
                                <?php if($images):?>
                                    <h2>【<?=$tag['name']?>】话题下图集</h2>
                                    <div class="layui-carousel" id="test1">
                                        <div carousel-item>
                                            <?php foreach ($images as $image): ?>
                                            <div><a href="javascript:;" data-url="<?=$image['url']?>"><img src="<?=$image['src']?>" alt="<?=$image['alt']?>"></a></div>
                                            <?php endforeach;?>
                                        </div>
                                    </div>
                                <?php endif;?>
                                <?php if($data):?>
                                    <ul class="yang-list">
                                        <h2>【<?=$tag['name']?>】话题下内容列表</h2>
                                        <?php foreach ($data as $item): ?>
                                            <li>
                                                <div class="yang-list-left">
                                                    <div class="yang-list-badge">
                                                        <span class="yang-list-count"><?=$item['views']?></span>
                                                        <span class="yang-list-liulan">浏览</span>
                                                    </div>
                                                </div>
                                                <div class="yang-list-right">
                                                    <?php if($item['type']===1): ?>
                                                        <h3>[<?=$item['pindao'];?>] <a class="ajax-click" href="javascript:;"><?=$item['title']?></a></h3>
                                                        <div class="content"><?=$item['content'];?></div>
                                                        <span class="fly-list-nums"><i class="iconfont icon-pinglun1" title="回答"></i><?=$item['comments_num']?></span>
                                                        <span class="date"><?=date('Y-m-d H:i',$item['create_time'])?> , <a href="javascript:;" class="ajax-click">有 <cite class="red"><?=$item['comments_num']?></cite> 条评论</a></span>
                                                        <span class="ajax-count" data-id="<?=$item['id'];?>" data-first="1" data-show="0" data-url="<?=$item['url'];?>"></span>
                                                        <div class="comment-list"></div>
                                                    <?php else:?>
                                                        <h3>[<?=$item['pindao'];?>] <a href="<?=$item['url']?>"><?=$item['title']?></a></h3>
                                                        <div class="content"><?=$item['content'];?></div>
                                                        <span class="fly-list-nums"><i class="iconfont icon-pinglun1" title="回答"></i><?=$item['comments_num']?></span>
                                                        <span class="date"><?=date('Y-m-d H:i',$item['create_time'])?> , 有 <span class="red"><?=$item['comments_num']?></span> 条评论</span>
                                                    <?php endif;?>
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
    layui.use(['jquery','layer','fly','carousel'], function(){
        var layer = layui.layer;
        var $=layui.$;
        var fly=layui.fly;
        layer.ready(function () {
            $("#topic-img").click(function () {
                _this=$(this);
                layer.open({
                    type:1,
                    content:'<img src="'+$(this).attr('data')+'">',
                    offset: ['100px', '100px']
                });
            });
            $(".ajax-click").on("click",function () {
               var _this=$(this);
               var ajaxData=_this.parent().siblings('.ajax-count');
               var id=ajaxData.attr('data-id');
               if(ajaxData.attr('data-first')==='1'){
                   var action="<?=url('api/comment/tag')?>?id="+id+"&table=bbs&size=10";
                   fly.json(action,{},function (res) {
                       _this.parent().siblings('.content').html(res.content);
                       _this.parent().siblings('.comment-list').html(res.data+'<div class="comment-list-switch"><a href="'+ajaxData.attr("data-url")+'">看更多回复</a><span onclick="toHide(this);">收起评论</span></div>');
                       ajaxData.attr('data-first','0');
                   },{type:'get'});
               }else {
                   _this.parent().siblings('.comment-list').toggle();
               }
               return false;
            });
        });
        window.toHide=function(ele){
            $(ele).parent().parent().hide();
        };
        //建造实例
        layui.carousel.render({
            elem: '#test1'
            ,width: '100%' //设置容器宽度
            ,arrow: 'always' //始终显示箭头
            //,anim: 'updown' //切换动画方式
        });
        //
        $("#test1").on("click","a",function () {
            this.href=$(this).attr("data-url");
        });
    });
</script>
{%end%}

