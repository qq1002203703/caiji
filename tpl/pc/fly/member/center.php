{%extend@common/base_portal%}
{%block@title%}
<title><?=$user['username'];?><?php if($user['nickname']){echo '['.$user['nickname'].']';}?>的个人主页_<?=$site_name?></title>
<meta name="keywords" content="<?=$user['username'].($user['nickname']?','.$user['nickname']:'');?>">
<meta name="description" content="用户[<?=$user['username'].($user['nickname']?'/'.$user['nickname']:'')?>]的个人主页，<?=$user['username']?>个人资料简介,<?=\extend\Helper::text_cut($user['more'],200)?>">
<meta name="mobile-agent" content="format=html5;url=<?=url('@member@',['uid'=>$user['id']],'http://'.$mobile_domain)?>">
<link rel="alternate" media="only screen and(max-width: 750px)" href="<?=url('@member@',['uid'=>$user['id']],'http://'.$mobile_domain)?>">
{%end%}
{%block@article%}
<div class="layui-container yang-space">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md3">
            {%include@common/member%}
        </div>
        <div class="layui-col-md9">
            <div class="fly-panel fly-panel-user" pad20>
                <div class="detail-body" style="background-color: #eee;padding: 10px;">
                    <p>个人简介：</p>
                    <?=$user['more']?>
                </div>
                <div class="layui-tab layui-tab-brief" lay-filter="user">
                    <ul class="layui-tab-title" id="LAY_mine">
                        <li class="layui-this">评论</li>
                        <li>正文</li>
                    </ul>
                    <div class="layui-tab-content">
                        <div class="layui-tab-item layui-show">
                            <ul class="home-jieda">
                                <?php if($comments): foreach ($comments as $item): if(!$item['oid']) continue; ?>
                                <li>
                                    <p><span><?=date('Y-m-d H:i',$item['create_time'])?></span>在<a href="<?=url("@{$item['type']}@",['id'=>$item['oid']])?>#comment-<?=$item['pid']>0?$item['pid']:$item['id']?>" target="_blank"><?=$item['title']?></a>中回答：</p>
                                    <div class="home-dacontent">
                                       <p><?=\extend\Helper::text_cut($item['content'],200);?><?php if($item['is_content']):?><a href="<?=url('@comment@',['id'=>$item['id']])?>"><i class="iconfont icon-lianjie"></i></a><?php endif;?></p>
                                    </div>
                                </li>
                                <?php endforeach;?>
                                <li><p><i class="layui-icon layui-icon-spread-left"></i> <a href="<?=url('@member_comment@',['uid'=>$user['id']])?>">关于他更多的评论</a></p></li>
                                <?php else:?>
                                <li><p>这个家伙还没有发布过评论！</p></li>
                                <?php endif;?>
                            </ul>
                        </div>
                        <div class="layui-tab-item">
                            <ul class="jie-row">
                                <?php if($data):foreach ($data as $item):?>
                                    <li><span class="layui-icon layui-icon-shrink-right"></span><a href="<?=url("@{$item['type']}@",['id'=>$item['id']])?>"><?=$item['title'];?></a><i><?=date('Y-m-d',$item['create_time'])?> </i><em class="layui-hide-xs"><?=$item['views']?>阅/<?=$item['comments_num']?>答</em></li>
                                <?php endforeach;?>
                                <li><span class="fly-jing layui-icon layui-icon-spread-left"></span><a href="<?=url('@member_article@',['uid'=>$user['id']])?>"> 关于他更多的文章和主题</a></li>
                                <?php else:?>
                                <li><p>这个家伙还没有发布过文章和主题！</p></li>
                                <?php endif;?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{%end%}

{%block@javascript%}
{%end%}
