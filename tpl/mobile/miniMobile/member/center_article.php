{%extend@common/main%}
{%block@title%}
<title><?=$user['username'];?>发布的文章和主题_<?=$site_name?></title>
<meta name="keywords" content="<?=$user['username'];?>发布的文章和主题">
<meta name="description" content="<?=$site_name?>的用户[<?=$user['username']?>]在本站发表布的文章和主题归档，也即是对他个人的文章和主题作的一些整理，并以列表形式展示出来。">
<link rel="canonical" href="<?=url('@member_article@',['uid'=>$user['id']],$site_url)?>">
{%end%}
{%block@article%}
<div class="yang-space">
    {%include@common/member%}
    <div class="f34 p2">
        <h2 class="pr2 pl1 color2 yang-title-border yang-one"><a href="<?=url('@member@',['uid'=>$user['id']])?>"> <?=$user['username']?></a> 发布的文章和主题</h2>
    </div>
    <div class="color3 f30 pl2 pr2">
        <div class="swiper-slide pr2 pt2 pb2">
            <ul class="jie-row">
                <?php if($data): foreach ($data as $item):?>
                    <li class="yang-one mb1 pb1">
                        <i class="icon iconfont icon-sortlight"></i> <a href="<?=url("@{$item['type']}@",['id'=>$item['id']])?>"><?=$item['title'];?></a>
                    </li>
                <?php endforeach;else:?>
                    <li class="yang-one mb1 pb1">这个家伙还没有发布过文章和主题！</li>
                <?php endif;?>
            </ul>
        </div>
        <?=$page?>
    </div>
</div>
{%end%}
