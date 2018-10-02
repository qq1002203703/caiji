{%extend@common/weixinqun%}
{%block@title%}
<title><?=$seo_title?>_<?=$site_name?></title>
<meta name="keywords" content="<?=$title?>">
<meta name="description" content="<?=$seo_description?>">
{%end%}

{%block@article%}
<div class="pure-u-3-4" id="article">
    <div class="wxq-other">
        <h2><?=$title?>下微信群展示：</h2>
        <ul>
            <?php if($data):?>
            <?php foreach ($data as $item):?><li><a class="img" title="<?=$item['title']?>" href="<?=url('@weixinqun@',['id'=>$item['id']])?>"><img alt="<?=$item['title']?>" src="<?=app('\app\weixinqun\model\Weixinqun')->getImg(['thumb'=>$item['thumb'],'qrcode'=>$item['qrcode'],'qun_qrcode'=>$item['qun_qrcode']],'thumb')?>"></a><span><a title="<?=$item['title']?>" href="<?=url('@weixinqun@',['id'=>$item['id']])?>"><?=$item['title']?></a></span></li><?php endforeach;?>
            <?php else:?>
            <li>此分类没有微信群</li>
            <?php endif;?>
        </ul>
        <?=$page?>
    </div>
</div><!--//article-->
{%end%}

{%block@javascript%}
{%end%}