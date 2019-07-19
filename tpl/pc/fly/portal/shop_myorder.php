{%extend@common/base_portal%}
{%block@title%}
<title><?=$title?>_<?=$site_name?></title>
{%end%}
{%block@article%}
<div class="layui-container yang-space">
    <div class="layui-row layui-col-space10">
        <div class="layui-col-md3">
            {%include@common/member%}
        </div>
        <div class="layui-col-md9">
            <div class="fly-panel fly-panel-user" pad20>
                <div class="layui-field-box">
                    <h2 class="text-title"><?=$title?></h2>
                    <?php if($isLogin):?>
                    <p class="text-p">本查询系统，查询的是在未登陆状态下购买产生的订单，如果你想查询登陆状态下产生的订单，请移步到 <a href="<?=url('portal/user/myorder')?>" class="green">登陆状态订单查询</a> !</p>
                    <?php endif;?>
                </div>
                <form class="layui-form" action="" method="get" lay-fillter="ss">
                    <div class="layui-form-item">
                        <label class="layui-form-label" id="L_email">邮箱</label>
                        <div class="layui-input-inline">
                            <input id="L_email" type="text" name="email" value="<?=$email?>" required lay-verify="required|email" autocomplete="off" class="layui-input" placeholder="输入你下订单时的邮箱">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label" id="L_email"></label>
                        <div class="layui-input-inline">
                            <button class="layui-btn" lay-filter="*" lay-submit>查询</button>
                        </div>
                    </div>
                    <div class="layui-tab layui-tab-brief" lay-filter="myorder">
                        <ul class="layui-tab-title">
                            <li class="myorder-click" lay-id="-1">全部</li>
                            <li class="myorder-click" lay-id="1">完结</li>
                            <li class="myorder-click" lay-id="0">待支付<?php if($totalWaiting >0 ):?><span class="layui-badge layui-bg-red"><?=$totalWaiting?></span><?php endif;?></li>
                        </ul>
                    </div>
                    <?php if(isset($data) && $data) :?>
                    <table class="layui-table cart">
                        <colgroup>
                            <col width="80">
                            <col width="140">
                            <col>
                        </colgroup>
                        <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($data as $item):?>
                        <tr>
                            <td><img class="cart-img" src="/<?php if($item['thumb']) echo $item['thumb'];else echo 'uploads/images/no_qrcode.png'; ?>"></td>
                            <td>
                                <div class="layui-text cart-text">
                                    <span>单价:</span>
                                    <span class="cart-price"><?=$item['price']?></span>
                                </div>
                                <div class="layui-text cart-text">
                                    <span>购买数量:</span>
                                    <span class="cart-price"><?=$item['buy_num']?></span>
                                </div>
                                <div class="layui-text cart-text">
                                    <span>合计:</span>
                                    <span class="cart-price"><?=$item['total']?></span>
                                </div>
                                <div class="layui-text cart-text">
                                    <span>状态:</span>
                                    <span class="cart-price cart-status"><?=$item['status']?></span>
                                </div>
                            </td>
                            <td>
                                <div class="layui-text cart-text cart-title"><?=$item['title']?></div>
                            </td>
                        </tr>
                        <?php endforeach;?>
                        </tbody>
                        <?php if($page):?>
                        <tfoot>
                        <tr>
                            <td colspan="3">
                               <?=$page?>
                            </td>
                        </tr>
                        </tfoot>
                        <?php endif;?>
                    </table>
                    <?php endif;?>
                </form>
            </div>
        </div>
    </div>
</div>
{%end%}

{%block@javascript%}
<script type="text/javascript" charset="utf-8">
   layui.use(['jquery','layer','element'], function(){
       var layer = layui.layer;
       var $=layui.$;
       var element = layui.element;
       layer.ready(function () {
           var layid=parseInt(getQueryString('status',-1));
           element.tabChange('myorder', layid);
           $('.myorder-click').on('click',function () {
               window.location.href=changeQueryString('status',this.getAttribute('lay-id')).replace(/&?page=\d+/,'');
           });
           $('.cart-status').each(function () {
               var dd=$(this).text();
               $(this).html(dd=='1' ? '完成' : '<span class=red>未完成</span>');
           });
       });
   });
    //获取url中某个查询变量的值
   function getQueryString(name,defaultValue) {
       var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
       var r = window.location.search.substr(1).match(reg);  //获取url中"?"符后的字符串并正则匹配
       var context = false;
       if (r != null)
           context = r[2];
       reg=null;r=null;
       return (context === false) ? defaultValue : context;
   }
   /**
    * 改变url中某个查询变量的值
    * @param {string} name
    * @param {string} newValue
    * @returns {string}
    */
   function changeQueryString (name,newValue) {
       var reg = new RegExp("(\\?|&)" + name + "=([^&=]*)(&|$)", "i");
       var url=window.location.href;
       if (reg.test(url)){
           return url.replace(reg,'$1'+name+'='+newValue+'$3');
       } else{
           reg=null;
           return url+(/\?/.test(url)?'&' :'?')+name+'='+newValue;
       }
   }
 </script>

{%end%}

