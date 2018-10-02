{%extend@common/base%}

{%block@main%}
<div class="path">
	<a href="<?=url('admin/index/index')?>">首页</a> > 门户管理 > <?=$title?>
</div>
<div class="content">
	<div class="content-card">
		<div class="content-detail">
			<div class="content-button">
				<div class="content-top">
					<a href="<?=url('admin/portal/add_post')?>" class="pure-button btn-custom">添加文章</a>
				</div>
			</div>

			<div class="content-item">				
				<form action="" methed="GET" class="pure-form search-form mb-8">
					<div class="pure-g">
						<div class="pure-u-1-2">
							<div class="dh-dropdown">
								<input type="checkbox" class="pure-checkbox js-check-all">
								<button class="pure-button btn-sm dh-dropdown-click" type="button">选中项<span>&#9660;</span></button>
								<ul class="dh-dropdown-content delete-dropdown-content">
									<li class="dh-dropdown-item delete-all"><a href="<?=url('admin/portal/delete_post')?>">删除</a></li>
								</ul>
							</div>
						</div>
						<div class="pure-u-1-2 search">
							<input type="text" name="keywords" placeholder="请输入关键词">
							<select name="category_id" class="pure-select">
								<option value="0">所有分类</option>
								<?=$categorys?>
							</select>
							<button class="pure-button" type="submit">筛选</button>
						</div>
					</div>
				</form>
				<form class="pure-form" action="" methed="GET" id="myform">
					<table class="full pure-table pure-table-bordered">
						<thead>
							<tr>
								<th width="18"></th>
								<th width="50">id</th>
								<th>标题</th>
								<th>所属分类</th>
								<th width="40">状态</th>
								<th width="120">创建时间</th>
								<th width="120">操作</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach($posts as $post):?>
							<tr>
								<td><input class="pure-checkbox" type="checkbox" value="<?=$post['id']?>" name="ids[]"></td>
								<td><?=$post['id']?></td>
								<td><a href="<?=url('admin/portal/edit_post','id='.$post['id'])?>"><?=$post['title']?><i class="iconfont icon-"></td>
								<td><?=$post['catename']?></td>
								<td><?=$post['status']?></td>
								<td><?=date('Y-m-d H:i',$post['create_time'])?></td>
								<td>
									<a class="pure-button btn-success btn-xs edit" href="<?=url('admin/portal/edit_post','id='.$post['id'])?>" target="_bank">编辑</a>
									<a class="pure-button btn-warning btn-xs delete" href="<?=url('admin/portal/delete_post','id='.$post['id'])?>">删除</a>
								</td>
							</tr>
						<?php endforeach;?>
						</tbody>
						<?php if($page): ?>
						<tfoot>
							<tr>
								<td colspan="6"><?=$page?></td>
							</tr>
						</tfoot>
						<?php endif; ?>
					</table>
				</form>
				<form id="form-bottom" class="pure-form search-form mt-8">
					<div class="dh-dropdown">
						<input type="checkbox" class="pure-checkbox js-check-all">
						<button class="pure-button btn-sm dh-dropdown-click" type="button">选中项<span>&#9660;</span></button>
						<ul class="dh-dropdown-content delete-dropdown-content">
							<li class="dh-dropdown-item delete-all"><a href="<?=url('admin/portal/delete_post')?>">删除</a></li>
						</ul>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
{%end%}

{%block@javascript%}
<script charset="UTF-8" type="text/javascript">
$(function(){
	ajaxLink(".delete");
	/*绑定点击事件到 .js-check-all*/
	$('.js-check-all').click(function(){
		if($(this).is(':checked')){
			$('#myform td >input[type="checkbox"]').prop('checked', true);
		}else{
			$('#myform td >input[type="checkbox"]').prop('checked', false);
		}
	});
	$(".delete-all").click(function(){
		var ids='';
		var checkbox=$('#myform td >input[type="checkbox"]:checked');
		if(checkbox.length>0){
			if(confirm('你真的要全部删除吗？')){
				checkbox.each(function(k){
					if(k==0)
						ids=$(this).val();
					else
						ids += ','+$(this).val();
				});
				var ajax=new Jajax();
				ajax.get($(this).children('a').attr('href'),{id:ids}, function(data){
					//_this.parents('tr').remove();
					location.reload();
				}, false, true,function(data){
					alert(data.msg);
				});
			}
		}else{
			alert('请先选中最少一项');
		}	
		return false;
	});
});

</script>
{%end%}