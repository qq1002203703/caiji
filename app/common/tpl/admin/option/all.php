{%extend@common/base%}

{%block@main%}
<div class="path" xmlns="http://www.w3.org/1999/html">
	<a href="<?=url('admin/index/index')?>">首页</a> &gt; 网站设置  &gt; <?=$title?>
</div>
<div class="content">
	<div class="content-card">
		<div class="content-detail">
			<div class="content-button">
				<div class="content-top">
					<a href="<?=url('admin/option/add_option')?>" class="pure-button btn-custom btn-sm">添加变量</a><a href="<?=url('admin/option/update_cache')?>" class="pure-button btn-custom btn-sm update-cache">更新缓存</a><br><br>
				</div>
			</div>
			<div class="content-item">

				<form class="pure-form" action="" id="myform" method="post">
				<table class="full pure-table pure-table-horizontal">
					<thead>
						<tr>
							<td width="80">名称</td>
							<td width="110">说明</td>
							<td>值</td>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($data as $k =>$v): ?>
							<tr>
								<td><?=$v['name']?></td>
								<td><?=$v['description']?></td>
								<td><textarea class="pure-input-1" name="<?=$v['name']?>"><?=$v['value']?></textarea></td>

							</tr>
						<?php endforeach;?>
					</tbody>
						<tfoot>
						<tr>
							<td colspan="3" align="center"><input class="pure-button btn-custom pure-input-1-2" type="submit"></td>
						</tr>
						</tfoot>
				</table>
				</form>
			</div>
		</div>
	</div>
</div>
{%end%}

{%block@javascript%}
<script charset="UTF-8" type="text/javascript">
$(function(){
	var someClick=new clickClass();
	someClick.ajaxLink('.update-cache');
});
</script>
{%end%}