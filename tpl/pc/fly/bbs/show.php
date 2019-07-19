{%extend@common/bbs%}
{%block@title%}
<title><?=$title?>_<?=$site_name?></title>
<meta name="keywords" content="<?=$data['keywords']?>">
<meta name="description" content="<?=$data['excerpt']?>">
{%end%}
{%block@article%}
        <div class="layui-col-md8 content detail">
            <div class="fly-panel detail-box">
                <h1><?=$title?></h1>
                <div class="fly-detail-info">
                    <!-- <span class="layui-badge">审核中</span> -->
                    <span class="layui-badge layui-bg-green fly-detail-column"><a href="<?=url('@bbscategory@',['id'=>$data['category_id']])?>" class="layui-badge"><?=$data['category_name']?></a></span>
                    <span class="layui-badge layui-bg-green fly-detail-column"><?=$data['category_second']?></span>

                    <span class="layui-badge" style="background-color: #999;">未结</span>
                    <!-- <span class="layui-badge" style="background-color: #5FB878;">已结</span> -->
                    <?php if($data['is_top']): ?>
                    <span class="layui-badge layui-bg-black">置顶</span>
                    <?php endif;?>
                    <?php if($data['recommended']): ?>
                    <span class="layui-badge layui-bg-red">精帖</span>
                    <?php endif;?>
                    <?php if($isAdmin):?>
                    <div class="fly-admin-box" data-id="123">
                        <span class="layui-btn layui-btn-xs jie-admin" type="del">删除</span>
                        <span class="layui-btn layui-btn-xs jie-admin" type="set" field="stick" rank="1">置顶</span>
                        <span class="layui-btn layui-btn-xs jie-admin" type="set" field="stick" rank="0" style="background-color:#ccc;">取消置顶</span>
                        <span class="layui-btn layui-btn-xs jie-admin" type="set" field="status" rank="1">加精</span>
                        <span class="layui-btn layui-btn-xs jie-admin" type="set" field="status" rank="0" style="background-color:#ccc;">取消加精</span>
                    </div>
                    <?php endif;?>
                    <span class="fly-list-nums">
                        <a href="#comment"><i class="iconfont" title="回答">&#xe60c;</i> <?=$data['comments_num']?></a>
                        <i class="iconfont" title="人气">&#xe60b;</i> <?=$data['views']?>
                    </span>
                </div>
                <div class="detail-about">
                    <a class="fly-avatar" href="#user/home.html">
                        <img src="/<?=($data['avatar']? :'uploads/user/default.png')?>" alt="<?=$data['username']?>">
                    </a>
                    <div class="fly-detail-user">
                        <a href="#" class="fly-link">
                            <cite><?=$data['username']?></cite>
                            <i class="iconfont icon-renzheng" title="认证信息"></i>
                            <i class="layui-badge fly-badge-vip">VIP0</i>
                        </a>
                        <span><?=date('Y-m-d H:i',$data['create_time'])?></span>
                    </div>
                    <div class="detail-hits" id="LAY_jieAdmin" data-id="123">
                        <span style="padding-right: 10px; color: #FF7200">悬赏：<?=$data['coin']?>金币</span>
                        <?php if($isAdmin || \core\Session::get('user.gid')==$data['uid']) : ?>
                        <span class="layui-btn layui-btn-xs jie-admin" type="edit"><a href="#">编辑此贴</a></span>
                        <?php endif;?>
                    </div>
                </div>
                <div class="detail-body photos">
                    <p><?=$data['content']?></p>
                </div>
            </div>

            <div class="fly-panel detail-box" id="flyReply">
                <fieldset class="layui-elem-field layui-field-title" style="text-align: center;">
                    <legend>回帖</legend>
                </fieldset>

                <ul class="jieda" id="jieda">
                    <?php if($comments): foreach ($comments as $comment):?>
                    <li data-id="111" class="jieda-daan">
                        <a name="item-<?=$comment['id']?>"></a>
                        <div class="detail-about detail-about-reply">
                            <a class="fly-avatar" href="">
                                <img src="/uploads/user/default.png" alt="">
                            </a>
                            <div class="fly-detail-user">
                                <a href="" class="fly-link">
                                    <cite><?=$comment['username']?></cite>
                                    <i class="iconfont icon-renzheng" title="认证信息：XXX"></i>
                                    <i class="layui-badge fly-badge-vip">VIP0</i>
                                </a>
                                <!--
                                 <span>(楼主)</span>
                                <span style="color:#5FB878">(管理员)</span>
                                <span style="color:#FF9E3F">（社区之光）</span>
                                <span style="color:#999">（该号已被封）</span>
                                -->
                            </div>

                            <div class="detail-hits">
                                <span><?=date('Y-m-d H:i',$comment['create_time'])?></span>
                            </div>
                            <?php if($comment['recommended']):?>
                                <i class="iconfont icon-caina" title="最佳答案"></i>
                            <?php endif;?>
                        </div>
                        <div class="detail-body jieda-body photos">
                            <?=$comment['content']?>
                        </div>
                        <div class="jieda-reply">
              <span class="jieda-zan zanok" type="zan">
                <i class="iconfont icon-zan"></i>
                <em><?=$comment['likes']?></em>
              </span>
                            <span type="reply">
                <i class="iconfont icon-svgmoban53"></i>
                回复
              </span>
                            <div class="jieda-admin">
                                <?php if($isAdmin):?>
                                <span type="edit">编辑</span>
                                <span type="del">删除</span>
                                <?php endif;?>
                                <!-- <span class="jieda-accept" type="accept">采纳</span> -->
                            </div>
                        </div>
                    </li>

                    <?php endforeach; else:?>
                    <li class="fly-none">消灭零回复</li>
                    <?php endif;?>
                </ul>
                <div style="text-align: center">
                    <?=$page?>
                </div>
                <div class="layui-form layui-form-pane">
                    <form action="#/jie/reply/" method="post">
                        <div class="layui-form-item layui-form-text">
                            <a name="comment"></a>
                            <div class="layui-input-block">
                                <textarea id="L_content" name="content" required lay-verify="required" placeholder="请输入内容"  class="layui-textarea fly-editor" style="height: 150px;"></textarea>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <input type="hidden" name="jid" value="123">
                            <button class="layui-btn" lay-filter="*" lay-submit>提交回复</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
{%end%}

