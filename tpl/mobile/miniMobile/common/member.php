<section class="userbox p5 bg-color-success clearfix">
    <div class="userbox-l fl w20">
        <img class="radius-o col-12" src="<?=$tuku.$user['avatar']?>" alt="<?=$user['username']?>的头像">
    </div>
    <div class="userbox-r fl w41 pl4 pt3">
        <p class="f60"><a class="color8" href="<?=url('@member@',['uid'=>$user['id']])?>"><?=$user['username'];?></a></p>
        <p class="f34 color3"><?php if($user['gid']<10):?>管理员<?php elseif($user['gid']==10):?>普通<?php else:?>会员<?php endif;?></p>
    </div>
</section>
<div class="user-info f32 color4 p2 bg-color6 m2 radius5">
    {%include@tmp/ad_article%}
    <div class="item mb1">
        <span class="item-l">金币 : </span>
        <span class="item-r color3"><?=$user['coin'];?></span>
    </div>
    <div class="item mb1">
        <span class="item-l">昵称 : </span>
        <span class="item-r color3"><?=$user['nickname'];?></span>
    </div>
    <div class="item mb1">
        <span class="item-l">性别 : </span>
        <span class="item-r color3"><?php $sex=['保密','男','女'];echo $sex[$user['sex']];?></span>
    </div>
    <div class="item mb1">
        <span class="item-l">生日 : </span>
        <span class="item-r color3"><?=date('Y-m-d',$user['birthday']);?></span>
    </div>
    <div class="item mb1">
        <span class="item-l">注册 : </span>
        <span class="item-r color3"><?=date('Y-m-d',$user['create_time']);?></span>
    </div>
    <div class="item">
        <span class="item-l">签名 : </span>
        <span class="item-r color3"><?=$user['signature']?></span>
    </div>
</div>