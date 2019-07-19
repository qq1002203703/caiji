{%extend@common/menu_main%}
{%block@main%}
<div class="fly-panel fly-panel-user" pad20>
    <div class="layui-tab layui-tab-brief" lay-filter="user">
        <ul class="layui-tab-title" id="LAY_mine">
            <li id="tab-pc" lay-id="list" class="layui-this"><a href="<?=url('admin/kami/list')?>"><?=$title;?></a></li>
        </ul>
        <fieldset class="layui-elem-field" style="margin-top: 15px">

            <div class="layui-field-box">
                <form class="layui-form" action="<?=url('api/kami_admin/type_edit')?>" method="post">
                    <div class="layui-form-item">
                        <label class="layui-form-label">名称</label>
                        <div class="layui-input-block">
                            <input type="text" name="name" required  lay-verify="required" placeholder="名称" autocomplete="off" class="layui-input" value="<?=$data['name']?>">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">值</label>
                        <div class="layui-input-block">
                            <input type="text" name="value" required  lay-verify="required" placeholder="价值" autocomplete="off" class="layui-input" value="<?=$data['value']?>">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">描述</label>
                        <div class="layui-input-block">
                            <input type="text" name="text" required  lay-verify="required" placeholder="描述" autocomplete="off" class="layui-input" value="<?=$data['text']?>">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">币种</label>
                        <div class="layui-input-block">
                            <select name="currency" lay-verify="required">
                                <option value="0"<?=echo_select($data['currency'],0)?>>金钱</option>
                                <option value="1"<?=echo_select($data['currency'],1)?>>金币</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">卡密类型</label>
                        <div class="layui-input-block">
                            <select name="type" lay-verify="required">
                                <option value="0"<?=echo_select($data['type'],0)?>>升级卡</option>
                                <option value="1"<?=echo_select($data['type'],1)?>>充值卡</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <input type="hidden" name="id" value="<?=$data['id']?>">
                            <button class="layui-btn" lay-submit lay-filter="ajax">立即提交</button>
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
</script>
{%end%}