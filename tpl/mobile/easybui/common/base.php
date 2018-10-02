<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>叮叮民工</title>
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
    <link rel="stylesheet" href="//unpkg.com/buijs/lib/latest/bui.css">
    <style type="text/css">
        header.bui-bar, header .bui-bar { background-color:#0178d6}
        /*a #0076DC*/
        .color-light-gray{color:#B4B4B4}
        .bui-scroll .bui-scroll-main {
            background: #f5f5f5;
        }
        /* 轮播图片 */
        .news-slide .bui-slide-head{
            text-align: center;
            bottom: 0;
        }
        .news-slide .bui-slide-head ul li{
            width: 0.1rem;
            height: 0.1rem;
            text-indent: -99999px;
        }
        /* 快捷入口导航 */
        .shortcut-nav{
            margin-bottom: 0.2rem;
        }
        .shortcut-nav .bui-btn{
            padding: 0.15rem 0;
            border-left: none;
            border-bottom: none;
        }
        .shortcut-nav .bui-btn:nth-child(4n){
            border-right:none ;
        }
        .shortcut-nav [class*=bui-btn] .icon{

            height: 0.7rem;
            width: 0.7rem;
            border-radius: 50%;
            line-height: 0.7rem;
            margin: 0.1rem 0;
            font-size: .5rem;
        }
        .short1{color: #FF8B13; }
        .short2{color: #558BFF; }
        .short3{color: #2AB8FA; }
        .short4{color: #FF634F; }
        .short5{color:#33C7A7; }
        .short6{color:#668BD5; }
        .short7{color:#777EDD; }
        .short8{color:#FF679B; }
        .short9{color:#FF4905}
        /* 广告 */
        .box-text{padding: .2rem 0.3rem;background-color: #fff;}
        .box-text-title{font-size: .25rem}
        /*footer*/
        .footer-nav a{color:#878787;}
    </style>
</head>
<body>
<div class="bui-page">
    <header id="bar" class="bui-bar">
        <div class="bui-bar-left">
            <a class="bui-btn" onclick="bui.back();"><i class="icon-back"></i></a>
        </div>
        <div class="bui-bar-main">叮叮民工</div>
        <div class="bui-bar-right">

        </div>
    </header>
    <main>
        <div id="uiScroll" class="bui-scroll">
            <div class="bui-scroll-head"></div>
            <div class="bui-scroll-main">
                <!-- 头部图片轮播 -->
                <div id="slide" class="bui-slide news-slide">
                    <div id="slidemain" class="bui-slide-main">
                        <ul>
                            <li>
                                <div style="background: #4B44FC;height: 100%;"></div>
                            </li>
                            <li>
                                <div style="background: #0A254F;height: 100%;"></div>
                            </li>
                            <li>
                                <div style="background: #FEF3EB;height: 100%;"></div>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- 快捷入口 -->
                <ul class="bui-fluid-4 shortcut-nav">
                    <li class="bui-btn bui-box-vertical"><i class="icon short1">&#xe659;</i><div class="span1">快捷入口1</div></li>
                    <li class="bui-btn bui-box-vertical"><i class="icon short2">&#xe65a;</i><div class="span1">快捷入口2</div></li>
                    <li class="bui-btn bui-box-vertical"><i class="icon short3">&#xe629;</i><div class="span1">快速入口三</div></li>
                    <li class="bui-btn bui-box-vertical"><i class="icon short4">&#xe65c;</i><div class="span1">快捷入口4</div></li>
                    <li class="bui-btn bui-box-vertical"><i class="icon short5">&#xe674;</i><div class="span1">快捷入口4</div></li>
                    <li class="bui-btn bui-box-vertical"><i class="icon short6">&#xe676;</i><div class="span1">快捷入口4</div></li>
                    <li class="bui-btn bui-box-vertical"><i class="icon short7">&#xe65c;</i><div class="span1">快捷入口4</div></li>
                    <li class="bui-btn bui-box-vertical"><i class="icon short8">&#xe60b;</i><div class="span1">快捷入口4</div></li>
                    <li class="bui-btn bui-box-vertical"><i class="icon short1">&#xe674;</i><div class="span1">快捷入口4</div></li>
                    <li class="bui-btn bui-box-vertical"><i class="icon short2">&#xe676;</i><div class="span1">快捷入口4</div></li>
                    <li class="bui-btn bui-box-vertical"><i class="icon short3">&#xe65c;</i><div class="span1">快捷入口4</div></li>
                    <li class="bui-btn bui-box-vertical"><i class="icon short4">&#xe60b;</i><div class="span1">快捷入口4</div></li>
                </ul>
                <div class="box-text">
                    <p class="box-text-title">邀请好友 . 得<span class="short9">现金奖励</span>!</p>
                    <p class="color-light-gray">每次好友下单成功，获得7%提成</p>
                </div>
            </div>
            <div class="bui-scroll-foot"></div>
        </div>
    </main>
    <footer>
        <ul class="bui-nav footer-nav">
            <li class="bui-btn bui-box-vertical active"><i class="icon">&#xe659;</i><div class="span1"><a href="/"> 首页</a></div></li>
            <li class="bui-btn bui-box-vertical"><i class="icon">&#xe62d;</i><div class="span1"><a href="#">预约</a></div></li>
            <li class="bui-btn bui-box-vertical"><i class="icon">&#xe67a;</i><div class="span1"><a href="#">我的</a></div></li>
        </ul>
    </footer>
</div>
    <!-- 依赖库 手机调试的js引用顺序如下 -->
    <script src="//unpkg.com/buijs/lib/zepto.js"></script>
    <script src="https://cdn.bootcss.com/fastclick/1.0.6/fastclick.min.js"></script>
    <script src="//unpkg.com/buijs/lib/latest/bui.js"></script>
    <script>
        var  uiSlide,       // 焦点图控件;
        pageview = {
            bind :function () {
                // 初始化页面的链接跳转
                bui.btn({ id:"#page" , handle:".bui-btn"}).load();
            },
            init:function () {
                // 初始化焦点图
                uiSlide = bui.slide({
                    id:"#slide",
                    height:200,
                    autoplay : true,
                    autopage: true,
                    zoom: true
                });
               // 绑定页面事件
               this.bind();
            }
        };
        bui.ready(function () {
            pageview.init();
        });
    </script>
</body>
</html>