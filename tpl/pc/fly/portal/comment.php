{%extend@common/base_portal%}
{%block@title%}
<title><?=$title;?>_<?=$site_name?></title>
<meta name="keywords" content="<?=$title;?>">
<meta name="description" content="<?=$title;?>">
<meta name="mobile-agent" content="format=html5;url=<?=url('@comment@',['id'=>$data['id']],'http://'.$mobile_domain)?>">
<link rel="alternate" media="only screen and(max-width: 750px)" href="<?=url('@comment@',['id'=>$data['id']],'http://'.$mobile_domain)?>">
{%end%}
{%block@article%}
<div class="layui-container">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md8 content detail">
            <div class="fly-panel detail-box">
                <div class="layui-breadcrumb" lay-separator="&gt;">
                    <a href="/">首页</a>
                    <a href="<?=url('@'.$post['type'].'@',['id'=>$post['id']])?>"><?=$post['title']?></a>
                    <a href="#">详情</a>
                </div>
                <h1><?=$title?></h1>
                <div class="fly-detail-info">
                    <span class="fly-detail-column">
                        <?=date('Y-m-d H:i',$data['create_time']);?>&nbsp;&nbsp;作者:<a href="<?=url('@member@',['uid'=>$data['uid']])?>" ><?=$data['username']?></a>
                    </span>
                    <span class="fly-list-nums">
                        <i class="iconfont" title="回复">&#xe60c;</i> <?=$data['children']?>
                  </span>
                </div>
                <div class="detail-body photos">
                    <div class="home-jieda">
                        <p class="home-dacontent">
                            原文：<a href="<?=url('@'.$post['type'].'@',['id'=>$post['id']])?>" ><?=$post['title']?></a><br>
                            链接：<a href="<?=url('@'.$post['type'].'@',['id'=>$post['id']])?>" ><?=url('@'.$post['type'].'@',['id'=>$post['id']])?></a></p>
                        <?=$data['content'];?>
                        <?php if ($comments):?>
                            <div class="home-dacontent">
                                <h2>跟帖回复 : </h2>
                            <?php foreach ($comments as $comment):?>
                                <p>
                                    <a class="color3" href="<?=url('@member@',['uid'=>$comment['uid']])?>"><?=$comment['username']?>:</a>
                                    <?=strip_tags($comment['content'])?>
                                </p>
                            <?php endforeach;?>
                            </div>
                        <?php endif;?>
                    </div>

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

