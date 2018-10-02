{%extend@common/base%}

{%block@main%}
<div class="path">
	<a href="<?=url('admin/index/index')?>">首页</a> > 门户管理 > <a href="<?=url('admin/portal/post')?>">文章管理</a> > <?=$title?>
</div>
<div class="content">
	<div class="content-card">
		<div class="content-detail">
			<div class="content-item" id="jtab_inline">
				
					<div class="tab-hander">
						<a href="javascript:;" class="tab-hander-item tab-select" data="tab-a">基本</a>
						<a href="javascript:;" class="tab-hander-item" data="tab-b">附件</a>
						<a href="javascript:;" class="tab-hander-item" data="tab-c">其他</a>
					</div>
					<form class="pure-form pure-form-stacked mb-8" action="" method="post" id="myform">
						 <fieldset>
							<div class="tab-item" id="tab-a">
								<table class="full pure-table pure-table-bordered">
									<tr>
										<td width="70"><label for="title">分类 <span class="red">*</span></label></td>
										<td width="66%">
											<input id="catename" class="pure-input-1" type="text" name="catename" placeholder="选择分类" required>
											<input id="cateid" type="hidden" name="cateid">
										</td>
										<td class="thumb-container" rowspan="5" align="center" valign="bottom" style="max-width:250px">
											<div class="thumb-img"></div>
											<input id="thumb" name="thumb" class="pure-input-1" type="text" placeholder="缩略图url">
										</td>
									</tr>
									<tr>
										<td><label for="title">标题 <span class="red">*</span></label></td>
										<td><input id="title" class="pure-input-1" type="text" name="title" placeholder="标题" required></td>
									</tr>
									<tr>
										<td><label for="keywords">关键词</label></td>
										<td>
											<input id="keywords" class="pure-input-1" type="text" name="keywords" placeholder="关键词">
											<span class="green">多个关键词用英文逗号分隔</span>
										</td>
									</tr>
									<tr>
										<td><label for="excerpt">摘要</label></td>
										<td>
											<textarea id="excerpt" class="pure-input-1" name="excerpt" placeholder="摘要"></textarea>
										</td>
									</tr>
									<tr>
										<td><label for="source">文章来源</label></td>
										<td>
											<input id="source" class="pure-input-1" type="text" name="source" placeholder="文章来源">
										</td>
									</tr>
									<tr>
										<td><label>售价</label></td>
										<td colspan="2">
											支付类型:
											<select id="pay_type" class="pure-select dh-inline" name="pay_type">
												<option value="0">免费</option>
												<option value="1">金币</option>
												<option value="2">金钱</option>
											</select>&nbsp;&nbsp;
											金币：<input id="coin" type="text" name="coin" placeholder="金币数" class="dh-inline">&nbsp;&nbsp;
											金钱：<input id="money" type="text" name="money" placeholder="金钱数" class="dh-inline">	
										</td>
									</tr>	
									<tr>
										<td><label for="content">内容 <span class="red">*</span></label></td>
										<td colspan="2">
											<textarea id="content" name="content" placeholder="内容" required></textarea>
										</td>
									</tr>
								</table>
							</div>
							<div class="tab-item" id="tab-b">
								<table class="full pure-table pure-table-bordered" id="table_file">
									<thead>
										<tr>
											<th width="120">名称</th>
											<th width="80">种类</th>
											<th>网址</th>
											<th>备注</th>
											<th width="120" align="center">控作</th>
										</tr>
									</thead>
									<tr>
										<td><input class="pure-input-1" type="text" name="files[0][name]"></td>
										<td><input class="pure-input-1" value="百度网盘" type="text" name="files[0][type]"></td>
										<td><input class="pure-input-1" type="text" name="files[0][url]"></td>
										<td><input class="pure-input-1" value="提取密码:" type="text" name="files[0][remark]"></td>
										<td><a href="javascript:;" class="pure-button btn-success btn-sm file_add">增加</a></td>
									</tr>
								</table>
							</div>
							<div class="tab-item" id="tab-c">
								<table class="full pure-table pure-table-bordered">
									<tr>
										<td width="70"><label for="status">状态</label></td>
										<td>
											<select id="status" name="status" class="pure-select">
												<option value="0">隐藏</option>
												<option value="1" selected>公开</option>
											</select>
										</td>
									</tr>
									<tr>
										<td width="70"><label for="is_top">是否置顶</label></td>
										<td>
											<select id="is_top" name="is_top" class="pure-select">
												<option value="0" selected>否</option>
												<option value="1">是</option>
											</select>
										</td>
									</tr>
									<tr>
										<td width="70"><label for="recommended">是否推荐</label></td>
										<td>
											<select id="recommended" name="recommended" class="pure-select">
												<option value="0" selected>否</option>
												<option value="1">是</option>
											</select>
										</td>
									</tr>
									<tr>
										<td width="70"><label for="allow_comment">允许评论</label></td>
										<td>
											<select id="allow_comment" name="allow_comment" class="pure-select">
												<option value="0">不允许</option>
												<option value="1" selected>允许</option>
											</select>
										</td>
									</tr>
									<tr>
										<td width="70"><label for="published_time">发布时间</label></td>
										<td>
											<input id="published_time" class="dh-inline" type="text" name="published_time" placeholder="发布时间">&nbsp;&nbsp;<i class="date-holder iconfont 
icon-rili"></i>
										</td>
									</tr>
									<tr>
										<td width="70"><label for="create_time">创建时间</label></td>
										<td>
											<input id="create_time" class="dh-inline" type="text" name="create_time" placeholder="创建时间">&nbsp;&nbsp;<i class="iconfont 
