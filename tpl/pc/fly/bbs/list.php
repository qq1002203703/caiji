{%extend@common/bbs%}
{%block@article%}
<div class="layui-col-md8">
    <div class="fly-panel" style="margin-bottom: 0;">
        {%include@common/fly-list-title%}
        <?php if($data):?>
            <ul class="fly-list">
                <?php foreach ($data as $item):?>
                    <li>
                        <a href="#user/home.html" class="fly-avatar">
                            <img src="/<?=($item['avatar']? :'uploads/user/default.png')?>" alt="<?=$item['username']?>">
                        </a>
                        <h2>
                            <a href="<?=url('@bbscategory@',['id'=>$item['category_id']])?>" class="layui-badge"><?=$item['category_name']?></a>
                            <a href="<?=url('@bbspost@',['id'=>$item['id']])?>"><?=$item['title']?></a>
                        </h2>
                        <div class="fly-list-info">
                            <a href="#user/home.html" link>
                                <cite><?=$item['username']?></cite>
                                <!--
                                <i class="iconfont icon-renzheng" title="认证信息：XXX"></i>
                                <i class="layui-badge fly-badge-vip">VIP3</i>
                                -->
                            </a>
                            <span><?=date('Y-m-d H:i',$item['create_time'])?></span>

                            <span class="fly-list-kiss layui-hide-xs" title="悬赏金币"><i class="iconfont icon-kiss"></i> <?=$item['coin']?></span>
                            <!--<span class="layui-badge fly-badge-accept layui-hide-xs">已结</span>-->
                            <span class="fly-list-nums">
                <i class="iconfont icon-pinglun1" title="回答"></i> <?=$item['comments_num']?>
              </span>
                        </div>
                        <div class="fly-list-badge">
                            <?php if($item['is_top']):?>
                                <span class="layui-badge layui-bg-black">置顶</span>
                            <?php endif;?>
                            <?php if($item['recommended']):?>
                                <span class="layui-badge layui-bg-red">精帖</span>
                            <?php endif;?>
                        </div>
                    </li>
                <?php endforeach;?>
            </ul>
        <?php endif;?>
        <!-- <div class="fly-none">没有相关数据</div> -->
        <div style="text-align: center">
            <?=$page?>
            <?php //dump($page);?>
        </div>
    </div>
</div>
{%end%}

