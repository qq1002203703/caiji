<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=yes">
    <meta http-equiv="Cache-Control" content="no-transform">
    <meta http-equiv="Cache-Control" content="no-siteapp">
    <meta name="applicable-device" content="pc,mobile">
    <?=$this->section('header')?>
    <link type="text/css" rel="stylesheet" href="/static/css/style.css?ver=kddksskdsss">
    <script type="text/javascript" src='/static/js/jquery.min.js?s=ssssss'></script>
</head>
<body>
 <div class="wrapper">
  <div class="header">
    <div class="nav">
        <span><a href="/" title="返回首页">首页</a></span>
        <?php if ($is_login):?>
        <span><a href="/member/info" title="个人中心">账号信息</a></span>
        <span><a href="/index/logout" title="个人中心">退出</a></span>
        <?php else:?>
         <span><a href="/index/login" title="登陆">登陆</a></span>
        <span><a href="/index/reg" title="注册">注册</a></span>
        <?php endif;?>
    </div>
  </div>

  <div class="content">  
	<?=$this->section('middle')?>

	</div><!--end content-->

  <div class="footer">
  	<span>&copy;&nbsp;2018&nbsp;<?=$this->e($site_name)?></span>
  </div><!--end footer-->
 </div><!--end wrapper-->

 <?=$this->section('java')?>
</body>

</html>



