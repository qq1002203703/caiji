{%extend@common/weixinqun%}

{%block@title%}
<title><?=$title?>|<?=$data['city']?><?=$data['category']?>微信群_<?=$site_name?></title>
<meta name="keywords" content="<?=$title?>微信群">
<meta name="description" content="<?=$title?>，本群是<?=$data['city']?><?=$data['category']?>微信群，有兴趣的朋友可以找群主入群！<?=$data['excerpt']?>">
{%end%}

{%block@article%}
<div class="pure-u-3-4" id="article">
    <div class="article-content">
        <div class="wxq pure-g">
            <div class="pure-u-5-8 wxq-content">
                <h1 class="wxq-title"><?=$data['title']?></h1>
                <div class="wxq-excerpt">本微信群简介：<p><?=$data['excerpt']?><br>本群免费对外开外，有兴趣的朋友可以找群主入群！</p></div>
                <div class="pure-g wxq-about">
                    <div class="pure-u-1-2">所属行业：<a href="<?=url('@fenlei@',['id'=>$data['category_id']])?>"><?=$data['category']?></a></div>
                    <div class="pure-u-1-2">所属地区：<a href="<?=url('@diqu@',['id'=>$data['city_id']])?>"><?=$data['city']?></a></div>
                    <div class="pure-u-1-2">发布时间：<?=date('Y-m-d H:i',$data['create_time'])?></div>
                    <div class="pure-u-1-2">群标签：<?=$data['tags']?></div>
                </div>
                <hr class="hr">
                <div class="pure-g wxq-about">
                    <div class="pure-u-1-2">群主微信号：<span class="blue"><?=$data['weixinhao']?></span></div>
                    <div class="pure-u-1-2">查看数：<span class="red"><?=$data['views']?></span></div>
                </div>
                <?php if ($data['content']):?>
                <hr class="hr">
                <div class="wxq-article">
                    <div id="wxq-article-pre"><p><?=$data['excerpt']?><span class="show-article">...显示全部</span></p></div>
                    <div id="wxq-article-main"><?=$data['content']?><span class="hide-article">折叠收起</span></div>
                </div>
                <?php endif;?>
            </div>
            <div class="pure-u-3-8 wxq-img">
                <?php if ($data['qrcode'] && $data['qun_qrcode']):?>
                    <div class="wxq-img-click"><a id="qunzhu" class="pure-button btn-sm btn-error doing-click" href="javascript:;">群主二维码</a><a  id="qun" class="pure-button btn-sm doing-click" href="javascript:;">群二维码</a></div>
                 <img alt="群主二维码" src="<?=url('/')?>uploads/images/<?=$data['qrcode']?>" class="qunzhu-qrcode">
                <img alt="群二维码" src="<?=url('/')?>uploads/images/<?=$data['qun_qrcode']?>" class="qun-qrcode">
                <?php elseif ($data['qrcode'] && !$data['qun_qrcode']):?>
                    <div class="wxq-img-click"><a  class="pure-button btn-sm btn-error" href="javascript:;">群主二维码</a></div>
                    <img alt="群主二维码" src="<?=url('/')?>uploads/images/<?=$data['qrcode']?>" class="qunzhu-qrcode">
                <?php elseif (!$data['qrcode'] && $data['qun_qrcode']):?>
                    <div class="wxq-img-click"><a  class="pure-button btn-sm btn-error" href="javascript:;">群二维码</a></div>
                    <img alt="群二维码" src="<?=url('/')?>uploads/images/<?=$data['qun_qrcode']?>" class="qunzhu-qrcode">
                <?php else: ?>
                    <div class="wxq-img-click"><a  class="pure-button btn-sm" href="javascript:;">没有二维码</a></div>
                    <img alt="没有二维码" src="<?=url('/')?>uploads/images/no_qrcode.png" class="qunzhu-qrcode">
                <?php endif;?>
                <div class="pre-next"><?=$pre_next?></div>
            </div>
        </div>
        <div class="wxq-other">
            <h2>相关微信群</h2>
            <ul><?=app('\app\weixinqun\model\Weixinqun')->getRandomItem(12)?></ul>
        </div>
        <?//=$post['content'];?>
    </div>
</div><!--//article-->
{%end%}

{%block@javascript%}
<script type="text/javascript" charset="UTF-8">
    $(function(){
        $(".doing-click").click(function(){
            var _this=$(this);
            var thisId=_this[0].id;
            var thisClass=_this.prop("class");
            if(/btn-error/i.test(thisClass)){
                return ;
            }
            _this.addClass('btn-error');
            $("."+thisId+"-qrcode").show();
            var otherId='qun';
            if(thisId=='qun'){
                otherId='qunzhu';
            }
            $('#'+otherId).removeClass('btn-error');
            $("."+otherId+"-qrcode").hide();
        });
        $(".show-article").click(function () {
            $("#wxq-article-pre").hide();
            $("#wxq-article-main").show();
        });
        $(".hide-article").click(function () {
            $("#wxq-article-main").hide();
            $("#wxq-article-pre").show();
        });
        //showArticle();
    });
    function showArticle() {
        var _this=$('.wxq-article');
        var html=_this.html();
        var text=_this.text();
        _this.html('<p>'+text.substring(0,54)+'<span class="show-article" role="hide">...显示全部</span></p>');
        $('.show-article').on("click",function () {
            console.log($(this).attr('role'));
            if($(this).attr('role')=='hide'){
                $('.wxq-article').html(html+'<p><span class="show-article" role="show">收起</span></p>');
            }else {
                $('.wxq-article').html('<p>'+text.substring(0,54)+'<span class="show-article" role="hide">...显示全部</span></p>');
            }
        });
    }
</script>
{%end%}

