{%extend@common/weixinqun%}

{%block@title%}
  <title><?=$site_title?></title>
  <meta name="keywords" content="<?=$site_keywords?>">
  <meta name="description" content="<?=$site_description?>">
{%end%}

{%block@article%}
<div class="pure-u-3-4" id="article">
    <h2>最新微信群</h2>
    <?php $model=app('\app\weixinqun\model\Weixinqun');?>
	<div class="wxq-other">
		<ul><?=$model->getRandomItem(60)?></ul>
	</div>
    <div class="gzh-index">
        <h2>微信群文章</h2>
        <?php $gzh=$model->getNewestGzh(10,'title,id,thumb,create_time,content');if($gzh):?>
        <ul>
        <?php foreach ($gzh as $item): ?>
            <li class="pure-u-g">
                <a class="img" href="<?=url('@gzh@',['id'=>$item['id']])?>" title="<?=$item['title']?>"><img src="/uploads/images/gzh/<?=$item['thumb']?>" alt="<?=$item['title']?>" width="200" height="100"></a>
                <span><a href="<?=url('@gzh@',['id'=>$item['id']])?>" title="<?=$item['title']?>"><?=$item['title']?></a></span>
                <span><?php echo \extend\Helper::text_cut($item['content'],150); ?></span>
                <?=date('Y-m-d H:i:s',$item['create_time'])?>
            </li>
        <?php endforeach;?>
        </ul>
        <?php endif;?>
    </div>
</div><!--//article-->
{%end%}

{%block@javascript%}
<script type="text/javascript" charset="UTF-8">
	$(function(){
	});
</script>
{%end%}

