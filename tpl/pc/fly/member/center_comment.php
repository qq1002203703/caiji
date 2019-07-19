{%extend@common/base_portal%}
{%block@title%}
<title><?=$user['username'];?>发表的评论|回答|点评_<?=$site_name?></title>
<meta name="keywords" content="<?=$user['username'];?>发表的评论|回答|点评">
<meta name="description" content="<?=$site_name?>的用户[<?=$user['username']?>]在本站发表过的评论|回答|点评归档，也即是对他个人的评论|回答|点评作的一些整理，并以列表形式展示出来。">
<meta name="mobile-agent" content="format=html5;url=<?=url('@member_comment@',['uid'=>$user['id']],'http://'.$mobile_domain)?>">
<link rel="alternate" media="only screen and(max-width: 750px)" href="<?=url('@member_comment@',['uid'=>$user['id']],'http://'.$mobile_domain)?>">
{%end%}
{%block@article%}
<div class="layui-container yang-space">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md3">
            {%include@common/member%}
        </div>
        <div class="layui-col-md9">
            <div class="fly-panel fly-panel-user" pad20>
                <ul class="home-jieda">
                    <h2 class="common-title"><a href="<?=url('@member@',['uid'=>$user['id']])?>"> <?=$user['username']?></a>发表的评论|回答|点评</h2>
                    <?php if($comments):foreach ($comments as $item): if(!$item['oid']) continue; ?>
                        <li>
                            <p><span><?=date('Y-m-d H:i',$item['create_time'])?></span>在<a href="<?=url("@{$item['type']}@",['id'=>$item['oid']])?>#comment-<?=$item['pid']>0?$item['pid']:$item['id']?>" target="_blank"><?=$item['title']?></a>中回答：</p>
                            <div class="home-dacontent">
                                <?=$item['content']?>
                            </div>
                        </li>
                    <?php endforeach;else:?>
                    <li>这个家伙还没有表过评论和回答！</li>
                    <?php endif;?>
                </ul>
                <?=$page?>
            </div>
        </div>
    </div>
</div>
{%end%}

{%block@javascript%}
{%end%}
