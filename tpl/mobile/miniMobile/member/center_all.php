{%extend@common/main%}
{%block@title%}
<title>会员中心_活跃用户列表_<?=$site_name?></title>
<meta name="keywords" content="<?=$title?>">
<meta name="description" content="这里是<?=$site_name?>的活跃用户列表，你可以找到你喜欢的人进行关注，一起和他/她进行交流、互动。">
<link rel="canonical" href="<?=url('@member_all@',[],$site_url)?>">
{%end%}
{%block@article%}
<div class="yang-bread f30 pl1 mt1"><a href="/">首页</a>&gt;活跃会员&gt;列表</div>
<p class="f30 color4 pl2 mt1 mb2 pr1">这里是<?=$site_name?>的活跃用户列表，你可以找到你喜欢的人进行关注，一起和他/她进行交流、互动。</p>
<div class="yang-space">
    <div class="f36 picbox clearfix pl1 pr1 mt1">
        <?php $groupList=\core\Conf::get('groupList','portal');foreach ($data as $item):?>
            <div class="col-4">
                <a class="link" href="<?=url('@member@',['uid'=>$item['id']])?>" title="<?=$item['username'];?>">
                    <img class="h15" <?=($item['avatar'] ?'src="'.$tuku.$item['avatar'].'" alt="'.$item['username'].'"' : 'src="'.$tuku.'/uploads/images/no.gif" alt="没有缩略图"')?>>
                    <span class="title f30 color3"><?=$item['username'];?></span>
                    <span class="sub  f28 color4 mb2"><?=$groupList[$item['gid']]?></span>
                </a>
            </div>
        <?php endforeach;?>
    </div>
    <?=$page;?>
</div>
{%end%}

{%block@javascript%}
<script type="text/javascript">
    var swiper = new Swiper('.swiper-container', {on: {
            slideChangeTransitionStart: function() {
                $(".tabindexList span").removeClass("tab-title").eq(this.activeIndex).addClass("tab-title");
            }
        }});
    $(".tabindexList span").click(function() {
        var index = $(this).index();
        $(".tabindexList span").removeClass("tab-title").eq(index).addClass("tab-title");
        swiper.slideTo(index, 300, false);
    });
</script>
{%end%}