icon-rili date-holder"></i>
										</td>
									</tr>
									<tr>
										<td width="70"><label for="views">计数</label></td>
										<td>
												查看次数:<input id="views" type="text" name="views" placeholder="查看次数" class="dh-inline">&nbsp;&nbsp;
												点赞次数:<input id="likes" type="text" name="likes" placeholder="点赞次数" class="dh-inline">&nbsp;&nbsp;
												下载次数:<input id="downloads" type="text" name="downloads" placeholder="下载次数" class="dh-inline">
										</td>
									</tr>
								</table>
							</div>				
						</fieldset>
						<div class="" style="max-width:300px;margin:10px auto">
							<button type="sumit" class="pure-button btn-custom pure-u-1">提交</button>
						</div>
					</form>
					<div class="msg" style="display:none"><?=$msg?></div>
					<div id="num" style="display:none">1</div>
					<div id="category-data" style="display:none">
						<table class="pure-table pure-table-bordered" style="width: 95%;margin: 5px auto;">
							<thead>
								<tr>
									<th width="18"><input class="js-check-all" type="checkbox"></th>
									<th width="50">分类id</th>
									<th>分类名</th>
								</tr>
							</thead>
							<tbody>
								<?=$category?>
							</tbody>
						</table>
					</div>
			</div>
		</div>
	</div>
</div>
{%end%}

{%block@javascript%}
<script type="text/javascript" src="/static/lib/My97DatePicker/WdatePicker.js"></script>
<script type="text/javascript" src="/static/lib/layer/layer.js"></script>

<link rel="stylesheet" type="text/css" href="/static/lib/simditor-2.3.16/builds/styles/simditor.css" />
<script type="text/javascript" src="/static/lib/simditor-2.3.16/builds/script/all.js?v=aaa"></script>

<script charset="UTF-8" type="text/javascript">
$(function(){
	Jtab('#jtab_inline');
	showMsg('.msg');
	//富文本编辑器
	var editor = new Simditor({
		textarea: $('#content'),
		toolbar: ['title','bold', 'italic', 'color','link', '|', 'ol', 'ul','code','|','html','fullscreen']
	});
	/*$("#myform").submit(function(){
		editor.setValue(editor.getValue());
		return true;
	});*/
	//var he = HE.getEditor('editor');
	/*绑定点击事件到.file_add*/
	$(document).on('click','.file_add',function(){
		$(this).parents('tr').after(getItem());
		$('#num').html(parseInt($('#num').html())+1);
	});
	/*绑定点击事件到.file_cancel*/
	$(document).on('click','.file_cancel',function(){
		$(this).parents('tr').remove();
		$('#num').html(parseInt($('#num').html())-1);
	});
	/*绑定点击事件到 .js-check-all*/
	$(document).on('click','.layui-layer .js-check-all',function(){
		var _this=$(this);
		if(_this.is(':checked')){
			_this.parents('table').find('td >input[type="checkbox"]').prop('checked', true);
		}else{
			_this.parents('table').find('td >input[type="checkbox"]').prop('checked', false);
		}
	});
	//日期选择器
	$('.date-holder').on('click', function(e){ 
		WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',el:($(this).siblings('input')[0].id)});
	});
	/*缩略图*/
	$("#thumb").blur(function(){
		var _this=$(this);
		var url=_this.val();
		console.log(_this.siblings('.thumb-img'));
		if(url !==''){
			if(/^https?:\/\/.*$/i.test(url)){
				if(/^https?:\/\/([a-z0-9]([a-z0-9\-]{0,61}[a-z0-9])?\.)+[a-z]{2,6}\/.*?(jpg|png|gif|jepg)$/i.test(url)){
					//外部图片https://img.alicdn.com/imgextra/i3/1120328108/TB2QwHvD3mTBuNjy1XbXXaMrVXa_!!1120328108-0-item_pic.jpg_430x430q90.jpg
					_this.siblings('.thumb-img').html('<img src="'+_this.val()+'" class="pure-img">');
				}
			}else if(/^\/?[\w\.\-]+\/.*?(jpg|png|gif|jepg)$/i.test(url)){
				//本地图片 uploads/images/demo.png
				url=url.replace(/^\//,'');
				_this.val(url);
				_this.siblings('.thumb-img').html('<img src="/'+_this.val()+'" class="pure-img">');
			}
		}
	});
	/*分类弹出*/
	$("#catename").click(function(e){
		layer.open({
			type: 1,
			content:$('#category-data').html(),
			btn:['确定','取消'],
			btn1:function(index, layero){
				var catid='',catname='';
				layero.find('.pure-table td > input[type="checkbox"]:checked').each(function(k){
					if(k == 0){
								catid = $(this).val();
								catname=$(this).attr('data-name');
					}else{
								catid += ','+$(this).val();
								catname += ','+$(this).attr('data-name');
					}
				});
				$("#cateid").attr("value",catid);
				$("#catename").attr("value",catname);
				layer.close(index);
			},
			//btn2:function(index, layero){layer.close(index);},
			area: '500px',
			title:'选择分类'
		});
	});
});
function getItem(){
	var file_i=$('#num').html();
	return '<tr><td><input class="pure-input-1" type="text" name="files['+file_i+'][name]"></td><td><input value="百度网盘" class="pure-input-1" type="text" name="files['+file_i+'][type]"></td><td><input class="pure-input-1" type="text" name="files['+file_i+'][url]"></td><td><input value="提取密码:" class="pure-input-1" type="text" name="files['+file_i+'][remark]"></td><td><a href="javascript:;" class="pure-button btn-success btn-sm file_add">增加</a><a href="javascript:;" class="pure-button btn-warning btn-sm file_cancel">取消</a></td></tr>';
}

</script>

{%end%}