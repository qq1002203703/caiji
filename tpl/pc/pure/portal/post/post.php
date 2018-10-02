{%extend@common/portal%}

{%block@title%}
  <title><?=$title?>_<?=$site_name?></title>
  <meta name="keywords" content="<?=$post['keywords']?>">
  <meta name="description" content="<?=$post['excerpt']?>">
{%end%}

{%block@article%}
			<div class="pure-box-left" id="article">
				<div class="article-title"><h1><?=$title?></h1></div>
				<div class="article-content">
					<?=$post['content'];?>
					<?php $files=json_decode($post['files']);if($files):?>
					<div class="download">
						<div class="download-title">
							<span>下载地址：</span>
						</div>
						<div class="pure-g download-content">
							<div class="pure-u-1-2">							
								<?php if($is_allow):?>
								<a class="pure-button btn-custom" href="javascript:;" title="点击显示下载地址" id="download-click">显示下载地址</a>
								<table class="pure-table pure-table-bordered" id="download-table" style="display:none">
									<thead>
									<tr>
										<td>名称</td>
										<td>种类</td>
										<td>地址</td>
										<td>备注</td>
									</tr>
									</thead>
									<?php foreach($files as $v):?>
									<tr>
										<td><?=$v->name?></td>
										<td><?=$v->type?></td>
										<td><a href="<?=$v->url?>" target="_bank">打开</a></td>
										<td><?=$v->remark?></td>
									</tr>
									<?php endforeach;?>
								</table>
								<?php else:?>
									<?php if($is_login):?>
									你没有权限下载
									<?php else:?>
									<a class="pure-button btn-custom" href="<?=url('portal/index/login')?>" target="_bank">请先登陆</a>
									<?php endif;?>
								<?php endif;?>				
							</div>
							<div class="pure-u-1-2">						
							</div>
						</div>
					</div><!--//download-->
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

