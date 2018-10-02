{%extend@common/portal%}

{%block@title%}
  <title><?=$title?></title>
{%end%}

{%block@article%}
			<div class="pure-box-left" id="article">
				
				<div class="article-content">
					<div class="regbox">
						<div class="regbox-title"><h2>注册账号</h2></div>
						<form class="pure-form pure-form-stacked" id="ajaxform" action="<?=url('portal/index/regsave_json')?>" method="post">							
							<label for="username">用户名</label>
							<input type="text" id="username" pattern='^[(\u4e00-\u9fa5)a-zA-Z\d_\-]{3,20}$' class="pure-input-1" name="username" required>
							<div class="check-user">
								<a href="javascript:;" class="pure-button btn-success btn-sm" data="<?=url('portal/index/check_username')?>">检测用户名是否可用</a>
								<span></span>
							</div>												
							<label for="email">Email</label>
							<input id="email" type="email"  class="pure-input-1" placeholder="Email" name="email" required>
							<div class="check-email">
								<a href="javascript:;" class="pure-button btn-success btn-sm" data="<?=url('portal/index/check_email')?>">检测Email是否可用</a>
								<span></span>
							</div>								
							<label for="password">密码</label>
							<input id="password" type="password" maxlength="30" minlength="6" class="pure-input-1" placeholder="密码" name="password" required>
							<label for="repassword">确认密码</label>
							<input id="repassword" type="password" class="pure-input-1" placeholder="重复密码" name="repassword" required>							
							<br>
							<button type="submit" class="pure-button pure-input-1 btn-custom" disabled>提交</button>
						</form>
					</div>
				</div>
			</div><!--//article-->
{%end%}

{%block@javascript%}
<link rel="stylesheet" href="//qidian.gtimg.com/lulu/theme/peak/css/common/ui/Tips.css">
<script src="//qidian.gtimg.com/c/=/lulu/theme/peak/js/common/ui/Follow.js,/lulu/theme/peak/js/common/ui/ErrorTip.js,/lulu/theme/peak/js/common/ui/Validate.js"></script>
<script type="text/javascript" charset="UTF-8">
$(function(){
	var pwd=$('#password'),repwd=$('#repassword');
	$('#ajaxform').validate(function () {
		sendJSON($(this), this.action,function(){
			alert("成功注册");
			location.href="<?=url('/')?>";
		});			
	},{
		validate:[
			{id:"username",prompt:{unmatch: "3~20位的中文/字母/数字/下划线_及破折号-"}},
			{id:"password",method:function(){if(pwd.val()!==repwd.val()) return '前后密码不一致';pwd.removeClass('error');}},
			{id:"repassword",method:function(){if(pwd.val()!==repwd.val()) return '前后密码不一致';pwd.removeClass('error');}},
		]
	});
	ajaxCheck("#username",".check-user");
	ajaxCheck("#email",".check-email");
});
/**检测用户名/email有没有被使用**/
/*用户名/email变更后可以再次检测，否则相同只可以检测一次*/
function ajaxCheck(box,check){
	var ajax=new Jajax();
	var inputbox=$(box);
	var checkbox=$(check);
	//监测值是否更改
	inputbox.change(function(){
		if(ajax.status==false && ajax.isloading==false)
			ajax.status=true;
	});
	//监视点击按钮
	checkbox.children("a").click(function(){
		ajax.get(checkbox.children("a").attr('data'),inputbox.attr("name")+"="+inputbox.val(), function(data){
			checkbox.children("span").attr("class","green").html(inputbox.val()+" 通过验证,没被使用");
		}, false, true,function(data){
			checkbox.children("span").attr("class","red").html(data.msg);
		});
	});
}
</script>
{%end%}

