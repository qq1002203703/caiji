<?php $this->layout('layout', [
	'site_name' => $site_name,
	'is_login'=>$is_login
]) ?>
<?php $this->start('header') ?>
  <title><?=$this->e($title) ?></title>
  <meta name="keywords" content="{{ conf.KEYWORD}}">
  <meta name="description" content="{{ conf.DESC}}">
<?php $this->stop() ?>

<?php $this->start('middle')?>
	<div class="post">
  <h2><?=$this->e($title) ?></h2>
  <form action="/index/regsave" method="post" class="form" onsubmit="return check(this)">
    <div><span>用户名：</span><input type="text" name="username" id="username" placeholder="用户名" /></div>
    <div><span>密&nbsp;码：</span><input type="password" name="password" id="password" placeholder="密码" /></div>
	  <div><span>确认：</span><input type="password" name="repassword" id="repassword" placeholder="确认密码" /></div>
	  <div><span>身份证：</span><input type="text" name="num" id="repassword" placeholder="身份证号码" /></div>
	  <div><span>邮箱：</span><input type="text" name="email" id="repassword" placeholder="邮箱" /></div>
    <div><span>&nbsp;</span><input type="submit" value="提交" class="submit" /></div>
  </form>
 </div><!--end post-->
<?php $this->stop() ?>

<?php $this->start('java') ?>
<script type="text/javascript">
function check(){
	//用姓名
	var username = document.getElementById("username");			
	if(username.value==''){
		alert('用户名不能为空');
		username.focus();
		return false;
	}
	//密码
	var password = document.getElementById("password");			
	if(password.value==''){
		alert('密码不能为空');
		password.focus();
		return false;
	}
}
</script>
<?php $this->stop() ?>
