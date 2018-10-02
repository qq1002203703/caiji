{%extend@common/base%}

{%block@main%}
<div class="path">
    <a href="<?=url('admin/index/index')?>">首页</a> > 其他设置 > <a href="<?=url('admin/other/queue')?>">定时任务</a> > <?=$title?>
</div>

<div class="content">
    <div class="content-card">
        <div class="content-detail">
            <div class="content-item pure-g" id="jtab">
                <div class="pure-u-1-4">
                    <div class="tab-hander">
                        <a href="javascript:;" class="tab-hander-item tab-select" data="category-main">基本属性</a>
                    </div>
                </div>
                <div class="pure-u-3-4">
                    <p class="red">说明：每天00：01，只执行一次的任务，如果已经执行过就会被删除，而每天执行的任务会被重置状态为未执行！</p>
                    <form class="pure-form pure-form-stacked" action="" method="post">
                        <fieldset>
                            <div class="tab-item" id="category-main">
                                <label for="description">任务说明：方便自己知道此任务是干什么的</label>
                                <input id="description" class="pure-u-5-6" type="text" name="description" value="<?=$data['description']?>" placeholder="任务说明">
                                <label for="run_time">执行时间<span class="red"> *</span>：格式：2018-10-21 10:08:30</label>
                                <input id="run_time" type="text" name="run_time" value="<?=date('Y-m-d H:i:s',$data['run_time'])?>" placeholder="执行时间" required>
                                <label for="callable">回调函数或方法<span class="red"> *</span>：支持格式：函数名，类名::静态方法名，类名@非静态方法名，类名必须带完整命名空间</label>
                                <input id="callable" class="pure-u-5-6" type="text" name="callable" value="<?=$data['callable']?>" placeholder="回调函数或方法" required>
                                <label for="class_param">类的参数：多个参数用空格分开，上面是函数和静态方法时，此项不用填</label>
                                <input class="pure-u-5-6" name="class_param" id="class_param" value="<?=$data['class_param']?>" placeholder="类的参数">
                                <label for="method_param">函数或方法的参数：多个参数用空格分开</label>
                                <input class="pure-u-5-6" name="method_param" id="method_param" value="<?=$data['method_param']?>" placeholder="函数或方法的参数">
                                <label for="status">状态：</label>
                                <select id="status" class="pure-select" name="status">
                                    <option value="0"<?=echo_select('0',$data['status'])?>>未执行</option>
                                    <option value="1"<?=echo_select('1',$data['status'])?>>已执行</option>
                                </select>
                                <label for="type">种类：</label>
                                <select id="type" class="pure-select" name="type">
                                    <option value="0"<?=echo_select('0',$data['type'])?>>每天执行</option>
                                    <option value="1"<?=echo_select('1',$data['type'])?>>只执行一次</option>
                                </select>
                                <label for="del_type">删除方式：</label>
                                <select id="del_type" class="pure-select" name="del_type">
                                    <option value="0"<?=echo_select('0',$data['del_type'])?>>任务删除</option>
                                    <option value="1"<?=echo_select('1',$data['del_type'])?>>队列删除</option>
                                </select>
                                <p>&nbsp;&nbsp;&nbsp;&nbsp;任务删除，队列启动任务后就不管了，由任务自己把状态改为已执行；<br>&nbsp;&nbsp;&nbsp;&nbsp;队列删除，队列启动任务后，等待任务执行完成，队列接着会把此任务的状态改为已执行.</p>
                            </div>
                            <br>
                            <button type="submit" class="pure-button btn-custom pure-input-1-4">提交</button>
                        </fieldset>
                    </form>
                    <div class="msg" style="display:none"><?=$msg?></div>
                </div>
            </div>
        </div>
    </div>
</div>
{%end%}

{%block@javascript%}
<style>.pure-form label{margin-top:16px;}  </style>
<script charset="UTF-8" type="text/javascript">
    $(function(){
        Jtab('#jtab');
        showMsg('.msg');
    });

</script>
{%end%}