{%extend@common/menu_main%}
{%block@main%}
<div class="fly-panel fly-panel-user" pad20>
    <f class="layui-tab layui-tab-brief" lay-filter="user">
        <ul class="layui-tab-title" id="LAY_mine">
            <li id="tab-pc" lay-id="list" class="layui-this"><a href="<?=url('admin/kami/list')?>"><?=$title;?></a></li>
        </ul>
        <fieldset class="layui-elem-field" style="margin-top: 15px">
            <legend>添加卡密类型</legend>
            <div class="layui-field-box">
                <form class="layui-form" action="<?=url('api/kami_admin/type_add')?>" method="post">
                    <div class="layui-form-item">
                        <label class="layui-form-label">名称</label>
                        <div class="layui-input-block">
                            <input type="text" name="name" required  lay-verify="required" placeholder="名称" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">值</label>
                        <div class="layui-input-block">
                            <input type="text" name="value" required  lay-verify="required" placeholder="价值" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">描述</label>
                        <div class="layui-input-block">
                            <input type="text" name="text" required  lay-verify="required" placeholder="描述" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">币种</label>
                        <div class="layui-input-block">
                            <select name="currency" lay-verify="required">
                                <option value="0">金钱</option>
                                <option value="1">金币</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">卡密类型</label>
                        <div class="layui-input-block">
                            <select name="type" lay-verify="required">
                                <option value="0">升级卡</option>
                                <option value="1">充值卡</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button class="layui-btn" lay-submit lay-filter="ajax">立即提交</button>
                        </div>
                    </div>
                </form>
            </div>
        </fieldset>

        <fieldset class="layui-elem-field" style="margin-top: 15px">
                <legend>已有的卡密种类</legend>
                <div class="layui-field-box">
                    <div class="layui-form">
                        <table class="layui-table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>名称</th>
                                <th>价值</th>
                                <th>描述</th>
                                <th>种类</th>
                                <th>币种</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if ($data): $type=['升级卡','充值卡'];$currency=['金钱','金币'];foreach ($data as $item) : ?>
                                <tr>
                                    <td><?=$item['id']?></td>
                                    <td><?=$item['name']?></td>
                                    <td><?=$item['value']?></td>
                                    <td><?=$item['text']?></td>
                                    <td><?=$type[$item['type']]?></td>
                                    <td><?=$currency[$item['currency']]?></td>
                                    <td>
                                        <i class="layui-icon" style="font-size: 20px;">
                                            <a title="编辑" href="<?=url('admin/kami/edit',['id'=>$item['id']])?>">&#xe642;</a>
                                        </i>
                                        <i class="layui-icon" style="font-size: 20px;">
                                            <a title="删除" id="deletea_<?=$item['id']?>" href="javascript:;" ac="<?=$item['id']?>">&#xe640;</a>
                                        </i>
                                    </td>
                                </tr>
                            <?php endforeach;endif;?>
                            </tbody>
                        </table>
                    </div>
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
        var fly=layui.fly;
        //删除一条
        $("a[id^='deletea']").on('click', function(){
            var ac=this.id;
            ac=ac.replace('deletea_','');
            $(this).attr('ac',"<?=url('api/kami_admin/type_del')?>?id="+ac);
            fly.ajaxClick(this);
            return false;
        });
    });
</script>
{%end%}