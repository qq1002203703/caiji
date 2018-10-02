<style>
    .unslider { position: relative; overflow: auto; text-align: center;}
    .unslider ul, .unslider ol{ padding: 0;}
    .unslider li { list-style: none; }
    .unslider ul li { float: left; }
    #unslider01 .dots { position: absolute; left: 0; right: 0; bottom: 0px;}
    #unslider01 .dots li { display: inline-block; width: 10px; height: 10px; margin: 0 4px; text-indent: -999em; border: 2px solid #fff; border-radius: 6px; cursor: pointer; opacity: .4; -webkit-transition: background .5s, opacity .5s; -moz-transition: background .5s,opacity .5s; transition: background .5s, opacity .5s;}
    #unslider01 .dots li.active {background: #fff;opacity: 1;}
</style>
<div class="slider">
    <div class="unslider" id="unslider01">
        <ul>
            <li><img src="http://demo.my/unslider/1.jpg" alt="广告图"/></li>
            <li><img src="http://demo.my/unslider/2.jpg" alt="广告图"/></li>
            <li><img src="http://demo.my/unslider/3.jpg" alt="广告图"/></li>
            <li><img src="http://demo.my/unslider/4.jpg" alt="广告图"/></li>
            <li><img src="http://demo.my/unslider/5.jpg" alt="广告图"/></li>
        </ul>
    </div>
</div>
<script type="text/javascript" src='/static/js/unslider.js?s=ssssss'></script>
<script type="text/javascript">
    $(document).ready(function(e) {
        $('#unslider01').unslider({
            speed: 500,               //  滚动速度
            delay: 3000,              //  动画延迟
            //complete: function() {},  //  动画完成的回调函数
            //keys: true,               //  启动键盘导航
            //fluid: true,              //  支持响应式设计
            dots: true              //  显示点导航
        });
    });
</script>