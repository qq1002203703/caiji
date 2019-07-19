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
    <link rel="stylesheet" href="<?=$tuku?>/static/lib/miniMobile/css/miniMobile.css">
    <!-- 字体图标 -->
    <link rel="stylesheet" type="text/css" href="<?=$tuku?>/static/lib/miniMobile/plugins/fonticon/iconfont.css">
    <link rel="stylesheet" href="https://cdn.bootcss.com/Swiper/4.5.0/css/swiper.min.css">
    <!-- animate.css -->
    <!--link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.css"-->
    <link href="/static/favicon.ico?v=1.1" rel="shortcut icon" type="image/x-icon">
    <style type="text/css">
        /*公共*/
        .bb{border-bottom: 1px solid #e9eff4}
        .bt{ border-top: 1px solid #e9eff4}
        .ui-header{position:fixed;z-index:9999;top:0;right:0;left:0;}
        .ui-header .ui-header-c{overflow: hidden;text-overflow: ellipsis;white-space: nowrap;}
        .ui-scrollTop {position: fixed;right: 0.2rem;bottom: 0.8rem;cursor: pointer;z-index:9999;}
        .yang-warp{position:absolute;}
        .yang-title-border{border-bottom:1px solid #efefef;border-left:4px solid #1AB394;}
        .yang-box{background-color:#A860F7;color:#FFF;line-height: 1.4;}
        .yang-bgcolor-success{background-color: #5FB878}
        .yang-color-primary{color: #009688}
        .yang-normal{font-style: normal}
        .yang-one{white-space: nowrap;overflow: hidden;text-overflow: ellipsis;}
        .img-wrap{position: relative; height: 0; overflow: hidden;display: block;}
        .img-wrap img{position: absolute; top: 0; width: 100%;display: block;}
        .img-wrap-pt1{padding-top: 62.5%}
        .red{color: red}
        .dib{display: inline-block}
        img{max-width: 100%;}
        /*内容*/
        .yang-content{line-height:1.4}
        .yang-content  h2,.yang-content  h3,.yang-content h4{font-size:.34rem; line-height: 1.6;margin: 0.3rem 0;}
        .yang-content  h2{border-bottom:1px solid #efefef;border-left:4px solid #1AB394;padding-left:5px;}
        .yang-content p{margin-bottom:.2rem;}
        .yang-content a{color:#009688;}
        .yang-content img{max-width: 100%; cursor: crosshair;}
        .yang-content table{margin: 0.2rem 0 0.3rem;}
        .yang-content table thead{background-color:#f2f2f2;}
        .yang-content table th, .yang-content table td{padding: 0.2rem 0.4rem;border: 1px solid #DFDFDF;}
        /*列表*/
        .yang-list>li{border-bottom: 1px solid #f1f1f1;}
        .yang-list>li>a{display:block;}
        .yang-list>li img{display: inline-block;width: 100%;height: auto;}
        .yang-list>li h3{font-weight:normal;}
        /*评论式列表*/
        .yang-list2 .item{border-bottom: 1px solid #f1f1f1;}
        .yang-list2 .item .item-l, .yang-list2 .item .item-r{display: inline-block;vertical-align: top}
        .yang-list2 .item .item-l img{width: 100%;}
        .yang-list2 .item .item-r .title{white-space: nowrap;overflow: hidden;text-overflow: ellipsis;}
        /*video页*/
        .video-info{border-bottom: 1px solid #f1f1f1;}
        .video-info .title{white-space: nowrap;overflow: hidden;text-overflow: ellipsis;}
        .video-info img{display: inline-block;max-width: 98%;height: auto;}
        .video-info .box{position: relative;overflow: hidden}
        .video-info .score{position: absolute;right: 6%;top: 0;border-radius: 0 0 15% 15%;}
        .tab-title{border-bottom:2px solid #009688;color: #009688;}
        .tabBox .tabindexList span{display: inline-block;line-height: 0.48rem}
        .video-list h3{font-weight: normal;}
        .video-list a{display: inline-block;padding:3px 10px;border:1px solid #ececec; }
        .video-list a:hover{border:1px solid #0aaf19; color:#009688}
        .video-list .item{border:1px solid #5FB878}
        /*播放页*/
        .player-info .xuanji{border: 1px solid  #5FB878;}
        .player-info .xuanji a{display: inline-block;padding:3px 10px;border:1px solid #ececec;margin-bottom: 0.02rem;}
        .player-info .xuanji a:hover{border:1px solid #0aaf19; color:#009688}
        .player-info .xuanji a.active{border-color:#5FB878;color:#009688;}
        .player-info .xuanji a.active:hover{color:#333;}
        /*图片列表*/
        .picbox .link{width: 96%;padding: 0 2%;cursor: pointer;display: block;position: relative;overflow: hidden;}
       .picbox img {width: 100%;border: none;display: inline-block;vertical-align: top;}
        .picbox .title{display: block;vertical-align: baseline;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;}
        .picbox .icon{position: absolute;right: 6%;top: 0;display:block;background:#FF8100;border-radius: 0 0 15% 15%;}
        .picbox .sub{display: block;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;}
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
        /*ad*/
        .ad-article a.btn{display: inline-block;width: 90%;color: #FFF !important;}
        /*member*/
        .yang-space .userbox {line-height: 2em;background: url("<?=$tuku?>/static/lib/miniMobile/img/s5.jpg") center 40% no-repeat;background-size: cover}
        .yang-space .user-info{line-height: 1.5em}
        .jie-row li{border-bottom: 1px dotted #E9E9E9;}
    </style>
</head>
<body class="pb12 fadeIn animated">
{%include@common/header%}
{%block@article%}
{%include@common/footer%}
<!--公共js-->
<script type="text/javascript" src="<?=$tuku?>/static/lib/miniMobile/js/zepto.min.js"></script>
<script type="text/javascript" src="<?=$tuku?>/static/lib/miniMobile/js/miniMobile.js"></script>
<script type="text/javascript" src="https://cdn.bootcss.com/Swiper/4.5.0/js/swiper.min.js"></script>
<script type="text/javascript">
    $(function () {
        //右侧
        var asideRight = $(".asideRight").asideUi({
            size: "4rem",
            position: "right",
        });
        $(".ui-header-r").tap(function(event) {
            asideRight.toggle();
            event.preventDefault();
        });
    });
</script>
<!--公共js-->
{%block@javascript%}
</body>
</html>