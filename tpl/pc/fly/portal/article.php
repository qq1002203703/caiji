{%extend@common/base_portal%}
{%block@title%}
<title><?=($data['seo_title'] ? : $title);?>_<?=$site_name?></title>
<meta name="keywords" content="<?=$data['keywords']?>">
<meta name="description" content="<?=$data['excerpt']?>">
<meta name="mobile-agent" content="format=html5;url=<?=url('@article@',['id'=>$data['id']],'http://'.$mobile_domain)?>">
<link rel="alternate" media="only screen and(max-width: 750px)" href="<?=url('@article@',['id'=>$data['id']],'http://'.$mobile_domain)?>">
{%end%}
{%block@article%}
<div class="layui-container">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md8 content detail">
            <div class="fly-panel detail-box">
                <div class="layui-breadcrumb" lay-separator="&gt;">
                    <a href="/">首页</a>
                    <?=$bread?>
                    <a href="#">详情</a>
                </div>
                <h1><?=$title?></h1>
                <div class="fly-detail-info">
                    <span class="fly-detail-column">
                        <?=date('Y-m-d H:i',$data['create_time']);?>
                    </span>
                    <span class="fly-list-nums">
                        <!--a href="#comment"><i class="iconfont" title="回答">&#xe60c;</i><?//=$data['comments_num']?></a-->
                        <i class="iconfont" title="人气">&#xe60b;</i> <?=$data['views']?>
                  </span>
                </div>
                <div class="detail-body photos">
                   <?=$data['content'];?>
                    <?php if($tags): ?>
                    <p class="detail-tags">
                        标签 :
                        <?php foreach ($tags as $tag):?>
                        <a href="<?=url('@tag@',['slug'=>$tag['slug']])?>"><?=$tag['name'];?></a>
                        <?php endforeach;?>
                    </p>
                    <?php endif;?>
                    <?php if($isAdmin):?>
                    <div class="yang-admin">
                        <a class="layui-btn layui-btn-sm" href="<?=url('bbs/post/add_multi')?>">批量发贴</a>
                        <a id="ctrl-multi-reply" class="layui-btn layui-btn-sm" ac="<?=url('bbs/post/add_multi')?>" type="button" href="javascript:;">批量回贴</a>
                    </div>
                    <?php endif;?>
                </div>
            </div>
            <div class="fly-panel detail-box" id="flyReply">
                <fieldset class="layui-elem-field layui-field-title" style="text-align: center;">
                    <legend>最新评论</legend>
                </fieldset>
                {%include@common/comment%}
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
<script type="text/javascript" charset="utf-8">
    var currentData={
        id:<?=$data['id']?>,
        table:"portal_post",
        shopCartUrl:"<?=url('portal/shop/cart')?>",
        shopCartJson:"<?=url('portal/shop/cart_json')?>",
        commentCtrlUrl:"<?=url('api/comment/ctrl')?>"
    };
    layui.config({version: "3.0.1", base: '/static/fly/mods/'}).extend({post: 'post'}).use('post');

 </script>
<form id="form-buy" action="" class="layui-form" style="display: none">
    <input type="hidden" name="id" value="<?=$data['id']?>">
    <div class="layui-form-item">
        <label class="layui-form-label">购买数量</label>
        <div class="layui-input-block">
            <input id="form-buy-num" class="spinner-input" type="text" name="num" required="'" lay-verify="required" autocomplete="off" value="1">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">接收邮箱</label>
        <div class="layui-input-block">
            <input id="form-buy-email" class="layui-input" type="text" name="email" value="" required="'" lay-verify="required|email" placeholder="ex@xxx.com" style="width: 180px;">
        </div>
        <div class="layui-field-box">
            <span class="red">说明：</span>购买后系统会自动把商品发到上面填写的邮箱，请注意核对，如果购买后邮箱接收不到邮件，可以联系我们的客服，客服会手动发给你！
        </div>
    </div>
</form>
{%end%}

