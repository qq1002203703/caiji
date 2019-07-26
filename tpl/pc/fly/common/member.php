<div class="sidebar fly-panel">
    <div class="user-avatar">
        <a href="<?=url('@member@',['uid'=>$user['id']])?>"><img src="<?=$tuku.$user['avatar']?>" alt="<?=$user['username']?>的头像"></a>
    </div>
    <h1 class="username"><?=$user['username'];?></h1>
    <div class="item">
        <span class="item-l">种类 : </span>
        <span class="item-r"><?php if($user['gid']<10):?>管理员<?php elseif($user['gid']==10):?>普通<?php else:?>会员<?php endif;?></span>
    </div>
    <div class="item">
        <span class="item-l">金币 : </span>
        <span class="item-r"><?=$user['coin'];?></span>
    </div>
    <div class="item">
        <span class="item-l">昵称 : </span>
        <span class="item-r"><?=$user['nickname'];?></span>
    </div>
    <div class="item">
        <span class="item-l">性别 : </span>
        <span class="item-r"><?php $sex=['保密','男','女'];echo $sex[$user['sex']];?></span>
    </div>
    <div class="item">
        <span class="item-l">生日 : </span>
        <span class="item-r"><?=date('Y-m-d',$user['birthday']);?></span>
    </div>
    <div class="item">
        <span class="item-l">现居 : </span>
        <span class="item-r"><?=$user['city']?></span>
    </div>
    <div class="item">
        <span class="item-l">注册 : </span>
        <span class="item-r"><?=date('Y-m-d',$user['create_time']);?></span>
    </div>
    <div class="item">
        <span class="item-l">签名 : </span>
        <span class="item-r"><?=$user['signature']?></span>
    </div>
</div>