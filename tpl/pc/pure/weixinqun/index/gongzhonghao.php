{%extend@common/weixinqun%}

{%block@title%}
<title><?=$title?>|<?=$data['city']?><?=$data['category']?>微信群_<?=$site_name?></title>
<meta name="keywords" content="<?=$title?>微信群">
<meta name="description" content="<?=$title?>，本群是<?=$data['city']?><?=$data['category']?>微信群，有兴趣的朋友可以找群主入群！<?=$data['excerpt']?>">
{%end%}

{%block@article%}
<div class="pure-u-3-4" id="article">
    <div class="article-content">
        <div class="wxq">
            <div class="wxq-content">
                <h1 class="wxq-title"><?=$data['title']?></h1>
                <div class="wxq-meta">
                    <span>所属行业：<a href="<?=url('@fenlei@',['id'=>$data['category_id']])?>"><?=$data['category']?></a></span>
                    <span>发布时间：<?=date('Y-m-d H:i',$data['create_time'])?></span>
                    <span>群主微信号：<span class="blue"><?=$data['weixinhao']?></span>
                    <span>查看数：<span class="red"><?=$data['views']?></span>
                </div>
                <?php if($data['thumb']) : ?>
                <img src="/uploads/images/gzh/<?=$data['thumb']?>" alt="<?=$data['title']?>">
                <?php endif;?>
                <?= ($data['content'])?>
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

