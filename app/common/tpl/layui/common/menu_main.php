{%content@common/base%}
<div class="main fly-user-main layui-clear">
    <!--sidebar-->
    <div class="layui-side layui-bg-black">
        <div class="layui-side-scroll">
            <ul class="layui-nav layui-nav-tree layui-inline" lay-filter="user">
                <li class="layui-nav-item" id="user-index">
                    <a href="<?=url('admin/user/index')?>">
                        <i class="nav-icon iconfont icon-homepage_fill"></i>
                        后台首页
                    </a>
                </li>
                <li class="layui-nav-item">
                    <a href="javascript:;">
                        <i class="nav-icon iconfont icon-createtask_fill"></i>
                        <strong>文章管理</strong>
                    </a>
                    <dl class="layui-nav-child">
                        <dd id="portal-post-article">
                            <a class="menu" href="<?=url('admin/portal/post')?>?type=article">列表</a>
                        </dd>
                        <dd id="portal-post-add-article">
                            <a class="menu" href="<?=url('admin/portal/post_add')?>?type=article">发布</a>
                        </dd>
                        <dd id="portal-category-article">
                            <a class="menu" href="<?=url('admin/portal/category')?>?type=article">分类</a>
                        </dd>
                    </dl>
                </li>
                <li class="layui-nav-item">
                    <a href="javascript:;">
                        <i class="nav-icon layui-icon layui-icon-cart"></i>
                        <strong>商品管理</strong>
                    </a>
                    <dl class="layui-nav-child">
                        <dd id="portal-post-goods">
                            <a class="menu" href="<?=url('admin/portal/post')?>?type=goods">列表</a>
                        </dd>
                        <dd id="portal-post-add-goods">
                            <a class="menu" href="<?=url('admin/portal/post_add')?>?type=goods">发布</a>
                        </dd>
                        <dd id="portal-category-goods">
                            <a class="menu" href="<?=url('admin/portal/category')?>?type=goods">分类</a>
                        </dd>
                    </dl>
                </li>
                <li class="layui-nav-item">
                    <a href="javascript:;">
                        <i class="nav-icon layui-icon layui-icon-username"></i>
                        <strong>小组管理</strong>
                    </a>
                    <dl class="layui-nav-child">
                        <dd id="portal-post-group">
                            <a class="menu" href="<?=url('admin/portal/post')?>?type=group">列表</a>
                        </dd>
                        <dd id="portal-post-add-group">
                            <a class="menu" href="<?=url('admin/portal/post_add')?>?type=group">发布</a>
                        </dd>
                        <dd id="portal-category-group">
                            <a class="menu" href="<?=url('admin/portal/category')?>?type=group">分类</a>
                        </dd>
                    </dl>
                </li>
                <li class="layui-nav-item">
                    <a href="javascript:;">
                        <i class="nav-icon layui-icon layui-icon-play"></i>
                        <strong>视频管理</strong>
                    </a>
                    <dl class="layui-nav-child">
                        <dd id="video-post">
                            <a class="menu" href="<?=url('admin/video/post')?>">列表</a>
                        </dd>
                        <dd id="video-post-add">
                            <a class="menu" href="<?=url('admin/video/post_add')?>">发布</a>
                        </dd>
                        <dd id="video-category">
                            <a class="menu" href="<?=url('admin/video/category')?>">分类</a>
                        </dd>
                    </dl>
                </li>
                <li class="layui-nav-item">
                    <a href="javascript:;">
                        <i class="nav-icon layui-icon layui-icon-component"></i>
                        <strong>论坛管理</strong>
                    </a>
                    <dl class="layui-nav-child">
                        <dd id="bbs-list">
                            <a class="menu" href="<?=url('admin/bbs/list')?>">帖子管理</a>
                        </dd>
                    </dl>
                </li>
                <li class="layui-nav-item">
                    <a href="javascript:;">
                        <i class="nav-icon layui-icon layui-icon-survey"></i>
                        <strong>采集管理</strong>
                    </a>
                    <dl class="layui-nav-child">
                        <dd id="caiji-list">
                            <a class="menu" href="<?=url('admin/caiji/list')?>">任务管理</a>
                        </dd>
                        <dd id="caiji-handler">
                            <a class="menu" href="<?=url('admin/caiji/handler')?>">结果管理</a>
                        </dd>
                        <dd id="caiji-queue">
                            <a class="menu" href="<?=url('admin/caiji/queue')?>">定时队列</a>
                        </dd>
                    </dl>
                </li>
                <li class="layui-nav-item">
                    <a href="#">
                        <i class="nav-icon iconfont icon-headlines_fill"></i>
                        <strong>高级管理</strong>
                    </a>
                    <dl class="layui-nav-child">
                        <dd id="other-links">
                            <a class="menu" href="<?=url('admin/other/links')?>">锚文本管理</a>
                        </dd>
                        <dd id="upload-manage">
                            <a class="menu" href="<?=url('admin/upload/manage')?>">图片管理</a>
                        </dd>
                        <dd id="tag-manage">
                            <a class="menu" href="<?=url('admin/tag/manage')?>">标签管理</a>
                        </dd>
                        <dd id="kami-list">
                            <a class="menu" href="<?=url('admin/kami/list')?>">卡密管理</a>
                        </dd>
                    </dl>
                </li>
                <!--li class="layui-nav-item">
                    <a href="index.php?m=site&c=manage&a=storage_xufei"><i class="nav-icon iconfont icon-homepage_fill"></i>我要续费</a>
                </li-->
            </ul>
        </div>
    </div>
    <script type="text/javascript">var navHeader='nav-header-home';</script>
    <!--//sidebar-->
    {%block@main%}
</div>