		<?php if($videos):?>
            <ul class="list-movie pl20">
                <h2 class="title">西游记最新影视</h2>
                <?php $type=['电影','电视']; foreach ($videos as $item):?>
                    <li class="item w180 mr15">
                        <a class="link" href="<?=url('@video@',['id'=>$item['id']])?>" title="<?=$item['title'];?>">
                            <div class="pic h240">
                                <img <?=($item['thumb'] ?'src="'.$item['thumb'].'" alt="'.$item['title'].'"' : 'src="/uploads/images/no.gif" alt="没有缩略图"')?>>
                                <span class="icon"><?=$type[$item['type']]?></span>
                            </div>
                            <div class="text">
                                <span class="title"><?=$item['title'];?></span>
                                <span class="sub">主演:<?=$item['actor'];?></span>
                                <span class="score"><?=$item['score'];?></span>
                            </div>
                        </a>
                    </li>
                <?php endforeach;?>
            </ul>
        <?php endif;?>
        <?php if($groups):?>
            <ul class="list-movie pl20">
                <h2 class="title">热门小组</h2>
                <?php foreach ($groups as $item):?>
                    <li class="item mr15" style="width: 170px;margin-bottom: 18px;">
                        <a class="link" href="<?=url('@goods_list@',['slug'=>$item['slug']])?>" title="<?=$item['name'];?>">
                            <div class="layui-row">
                                <div class="layui-col-md4">
                                    <div class="pic" style="height: 58px">
                                        <img <?=($item['thumb'] ?'src="'.$item['thumb'].'" alt="'.$item['name'].'"' : 'src="/uploads/images/no.gif" alt="没有缩略图"')?>>
                                    </div>
                                </div>
                                <div class="layui-col-md8">
                                    <div class="text">
                                        <span class="title"><?=$item['name'];?></span>
                                        <span class="sub">小组话题 : <span class="red"><?=$item['counts']?></span></span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                <?php endforeach;?>
            </ul>
        <?php endif;?>
        <?php if ($articles):?>
            <ul class="list-block pl20">
                <h2 class="list-block-title">最新文章</h2>
                <?php foreach ($articles as $article):?>
                    <li class="list-block-item">
                        <a class="list-block-pic" href="<?=url('@article@',['id'=>$article['id']])?>"><img <?=($article['thumb'] ?'src="'.$article['thumb'].'" alt="'.$article['title'].'"' : 'src="'.$tuku.'/uploads/images/notpic.gif" alt="没有缩略图"')?> class="item-img"></a>
                        <div class="list-block-detail">
                            <h3 class="item-title"><a href="<?=url('@article@',['id'=>$article['id']])?>" ><?=$article['title']?></a></h3>
                            <p class="item-desc layui-hide-xs"><?=($article['excerpt'] ? :\extend\Helper::text_cut($article['content'],200))?></p>
                            <p class="item-about"><a class="item-user" href="<?=url('@article_list@',['slug'=>$article['category_slug']])?>"><?=$article['category_name']?></a><span class="item-date"><?=date('Y-m-d H:i',$article['create_time'])?></span></p>
                        </div>
                    </li>
                <?php endforeach;?>
            </ul>
        <?php endif;?>