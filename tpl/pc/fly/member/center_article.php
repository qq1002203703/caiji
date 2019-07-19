{%extend@common/base_portal%}
{%block@title%}
<title><?=$user['username'];?>发布的文章和主题_<?=$site_name?></title>
<meta name="keywords" content="<?=$user['username'];?>发布的文章和主题">
<meta name="description" content="<?=$site_name?>的用户[<?=$user['username']?>]在本站发表布的文章和主题归档，也即是对他个人的文章和主题作的一些整理，并以列表形式展示出来。">
<meta name="mobile-agent" content="format=html5;url=<?=url('@member_article@',['uid'=>$user['id']],'http://'.$mobile_domain)?>">
<link rel="alternate" media="only screen and(max-width: 750px)" href="<?=url('@member_article@',['uid'=>$user['id']],'http://'.$mobile_domain)?>">
{%end%}
{%block@article%}
<div class="layui-container yang-space">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md3">
            {%include@common/member%}
        </div>
        <div class="layui-col-md9">
            <div class="fly-panel fly-panel-user" pad20>
                <ul class="jie-row">
                    <h2 class="common-title"><a href="<?=url('@member@',['uid'=>$user['id']])?>"> <?=$user['username']?></a>发表的文章和主题</h2>
                    <?php if($data):foreach ($data as $item):?>
                        <li><span class="layui-icon layui-icon-shrink-right"></span><a href="<?=url("@{$item['type']}@",['id'=>$item['id']])?>"><?=$item['title'];?></a><i><?=date('Y-m-d',$item['create_time'])?> </i><em class="layui-hide-xs"><?=$item['views']?>阅/<?=$item['comments_num']?>答</em></li>
                    <?php endforeach;else:?>
                    <li>这个家伙还没有发布过文章和主题！</li>
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
