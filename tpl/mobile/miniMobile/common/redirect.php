<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes" />
    <title>跳转提示</title>
    <!------移动端的css
        <style type="text/css">
            body, h1, h2, p,dl,dd,dt{margin: 0;padding: 0;font: 15px/1.5 微软雅黑,tahoma,arial;}
            body{background:#efefef;}
            h1, h2, h3, h4, h5, h6 {font-size: 100%;cursor:default;}
            ul, ol {list-style: none outside none;}
            a {text-decoration: none;color:#447BC4}
            a:hover {text-decoration: underline;}
            .ip-attack{width:100%; margin:200px auto 0;}
            .ip-attack dl{ background:#fff; padding:30px; border-radius:10px;border: 1px solid #CDCDCD;-webkit-box-shadow: 0 0 8px #CDCDCD;-moz-box-shadow: 0 0 8px #cdcdcd;box-shadow: 0 0 8px #CDCDCD;}
            .ip-attack dt{text-align:center;}
            .ip-attack dd{font-size:16px; color:#333; text-align:center;}
            .tips{text-align:center; font-size:14px; line-height:50px; color:#999;}
        </style>
    ----------->
    <style type="text/css">
        body, h1, h2, p,dl,dd,dt{margin: 0;padding: 0;font: 15px/1.5 "Microsoft YaHei",SimSun,tahoma,arial;}
        body{background:#efefef;}
        h1, h2, h3, h4, h5, h6 {font-size: 100%;cursor:default;}
        ul, ol {list-style: none outside none;}
        a {text-decoration: none;color:#447BC4}
        a:hover {text-decoration: underline;}
        .ip-attack{width:600px; margin:200px auto 0;}
        .ip-attack dl{ background:#fff; padding:30px; border-radius:10px;border: 1px solid #CDCDCD;-webkit-box-shadow: 0 0 8px #CDCDCD;-moz-box-shadow: 0 0 8px #cdcdcd;box-shadow: 0 0 8px #CDCDCD;}
        .ip-attack dt{text-align:center;}
        .ip-attack dd{font-size:16px; color:#333; text-align:center;}
        .tips{text-align:center; font-size:14px; line-height:50px; color:#999;}
    </style>
</head>
<body>
<div class="ip-attack">
	<dl>
		<?php $code_arr=['color: green','color: red', 'color: blue']; ?>
		<dt style="<?=$code_arr[$code-1]?>"><?=$msg?></dt>
        <br>
        <dt>
            点击立刻 <a id="href" href="<?=$url?>">跳转</a> 等待时间： <b id="wait"><?=$wait?></b>
        </dt>
	</dl>
</div>
<script type="text/javascript">
    (function(){
        var wait = document.getElementById('wait'),
            href = document.getElementById('href').href;
        var interval = setInterval(function(){
            var time = --wait.innerHTML;
            if(time <= 0) {
                location.href = href;
                clearInterval(interval);
            };
        }, 1000);
    })();
</script>
</body>
</html>