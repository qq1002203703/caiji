{%extend@common/base_portal%}
{%block@title%}
<title><?=$title?>_<?=$site_name?></title>
{%end%}
{%block@article%}
<div class="fly-panel fly-panel-user" pad20>
    <div class="layui-field-box">
        <h2><?=$title?></h2>
    </div>
    <form class="layui-form" action="" method="post">
        <table class="layui-table cart">
            <colgroup>
                <col width="8">
                <col width="80">
                <col>
            </colgroup>
            <thead>
            <tr>
                <th><input class="check-all" type="checkbox" lay-skin="primary"></th>
                <th></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php if($data): foreach ($data as $item):?>
            <tr>
                <td><input class="cart-check" type="checkbox" value="<?=$item['id']?>" name="ids[]" lay-skin="primary"></td>
                <td><img class="cart-img" src="/<?php if($item['thumb']) echo $item['thumb'];else echo 'uploads/images/no_qrcode.png'; ?>"></td>
                <td>
                    <div class="layui-text cart-text cart-title"><?=$item['title']?></div>
                    <div class="layui-text cart-text"><span class="red">单价:</span><span class="cart-price"><?=$item['money']?></span></div>
                    <div class="layui-text cart-text">
                        <div class="layui-row">
                            <div class="layui-col-xs12 layui-col-sm3 layui-col-md2"><span class="green">购买数量:</span></div>
                            <div class="layui-col-xs12 layui-col-sm9  layui-col-md10">
                                <input class="spinner-input cart-num" type="text" value="<?=$buyNum[$item['id']]?>" name="buy_num">
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            <?php endforeach;endif;?>
            </tbody>
            <tfoot>
            <tr>
                <td><input class="check-all" type="checkbox" lay-skin="primary"></td>
                <td colspan="2">
                    <div class="layui-row">
                        <div class="layui-col-xs12 layui-col-sm6 layui-col-md4">
                            <di>选中订单合计：<span class="cart-total red">200</span></di>
                            <div class="cart-submit"><input class="layui-btn layui-btn-danger" type="submit" value="提交"></div>
                        </div>
                        <div class="layui-hide-xs layui-col-sm6 layui-col-md8">
                        </div>
                    </div>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>
{%end%}

{%block@javascript%}
<?php if(!$data):?>
<script type="text/javascript" charset="utf-8">
   layui.use(['layer'], function(){
       var layer = layui.layer;
      layer.alert('订单生成失败，原因：<?=$msg?>');
   });
 </script>
<?php endif;?>
{%end%}

