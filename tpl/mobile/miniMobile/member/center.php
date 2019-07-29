{%extend@common/main%}
{%block@title%}
<title><?=$user['username'];?><?php if($user['nickname']){echo '['.$user['nickname'].']';}?>的个人主页_<?=$site_name?></title>
<meta name="keywords" content="<?=$user['username'].($user['nickname']?','.$user['nickname']:'');?>">
<meta name="description" content="用户[<?=$user['username'].($user['nickname']?'/'.$user['nickname']:'')?>]的个人主页，<?=$user['username']?>个人资料简介,<?=\extend\Helper::text_cut($user['more'],200)?>">
<link rel="canonical" href="<?=url('@member@',['uid'=>$user['id']],$site_url)?>">
{%end%}
{%block@article%}
<div class="yang-space">
    {%include@common/member%}
    <div class="user-info f32 color4 p2 bg-color6 m2 radius5">
        <div class="item mb1">
            <span class="item-l">简介 : </span>
            <span class="item-r color3"><?=$user['more'];?></span>
        </div>
    </div>
    <section class="swiper-container">
        <div class="tabindexList f34 p2">
            <span class="pr2 tab-title color2">评论/回答</span>
            <span class="pl2 pr2 color2">文章/主题</span>
        </div>
        <div class="swiper-wrapper color3 f30 pl2 pr2">
            <div class="swiper-slide pr2 pt2 pb2">
                <ul class="home-jieda">
                    <?php if ($comments): foreach ($comments as $item): if(!$item['oid']) continue; ?>
                        <li>
                            <p><span><?=date('Y-m-d',$item['create_time'])?></span> 在<a class="color-primary" href="<?=url("@{$item['type']}@",['id'=>$item['oid']])?>#comment-<?=$item['pid']>0?$item['pid']:$item['id']?>"><?=$item['title']?></a>中回答：</p>
                            <div class="p2 bg-color6 radius5 mt2 mb2 mr2">
                                <p><?=\extend\Helper::text_cut($item['content'],200);?><?php if ($item['is_content']):?><a href="<?=url('@comment@',['id'=>$item['id']])?>" class="pl1 f32"><i class="icon iconfont icon-comment"></i></a><?php endif;?></p>
                            </div>
                        </li>
                    <?php endforeach;?>
                        <li><p><i class="icon iconfont icon-anniu"></i> <a class="color-primary" href="<?=url('@member_comment@',['uid'=>$user['id']])?>">关于他更多的评论</a></p></li>
                    <?php else:?>
                    <li><p>这个家伙还没有发布过评论！</p></li>
                    <?php endif;?>
                </ul>
            </div>
            <div class="swiper-slide pr2 pt2 pb2">
                <ul class="jie-row">
                    <?php if($data): foreach ($data as $item):?>
                        <li class="yang-one mb2 pb2">
                            <i class="icon iconfont icon-sortlight"></i> <a href="<?=url("@{$item['type']}@",['id'=>$item['id']])?>"><?=$item['title'];?></a>
                        </li>
                    <?php endforeach;?>
                        <li class="yang-one mb1 pb1"><i class="icon iconfont icon-anniu"></i> <a class="color-primary" href="<?=url('@member_article@',['uid'=>$user['id']])?>">关于他更多的文章和主题</a></li>
                    <?php else:?>
                        <li class="yang-one mb1 pb1">这个家伙还没有发布过文章和主题！</li>
                    <?php endif;?>
                </ul>
            </div>

        </div>
    </section>
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