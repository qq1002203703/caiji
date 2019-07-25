{%extend@common/base_portal%}
{%block@title%}
<title>404页_<?=$site_name?></title>
<style>
    #box-tip{width: 300px;margin: 0 auto;text-align: left;font-size: 14px}
    #box-tip p{margin: 10px 10px;color: black;}
    #box-tip a{color: blue}
</style>
{%end%}
{%block@article%}
<div class="layui-container fly-marginTop">
    <div class="fly-panel">
        <div class="fly-none" style="margin-top:0;padding-top:0  ">
            <div><i class="iconfont icon-404"></i></div>
            <div id="box-tip">
                <p>你访问的页面不存在！</p>
                <p>你可以<a href="/">返回首页 </a></p>
            </div>
        </div>
    </div>
</div>
{%end%}

