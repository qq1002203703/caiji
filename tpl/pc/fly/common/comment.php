<ul class="comment-list" id="comment-list">
<?php if($comments): foreach ($comments as $comment):?>
    <li class="comment-list-item" id="comment-<?=$comment['id'];?>">
        <div class="comment-list-img">
            <a href="<?=url('@member@',['uid'=>$comment['uid']])?>">
                <?php echo $comment['avatar'] ? '<img src="'.$tuku.$comment['avatar'].'" alt="'.$comment['username'].'">' : '<img src="'.$tuku.'/uploads/user/default.png" alt="">';?>
            </a>
        </div>
        <div class="comment-list-body">
            <div class="comment-list-info">
                <a class="comment-list-user" href="<?=url('@member@',['uid'=>$comment['uid']])?>" id="user-<?=$comment['id']?>"><?=$comment['username']?></a>
                <span class="comment-list-date"><?=date('Y-m-d H:i',$comment['create_time'])?></span>
            </div>
            <div class="comment-list-text" id="content-text-<?=$comment['id']?>"><?=$comment['content'];?><?php if($comment['is_content']):?><a href="<?=url('@comment@',['id'=>$comment['id']])?>"><i class="iconfont icon-lianjie"></i></a><?php endif;?></div>
            <div class="comment-list-children">
                <?php if($comment['children'] >0 ):
                    if(!isset($cmModel))
                        $cmModel=app('\app\admin\model\Comment');
                    $children=$cmModel->getSome(['c.pid'=>$comment['id']],10);
                    if($children):?>
                        <ul>
                            <?php foreach ($children as $child):?>
                                <li>
                                    <div class="children-img">
                                        <a href="<?=url('@member@',['uid'=>$child['uid']])?>"><?php echo $child['avatar'] ? '<img src="'.$tuku.$child['avatar'].'" alt="'.$child['username'].'">' : '<img src="'.$tuku.'/uploads/user/default.png" alt="">';?></a>
                                    </div>
                                    <div class="children-body">
                                        <a href="<?=url('@member@',['uid'=>$child['uid']])?>" class="children-user" id="user-<?=$child['id']?>"><?=$child['username'];?></a>:
                                        <span class="children-text" id="content-text-<?=$child['id']?>"><?=$child['content'];?></span>
                                        <span class="children-date"><?=date('Y-m-d H:i',$child['create_time']);?></span>
                                    </div>
                                </li>
                            <?php endforeach;?>
                        </ul>
                    <?php endif;endif;?>
            </div>
            <div class="comment-list-tools">
                <a href="javascript:;" title="赞" onclick="clickLikes(1,<?=$comment['id'];?>,this)"><i class="layui-icon layui-icon-praise"></i><span>赞同 <cite><?=$comment['likes'];?></cite></span></a>
                <a href="javascript:;" title="评论"><i class="layui-icon layui-icon-reply-fill"></i><span>评论 <?=$comment['children'];?></span></a>
                <a href="javascript:;" title="回复" class="comment-list-reply" data-id="<?=$comment['id'];?>" data-do="0" data-pid="<?=$comment['pid'];?>">回复</a>
            </div>
            <?php if($isAdmin):?>
                <div class="comment-list-admin">
                    <a href="javascript:;" title="推荐" class="comment-list-ctrl" data-id="<?=$comment['id'];?>" data-name="recommended" data-value="1">推荐</a>
                    <a href="javascript:;" title="回复" class="comment-list-rp" data-id="<?=$comment['id'];?>">回复</a>
                    <a href="javascript:;" title="编辑" class="comment-list-edit" data-id="<?=$comment['id'];?>">编辑</a>
                    <a href="javascript:;" title="删除" class="comment-list-del" data-id="<?=$comment['id'];?>">删除</a>
                </div>
            <?php endif;?>
        </div>
        <div class="layui-clear"></div>
    </li>
    <?php endforeach;?>
    <?php if($isMore):?>
    <div style="text-align: center">
        <a href="javascript:;" id="comment-list-more" class="layui-btn layui-btn-primary"><cite>加载更多</cite></a>
    </div>
    <?php endif;?>
<?php else: ?>
    <li class="comment-list-item"><div class="comment-list-none">消灭零回复</div></li>
<?php endif;?>
</ul>

<div class="layui-form layui-form-pane" id="comment-form">
    <form action="<?=url('api/comment/reply')?>" method="post">
        <div class="layui-form-item layui-form-text">
            <a name="comment"></a>
            <div class="layui-input-block">
                <textarea id="L_content" name="content" required lay-verify="required" placeholder="请输入内容"  class="layui-textarea fly-editor" style="height: 150px;"></textarea>
            </div>
        </div>
        <div class="layui-form-item">
            <input type="hidden" name="id" value="<?=$data['id']?>">
            <input type="hidden" id="token" name="__token__" value="<?=\app\common\ctrl\Func::token();?>">
            <input type="hidden" name="table_name" value="portal_post">
            <input type="hidden" name="pid" value="0">
            <button class="layui-btn" lay-filter="post" lay-submit>提交回复</button>
        </div>
    </form>
</div>
