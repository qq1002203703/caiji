<div class="fly-header layui-bg-black">
    <div class="layui-container" style="position: relative;height: 60px">
        <a class="fly-logo" href="/" title="<?=$site_name?>">
            <img src="/static/fly/images/logo.png" alt="<?=$site_name?>" title="返回首页">
        </a>
        <div class="search-box layui-hide-xs">
            {%include@tmp/search%}
        </div>
        <ul class="layui-nav fly-nav-user">
            {%include@tmp/menu_top%}
        </ul>
    </div>
    <div style="width: 100%;border-top: 1px solid #555;overflow: hidden;height: 40px">
        <div class="layui-container">
            <ul class="layui-nav my-nav">
                {%include@tmp/menu_main%}
            </ul>
        </div>
    </div>
</div>