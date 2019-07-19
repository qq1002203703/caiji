{%extend@common/main%}
{%block@title%}
<title>404页_<?=$site_name?></title>
<style>
    #box-tip{margin: 0.1rem auto 1rem auto;text-align: left;line-height: 1.2}
    #box-tip p{margin: 0.15rem;}
</style>
{%end%}
{%block@article%}
<div class="p3 color3 f30" id="box-tip">
    <p class="f36 pb2 color2">404 错误 : 你访问的页面不存在！</p>
    <p><strong>造成的原因如下：</strong></p>
    <p>1、你输入的链接可能出错了，请检查链接。</p>
    <p>2、链接对应的页面可能已经删除，也可以移动到其他的地方了。</p>
    <p><strong>现在你可以：</strong></p>
    <p>1、 返回 <a  class="color-primary" href="/">首页</a> 访问其他内容。</p>
    <p>2、 使用本站的搜索功能搜一搜看能不能找出对应的页面。</p>
</div>
{%end%}

