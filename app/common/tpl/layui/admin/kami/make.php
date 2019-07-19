{%extend@common/menu_main%}
{%block@main%}
<div class="fly-panel fly-panel-user" pad20>
    <div class="layui-tab layui-tab-brief" lay-filter="user">
        <ul class="layui-tab-title" id="LAY_mine">
            <li id="tab-pc" lay-id="list" class="layui-this"><a href="<?=url('admin/kami/list')?>"><?=$title;?></a></li>
        </ul>
        <fieldset class="layui-elem-field" style="margin-top: 15px">
            <legend>卡密生成</legend>
            <div class="layui-field-box">
                <form class="layui-form" action="<?=url('api/kami_admin/make')?>" method="post">
                    <div class="layui-form-item">
                        <label class="layui-form-label">卡密种类</label>
                        <div class="layui-input-block">
                            <select id="p_type" name="type" lay-filter="selectType">
                                <option  value="-1">选择种类</option>
                                <?php $str='';$type=['升级卡','充值卡'];$currency=['金钱','金币']; foreach ($data as $item): ?>
                                    <option  value="<?=$item['id']?>"><?=$item['name']?></option>
                                    <?php $str.='<p id="details_'.$item['id'].'">'.$item['text'].' -> '.$item['value'].' -> '.$type[$item['type']].' -> '.$currency[$item['currency']].'</p>';?>
                                <?php endforeach;?>
                            </select>
                        </div>
                    </div>
                    <style>#details-container {display: none;}</style>
                    <div class="layui-form-item" id="details-container">
                        <label class="layui-form-label"></label>
                        <div class="layui-input-block"><?=$str;?></div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">生成个数</label>
                        <div class="layui-input-block">
                            <input type="text" name="number" required  lay-verify="required" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button class="layui-btn" lay-submit lay-filter="ajax">开始生成</button>
                        </div>
                    </div>
                </form>
            </div>
        </fieldset>

    </div>
</div>
<script type="text/javascript">
    var navSidebar="kami-list";
</script>
{%end%}
{%block@javascript%}
<script type="text/javascript">
    layui.use(['layer','fly','form'],function () {
        var $ = layui.jquery;
        var form=layui.form;
        //监控选择框
        form.on('select(selectType)', function(data){
            var  container=$('#details-container');
            var selectValue=$(data.elem).find("option:selected").val();
            if(selectValue < 0){
                if(container.is(":visible"))
                    container.hide();
            }else {
                if(container.is(":hidden"))
                    container.show();
                container.find('p').hide();
                $("#details_"+selectValue).show();
            }
        });
    });
</script>
{%end%}