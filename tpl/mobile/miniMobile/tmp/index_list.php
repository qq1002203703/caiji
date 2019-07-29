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
<?php if ($comments):?>
<ul class="home-jieda p2 color3 f30">
    <h2 class="yang-title-border f32 pl1 mb1">最新评论</h2>
    <?php foreach ($comments as $item): if(!$item['oid']) continue; ?>
        <li>
            <p><a class="color-primary" href="<?=url('@member@',['uid'=>$item['uid']])?>"><?=$item['username']?></a> 在【<a class="color3" href="<?=url("@{$item['type']}@",['id'=>$item['oid']])?>#comment-<?=$item['pid']>0?$item['pid']:$item['id']?>"><?=$item['title']?></a>】中回答：</p>
            <div class="p2 bg-color6 radius5 mt2 mb2 mr2">
                <p><?=\extend\Helper::text_cut($item['content'],200);?><?php if ($item['is_content']):?><a href="<?=url('@comment@',['id'=>$item['id']])?>" class="pl1 f32"><i class="icon iconfont icon-comment"></i></a><?php endif;?></p>
            </div>
        </li>
    <?php endforeach;?>
</ul>
<?php endif;?>
