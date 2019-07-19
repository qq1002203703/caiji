{%extend@common/main%}
{%block@title%}
<title><?=$title?>|<?=$data['city']?><?=$data['category']?>微信群_<?=$site_name?></title>
<meta name="keywords" content="<?=($data['keywords']?:$title.'微信群');?>">
<meta name="description" content="<?=$title?>，本群是<?=$data['city']?><?=$data['category']?>微信群，有兴趣的朋友可以找群主入群！<?=$data['excerpt']?>">
<link rel="canonical" href="<?=url('@weixinqun@',['id'=>$data['id']],$site_url)?>">
{%end%}
{%block@article%}
<div class="yang-bread f30 pl1"><a href="/">首页</a>&gt<a href="<?=url('@weixinqun_list@',['id'=>$data['category_id']])?>"><?=$data['category']?></a> &gt 详情</div>
<h1 class="t-c f40 color2 mt4 mb2 pl3 pr3"><?=$title?></h1>
<div class="yang-desc pl3 pr3 color4 f28">
    <?=date('Y-m-d',$data['create_time'])?> <i class="icon iconfont icon-refresh" title="人气"></i> <?=$data['views']?>
</div>
<div class="yang-content mb6 mt1 normal color3 pl3 pr3">
    {%include@tmp/ad_article%}
    <div class="yang-wxq-details">
        <div class="wxq-qrcode">
            <?php if ($data['qrcode'] && $data['qun_qrcode']):?>
                <div class="wxq-img-click"><a id="qunzhu" class="pure-button btn-sm btn-error doing-click" href="javascript:;">群主二维码</a><a  id="qun" class="pure-button btn-sm doing-click" href="javascript:;">群二维码</a></div>
                <img alt="<?=$data['title']?>群主二维码" src="<?=$data['qrcode']?>" class="qunzhu-qrcode">
                <img alt="<?=$data['title']?> 二维码" src="<?=$data['qun_qrcode']?>" class="qun-qrcode">
            <?php elseif ($data['qrcode'] && !$data['qun_qrcode']):?>
                <div class="wxq-img-click"><a  class="pure-button btn-sm btn-error" href="javascript:;">群主二维码</a></div>
                <img alt="<?=$data['title']?>群主二维码" src="<?=$data['qrcode']?>" class="qunzhu-qrcode">
            <?php elseif (!$data['qrcode'] && $data['qun_qrcode']):?>
                <div class="wxq-img-click"><a  class="pure-button btn-sm btn-error" href="javascript:;">群二维码</a></div>
                <img alt="<?=$data['title']?>群二维码" src="<?=$data['qun_qrcode']?>" class="qunzhu-qrcode">
            <?php else: ?>
                <img alt="没有二维码" src="/uploads/images/no.gif" class="qunzhu-qrcode">
            <?php endif;?>
        </div>
        <p>本<a href="<?=$site_url;?>">微信群</a>简介：</p>
        <p><?=$data['excerpt']?><br>本群免费对外开外，有兴趣的朋友可以找群主入群！</p>
        <p>群主微信号：<span class="color-primary"><?=$data['weixinhao']?></span></p>
        <p>所属地区：<a href="<?=url('@weixinqun_city@',['id'=>$data['city_id']])?>"> <?=$data['city'];?></a></p>
    <?php if($data['tags']): ?>
        <p class="f28 color4">
            群标签 :
            <?php foreach ($data['tags'] as $tag):?>
                <a href="<?=url('@tag@',['slug'=>$tag['slug']])?>"><?=$tag['name'];?></a>
            <?php endforeach;?>
        </p>
    <?php endif;?>
        <div class="pre-next"><?=$pre_next?></div>
        <hr>
        <?=$data['content']?>
    </div>
</div>
<h3 class="f36 pr3 ml3 color3 pl1 yang-title-border">相关微信群</h3>
<ul class="yang-list">
    <?=app('\app\weixinqun\model\Weixinqun')->getRandomItem(7,'<li><a class="w75 grid mb2 mt2 clearfix pr2 pl2" href="{%url%}"><div class="box col-3"><img src="{%qun_qrcode%}"></div><div class="box col-9"><h3 class="f34">{%title%}</h3><p class="f30 color4">{%content%}…</p></div></a></li>');?>
    <li><a class="w75 grid mb2 mt2 clearfix pr2 pl2" href="/weixinqun/xuexiao/zuixin"><div class="box col-3"><img src="/uploads/images/no.gif"></div><div class="box col-9"><h3 class="f34">最新学校</h3><p class="f30 color4">…</p></div></a></li>
</ul>
{%end%}