<?php if($groups):?>
    <ul class="list-movie pl20">
        <h2 class="title">热门小组</h2>
        <?php foreach ($groups as $item):?>
            <li class="item mr15" style="width: 170px;margin-bottom: 18px;">
                <a class="link" href="<?=url('@group_list@',['slug'=>$item['slug']])?>" title="<?=$item['name'];?>">
                    <div class="layui-row">
                        <div class="layui-col-md4">
                            <div class="pic" style="height: 58px">
                                <img <?=($item['thumb'] ?'src="'.$item['thumb'].'" alt="'.$item['name'].'"' : 'src="'.$tuku.'/uploads/images/no.gif" alt="没有缩略图"')?>>
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
<?php if (isset($goods) && $goods):?>
    <ul class="index-list pl20">
        <h2 class="title">股票教程与工具</h2>
        <?php foreach ($goods as $good):?>
            <li>
                <a class="" href="<?=url('@goods@',['id'=>$good['id']])?>"><img <?=($good['thumb'] ?'src="'.$good['thumb'].'_200x200.jpg" alt="'.$good['title'].'"' : 'src="/uploads/images/no.gif" alt="没有缩略图"')?> class="item-img"></a>
                <a href="<?=url('@goods@',['id'=>$good['id']])?>"><?=$good['title']?></a>
            </li>
        <?php endforeach;?>
    </ul>
<?php endif;?>
<?php if ($articles):?>
    <ul class="list-block pl20">
        <h2 class="list-block-title">最新文章</h2>
        <?php foreach ($articles as $article):?>
            <li class="list-block-item">
                <a class="list-block-pic" href="<?=url('@article@',['id'=>$article['id']])?>"><img <?=($article['thumb'] ?'src="'.$article['thumb'].'_200x200.jpg" alt="'.$article['title'].'"' : 'src="/uploads/images/no.gif" alt="没有缩略图"')?> class="item-img"></a>
                <div class="list-block-detail">
                    <h3 class="item-title"><a href="<?=url('@article@',['id'=>$article['id']])?>" ><?=$article['title']?></a></h3>
                    <p class="item-desc layui-hide-xs"><?=($article['excerpt'] ? :\extend\Helper::text_cut($article['content'],200))?></p>
                    <p class="item-about"><a class="item-user" href="<?=url('@article_list@',['slug'=>$article['category_slug']])?>"><?=$article['category_name']?></a><span class="item-date"><?=date('Y-m-d H:i',$article['create_time'])?></span></p>
                </div>
            </li>
        <?php endforeach;?>
    </ul>
<?php endif;?>

 <div class="fly-panel fly-panel-user" pad20>
     <div class="layui-row layui-col-space15">
         <div class="layui-col-md6">
             <ul class="fly-list pl20">
                 <h2 class="common-title layui-bg-green">最新帖子</h2>
                 <?php if(isset($groupPost) && $groupPost):foreach ($groupPost as $item):?>
                     <li>
                         <a href="<?=url('@member@',['uid'=>$item['uid']])?>" class="fly-avatar">
                             <img src="<?=$tuku.($item['avatar']? :'/uploads/user/default.png')?>" alt="<?=$item['username']?>">
                         </a>
                         <div class="fly-list-title">
                             <a href="<?=url('@group_list@',['slug'=>$item['category_slug']])?>" class="layui-badge"><?=$item['category_name']?></a>
                             <a href="<?=url('@group@',['id'=>$item['id']])?>"><?=$item['title']?></a>
                         </div>
                         <div class="fly-list-info">
                             <a href="<?=url('@member@',['uid'=>$item['uid']])?>"><cite><?=$item['username']?></cite></a>
                             <span><?=date('Y-m-d H:i',$item['create_time'])?></span>
                             <span class="fly-list-nums">
                            <i class="iconfont icon-pinglun1" title="评论"></i> <?=$item['comments_num']?>
                        </span>
                         </div>
                     </li>
                 <?php endforeach;endif;?>
             </ul>
         </div>
         <div class="layui-col-md6">
             <ul class="home-jieda">
                 <h2 class="common-title layui-bg-green">最新评论</h2>
                 <?php if (isset($comments) && $comments):foreach ($comments as $item): if(!$item['oid']) continue;?>
                     <li>
                         <p><a class="green" href="<?=url('@member@',['uid'=>$item['uid']])?>"><?=$item['username']?></a>说:<a class="color3" href="<?=url("@{$item['type']}@",['id'=>$item['oid']])?>#comment-<?=$item['pid']>0?$item['pid']:$item['id']?>" target="_blank"><?=\extend\Helper::text_cut($item['content'],200);?></a><?php if($item['is_content']):?><a href="<?=url('@comment@',['id'=>$item['id']])?>"><i class="iconfont icon-lianjie"></i></a><?php endif;?></p>

                     </li>
                 <?php endforeach;endif;?>
             </ul>
         </div>

     </div>

 </div>

<?php if(isset($bbsData) && $bbsData):?>
    <ul class="fly-list pl20">
        <h2 class="text-title">最新讨论</h2>
        <?php foreach ($bbsData as $item):?>
            <li>
                <a href="javascript:;" class="fly-avatar">
                    <img src="<?=($item['avatar']? :'/uploads/user/default.png')?>" alt="<?=$item['username']?>">
                </a>
                <div class="fly-list-title">
                    <a href="<?=url('@bbs_list@',['id'=>$item['category_id']])?>" class="layui-badge"><?=$item['category_name']?></a>
                    <a href="<?=url('@bbs_show@',['id'=>$item['id']])?>"><?=$item['title']?></a>
                </div>
                <div class="fly-list-info">
                    <a href="javascript:;"><cite><?=$item['username']?></cite></a>
                    <span><?=date('Y-m-d H:i',$item['create_time'])?></span>
                    <span class="fly-list-nums">
                            <i class="iconfont icon-pinglun1" title="评论"></i> <?=$item['comments_num']?>
                        </span>
                </div>
            </li>
        <?php endforeach;?>
    </ul>
<?php endif;?>