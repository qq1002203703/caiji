<ul class="comment-list w71 ml2 mr2 f34 color3 clearfix">
    <?php foreach ($comments as $comment):?>
    <li class="item clearfix mb3" id="comment-<?=$comment['id']?>">
        <div class="item-img w8 fl"><a href="<?=url('@member@',['uid'=>$comment['uid']])?>"><?php echo $comment['avatar'] ? '<img class="w8 h8 radius5" src="'.$tuku.$comment['avatar'].'" alt="'.$comment['username'].'">' : '<img src="'.$tuku.'/uploads/user/default.png" alt="">';?></a></div>
        <div class="w60 pl2 pr1 fl">
            <div class="item-info">
                <a class="comment-list-user color-primary f32" href="<?=url('@member@',['uid'=>$comment['uid']])?>" id="user-<?=$comment['id']?>"><?=$comment['username']?></a>
                <?php if ($comment['is_content']):?><a href="<?=url('@comment@',['id'=>$comment['id']])?>" class="pl1 f32"><i class="icon iconfont icon-dialog"></i></a><?php endif;?>
            </div>
            <div class="item-text color3 f30"  id="content-text-<?=$comment['id']?>">
                <?=$comment['content']?>
            </div>
            <div class="item-btn clearfix color4 f28 pt2">
                <span class="color5"><?=date('Y-m-d H:i',$comment['create_time'])?></span>
                <span class="fr ml2"><i class="f32 pr1 icon iconfont icon-comment"></i><?=$comment['children'];?></span>
                <span class="fr ml2"><i class="f32 pr1 icon iconfont icon-appreciate"></i><?=$comment['likes'];?></span>
            </div>
            <?php if($comment['children'] >0 ):
            if(!isset($cmModel)){$cmModel=app('\app\admin\model\Comment');}
            $children=$cmModel->getSome(['c.pid'=>$comment['id']],10);
            if($children):?>
                <div class="item-children bg-color6 p1 f28">
                    <?php foreach ($children as $child):?>
                    <p class="pt1 pb1"><a class="color-primary" href="<?=url('@member@',['uid'=>$child['uid']])?>"><?=$child['username'];?>：</a><?=strip_tags($child['content']);?></p>
                    <?php endforeach;?>
                </div>
            <?php endif;endif;?>
        </div>
    </li>
    <?php endforeach;?>
</ul>
