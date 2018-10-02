{%extend@common/base%}

{%block@main%}
<div class="path">
	<a href="<?=url('admin/index/index')?>">首页</a> > 网站设置 > <a href="<?=url('admin/option/all')?>">全局设置</a> > <?=$title?>
</div>

<div class="content">
	<div class="content-card">
		<div class="content-detail">
			<div class="content-item pure-g" id="jtab">
				<div class="pure-u-1-4">
					<div class="tab-hander">
						<a href="javascript:;" class="tab-hander-item tab-select" data="category-main">基本属性</a>
					</div>
				</div>
				<div class="pure-u-3-4">
					<form class="pure-form pure-form-stacked" action="" method="post">
						<fieldset>
							<div class="tab-item" id="category-main">
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

							</div>
							<br>
							<button type="submit" class="pure-button btn-custom pure-input-1-4">提交</button>
						</fieldset>
					</form>
					<div class="msg" style="display:none"><?=$msg?></div>
				</div>
			</div>
		</div>
	</div>
</div>
{%end%}

{%block@javascript%}
<style>.pure-form label{margin-top:16px;}  </style>
<script charset="UTF-8" type="text/javascript">
	$(function(){
		Jtab('#jtab');
		showMsg('.msg');
	});

</script>
{%end%}