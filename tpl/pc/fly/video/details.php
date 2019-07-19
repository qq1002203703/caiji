{%extend@common/base_portal%}
{%block@title%}
<title><?=($data['seo_title'] ? : $title);?>在线观看_<?=$site_name?></title>
<meta name="keywords" content="<?=$data['keywords']?>">
<meta name="description" content="<?=$data['excerpt']?>">
<meta name="mobile-agent" content="format=html5;url=<?=url('@video@',['id'=>$data['id']],'http://'.$mobile_domain)?>">
<link rel="alternate" media="only screen and(max-width: 750px)" href="<?=url('@video@',['id'=>$data['id']],'http://'.$mobile_domain)?>">
{%end%}
{%block@article%}
<div class="layui-container">
    <div class="layui-row layui-col-space15">
        <!--左边主要区域-->
        <div class="layui-col-md8 content detail">
            <div class="fly-panel detail-box">
                <div class="layui-breadcrumb" lay-separator="&gt;">
                    <a href="/">首页</a>
                    <?//=$bread?>
                    <a href="#">详情</a>
                </div>

                <div class="fly-detail-info"></div>
                <div class="detail-yang">
                    <div class="detail-thumb">
                        <div class="layui-row layui-col-space5">
                            <div class="layui-col-xs3">
                                <div class="detail-thumb-left">
                                    <div class="thumb-img">
                                        <img src="<?=$data['thumb']?>" alt="<?=$data['title']?>">
                                    </div>
                                    <div class="thumb-info">
                                        <i class="iconfont" title="人气">&#xe60b;</i> <?=$data['views']?>
                                        <i class="layui-icon" title="喜欢">&#xe601;</i> <?=$data['likes']?>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-col-xs9">
                                <div class="detail-thumb-right">
                                    <h1 style="margin-left:-50px;"><?=$title?></h1>
                                    <a class="zhubo-like">
                                        <!--i class="iconfont icon-zan"></i-->
                                        <span>豆瓣评分 <i class="like-num"><?=$data['score']?></i></span>
                                    </a>
                                    <div class="zhubo-info">
                                        <div class="item"><span>别名：<em><?=$data['other_name']?></em></span><span>地区：<em><?=$data['area']?></em></span></div>
                                        <div class="item"><span>导演：<em><?=$data['director']?></em></span><span>上映日期：<em><?=date('Y-m-d',$data['date_published'])?></em></span></div>
                                        <div  class="item"><span>演员：<em><?=$data['actor']?></em></span></div>
                                        <div class="item">
                                            <span>类型 :
                                                <?php foreach ($tags as $tag):?>
                                                    <em><a href="<?=url('@tag@',['slug'=>$tag['slug']])?>"><?=$tag['name'];?></a></em>
                                                <?php endforeach;?>
                                            </span>
                                        </div>
                                        <div class="item"><span>简介：</span>
                                            <div id="jianjie"><?=$data['content']?></div>
                                        </div>
                                    </div>
                                    <div class="zhubo-buy">
                                        <a class="layui-btn layui-btn-sm"  href="#video-online">开始观看</a>
                                        <a class="layui-btn layui-btn-sm layui-btn-primary" href="#video-download">影视下载</a>
                                        <a class="layui-btn layui-btn-sm layui-btn-primary" href="#flyReply">发表评论</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="detail-body photos">
                    <ul class="detail-video" id="video-online">
                        <?php $typeArr=[
                                'm3u8'=>'m3u8在线观看',
                            'xunlei'=>'迅雷下载',
                            'baidupan'=>'百度盘下载',
                            'xfplay'=>'先锋影音在线观看',
                            'xigua'=>'西瓜影音在线观看',
                        ]; foreach ($source as $item):?>
                            <li><h3><?=$item['name'];?>:<?=$typeArr[$item['type']];?></h3>
                                <?php $urlList=explode("\n",$item['url']);foreach ($urlList as $key => $value):$value=explode('$',$value);?>
                                    <a href="<?=url('@video_play@',['vid'=>$data['id'],'id'=>$item['id'],'pid'=>$key])?>"><?=$value[0];?></a>
                                <?php endforeach;?>
                            </li>
                        <?endforeach;?>
                    </ul>
                </div>
                <?php if($isAdmin):?>
                    <div class="yang-admin">
                        <a class="layui-btn layui-btn-sm" href="<?=url('bbs/post/add_multi')?>">批量发贴</a>
                        <a id="ctrl-multi-reply" class="layui-btn layui-btn-sm" ac="<?=url('bbs/post/add_multi')?>" type="button" href="javascript:;">批量回贴</a>
                    </div>
                <?php endif;?>
            </div>
            <div class="fly-panel detail-box" id="flyReply">
                <fieldset class="layui-elem-field layui-field-title" style="text-align: center;">
                    <legend>发表评论</legend>
                </fieldset>
                {%include@common/comment%}
            </div>
        </div>
        <!--sidebar-->
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
<script type="text/javascript" charset="utf-8">
    var currentData={
        id:<?=$data['id']?>,
        table:"video",
        shopCartUrl:"<?=url('portal/shop/cart')?>",
        shopCartJson:"<?=url('portal/shop/cart_json')?>",
        commentCtrlUrl:"<?=url('api/comment/ctrl')?>"
    };
    layui.config({version: "3.0.1", base: '/static/fly/mods/'}).extend({post: 'post'}).use('post');
    layui.use(['util','shorten'], function(){
        var $ = layui.jquery;
        //显示更多
        $('#jianjie').shorten({
            showChars: 80,
            moreText: ' 展开<i class="layui-icon layui-icon-down"></i>',
            lessText: ' 收起<i class="layui-icon layui-icon-up"></i>'
        });
    });
</script>
{%end%}

