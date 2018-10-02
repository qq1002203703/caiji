{%extend@common/portal%}

{%block@title%}
  <title><?=$title?>_<?=$site_name?></title>
  <meta name="keywords" content="<?=$post['keywords']?>">
  <meta name="description" content="<?=$post['excerpt']?>">
{%end%}

{%block@article%}
			
			<div class="pure-box-left" id="article">
			<div id="path"><a href="/">首页</a>&gt;<?=$path?></div>
				<div class="article-content">
					<?php if($posts):?>
					<?php foreach($posts as $post):?>
					<div class="clist">
						<div class="clist-title"><a href="<?=url('@post@','id='.$post['id'])?>"><?=$post['title']?></a></div>
						<div class="clist-text"><?=\extend\Helper::text_cut($post['content'],120)?></div>
					</div>
					<?php endforeach;?>
					<?php else:?>
					<div class="clist">此分类没有文章</div>
					<?php endif;?>
				</div>
			</div><!--//article-->
{%end%}

{%block@javascript%}
<script type="text/javascript" charset="UTF-8">
	$(function(){
		$("#download-click").click(function(){
			$(this).hide();
			$("#download-table").show();
			var ajax=new Jajax();
			ajax.get("<?=url('portal/post/downloads_click')?>?id=<?=$post['id']?>",'','',false,true);
		});
	});
</script>
{%end%}

