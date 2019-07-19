{%extend@common/base_portal%}
{%block@title%}
<title><?=($data['seo_title'] ? : $title);?>_<?=$site_name?></title>
<meta name="keywords" content="<?=$data['keywords']?>">
<meta name="description" content="<?=$data['excerpt']?>">
<meta name="mobile-agent" content="format=html5;url=<?=url('@goods@',['id'=>$data['id']],'http://'.$mobile_domain)?>">
<link rel="alternate" media="only screen and(max-width: 750px)" href="<?=url('@goods@',['id'=>$data['id']],'http://'.$mobile_domain)?>">
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
                        <?=date('Y-m-d H:i',$data['create_time'])?>
                    </span>
                    <span class="fly-list-nums">
                        <!--a href="#comment"><i class="iconfont" title="回答">&#xe60c;</i><?//=$data['comments_num']?></a-->
                        <i class="iconfont" title="人气">&#xe60b;</i> <?=$data['views']?>
                  </span>
                </div>
                <div class="detail-yang">
                    <table class="layui-table">
                        <colgroup>
                            <col width="100">
                            <col width="130">
                            <col>
                        </colgroup>
                        <tbody>
                        <tr>

                        </tr>
                        <tr>
                            <td>价格</td>
                            <td class="red"><?=$data['money']?> 元</td>
                            <?php $isMore=($data['more'] && is_array($data['more'])); $rowspan=$isMore?count($data['more'])+2 : 2; ?>
                            <td rowspan="<?=$rowspan?>">
                                <div class="layui-row yang-buy">
                                    <?php if($allow['download']):?>
                                        <div class="layui-col-xs12 layui-col-sm6" style="text-align: right;">
                                            <a class="layui-btn layui-btn-sm"  href="#detail-dowload">马上下载</a>
                                        </div>
                                    <?php else: ?>
                                    <div class="layui-col-xs12 layui-col-sm6" style="text-align: right;">
                                        <a class="layui-btn layui-btn-sm"  id="shop-cart" href="javascript:;">加购物车</a>
                                    </div>
                                    <div class="layui-col-xs12 layui-col-sm6">
                                        <a class="layui-btn layui-btn-danger layui-btn-sm" id="shop-buy" href="javascript:;">马上购买</a>
                                    </div>
                                    <?php endif;?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>已买次数</td>
                            <td><?=$data['downloads']?></td>
                        </tr>
                        <?php if($isMore) : foreach ($data['more'] as $item): ?>
                        <tr>
                            <td><?=$item['name']?></td>
                            <td><?=$item['value']?></td>
                        </tr>
                        <?php endforeach; endif;?>
                        </tbody>
                    </table>
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
                </div>
                <div class="detail-box" id="detail-dowload" style="line-height: 18px">
                    <h3 class="page-title layui-bg-green text-title">下载地址:</h3>
                    <?php if($allow['download']):?>
                        <?php if($data['files'] && is_array($data['files'])): ?>
                            <table class="layui-table">
                                <thead>
                                <tr>
                                    <td>名称</td>
                                    <td>种类</td>
                                    <td>地址</td>
                                    <td>备注</td>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($data['files'] as $v):?>
                                    <tr>
                                        <td><?=$v['name']?></td>
                                        <td><?=$v['type']?></td>
                                        <td><a href="<?=$v['url']?>" target="_bank" rel="nofollow" class="blue">打开</a></td>
                                        <td><?=$v['remark']?></td>
                                    </tr>
                                <?php endforeach;?>
                                </tbody>
                            </table>
                        <?php endif;?>
                    <?php else:?>
                        <div class="detail-dowload-tips">
                            <p class="node1">请点击上面“马上购买”按钮，进行购买！</p>
                            <?php if($isLogin):?>
                            <p class="green">需要购买后才能看到下载链接，谢谢！</p>
                            <?php else: ?>
                                <p>1、本商品支持24小时全自动发货，且可以不用在本站注册！</p>
                                <p>2、如果没有在本站注册，购买时需填上你的邮箱，系统会自动把下载地址发到你的邮箱中。</p>
                                <p>3、你可以到 <a href="<?=url('portal/shop/myorder')?>" class="red">查询订单</a> 中通过邮箱随时查询你购买过的商品。</p>
                                <p>4、购买后如果邮箱接收不到邮件，可以凭邮箱和支付截图找客服手动发给你！</p>
                            <?php endif;?>
                        </div>
                    <?php endif;?>
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
                    <legend>提问/评论</legend>
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
<?php
//获取购物车的url
$shopCartUrl=url($isLogin? 'portal/user/shopcart' : 'portal/shop/cart');
?>
<script type="text/javascript" charset="utf-8">
    var currentData={
        id:<?=$data['id']?>,
        table:"portal_post",
        shopCartUrl:"<?=url('portal/shop/cart')?>",
        shopCartJson:"<?=url('portal/shop/cart_json')?>",
        commentCtrlUrl:"<?=url('api/comment/ctrl')?>"
    };
    layui.config({version: "3.0.1", base: '/static/fly/mods/'}).extend({post: 'post'}).use('post');
    layui.use('util', function(){
       layui.util.fixbar({
           bgcolor: '#009688',
           bar1: '&#xe698;'
           ,click: function(type){
               if(type === 'bar1'){
                   //打开购物车
                   location.href = '<?=$shopCartUrl?>';
               }
           }
       });
   });
 </script>
<form id="form-buy" action="<?=url('portal/shop/order')?>" method="post" class="layui-form" style="display: none">
    <input type="hidden" name="oid" value="<?=$data['id']?>">
    <input type="hidden" name="token" value="<?=(\app\common\ctrl\Func::token())?>">
    <div class="layui-form-item">
        <label class="layui-form-label">购买数量</label>
        <div class="layui-input-block">
            <input id="form-buy-num" class="spinner-input" type="text" name="buy_num" required="'" lay-verify="required" autocomplete="off" value="1">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">接收邮箱</label>
        <div class="layui-input-block">
            <input id="form-buy-email" class="layui-input" type="text" name="email" value="" required="'" lay-verify="required|email" placeholder="ex@xxx.com" style="width: 180px;">
        </div>
        <div class="layui-field-box">
            <span class="red">说明：</span>购买后系统会自动把下载链接发到上面填写的邮箱，请注意核对，如果购买后邮箱接收不到邮件，可以联系我们的客服，客服会手动发给你！
        </div>
    </div>
</form>
{%end%}

