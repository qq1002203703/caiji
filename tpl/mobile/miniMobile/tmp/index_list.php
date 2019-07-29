<div class="f36 picbox clearfix pl1 pr1 mt1">
    <h2 class="yang-title-border f32 pl1 mb1">最新小组</h2>
    <?php foreach ($groups as $item):?>
        <div class="col-4">
            <a class="link" href="<?=url('@group_list@',['slug'=>$item['slug']])?>" title="<?=$item['name'];?>">
                <img class="h15" <?=($item['thumb'] ?'src="'.$item['thumb'].'" alt="'.$item['name'].'"' : 'src="'.$tuku.'/uploads/images/no.gif" alt="没有缩略图"')?>>
                <span class="title f30 color3"><?=$item['name'];?></span>
                <span class="sub  f28 color4 mb2">小组话题: <span class="red"><?=$item['counts']?></span></span>
            </a>
        </div>
    <?php endforeach;?>
</div>
<?php if ($articles):?>
<ul class="yang-list pl1 pr1">
    <h2 class="yang-title-border f32 pl1 mb1">最新文章</h2>
    <?php  foreach ($articles as $article):?>
        <li>
            <a class="w75 grid mb2 mt2 clearfix pr2 pl2" href="<?=url('@article@',['id'=>$article['id']],'http://'.$mobile_domain)?>">
                <div class="box col-3"><img <?=($article['thumb'] ?'src="'.$article['thumb'].'" alt="'.$article['title'].'"' : 'src="'.$tuku.'/uploads/images/no.gif" alt="没有缩略图"')?>></div>
                <div class="box col-9">
                    <h3 class="f34 pl1"><?=$article['title']?></h3>
                </div>
            </a>
        </li>
    <?php endforeach;?>
</ul>
<?php endif;?>
<?php if (isset($groupPost) && $groupPost):?>
    <ul class="yang-list2 mt2 color3 p2">
        <h2 class="yang-title-border f32 pl2 mb2">最新帖子</h2>
        <?php  foreach ($groupPost as $article): ?>
            <li class="item w75 h10 pl2 pr2 mb2 pb1">
                <div class="item-l w9">
                    <a href="<?=url('@member@',['uid'=>$article['uid']])?>"><img src="<?=($article['avatar']? $tuku.$article['avatar']:$tuku.'/uploads/user/default.png')?>" alt="<?=$article['username']?>"></a>
                </div>
                <div class="item-r w57 ml1">
                    <div class="title color3 f32">
                        <a href="<?=url('@group@',['id'=>$article['id']])?>"><?=$article['title']?></a>
                    </div>
                    <div class="sub color4 f28">
                        <span class="dib"><?=date('Y-m-d H:i',$article['create_time'])?></span>
                        <i class="icon iconfont icon-comment dib pr1 pl1"></i>
                        <span class="dib"><?=$article['comments_num']?></span>
                    </div>
                </div>
            </li>
        <?php endforeach;?>
    </ul>
<?php endif;?>
<?php if ($comments):?>
<ul class="home-jieda p2 color3 f30">
    <h2 class="yang-title-border f32 pl2 mb2">最新评论</h2>
    <?php foreach ($comments as $item): if(!$item['oid']) continue; ?>
        <li>
            <p class="p2 bg-color6 radius5 mt2 mb2 mr2"><a class="color-primary" href="<?=url('@member@',['uid'=>$item['uid']])?>"><?=$item['username']?></a> 说:<a class="color3" href="<?=url("@{$item['type']}@",['id'=>$item['oid']])?>#comment-<?=$item['pid']>0?$item['pid']:$item['id']?>">
                    <?=\extend\Helper::text_cut($item['content'],200);?>
                </a><?php if ($item['is_content']):?><a href="<?=url('@comment@',['id'=>$item['id']])?>" class="pl1 f32"><i class="icon iconfont icon-comment"></i></a><?php endif;?></p>

        </li>
    <?php endforeach;?>
</ul>
<?php endif;?>
