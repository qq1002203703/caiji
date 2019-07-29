{%extend@common/main%}
{%block@title%}
<title><?=$user['username'];?>发表的评论|回答|点评_<?=$site_name?></title>
<meta name="keywords" content="<?=$user['username'];?>发表的评论|回答|点评">
<meta name="description" content="<?=$site_name?>的用户[<?=$user['username']?>]在本站发表过的评论|回答|点评归档，也即是对他个人的评论|回答|点评作的一些整理，并以列表形式展示出来。">
<link rel="canonical" href="<?=url('@member_comment@',['uid'=>$user['id']],$site_url)?>">
{%end%}
{%block@article%}
<div class="yang-space">
    {%include@common/member%}
    <div class="f34 p2">
        <span class="pr2 pl1 color2 yang-title-border yang-one"><a href="<?=url('@member@',['uid'=>$user['id']])?>"><?=$user['username']?></a> 发表的评论|回答|点评</span>
    </div>
    <div class="color3 f30 pl2 pr2">
        <div class="pr2 pt2 pb2">
            <ul class="home-jieda">
                <?php if ($comments): foreach ($comments as $item): if(!$item['oid']) continue; ?>
                    <li>
                        <p><span><?=date('Y-m-d',$item['create_time'])?></span> 在<a class="color-primary" href="<?=url("@{$item['type']}@",['id'=>$item['oid']])?>#comment-<?=$item['pid']>0?$item['pid']:$item['id']?>"><?=$item['title']?></a>中回答：</p>
                        <div class="p2 bg-color6 radius5 mt2 mb2 mr2">
                            <p><?=\extend\Helper::text_cut($item['content'],200);?><?php if ($item['is_content']):?><a href="<?=url('@comment@',['id'=>$item['id']])?>" class="pl1 f32"><i class="icon iconfont icon-comment"></i></a><?php endif;?></p>
                        </div>
                    </li>
                <?php endforeach;else:?>
                    <li><p>这个家伙还没有发表过评论和回答！</p></li>
                <?php endif;?>
            </ul>
        </div>
        <?=$page?>
    </div>
</div>
{%end%}
