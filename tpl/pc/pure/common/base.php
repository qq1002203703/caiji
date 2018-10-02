<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="Cache-Control" content="no-transform">
    <meta http-equiv="Cache-Control" content="no-siteapp">
    <meta name="applicable-device" content="pc,mobile">
	{%block@title%}
	<link href="/static/pure/css/pure.css?v=1.0" rel="stylesheet" type="text/css">
	<link href="/static/pure/css/style.css?v=1.0.4" rel="stylesheet" type="text/css">
	<link href="http://ico.z01.com/zico.min.css" rel="stylesheet">
	{%block@head_remark%}
</head>
<body>
<div id="header">
	<div class="top conteiner">
		<div class="pure-box">
			<div class="logo pure-box-left">
				<a href="<?=url('/')?>"><?=$site_name?></a>
			</div>
			<div class="pure-box-right">
				<ul class="top-menu">
						<!--菜单按钮-->
						<li class="top-menu-item menu-button">
							<a class="top-menu-link" title="菜单" id="menu-button-click">
                                <div class="lines-menu-button">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                            </a>
						</li>
						<?php if(!$is_login): ?>
						<!--登陆-->
						<li class="top-menu-item login"><a href="<?=url('portal/index/login')?>" class="top-menu-link pure-button btn-white btn-sm" title="登陆账号" role="button"><span class="top-menu-user">登陆</span></a></li>
						<!--注册-->
						<li class="top-menu-item register"><a href="<?=url('portal/index/reg')?>" class="top-menu-link pure-button btn-custom btn-sm" title="注册账号"><span class="top-menu-user">注册</span></a></li>
						<?php else: ?>
						<!--通知-->
						<li class="top-menu-item notice"><a class="top-menu-link" title="查看通知"><i class="zi zi_lingdang"></i><span class="top-menu-count badge rounded-pill bgc-custom">4</span></a></li>
						<!--消息-->
						<li class="top-menu-item inbox"><a class="top-menu-link" title="查看消息"><i class="zi zi_envelopeSquare"></i><span class="top-menu-count badge rounded-pill bgc-danger">6</span></a></li>		<!--个人中心-->
						<li class="top-menu-item user menu-dropdown">
							<a class="top-menu-link menu-dropdown-click" title="用户中心">
								<img src="/<?=($_SESSION['user']['avatar']??'uploads/user/default.png')?>" alt="用户" class="rounded-circle"><i>&#9662;</i>
							</a>
							<ul class="top-user-list menu-dropdown-content">
								<div class="menu-dropdown-title">当前用户:share98</div>
								<li class="top-user-item menu-dropdown-item"><a href="#"><i class="zi zi_lingdang"></i>个人中心</a></li>
								<li class="top-user-item menu-dropdown-item"><a href="#"><i class="zi zi_lingdang"></i>账号设置</a></li>
								<li class="top-user-item menu-dropdown-item"><a href="<?=url('portal/index/logout')?>"><i class="zi zi_lingdang"></i>安全退出</a></li>
							</ul>
						</li>
						<?php endif; ?>
					</ul>
			</div>
		</div>
	</div>
	<div class="nav">
		<div class="conteiner">
			<ul class="pure-box">
				<li class="pure-box-item"><a href="/">首页</a></li>
				<li class="pure-box-item"><a href="/">地方群</a></li>
				<li class="pure-box-item"><a href="/">行业群</a></li>
				<li class="pure-box-item"><a href="/">搜索群</a></li>
			</ul>
		</div>
	</div>
</div>
{%content%}
<footer id="footer">
	<div class="conteiner footer-box">
		2018 © <a href="/"><?=$site_name?></a>. - www.iweixinqun.cn<br>
		<span class="beian"><a target="_blank" href="http://www.beian.gov.cn/portal/registerSystemInfo?recordcode=18096630" rel="nofollow">粤ICP备18096630号-1</a></span>
	</div>
</footer>
{%include@common/js%}
{%block@javascript%}
</body>
</html>