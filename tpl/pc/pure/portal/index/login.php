<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta http-equiv="Cache-Control" content="no-transform">
	<meta http-equiv="Cache-Control" content="no-siteapp">
	<meta name="applicable-device" content="pc">
	<title><?=$title?>_<?=$site_name?></title>
	<link href="/static/pure/css/pure.css?v=1.0" rel="stylesheet" type="text/css">
	<style>
		body{background-color:#F6F6F3;}
		main{margin:0 auto;height:100%;display:table}
		.login{display:table-cell;padding: 30px 0 100px;text-align: center;vertical-align: middle;width: 280px;}
		.title{text-align:center;margin-bottom:20px}
		.login label{text-align:left;}
		.meta{color:#767676;padding-top:10px;padding-bottom:10px;}
		.meta a{color:#02B4C1;text-decoration: none;}
	</style>
</head>
<body>
<main>
	<div class="login">
		<div class="title"><?=$title?></div>
		<form class="pure-form pure-form-stacked" id="ajaxform" action="<?=(url('portal/index/canlogin').$is_admin)?>" method="post">
			<label for="username">用户名</label>
			<input type="text" id="username" class="pure-input-1" name="username" placeholder="用户名" required>
			<label for="password">密码</label>
			<input id="password" type="password" maxlength="30" minlength="6" class="pure-input-1" placeholder="密码" name="password" required>
			<div class="meta">没有账号？<a href="<?=url('portal/index/reg')?>">注册一个</a></div>
			<button type="submit" class="pure-button pure-input-1 btn-custom">提交</button>
		</form>
	</div>
</main>
</body>
</html>