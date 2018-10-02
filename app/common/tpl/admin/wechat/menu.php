{%extend@common/base%}

{%block@main%}
<div class="path">
	<a href="<?=url('admin/index/index')?>">首页</a> >  <a href="<?=url('admin/portal/post')?>">微信管理</a> > <?=$titl?>
</div>
<div class="content">
	<div class="content-card">
		<div class="content-detail">
			<div class="content-button">
				<div class="content-top">
					说明：所有修改都只是修改网站的数据，要想把数据同步到公众号，修改完必须点击<span class="orange"> 【同步到公众号】</span>按钮<br><br>
					本地：<a href="<?=url('admin/wechat/add_menu')?>" class="pure-button btn-custom btn-sm">添加菜单</a><a href="<?=url('admin/wechat/update_menu_cache')?>" class="pure-button btn-custom btn-sm update-cache">更新缓存</a><br><br>
					远程：<a href="<?=url('admin/wechat/upload_menu')?>" class="pure-button btn-warning btn-sm upload_menu">同步到公众号</a><br><br>
					<span class="red">进一步说明</span>：只有状态为使用中的菜单，才会同步到公众号。最多包括3个一级菜单，每个一级菜单最多包含5个二级菜单。公众号菜单创建后，已关注的用户5分钟后才能看到新菜单，要想马上看到效果，可以尝试取消关注公众账号后再次关注，则可以马上看到创建后的效果。
				</div>
			</div>
			<div class="content-item">
				<form class="pure-form search-form mb-8" method="get">
					<div class="dh-dropdown">
						<input type="checkbox" class="pure-checkbox js-check-all">
						<button class="pure-button btn-sm dh-dropdown-click" type="button">选中项<span>&#9660;</span></button>
						<ul class="dh-dropdown-content delete-dropdown-content">
							<li class="dh-dropdown-item delete-all"><a href="<?=url('admin/wechat/delete_menu')?>">删除</a></li>
						</ul>
					</div>
				</form>
				<form class="pure-form" action="" id="myform">
				<table class="full pure-table pure-table-bordered">
					<thead>
						<tr>
							<th width="18"></th>
							<td width="50">id</td>
							<td>名称</td>
							<td>种类</td>
							<td>text</td>
							<td width="50">状态</td>
							<td width="120" align="center">操作</td>
						</tr>
					</thead>
					<tbody>
						<?=$data?>
					</tbody>
					<?php if(isset($page) && $page): ?>
						<tfoot>
						<tr>
							<td colspan="7"><?=$page?></td>
						</tr>
						</tfoot>
					<?php endif; ?>
				</table>
				</form>
				<form class="pure-form search-form mt-8" method="get">
					<div class="dh-dropdown">
						<input type="checkbox" class="pure-checkbox js-check-all">
						<button class="pure-button btn-sm dh-dropdown-click" type="button">选中项<span>&#9660;</span></button>
						<ul class="dh-dropdown-content delete-dropdown-content">
							<li class="dh-dropdown-item delete-all"><a href="<?=url('admin/wechat/delete_menu')?>">删除</a></li>
						</ul>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
{%end%}

{%block@javascript%}
<style>
#jtab label{margin-top:16px !important;margin-bottom:8px !important;}
</style>
<script charset="UTF-8" type="text/javascript">
$(function(){
	changeStatus();
	var someClick=new clickClass();
	someClick.ajaxLink('.update-cache');
	someClick.ajaxLink('.upload_menu');
	someClick.checkedAll('.js-check-all','#myform td >input[type="checkbox"]');
	someClick.ajaxCheckbox('.delete-all','#myform td >input[type="checkbox"]:checked');
	someClick.ajaxLink('.pure-table .change',function () {
		location.reload();
	});
});
	function  changeStatus() {
		$('.status').each(function () {
			if($(this).html()=='1')
				$(this).html('使用中');
			else
				$(this).html('不使用');
		});
	}
</script>
{%end%}