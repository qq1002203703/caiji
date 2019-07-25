{%extend@common/base_portal%}
{%block@title%}
<title>会员中心_活跃用户列表_<?=$site_name?></title>
<meta name="keywords" content="<?=$title?>">
<meta name="description" content="这里是<?=$site_name?>的活跃用户列表，你可以找到你喜欢的人进行关注，一起和他/她进行交流、互动。">
<meta name="mobile-agent" content="format=html5;url=<?=url('@member_all@','','http://'.$mobile_domain)?>">
<link rel="alternate" media="only screen and(max-width: 750px)" href="<?=url('@member_all@','','http://'.$mobile_domain)?>">
{%end%}
{%block@article%}
<div class="layui-container yang-space">
    <div class="fly-panel fly-panel-user" pad20>
        <ul class="list-movie pl20">
            <h1 class="title">会员列表</h1>
            <div class="layui-text" style="margin-bottom: 14px;">
                <p>这里是<?=$site_name?>的活跃用户列表，你可以找到你喜欢的人进行关注，一起和他/她进行交流、互动。</p>
            </div>
            <?php $groupList=\core\Conf::get('groupList','portal'); foreach ($data as $item):?>
                <li class="item mr15" style="width: 170px;margin-bottom: 18px;">
                    <a class="link" href="<?=url('@member@',['uid'=>$item['id']])?>" title="<?=$item['username'];?>">
                        <div class="layui-row">
                            <div class="layui-col-md4">
                                <div class="pic" style="height: 58px">
                                    <img <?=($item['avatar'] ?'src="'.$tuku.$item['avatar'].'" alt="'.$item['username'].'"' : 'src="'.$tuku.'/uploads/images/no.gif" alt="没有缩略图"')?>>
                                </div>
                            </div>
                            <div class="layui-col-md8">
                                <div class="text">
                                    <span class="title"><?=$item['username'];?></span>
                                    <span class="sub"><?=$groupList[$item['gid']]?></span>
                                </div>
                            </div>
                        </div>
                    </a>
                </li>
            <?php endforeach;?>
        </ul>
        <?=$page?>
    </div>
</div>
{%end%}

{%block@javascript%}
{%end%}
