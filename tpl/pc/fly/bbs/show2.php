{%extend@common/bbs%}
{%block@title%}
<title><?=$title?>_<?=$site_name?></title>
<meta name="keywords" content="<?=$data['keywords']?>">
<meta name="description" content="<?=$data['excerpt']?>">
{%end%}
{%block@article%}
<div class="layui-col-md8 content detail">
    <div class="fly-panel detail-box">
        <h1><?=$title?></h1>
        <div class="fly-detail-info">
            <!-- <span class="layui-badge">审核中</span> -->
            <span class="layui-badge layui-bg-green fly-detail-column"><a href="<?=url('@bbs_list@',['id'=>$data['category_slug']])?>" class="layui-badge"><?=$data['category_name']?></a></span>
            <span class="layui-badge layui-bg-green fly-detail-column"><?=$data['category_second']?></span>
            <?php if($data['is_top']): ?>
                <span class="layui-badge layui-bg-black">置顶</span>
            <?php endif;?>
            <?php if($data['recommended']): ?>
                <span class="layui-badge layui-bg-red">精帖</span>
            <?php endif;?>
            <?php if($isAdmin):?>
                <div class="fly-admin-box" data-id="123">
                    <span class="layui-btn layui-btn-xs jie-admin" type="del">删除</span>
                    <span class="layui-btn layui-btn-xs jie-admin" type="set" field="stick" rank="1">置顶</span>
                    <span class="layui-btn layui-btn-xs jie-admin" type="set" field="stick" rank="0" style="background-color:#ccc;">取消置顶</span>
                    <span class="layui-btn layui-btn-xs jie-admin" type="set" field="status" rank="1">加精</span>
                    <span class="layui-btn layui-btn-xs jie-admin" type="set" field="status" rank="0" style="background-color:#ccc;">取消加精</span>
                </div>
            <?php endif;?>
            <span class="fly-list-nums">
                <a href="#comment"><i class="iconfont" title="回答">&#xe60c;</i><?=$data['comments_num']?></a>
                <i class="iconfont" title="人气">&#xe60b;</i> <?=$data['views']?>
          </span>
        </div>
        <div class="detail-about">
            <a class="fly-avatar" href="#user/home.html">
                <img src="<?=($data['avatar']? :'/uploads/user/default.png')?>" alt="<?=$data['username']?>">
            </a>
            <div class="fly-detail-user">
                <a href="javascript:;" class="fly-link">
                    <cite><?=$data['username']?></cite>
                    <i class="iconfont icon-renzheng" title="认证信息"></i>
                    <i class="layui-badge fly-badge-vip">VIP0</i>
                </a>
                <span><?=date('Y-m-d H:i',$data['create_time'])?></span>
            </div>
            <div class="detail-hits" id="LAY_jieAdmin" data-id="123">
                <span style="padding-right: 10px; color: #FF7200">悬赏：<?=$data['coin']?>金币</span>
                <?php if($isAdmin || \core\Session::get('user.gid')==$data['uid']) : ?>
                    <span class="layui-btn layui-btn-xs jie-admin" type="edit"><a href="#">编辑</a></span>
                <?php endif;?>
            </div>
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
                <a class="layui-btn layui-btn-sm" href="javascript:;" onclick="renewDate()">更新时间</a>
                <a class="layui-btn layui-btn-sm" href="<?=url('bbs/post/add_multi')?>">批量发贴</a>
                <a id="ctrl-multi-reply" class="layui-btn layui-btn-sm" ac="<?=url('bbs/post/add_multi')?>" type="button" href="javascript:;">批量回贴</a>
            </div>
            <?php endif;?>
            <?php $cmModel=app('\app\admin\model\Comment');?>
            {%include@common/re_comment%}
        </div>
    </div>

    <div class="fly-panel detail-box" id="flyReply">
        <fieldset class="layui-elem-field layui-field-title" style="text-align: center;">
            <legend>回帖</legend>
        </fieldset>
        {%include@common/comment%}
    </div>
</div>
{%end%}

{%block@javascript%}
<script type="text/javascript" charset="utf-8">
    var currentData={
        id:<?=$data['id']?>,
        table:"bbs",
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

