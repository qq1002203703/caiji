<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,minimum-scale=1,user-scalable=0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta http-equiv="Cache-Control" content="no-transform">
    <meta http-equiv="Cache-Control" content="no-siteapp">
    <meta name="applicable-device" content="mobile">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    {%block@title%}
    <link rel="stylesheet" href="/static/lib/miniMobile/css/miniMobile.css">
    <!-- 字体图标 -->
    <link rel="stylesheet" type="text/css" href="/static/lib/miniMobile/plugins/fonticon/iconfont.css">
    <!-- animate.css -->
    <!--link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.css"-->
    <style type="text/css">
        /*公共*/
        .bb{border-bottom: 1px solid #e9eff4}
        .bt{ border-top: 1px solid #e9eff4}
        .ui-header{position:fixed;z-index:9999;top:0;right:0;left:0;}
        .ui-scrollTop {position: fixed;right: 0.2rem;bottom: 0.8rem;cursor: pointer;z-index:9999;}
        .yang-warp{position:absolute;}
        .yang-title-border{border-bottom:1px solid #efefef;border-left:4px solid #1AB394;}
        .yang-box{background-color:#A860F7;color:#FFF;line-height: 1.4;}
        /*内容*/
        .yang-content{line-height:1.4}
        .yang-content  h2,.yang-content  h3,.yang-content h4{font-size:.36rem; line-height: 1.6;margin: 0.3rem 0;}
        .yang-content  h2{border-bottom:1px solid #efefef;border-left:4px solid #1AB394;padding-left:5px;}
        .yang-content p{margin-bottom:.3rem;}
        .yang-content a{color:#4f99cf;}
        .yang-content img{max-width: 100%; cursor: crosshair;}
        .yang-content table{margin: 0.2rem 0 0.3rem;}
        .yang-content table thead{background-color:#f2f2f2;}
        .yang-content table th, .yang-content table td{padding: 0.2rem 0.4rem;border: 1px solid #DFDFDF; }
        /*列表*/
        .yang-list>li{border-bottom: 1px solid #f1f1f1;}
        .yang-list>li>a{display:block;}
        .yang-list>li img{display: inline-block;max-width: 98%;}
        .yang-list>li h3{font-weight:normal;}
        /*分页*/
        .pagination {padding:0.03rem;margin:0.03rem;font-size:0.28rem;}
        .pagination a,.pagination span{height:0.5rem;line-height:.5rem;display: inline-block; padding: 0 0.1rem;margin:0.02rem;}
        .pagination a,.pagination a:visited {border:#ddd 1px solid; color:#333;text-decoration:none}
        .pagination a:hover {border:#ddd 1px solid; background-color:#F3F6F8;color:#02C0CF;text-decoration:none;}
        .pagination .active,.pagination .current {border:#009688 1px solid;color:#fff;background-color:#009688;}
        .pagination .disabled {border:#ddd 1px solid;color:#555;}
        .pagination .count {margin-right:0.1rem;color:#666;}
        .pagination .count strong {margin:0 0.03rem;color:#CC3300; ;font-weight:bold;}
        /*底部导航*/
        .yang-bottom-nav {line-height: 1.4em;border-top: 1px solid #F1F1F1;position:fixed;z-index:9998;bottom:0;right:0;left:0;}
        .yang-bottom-nav a {display: block;width: 100%;height: 100%;}
        /*tag*/
        .yang-tag-img img{max-width: 98%;padding:1px; }
        /*微信群*/
        .yang-wxq-details .wxq-qrcode img{display: block;margin-left: auto;margin-right: auto;max-width: 100%}
        /*下一篇 下一篇*/
        .pre-next{font-size: 0.28rem}
        .pre-next a{display: inline-block}
        .pre-next a.next{margin-left: 0.3rem}
        /*学校*/
        .xuexiao-info{background-color: #E8F0F7;}
        /*友情链接*/
        .yang-links a{display: inline-block; margin-right: 0.2rem}
    </style>
</head>
<body class="pb12 fadeIn animated">
{%include@common/header%}
{%block@article%}
{%include@common/footer%}
<!--公共js-->
<script type="text/javascript" src="/static/lib/miniMobile/js/zepto.min.js"></script>
<script type="text/javascript" src="/static/lib/miniMobile/js/miniMobile.js"></script>
<!--公共js-->
{%block@javascript%}
</body>
</html>