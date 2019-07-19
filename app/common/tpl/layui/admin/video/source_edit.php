<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?=$title?></title>
    <link href="/static/lib/layui/css/layui.css?v=1.01" rel="stylesheet" type="text/css">
    <link href="/static/admin/layui/css/global.css?v=1.0.0" rel="stylesheet" type="text/css">
    <script src="/static/lib/layui/layui.js" type="text/javascript" charset="utf-8"></script>
    <style>
        html{margin:0;padding:0;}
        .layui-container{margin-top:10px;}
    </style>
</head>
<body>
<div class="layui-container">
        <form class="layui-form">
            <input type="hidden" name="id" value="<?=get('id','int',0)?>">
            <input type="hidden" name="iscaiji" value="1">
            <input type="hidden" name="vid" value="<?=$data['vid'];?>">
            <div class="layui-form-item">
                <label class="layui-form-label" for="name">来源名称</label>
                <div class="layui-input-inline">
                    <input id="name" type="text" name="name" class="layui-input" value="<?=$data['name']?>">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">更新时间</label>
                <div class="layui-input-inline">
                    <input id="update_time" class="layui-input datetime-input" type="text" name="update_time" value="<?=date('Y-m-d H:i:s',$data['update_time']);?>">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">状态</label>
                <div class="layui-input-inline">
                    <select id="status" name="status">
                        <option value="0"<?=echo_select($data['status'],0)?>>失效</option>
                        <option value="1"<?=echo_select($data['status'],1)?>>正常</option>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">种类</label>
                <div class="layui-input-inline">
                    <select id="type" name="type"  lay-filter="select1">
                        <option value="m3u8"<?=echo_select($data['type'],'m3u8')?>>m3u8</option>
                        <option value="xigua"<?=echo_select($data['type'],'xigua')?>>西瓜影音</option>
                        <option value="xunlei"<?=echo_select($data['type'],'xunlei')?>>迅雷下载</option>
                        <option value="xfplay"<?=echo_select($data['type'],'xfplay')?>>先锋影音</option>
                        <option value="baidupan"<?=echo_select($data['type'],'baidupan')?>>百度云盘</option>
                    </select>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">是否完结</label>
                <div class="layui-input-inline">
                    <select id="isend" name="isend">
                        <option value="0"<?=echo_select($data['isend'],'0')?>>否</option>
                        <option value="1"<?=echo_select($data['isend'],'1')?>>是</option>
                    </select>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">
                    <p>资源地址 :<br></p>
                    <p style="margin-top: 5px;"><a href="javascript:;" class="layui-btn layui-btn-sm" id="url-reverse">排序反转</a></p>
                    <p style="margin-top: 5px;"><a href="javascript:;" class="layui-btn layui-btn-sm" id="url-replace">批量替换</a></p>
                </label>
                <div class="layui-input-block">
                    <textarea name="url" class="layui-textarea" rows="8"><?=$data['url'];?></textarea>
                </div>
            </div>
        </form>
</div>


<script type="text/javascript">
    layui.use(['layer','jquery','form','laydate'], function(){
        var $ = layui.jquery, layer = layui.layer,laydate=layui.laydate;
        if($('.datetime-input').length){
            var renderData;
            $('.datetime-input').each(function(i, item){
                renderData = {
                    elem: item
                    ,type: 'datetime'
                    ,format: 'yyyy-MM-dd'
                };
                if($(item).attr('value')){
                    renderData.value = $(item).attr('value');
                }
                if($(item).attr('min')){
                    renderData.min = $(item).attr('min');
                }
                if($(item).attr('max')){
                    renderData.max = $(item).attr('max');
                }
                laydate.render(renderData);
            });
        }
        //反转
        $('#url-reverse').click(function () {
            var textBox=$('textarea[name=url]');
            var urls=textBox.val();
            urls=urls.replace(/^\s+|\s+$/gm,'').split("\n").reverse().join("\n");
            textBox.val(urls);
            return false;
        });
        $('#url-replace').click(function () {
            layer.open({
                title:'批量替换',
                type: 1,
                content: '<div class="layui-container"><form class="layui-form"><div class="layui-form-item"><label class="layui-form-label">匹配正则</label><div class="layui-input-inline"><input id="update_time" class="layui-input datetime-input" type="text" name="regex" value=""></div></div><div class="layui-form-item"><label class="layui-form-label">替换内容</label><div class="layui-input-inline"><input id="update_time" class="layui-input datetime-input" type="text" name="replace" value=""></div></div></form></div>',
                area: '500px',
                btn: ['确定', '取消'],
                yes: function(index, layero){
                    var regex_text= layero.find('input[name=regex]').val().replace(/^\s+|\s+$/gm,'');
                    if(!regex_text && regex_text !=='0'){
                        layer.msg('匹配正则不能为空');
                        return false;
                    }
                    var re=new RegExp(regex_text,'g');
                    var replace_text=layero.find('input[name=replace]').val();
                    var textBox=$('textarea[name=url]');
                    var urls=textBox.val();
                    urls=urls.replace(/^\s+|\s+$/gm,'').split("\n");
                    layui.each(urls,function (i,v) {
                        v=v.split("$");
                       urls[i]=v[0].replace(re,replace_text)+'$'+v[1];
                    });
                    textBox.val(urls.join("\n"));
                    layer.close(index);
                }
            });
            return false;
        });
    });
</script>
</body>
</html>