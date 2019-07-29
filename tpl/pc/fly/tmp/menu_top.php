<?php if($isLogin):?>
    <!-- 登入后的状态 -->
    <li class="layui-nav-item">
        <a class="fly-nav-avatar" href="javascript:;">
            <cite class="layui-hide-xs"><?=\core\Session::get('username')?></cite>
            <i class="iconfont icon-renzheng layui-hide-xs"></i>
            <i class="layui-badge fly-badge-vip layui-hide-xs">VIP3</i>
            <img src="<?=$tuku.(\core\Session::get('user.avatar')?:'/uploads/user/default.png')?>">
        </a>
        <dl class="layui-nav-child">
            <dd><a href="<?=url('portal/user/info')?>"><i class="layui-icon layui-icon-set-sm"></i>个人设置</a></dd>
            <dd><a href="<?=url('portal/user/message')?>"><i class="iconfont icon-tongzhi" style="top: 4px;"></i>我的消息</a></dd>
            <dd><a href="<?=url('portal/user/myorder')?>"><i class="layui-icon layui-icon-home" style="margin-left: 2px; font-size: 22px;"></i>我的订单</a></dd>
            <hr style="margin: 5px 0;">
            <dd><a href="<?=url('portal/index/logout')?>" style="text-align: center;">退出</a></dd>
        </dl>
    </li>
<?php else:?>
    <!-- 未登入的状态 -->
    <li class="layui-nav-item">
        <a class="iconfont icon-touxiang layui-hide-xs" href="<?=url('portal/index/login')?>"></a>
    </li>
    <li class="layui-nav-item">
        <a href="<?=url('portal/index/login')?>">登入</a>
    </li>
    <li class="layui-nav-item">
        <a href="<?=url('portal/index/reg')?>">注册</a>
    </li>
<?php endif; ?>