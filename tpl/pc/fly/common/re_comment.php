<?php if($re_comments): ?>
<ul class="comment-list" id="re_comment-list">
    <h2>推荐答案</h2>
    <?php foreach ($re_comments as $re_comment): ?>
    <li class="comment-list-item" id="comment-<?=$re_comment['id'];?>">
        <div class="comment-list-img">
            <a href="javascript:;">
                <?php echo $re_comment['avatar'] ? '<img src="'.$re_comment['avatar'].'" alt="'.$re_comment['username'].'">' : '<img src="/uploads/user/default.png" alt="">';?>
            </a>
        </div>
        <div class="comment-list-body">
            <div class="comment-list-info">
                <a class="comment-list-user" href="javascript:;" id="user-<?=$re_comment['id']?>"><?=$re_comment['username']?></a>
                <span class="comment-list-date"><?=date('Y-m-d H:i',$re_comment['create_time'])?></span>
            </div>
            <div class="comment-list-text" id="content-text-<?=$re_comment['id']?>"><?=$re_comment['content']?></div>
            <div class="comment-list-children">
                <?php if($re_comment['children'] >0 ):
                    if(!isset($cmModel))
                        $cmModel=app('\app\admin\model\Comment');
                    $children=$cmModel->getSome(['pid'=>$re_comment['id']],10);
                    if($children):?>
                        <ul>
                            <?php foreach ($children as $child):?>
                                <li>
                                    <div class="children-img">
                                        <a href="javascript:;"><?php echo $child['avatar'] ? '<img src="'.$child['avatar'].'" alt="'.$child['username'].'">' : '<img src="/uploads/user/default.png" alt="">';?></a>
                                    </div>
                                    <div class="children-body">
                                        <a href="javascript:;" class="children-user" id="user-<?=$child['id']?>"><?=$child['username'];?></a>:
                                        <span class="children-text" id="content-text-<?=$child['id']?>"><?=$child['content'];?></span>
                                        <span class="children-date"><?=date('Y-m-d H:i',$child['create_time']);?></span>
                                    </div>
                                </li>
                            <?php endforeach;?>
                        </ul>
                    <?php endif;endif;?>
            </div>
            <div class="comment-list-tools">
                <a href="javascript:;" title="赞" onclick="clickLikes(1,<?=$re_comment['id'];?>,this)"><i class="layui-icon layui-icon-praise"></i><span>赞同 <cite><?=$re_comment['likes'];?></cite></span></a>
                <a href="javascript:;" title="评论"><i class="layui-icon layui-icon-reply-fill"></i><span>评论 <?=$re_comment['children'];?></span></a>
                <a href="javascript:;" title="回复" class="comment-list-reply" data-id="<?=$re_comment['id'];?>" data-do="0" data-pid="<?=$re_comment['pid'];?>">回复</a>
            </div>
            <?php if($isAdmin):?>
                <div class="comment-list-admin">
                    <a href="javascript:;" title="取消推荐" class="comment-list-ctrl" data-id="<?=$re_comment['id'];?>" data-name="recommended" data-value="0">取消推荐</a>
                    <a href="javascript:;" title="回复" class="comment-list-rp" data-id="<?=$re_comment['id'];?>">回复</a>
                    <a href="javascript:;" title="编辑" class="comment-list-edit" data-id="<?=$re_comment['id'];?>">编辑</a>
                    <a href="javascript:;" title="删除" class="comment-list-del" data-id="<?=$re_comment['id'];?>">删除</a>
                </div>
            <?php endif;?>
        </div>
        <div class="layui-clear"></div>
    </li>
    <?php endforeach;?>
<?php endif;?>
</ul>
