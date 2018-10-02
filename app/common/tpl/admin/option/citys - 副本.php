{%extend@common/base%}

{%block@main%}
<div class="path">
	<a href="<?=url('admin/index/index')?>">首页</a> > 网站设置 > <a href="<?=url('admin/option/all')?>">全局设置</a> > <?=$title?>
</div>

<link href="/static/lib/lou-multi-select/css/multi-select.css" media="screen" rel="stylesheet" type="text/css">
<div class="content">
	<div class="content-card">
		<div class="content-detail">
			<div class="content-item" id="">

					<form class="pure-form pure-form-stacked" action="" method="post">
						<fieldset>
							<h2>1、省选择</h2>
							<p>说明：选中省份会自动选中其下的所有市、区（县）</p>
							<select multiple="multiple" id="province" name="province[]" class="province">
							</select>
							<h2>2、市选择</h2>
							<p>说明：选中市会自动选中其下的所有区（县）</p>

							<p>
								<select id="city-province" class="province city-province" style="display: inline-block"></select>
							</p>
							<select multiple="multiple" id="city" name="city[]" class="city">
								<option value="110000">北京市</option>
							</select>
							<div class="pure-box">
								<div class="pure-box-left" style="width: 210px"></div>
								<div class="pure-box-right">添加 清空</div>
							</div>


							<h2>3、区（县）选择</h2>
								<label for="name">名称<span class="red"> *</span>：只能是字母、下线线和数字，且开头只能是字母或下划线</label>
								<input id="name" type="text" name="name" placeholder="名称" required>
								<label for="status">状态</label>
								<select id="status" class="pure-select" name="status">
									<option value="0">禁用</option>
									<option value="1" selected>启用</option>
								</select>
								<label for="description">说明：</label>
								<input class="pure-u-5-6" name="description" id="description" placeholder="说明描述">
								<label for="value">值：</label>
								<textarea name="value" id="value" placeholder="输入值" rows="3" class="pure-u-5-6"></textarea>


							<br>
							<button type="submit" class="pure-button btn-custom pure-input-1-4">提交</button>
						</fieldset>
					</form>
			</div>
		</div>
	</div>
</div>
{%end%}

{%block@javascript%}
<script src="/static/lib/lou-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
<script src="/static/lib/citys/citys-small.js" type="text/javascript"></script>
<script charset="UTF-8" type="text/javascript">
	$(function(){
		
		//$(".province").html(citySelect(cityData));
		//$('#province').multiSelect();
		/*$('.city-province').change(function () {
				var index=parseInt($(this).get(0).selectedIndex);
			//$('#city').html(citySelect(cityData[index].city));
			$(this).parent().next().html(citySelect(cityData[index].city)).multiSelect();
		});*/
		/*$('#city').multiSelect({
			afterInit: function(ms){
				var that = this;

				console.log(that.$selectableUl);
			}
		});*/
	});
	function setProvince() {
		var input, modelVal;
		$("#province").html("");
		for (var i = 0, len = cityData.length; i < len; i++) {
			modelVal = cityData[i];
			input = '<label><input type="checkbox" name="province" value="'+modelVal+'"/>'+'<span>'+modelVal+'</span></label>';
			$("#province").append(input);
		}
	}


	function citySelect(data) {
	var html='';
	$.each(data,function(index, el) {
		html +='<option value="'+el.id+'">'+el.name+'</option>';
	});
	return html;
}
</script>
{%end%}