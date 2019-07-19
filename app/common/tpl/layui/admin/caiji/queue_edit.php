{%extend@common/menu_main%}
{%block@main%}
<div class="fly-panel fly-panel-user" pad20>
    <div class="layui-tab layui-tab-brief" lay-filter="user">
        <ul class="layui-tab-title" id="LAY_mine">
            <li class="layui-this" lay-id="list"><?=$title;?></li>
        </ul>
        <div class="layui-tab-content" style="padding: 20px 0;">
            <div class="layui-tab-item layui-show">
                <form class="layui-form ayui-form-pane" method="post" action="<?=url('api/caiji_admin/queue_edit');?>" id="form-main">
                    <div class="layui-form-item">
                        <label class="layui-form-label">任务说明</label>
                        <div class="layui-input-inline">
                            <input  id="description"  class="layui-input" type="text" name="description" value="<?=$data['description']?>">
                        </div>
                        <div class="layui-form-mid layui-word-aux">方便自己知道此任务是干什么的</div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">执行时间<span class="red"> *</span></label>
                        <div class="layui-input-inline">
                            <input id="run_time" class="layui-input datetime-input" name="run_time" type="text" value="<?=date('Y-m-d H:i:s',$data['run_time'])?>" lay-verify="required" required>
                        </div>
                        <div class="layui-form-mid layui-word-aux">格式：2018-10-21 10:08:30</div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">回调函数<span class="red"> *</span></label>
                        <div class="layui-input-inline">
                            <input id="callback" class="layui-input" name="callback" type="text" value="<?=$data['callback']?>" lay-verify="required" required>
                        </div>
                        <div class="layui-form-mid layui-word-aux">支持格式：函数名，类名::静态方法名，类名@非静态方法名，类名必须带完整命名空间</div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">类的参数</label>
                        <div class="layui-input-inline">
                            <input id="class_param" class="layui-input" name="class_param" type="text" value="<?=$data['class_param']?>">
                        </div>
                        <div class="layui-form-mid layui-word-aux">多个参数用空格分开，上面是函数和静态方法时，此项不用填</div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">函数参数</label>
                        <div class="layui-input-inline">
                            <input id="method_param" class="layui-input" name="method_param" type="text" value="<?=$data['method_param']?>">
                        </div>
                        <div class="layui-form-mid layui-word-aux">多个参数用空格分开</div>
                    </div>`

                    <div class="layui-form-item">
                        <label class="layui-form-label">状态</label>
                        <div class="layui-input-inline">
                            <select id="status" name="status">
                                <option value="0"<?=echo_select('0',$data['status'])?>>未执行</option>
                                <option value="1"<?=echo_select('1',$data['status'])?>>已执行</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">种类</label>
                        <div class="layui-input-inline">
                            <select id="type" name="type">
                                <option value="0"<?=echo_select('0',$data['type'])?>>每天执行</option>
                                <option value="1"<?=echo_select('1',$data['type'])?>>只执行一次</option>`
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">删除方式：</label>
                        <div class="layui-input-inline">
                            <select id="del_type" name="del_type">
                                <option value="0"<?=echo_select('0',$data['del_type'])?>>任务删除</option>
                                <option value="1"<?=echo_select('1',$data['del_type'])?>>队列删除</option>`
                            </select>
                        </div>
                        <div class="layui-clear"></div>
                        <div class="layui-input-block layui-word-aux">
                            <label class="layui-form-label"></label>任务删除，队列启动任务后就不管了，由任务自己把状态改为已执行；<br>
                            <label class="layui-form-label"></label>队列删除，队列启动任务后，等待任务执行完成，队列接着会把此任务的状态改为已执行.
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label"></label>
                        <div class="layui-input-block">
                            <input type="hidden" name="id" value="<?=$data['id']?>">
                            <input type="button" class="layui-btn" lay-filter="ajax" lay-submit value="提交更改">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var navSidebar="caiji-queue";
</script>
{%end%}

{%block@javascript%}

<script type="text/javascript">
    layui.use(['layer','form','fly'], function() {
        //var $=layui.jquery,
            //layer=layui.layer,
            //fly=layui.fly;
        //更新缓存
        /*$("#update-cache").on('click', function(){
            fly.ajaxClick(this,function(res){
                layer.alert(res.msg, {title:'提示',time: 2000,end:function () {
                        window.location.reload();
                    }
                });
            });
            return false;
        });*/
    });
</script>
{%end%}