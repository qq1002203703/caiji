{%extend@common/main%}
{%block@title%}
<title><?=($data['seo_title'] ? : $title);?>_<?=$site_name?></title>
<meta name="keywords" content="<?=$title?>">
<meta name="description" content="<?=$title?>微信群，分享和寻找<?=$title?>同学群，同校交友、聊天群，发现同校的人和老师。">
{%end%}
{%block@article%}
<div class="yang-bread f30 pl1"><a href="/">首页</a>&gt<a href="<?=url('@xuexiao_city@',['id'=>$data['city_id']])?>"><?=$data['city']['name'];?></a> &gt 详情</div>
<h1 class="t-c f40 color2 mt4 mb2 pl3 pr3"><?=$data['title']?></h1>
<div class="yang-desc pl3 pr3 color4 f28">
    <p>
        学校电话： <?=$data['phone'];?><br>
        学校地址： <?=$data['address'];?>
    </p>
</div>
<div class="yang-content mb6 mt1 normal color3 pl3 pr3">
    {%include@tmp/ad_article%}
    <div class="xuexiao-info p2 radius5">
        <p>欢迎<strong><?=$data['title']?></strong>的老师和同学来到本学校的<a href="/">微信群</a>分享页，在这里，只要你就读于或曾读于、就职于或曾就职于【<?=$data['title']?>】，就可以把与本学校相关的微信群分享出来！</p>
        <p>马上开始：<button class="layui-btn layui-btn-sm" href="javascript:;">分享微信群</button></p>
        <?php if(isset($data['thumb_ids'][0])):?>
        <p><img src="<?=$data['thumb_ids'][0]['uri']?>" alt="<?=$data['title']?>微信群"></p>
        <?php endif;?>
        <p>微信群内容没有太多约束，可以是关于学习的、交友的、聊天的、吐槽的等等，可以是同学圈、老师圈、家长圈等等，只要你想把你的微信群让更多的本校同学或老师知道，就可以发享出来。</p>
        <p>iweixinqun.cn网会免费帮你宣传，让更多的人找到你的微信群。你坐等越来越多的<?=$data['title']?>同校师生加你的群吧！</p>
        <p>马上开始：<button class="layui-btn layui-btn-sm" href="javascript:;">添加微信群</button></p>
    </div>
    <?php if(isset($data['thumb_ids'][1])):?>
        <p><img src="<?=$data['thumb_ids'][1]['uri']?>" alt="<?=$data['title']?>简介"></p>
    <?php endif;?>
    <h2>学校简介：</h2>
    <?=$data['content'];?>
    <?php if(isset($data['thumb_ids'][2])):?>
        <p><img src="<?=$data['thumb_ids'][2]['uri']?>" alt="<?=$data['title']?>讨论"></p>
    <?php endif;?>
    <h3 class="yang-title-border pl1 f32 color3 mb2 ml1">最新评论</h3>
    {%include@common/comment%}
    <?php if(isset($data['thumb_ids'][3])):?>
        <p><img src="<?=$data['thumb_ids'][3]['uri']?>" alt="<?=$data['title']?>相关风采"></p>
    <?php endif;?>
    <?php if(isset($data['thumb_ids'][4])):?>
        <p><img src="<?=$data['thumb_ids'][4]['uri']?>" alt="<?=$data['title']?>相关学校"></p>
    <?php endif;?>
</div>
<h3 class="f36 pr3 ml3 color3 pl1 yang-title-border">相关学校</h3>
<ul class="yang-list">
    <?=app('\app\weixinqun\model\Xuexiao')->getRelatedItem($data['city_id'],7,$data['id'],'<li><a class="w75 grid mb2 mt2 clearfix pr2 pl2" href="{%url%}"><div class="box col-3"><img src="{%thumb%}" alt="{%title%}"></div><div class="box col-9"><h3 class="f34">{%title%}</h3><p class="f30 color4">{%content%}…</p></div></a></li>');?>
</ul>
{%end%}