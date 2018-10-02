{%extend@common/base%}

{%block@main%}
<div class="path">
    <a href="<?=url('admin/index/index')?>">首页</a> > 采集设置 > <a href="<?=url('admin/caiji/handler')?>">项目管理</a> > <?=$title?>
</div>
<div class="content">
    <div class="content-card">
        <div class="content-detail">
            <div class="content-item" id="jtab_inline">
                <div class="content-item-header">
                    <p>剩余待审核：<span class="red"><?=$total?></span>, 当前id:<span class="red"> <?=$data['id']?></span></p>
                </div>
                <div class="tab-hander">
                    <a href="javascript:;" class="tab-hander-item tab-select" data="tab-a">基本</a>
                    <a href="javascript:;" class="tab-hander-item" data="tab-b">附件</a>
                    <a href="javascript:;" class="tab-hander-item" data="tab-c">其他</a>
                </div>
                <form class="pure-form pure-form-stacked mb-8" action="" method="post" id="myform">
                    <fieldset>
                        <div class="tab-item" id="tab-a">
                            <table class="full pure-table pure-table-bordered">
                                <tr>
                                    <td width="70"><label for="title">标题 <span class="red">*</span></label></td>
                                    <td><input id="title" value="<?=$data['title']?>" class="pure-input-1" type="text" name="title" placeholder="标题" required></td>
                                </tr>
                                <tr>
                                    <td width="70"><label for="create_time">创建时间</label></td>
                                    <td>
                                        <input id="create_time" class="dh-inline" type="text" name="create_time" value="<?=date('Y-m-d H:i:s',$data['create_time'])?>" placeholder="创建时间">&nbsp;&nbsp;<i class="iconfont
icon-rili date-holder"></i>
                                    </td>
                                </tr>
                                <?php if(isset($data['keywords'])):?>
                                <tr>
                                    <td><label for="keywords">关键词</label></td>
                                    <td>
                                        <input id="keywords" value="<?=$data['keywords']?>" class="pure-input-1" type="text" name="keywords" placeholder="关键词">
                                        <span class="green">多个关键词用英文逗号分隔</span>
                                    </td>
                                </tr>
                                <?php endif;?>
                                <?php if(isset($data['excerpt'])):?>
                                <tr>
                                    <td><label for="excerpt">摘要</label></td>
                                    <td>
                                        <textarea id="excerpt" class="pure-input-1" name="excerpt" placeholder="摘要"><?=$data['excerpt']?></textarea>
                                    </td>
                                </tr>
                                <?php endif;?>
                                <?php if(isset($data['url'])):?>
                                <tr>
                                    <td><label for="url">文章来源</label></td>
                                    <td>
                                        <input id="url" class="pure-input-1" value="<?=$data['url']?>" type="text" name="url" placeholder="文章来源">
                                    </td>
                                </tr>
                                <?php endif;?>
                                <tr>
                                    <td><label for="comments_num">评论数</label></td>
                                    <td><input id="comments_num" value="<?=$data['comments_num']?>" class="dh-inline" type="text" name="comments_num" placeholder="评论数"></td>
                                </tr>
                                <tr>
                                    <td width="70">状态</td>
                                    <td>
                                        <span class="blue">是否审核:</span>
                                        <select id="isshenhe" name="isshenhe" class="pure-select dh-inline">
                                            <option value="0">否</option>
                                            <option value="1" selected>是</option>
                                        </select> &nbsp;&nbsp;
                                        <span>是否结帖:</span>
                                        <select id="isend" name="isend" class="pure-select dh-inline">
                                            <option value="0"<?=echo_select($data['isend'],0)?>>否</option>
                                            <option value="1"<?=echo_select($data['isend'],1)?>>是</option>
                                        </select> &nbsp;&nbsp;
                                        <span>是否采集:</span>
                                        <select id="iscaiji" name="iscaiji" class="pure-select dh-inline">
                                            <option value="0"<?=echo_select($data['iscaiji'],0)?>>否</option>
                                            <option value="1"<?=echo_select($data['iscaiji'],1)?>>是</option>
                                        </select>&nbsp;&nbsp;
                                        <span class="red">是否垃圾:</span>
                                        <select id="islaji" name="islaji" class="pure-select dh-inline">
                                            <option value="0"<?=echo_select($data['islaji'],0)?>>否</option>
                                            <option value="1"<?=echo_select($data['islaji'],1)?>>是</option>
                                        </select>&nbsp;&nbsp;
                                    </td>
                                </tr>
                                <tr>
                                    <td><label for="content">内容</label></td>
                                    <td>
                                        <textarea id="content" class="pure-input-1" name="content" placeholder="内容"><?=$data['content']?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label for="tu">附加图片</label></td>
                                    <td>
                                        <textarea id="tu" class="pure-input-1" name="tu"><?=$data['tu']?></textarea>
                                        <input type="hidden" name="id" value="<?=$data['id']?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td><label for="tu">采集名称</label></td>
                                    <td>
                                        <input id="caiji_name"  type="text" name="caiji_name" value="<?=$data['caiji_name']?>" readonly>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="tab-item" id="tab-b">
                            <table class="full pure-table pure-table-bordered" id="table_file">
                                <thead>
                                <tr>
                                    <th width="120">名称</th>
                                    <th width="80">种类</th>
                                    <th>网址</th>
                                    <th>备注</th>
                                    <th width="120" align="center">控作</th>
                                </tr>
                                </thead>

                            </table>
                        </div>
                        <div class="tab-item" id="tab-c">
                            <table class="full pure-table pure-table-bordered">
                                <tr>
                                    <td></td>
                                </tr>
                            </table>
                        </div>
                    </fieldset>
                    <div class="" style="max-width:300px;margin:10px auto">
                        <button type="sumit" class="pure-button btn-custom pure-u-1">提交</button>
                    </div>
                </form>
                <!--div class="msg" style="display:none"><$msg></div-->

            </div>
        </div>
    </div>
