{%extend@common/base%}

{%block@main%}
<div class="path">
	<a href="<?=url('admin/index/index')?>">首页</a> > 微信管理 > <a href="<?=url('admin/wechat/menu')?>">菜单管理</a> > <?=$title?>
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
							 <label for="parent">上级<span class="red"> *</span>：最多只支持二级菜单</label>
							<select id="pid" class="pure-select" name="pid" required>
								<option value="0">顶级分类</option>
								<?php foreach ($select as $v):?>
									<option value="<?=$v['id']?>"<?=echo_select($v['id'],$data['pid'])?>><?=$v['name']?></option>
								<?php endforeach;?>
							</select>
							 <label for="name">菜单名<span class="red"> *</span>：1级菜单最多4个汉字，二级菜单最多7个汉字</label>
							 <input id="name" type="text"  value="<?=$data['name']?>" name="name" placeholder="菜单名" required>
							 <label for="type">类型：<span class="red"> *</span></label>
							 <select id="type" class="pure-select" name="type" required>
								 <option value="view"<?=echo_select('view',$data['type'])?>>view</option>
								 <option value="click"<?=echo_select('click',$data['type'])?>>click</option>
								 <option value="scancode_push"<?=echo_select('scancode_push',$data['type'])?>>scancode_push</option>
								 <option value="scancode_waitmsg"<?=echo_select('scancode_waitmsg',$data['type'])?>>scancode_waitmsg</option>
								 <option value="pic_sysphoto"<?=echo_select('pic_sysphoto',$data['type'])?>>pic_sysphoto</option>
								 <option value="pic_photo_or_album"<?=echo_select('pic_photo_or_album',$data['type'])?>>pic_photo_or_album</option>
								 <option value="pic_weixin"<?=echo_select('pic_weixin',$data['type'])?>>pic_weixin</option>
								 <option value="location_select"<?=echo_select('location_select',$data['type'])?>>location_select</option>
							 </select>
							 <div class="text-ipput">
								 <label for="text">
									 text：当类型为view时,此值为一个url <br>
									 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;当类型为click时，此值为一组关键词</label>
								 <input type="text" value="<?=$data['text']?>" name="text" id="text" class="pure-u-1-2">
							 </div>
							 <label for="status">状态：</label>
							 <select id="status" class="pure-select" name="status" required>
								 <option value="0"<?=echo_select('0',$data['status'])?>>不使用</option>
								 <option value="1"<?=echo_select('1',$data['status'])?>>使用</option>
							 </select>
							</div>
							 <input name="id" value="<?=$data['id']?>" type="hidden">
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
<style>
	#jtab label{margin-top:16px !important;margin-bottom:8px !important;}
</style>
<script charset="UTF-8" type="text/javascript">
$(function(){
	Jtab('#jtab');
	showMsg('.msg');
	$("#type").change(function () {
		var va=$(this).val();
		if(va=='click' || va =='view'){
			$('.text-ipput').show();
		}else {
			$('.text-ipput input').val('').parent().hide();
		}
	});
});
</script>
{%end%}