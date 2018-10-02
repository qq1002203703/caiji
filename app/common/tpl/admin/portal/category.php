{%extend@common/base%}

{%block@main%}
<div class="path">
	<a href="<?=url('admin/index/index')?>">首页</a> > 门户管理 > 分类管理
</div>
<div class="content">
	<div class="content-card">
		<div class="content-detail">
			<div class="content-button">
				<a href="<?=url('admin/portal/add_category')?>" class="pure-button btn-custom btn-sm">添加分类</a>
			</div>
			<div class="content-item">
				<form class="pure-form">
				<table class="full pure-table pure-table-bordered">
					<thead>
						<tr>
							<td width="50">分类id</td>
							<td>分类名称</td>
							<td>分类描述</td>
							<td width="120" align="center">操作</td>
						</tr>
					</thead>
					<tbody>
						<?=$category?>
					</tbody>bl
				</table>
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
var url_edit="<?=url('admin/portal/edit_category')?>",
		url_delete="<?=url('admin/portal/delete_category')?>";
$(function(){
	doit(".edit",url_edit);
	doit(".delete",url_delete);
});

function doit(elme,url){
	$("table "+elme).click(function(){
		_this=$(this);		
		if(elme=='.edit'){
			_this.attr('href',url + '?id='+ _this.attr('data'));
			return true;
		}else if(elme=='.delete'){
			if( confirm("你真的要删除这个分类？")){
				var ajax=new Jajax();
				ajax.get(url + '?id='+ _this.attr('data'),'', function(data){
					_this.parents('tr').remove();
					return true;
				}, false, true,function(data){
					//console.log(data);
					alert(data.msg);
				});
			}
			return false;
		}
	});
}
</script>
{%end%}