</div>
{%end%}

{%block@javascript%}
<script type="text/javascript" src="/static/lib/My97DatePicker/WdatePicker.js"></script>
<script type="text/javascript" src="/static/lib/layer/layer.js"></script>

<!--link rel="stylesheet" type="text/css" href="/static/lib/simditor-2.3.16/builds/styles/simditor.css" /-->
<!--script type="text/javascript" src="/static/lib/simditor-2.3.16/builds/script/all.js?v=aaa"></script-->

<script charset="UTF-8" type="text/javascript">
    $(function(){
        Jtab('#jtab_inline');
        //showMsg('.msg');
        //富文本编辑器
        /*var editor = new Simditor({
            textarea: $('#content'),
            toolbar: ['title','bold', 'italic', 'color','link', '|', 'ol', 'ul','code','|','html','fullscreen']
        });*/
        /*$("#myform").submit(function(){
         editor.setValue(editor.getValue());
         return true;
         });*/
        //日期选择器
        $('.date-holder').on('click', function(e){
            WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',el:($(this).siblings('input')[0].id)});
        });
        // 为每一个textarea绑定事件使其高度自适应
        $.each($("textarea"), function(i, n){
            autoTextarea($(n)[0]);
        });
    });

    /**
     * 文本框根据输入内容自适应高度
     * {HTMLElement}   输入框元素
     * {Number}        设置光标与输入框保持的距离(默认0)
     * {Number}        设置最大高度(可选)
     */
    var autoTextarea = function (elem, extra, maxHeight) {
        extra = extra || 0;
        var isFirefox = !!document.getBoxObjectFor || 'mozInnerScreenX' in window,
            isOpera = !!window.opera && !!window.opera.toString().indexOf('Opera'),
            addEvent = function (type, callback) {
                elem.addEventListener ?
                    elem.addEventListener(type, callback, false) :
                    elem.attachEvent('on' + type, callback);
            },
            getStyle = elem.currentStyle ?
                function (name) {
                    var val = elem.currentStyle[name];
                    if (name === 'height' && val.search(/px/i) !== 1) {
                        var rect = elem.getBoundingClientRect();
                        return rect.bottom - rect.top -
                            parseFloat(getStyle('paddingTop')) -
                            parseFloat(getStyle('paddingBottom')) + 'px';
                    };
                    return val;
                } : function (name) {
                return getComputedStyle(elem, null)[name];
            },
            minHeight = parseFloat(getStyle('height'));
        elem.style.resize = 'both';//如果不希望使用者可以自由的伸展textarea的高宽可以设置其他值

        var change = function () {
            var scrollTop, height,
                padding = 0,
                style = elem.style;

            if (elem._length === elem.value.length) return;
            elem._length = elem.value.length;

            if (!isFirefox && !isOpera) {
                padding = parseInt(getStyle('paddingTop')) + parseInt(getStyle('paddingBottom'));
            };
            scrollTop = document.body.scrollTop || document.documentElement.scrollTop;

            elem.style.height = minHeight + 'px';
            if (elem.scrollHeight > minHeight) {
                if (maxHeight && elem.scrollHeight > maxHeight) {
                    height = maxHeight - padding;
                    style.overflowY = 'auto';
                } else {
                    height = elem.scrollHeight - padding;
                    style.overflowY = 'hidden';
                };
                style.height = 20+height + extra + 'px';
                scrollTop += parseInt(style.height) - elem.currHeight;
                document.body.scrollTop = scrollTop;
                document.documentElement.scrollTop = scrollTop;
                elem.currHeight = parseInt(style.height);
            };
        };

        addEvent('propertychange', change);
        addEvent('input', change);
        addEvent('focus', change);
        change();
    };
</script>

{%end%}