{%extend@common/menu_main%}
{%block@main%}
<div class="fly-panel fly-panel-user" pad20>
    <div class="layui-tab layui-tab-brief" lay-filter="user">
        <ul class="layui-tab-title" id="LAY_mine">
            <li class="layui-this" lay-id="list">后台首页</li>
        </ul>
        <div class="layui-tab-content">
            <div class="container">
                <div class="layui-row" style="position:relative;">
                    <div class="manage-index-left">
                        <div class="layui-form">
                            <div style="margin-top:-10px;margin-bottom:10px;padding-left:20px;">
                                你好！
                            </div>
                            <!--div class="quick-content-show">
                                                      <span class="show-item">
                                <a href="index.php?m=site&c=content&a=product_index&status=1">产品已发布(8)</a>
                                                                                            </span>

                                <span class="show-item">
                                <a href="index.php?m=site&c=content&a=article_index&status=1">文章已发布(19)</a>
                                  <a href="index.php?m=site&c=content&a=article_index&status=2">定时发布(1)</a>                                                              </span>

                                <span class="show-item">
                                    <a href="index.php?m=mod&c=neizhan&a=posts&neizhan_id=1&status=1">站内站已发布(0)</a>
                                                                                                    </span>
                                <span class="show-item">
                                <a href="index.php?m=mod&c=baike&a=index">百科页面()</a>
                                </span>
                            </div>
                            <div class="quick-content-show">
                                                      <span class="show-item">
                            <a href="index.php?m=mod&c=guestbook&a=index">用户留言(0)</a>
                            </span>
                                <span class="show-item">
                            <a href="index.php?m=mod&c=comment&a=index">评论(0)</a>
                            </span>
                                <span class="show-item">
                            <a href="index.php?m=mod&c=link&a=index">友情链接(1)</a>
                            </span>
                            </div>
                            <div class="quick-content-show">
                            <span class="show-item">
                            <a href="index.php?m=site&c=tongji&a=spider_index&time=h24">24小时蜘蛛抓取(0)</a>
                            <a href="index.php?m=site&c=tongji&a=spider_index&time=d7">7天(0)</a>
                            <a href="index.php?m=site&c=tongji&a=spider_index&time=d30">30天(0)</a>
                            </span>
                                <span class="show-item">
                                  <a href="index.php?m=site&c=tongji&a=liuliang">24小时网站流量(0)</a>
                                  <a href="index.php?m=site&c=tongji&a=liuliang">昨天(0)</a>
                                  <a href="index.php?m=site&c=tongji&a=liuliang">7天(0)</a>
                                  <a href="index.php?m=site&c=tongji&a=liuliang">30天(0)</a>
                            </span>
                            </div>
                        </div-->
                        <div class="index-banner" style="position: relative;">
                            <fieldset class="layui-elem-field layui-field-title">
                                <legend style="margin: 0 auto;font-style:italic;color: #999;font-size:16px;">2018-10-25</legend>
                            </fieldset>
                            <div class="banner-image" style="background-color: #666;"></div>
                            <div class="banner-logo"><!--img style="max-width: 300px;" src="/storage/6169/images/20180103/20180103151845_62720.png"-->建站利器</div>
                        </div>
                    </div>
                    <div class="index-aside">
                        <div class="fly-msg" style="height:153px;">
                            <strong>记事本</strong><br>
                            暂无内容，<a href="index.php?m=mod&c=notepad&a=index" style="color:#666;">可以点击这里添加。</a>
                        </div>
                        <div class="fly-msg" style="height:153px;">
                            <strong>最新登录</strong><br>
                            用户名：admin<br>
                            登陆IP：<?=ip()?><br>
                            现在时间：<?=date('Y-m-d H:i',time())?><br>
                        </div>
                        <!--div class="fly-msg" style="height:153px;">
                            <strong>网站信息</strong><br>
                            网站名称：默认演示站点<br>
                            首页域名：未设置<br>
                            手机域名：未设置<br>
                            安全后台：admin.php<br>
                        </div>
                        <div class="fly-msg" style="height:153px;">
                            <strong>空间信息</strong><br>
                            空间：20M站点免费版<br>
                            大小：20MB<br>
                            已用：4.78MB<br>
                            剩余：15.22MB<br>
                        </div-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript"></script>
<script type="text/javascript">
    var navSidebar="user-index";
</script>
{%end%}

{%block@javascript%}
{%end%}