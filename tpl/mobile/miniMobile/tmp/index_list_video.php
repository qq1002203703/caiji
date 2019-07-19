<?php if($videos):?>
    <h2 class="yang-title-border f32 pl1 mb1 ml1">西游记最新影视</h2>
    <div class="picbox clearfix pl1">
        <?php $type=['电影','电视']; foreach ($videos as $item):?>
            <div class="col-4">
                <a class="link" href="<?=url('@video@',['id'=>$item['id']])?>" title="<?=$item['title'];?>">
                    <img class="h34" <?=($item['thumb'] ?'src="'.$item['thumb'].'" alt="'.$item['title'].'"' : 'src="/uploads/images/no.gif" alt="没有缩略图"')?>>
                    <span class="icon f28 t-c color8 pl1 pr1"><?=$type[$item['type']]?></span>
                    <span class="title f30 color3"><?=$item['title'];?></span>
                    <span class="sub  f28 color4 mb2"><?=($item['actor']?:$item['director']);?></span>
                </a>
            </div>
        <?php endforeach;?>
    </div>
<?php endif;?>
<?php if($groups):?>
    <div class="f36 picbox clearfix pl1 pr1 mt1">
        <h2 class="yang-title-border f32 pl1 mb1">最新小组</h2>
        <?php foreach ($groups as $item):?>
            <div class="col-4">
                <a class="link" href="<?=url('@goods_list@',['slug'=>$item['slug']])?>" title="<?=$item['name'];?>">
                    <img class="h15" <?=($item['thumb'] ?'src="'.$item['thumb'].'" alt="'.$item['name'].'"' : 'src="/uploads/images/no.gif" alt="没有缩略图"')?>>
                    <span class="title f30 color3"><?=$item['name'];?></span>
                    <span class="sub  f28 color4 mb2">小组话题: <span class="red">1211<?=$item['counts']?></span></span>
                </a>
            </div>
        <?php endforeach;?>
    </div>
<?php endif;?>
<ul class="yang-list pl1 pr1">
    <h2 class="yang-title-border f32 pl1 mb1">最新文章</h2>
    <?php  foreach ($articles as $article):?>
        <li class="pl1 pr1">
            <a class="w75 grid mb2 mt2 clearfix" href="<?=url('@article@',['id'=>$article['id']])?>">
                <div class="box col-3"><img <?=($article['thumb'] ?'src="'.$article['thumb'].'" alt="'.$article['title'].'"' : 'src="'.$tuku.'/uploads/images/notpic.gif" alt="没有缩略图"')?>></div>
                <div class="box col-9 pl2">
                    <h3 class="f34"><?=$article['title']?></h3>
                </div>
            </a>
        </li>
    <?php endforeach;?>
</ul>