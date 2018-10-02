{%extend@common/base%}

{%block@main%}
<div class="path">
	<a href="<?=url('admin/index/index')?>">首页</a> > 门户管理 > <?=$title?>
</div>
<div class="content">
	<div class="content-card">
		<div class="content-detail">
			<div class="content-item pure-g" id="jtab">
				<div class="pure-u-1-4">
					<div class="tab-hander">
						<a href="javascript:;" class="tab-hander-item tab-select" data="category-main">基本属性</a>
						<a href="javascript:;" class="tab-hander-item" data="category-seo">SEO属性</a>
						<a href="javascript:;" class="tab-hander-item" data="category-tpl">模板属性</a>
					</div>
				</div>
				<div class="pure-u-3-4">
					<form class="pure-form pure-form-stacked" action="" method="post">
						 <fieldset>
						 <div class="tab-item" id="category-main">
							<label for="parent">上级（必填）</label>
							<select id="pid" class="pure-select" name="pid" required>
								<option value="0">顶级分类</option>
								<?=$select?>
							</select>
							<label for="name">分类名（必填）</label>
							<input id="name" type="text" name="name" placeholder="分类名" required>
							<label for="slug">分类别名（slug）</label>
							<input id="slug" type="text" name="slug" placeholder="分类别名">
							<label for="description">描述（最多255个字）</label>
							<textarea id="description" name="description" class="pure-input-1-2"></textarea>
							</div>
							 <div class="tab-item" id="category-seo">
								<label for="seo_title">SEO标题（最多100个字）</label>
								<input class="pure-input-1-2" id="seo_title" type="text" name="seo_title" placeholder="SEO标题">
								<label for="seo_keywords">SEO关键词(多个关键词用英文逗号分隔)</label>
								<input class="pure-input-1-2" id="seo_keywords" type="text" name="seo_keywords" placeholder="SEO关键词">
								<label for="seo_description">SEO描述（最多255个字）</label>
								<input class="pure-input-1-2" id="seo_description" type="text" name="seo_description" placeholder="SEO描述">
							 </div>
							 <div class="tab-item" id="category-tpl">
								<label for="list_tpl">列表模板</label>
								<input class="pure-input-1-2" id="list_tpl" type="text" name="list_tpl" placeholder="列表模板">
								<label for="one_tpl">文章模板</label>
								<input class="pure-input-1-2" id="one_tpl" type="text" name="one_tpl" placeholder="文章模板">
							 </div>
							 <br>
							 <button type="sumit" class="pure-button btn-custom pure-input-1-4">提交</button>
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
<script charset="UTF-8" type="text/javascript">
$(function(){
	Jtab('#jtab');
	showMsg('.msg');
});
</script>
{%end%}