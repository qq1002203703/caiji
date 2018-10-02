{%extend@common/base%}

{%block@main%}
<div class="path">
	<a href="<?=url('admin/index/index')?>">首页</a> > 网站设置 > <a href="<?=url('admin/option/all')?>">全局设置</a> > <?=$title?>
</div>

<div class="content">
	<div class="content-card">
		<div class="content-detail">
			<div class="content-item" id="">
<style>
	.top_space{font-size: 20px;line-height: 28px;display: block;font-weight: 500 }
	#province,#city,#area{margin-bottom: 12px}
	#province span,#city span,#area span{vertical-align: middle;display: inline-block;margin-right:4px }
	#city div,#area div {margin-left: 8px;border-bottom: 1px solid #EAEDEF}
</style>
					<form class="pure-form" action="" method="post">
						<fieldset>
							<label for="province"  class="top_space">省</label>
							<div id="province">
							</div>
							<label for="city" class="top_space">市</label>
							<div id="city">
							</div>
							<label for="area" class="top_space">区（县）</label>
							<div id="area">
							</div>
							<select class="pure-select" name="type" id="type"><option value="0">选中法</option> <option value="1">排除法</option></select> 选中法为只选上面所选的区域，排除法则是从全国所有区域中减去上面所选的区域
							<br><br><br>
							<button type="submit" class="pure-button btn-custom pure-input-1-4">提交</button>
						</fieldset>
					</form>
				<div class="msg" style="display:none"><?=$msg?></div>
			</div>
		</div>
	</div>
</div>
{%end%}

{%block@javascript%}
<script src="/static/lib/citys/citys-small.js" type="text/javascript"></script>
<script charset="UTF-8" type="text/javascript">
	$(function(){
		showMsg('.msg');
		setProvince();
		//省份选中事件
		$("#province").on('click','input',function(){  //点击的事件  与$("#selProvince").click(function(){}) 有区别
			$("#city").empty();
			$("#province input.input-province:checked").each(function(){
				var index=parseInt($(this).attr('index')),
					html=setCity(index);
				if( html != '' )
					$("#city").append(html);
			});
		});

		//城市选中事件
		$("#city").on('click','input',function(){
			$("#area").empty();
			$("#city input.input-city:checked").each(function(){
				var _this=$(this),
					html=setArea(parseInt(_this.attr('index')),parseInt(_this.attr('index-city')));
				if(html != '')
					$("#area").append(html);
			});
		});
	});
	function setProvince() {
		var input,
		_this=$("#province");
		_this.html("");
		for (var i = 0, len = cityData.length; i < len; i++) {
			input = '<input class="pure-checkbox input-province" type="checkbox" name="city[]" value="'+cityData[i].id+'" index="'+i+'">'+'<span>'+cityData[i].name+'</span>';
			_this.append(input);
		}
	}
	function setCity(index) {
		var city=cityData[index].city;
		if(city===undefined) return '';
		var html='<div>';
		for (var i = 0, len = city.length; i < len; i++) {
			html += '<input index-city="'+i+'" index="'+index+'" type="checkbox" name="city[]" value="'+city[i].id+'" class="pure-checkbox input-city">'+'<span>'+city[i].name+'</span>';
		}
		return html+'</div>';
	}
	function setArea(index,index_city) {
		var area=cityData[index].city[index_city].area;
		if(area===undefined) return '';
		var html='<div>';
		for (var i = 0, len = area.length; i < len; i++) {
			html += '<input type="checkbox" name="city[]" value="'+area[i].id+'" class="pure-checkbox input-area" checked>'+'<span>'+area[i].name+'</span>';
		}
		return html+'</div>';
	}

</script>
{%end%}