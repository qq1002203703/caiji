{%content@common/base%}
<div class="main fly-user-main layui-clear">
    <!--sidebar-->
    <div class="layui-side layui-bg-black">
        <div class="layui-side-scroll">
            <ul class="layui-nav layui-nav-tree layui-inline" lay-filter="user">
                <li class="layui-nav-item">
                    <a href="javascript:;">
                        <i class="nav-icon iconfont icon-setup_fill"></i>
                        <strong>网站设置</strong>
                    </a>
                    <dl class="layui-nav-child">
                        <dd id="option-all-site">
                            <a class="menu" href="<?=url('admin/option/all')?>">全局设置</a>
                        </dd>
                        <dd id="option-all-portal">
                            <a class="menu" href="#">门户设置</a>
                        </dd>
                    </dl>
                </li>
                <li class="layui-nav-item">
                    <a href="javascript:;">
                        <i class="nav-icon iconfont icon-setup_fill"></i>
                        <strong>模板设置</strong>
                    </a>
                    <dl class="layui-nav-child">
                        <dd id="tpl-list">
                            <a class="menu" href="<?=url('admin/tpl/list')?>">模板管理</a>
                        </dd>
                        <dd id="option-add">
                            <a class="menu" href="<?=url('admin/option/add')?>"></a>
                        </dd>
                    </dl>
                </li>
                <li class="layui-nav-item">
                    <a href="javascript:;">
                        <i class="nav-icon iconfont icon-people_fill"></i>
                        <strong>账户设置</strong>
                    </a>
                    <dl class="layui-nav-child">
                        <dd id="option-info">
                            <a class="menu" href="<?=url('admin/option/info')?>">个人信息</a>
                        </dd>
                        <dd id="option-pwd">
                            <a class="menu" href="<?=url('admin/option/pwd')?>">密码修改</a>
                        </dd>
                    </dl>
                </li>
            </ul>
        </div>
    </div>
    <script type="text/javascript">var navHeader='nav-header-setting';</script>
    <!--//sidebar-->
    {%block@main%}
</div>