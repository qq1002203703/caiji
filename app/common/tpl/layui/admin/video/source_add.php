<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>添加资源</title>
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
            <input type="hidden" name="vid" value="<?=get('vid','int',0)?>">
            <input type="hidden" name="iscaiji" value="1">
            <div class="layui-form-item">
                <label class="layui-form-label" for="name">来源名称</label>
                <div class="layui-input-inline">
                    <input id="name" type="text" name="name" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">更新时间</label>
                <div class="layui-input-inline">
                    <input id="update_time" class="layui-input datetime-input" type="text" name="update_time" value="">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">状态</label>
                <div class="layui-input-inline">
                    <select id="status" name="status">
                        <option value="0">失效</option>
                        <option value="1" selected>正常</option>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">种类</label>
                <div class="layui-input-inline">
                    <select id="type" name="type"  lay-filter="select1">
                        <option value="m3u8" selected>m3u8</option>
                        <option value="xigua">西瓜影音</option>
                        <option value="xunlei">迅雷下载</option>
                        <option value="xfplay">先锋影音</option>
                        <option value="baidupan">百度云盘</option>
                    </select>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">是否完结</label>
                <div class="layui-input-inline">
                    <select id="isend" name="isend">
                        <option value="0">否</option>
                        <option value="1" selected>是</option>
                    </select>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">资源地址</label>
                <div class="layui-input-block">
                    <textarea name="url" class="layui-textarea" rows="8"></textarea>
                </div>
            </div>

        </form>
</div>


<script type="text/javascript">
    layui.use(['layer','jquery','form','laydate'], function(){
        var $ = layui.jquery,laydate=layui.laydate;
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
    });
</script>
</body>
</html>