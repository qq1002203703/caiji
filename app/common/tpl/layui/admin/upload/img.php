<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>图片上传</title>
    <link href="/static/lib/layui/css/layui.css?v=1.01" rel="stylesheet" type="text/css">
    <link href="/static/admin/layui/css/global.css?v=1.0.0" rel="stylesheet" type="text/css">
    <script src="/static/lib/layui/layui.js" type="text/javascript" charset="utf-8"></script>
    <style>
        html{margin:0;padding:0;}
        .layui-tab{margin:0;}
        .inner { margin:0 !important; }
        .image-file a { position: absolute;  top: -10px !important;  left: 0; }
        .image-check{position:absolute;z-index:1003;left: 10px;top: 10px;}
        .image-check input{width:20px;height: 20px;}
        .image-check2{position:absolute;z-index:1003;right: 10px;top: 10px;}
        .image-check2 a{color: #880000}
        .image-check2 a:hover{color: #666}
        .layui-this { background-color: rgb(233, 229, 229); }
        .image-item{min-width:160px;max-width:180px;margin-left:8px;margin-bottom:8px;overflow:hidden}
        .upload-list .layui-flow-more{display: block;min-width:160px;max-width:180px;float: left;margin-bottom:8px;}
    </style>
</head>
<body>
<div class="layui-tab layui-tab-card" lay-filter="aaaa">
    <ul class="layui-tab-title">
        <li class="layui-this">本地上传</li>
        <li>网络图片</li>
        <li>相册</li>
    </ul>
    <div class="layui-tab-content" style="height: 454px;" id="remark">
        <div class="layui-tab-item layui-show"><!--本地图片-->
            <table class="layui-table" lay-skin="nob">
                <tbody>
                <tr>
                    <td width="100px" valign="top" align="center" style="border-right:1px dashed #cccccc;min-height: 100%; ">
                        <div style="margin-top:20px;display: inline-block;">
                            <button id="thumb-button" type="button" class="layui-btn thumbnail">
                                <i class="layui-icon">&#xe67c;</i>上传图片
                            </button>
                        </div>
                    </td>
                    <td><div id="loc-container" class="upload-list"></div></td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="layui-tab-item"><!--网络图片-->
            <form class="layui-form">
                <div class="layui-form-item">
                    <label class="layui-form-label">网络图片</label>
                    <div class="layui-input-inline" style="width: 60%;">
                        <input id="net-input" type="text" name="img2net"  placeholder="输入网络图片的url" class="layui-input">
                    </div>
                    <div class="layui-input-inline" style="width: 80px;"><a id="net-add" href="javascript:;" class="layui-btn layui-btn-normal">添加</a></div>
                </div>
            </form>
            <div id="net-container" class="upload-list"></div>
        </div>
        <div class="layui-tab-item"><!--相册-->
            <div id="inner_pic" class="upload-list">
                <?php if($data): foreach ($data as $item) :?>
                        <div class="image-item">
                            <div class="inner">
                                <div class="image-file">
                                    <a href="javascript:;">
                                        <img class="lazy" lay-src="<?=$item['uri']?>" id="attachment-<?=$item['id']?>"/>
                                    </a>
                                </div>
                            </div>
                            <div class="image-check">
                                <input type="checkbox" name="images[]" id="img_<?=$item['id']?>" data-uri='<?=$item['uri']?>' data-id="<?=$item['id']?>">
                            </div>
                            <div class="image-check2">
                                <a href="#" onclick="js_del(this);return false;" data-id="<?=$item['id']?>">删除</a>
                            </div>
                        </div>
                    <?php endforeach; endif;?>
            </div>
        </div>
    </div>
    <span class="layui-hide" id="num_more">1</span>
</div>
<script type="text/javascript">
    var js_cancel, js_del;
    layui.use(['layer','jquery','flow','upload', 'element'], function(){
        $ = layui.jquery;
        var flow = layui.flow;
        var upload = layui.upload;
        var element = layui.element;
        var  layer = layui.layer;
        var isFirstOpenTab2=false;//是否第一次打开相册
        //文件上传
        upload.render({
            elem: '#thumb-button'
            ,size:1024
            ,url:'<?=url('admin/upload/verify')?>'
            ,field: 'pic'
            ,exts: 'jpg|jpeg|gif|png'
            ,accept:'images'
            ,multiple:false
            ,number:3
            ,data:{'__token__':"<?=\app\common\ctrl\Func::token()?>"}
            ,done: function(res, index, upload){
                if(res.status==0) {
                    layer.msg('上传成功!');
                    var ele=getItem({id:res.data.id,uri:res.data.uri},'js_del','删除');
                    $("#loc-container").append(ele);
                }else {
                    layer.msg(res.msg);
                }
            }
        });

        //监控tab
        element.on('tab(aaaa)', function(data){
            //当第一次打开相册时，进行流加载
            if(data.index==2 && !isFirstOpenTab2){
                isFirstOpenTab2=true;
                flow.load({
                    elem: '#inner_pic'
                    ,isAuto:false
                    ,isLazyimg:true
                    ,done: function(page, next){ //到达临界点（默认滚动触发），触发下一页
                        var lis = [];
                        //以jQuery的Ajax请求为例，请求下一页数据（注意：page是从2开始返回）
                        $.get('<?=url('admin/upload/flow')?>?per=<?=get('per','int',1)?>&page='+page, function(res){
                            //假设你的列表返回在data集合中
                            if(res.status==0){
                                layui.each(res.data, function(index, item){
                                    lis.push(getItem(item,'js_del','删除',false));
                                });
                                //执行下一页渲染，第二参数为：满足“加载更多”的条件，即后面仍有分页
                                //pages为Ajax返回的总页数，只有当前页小于总页数的情况下，才会继续出现加载更多
                                next(lis.join(''), page < res.pages);
                            }else {
                                layer.msg(res.msg);
                            }
                        });
                    }
                });
            }
        });
        layer.ready(function () {
            //懒加载
            flow.lazyimg({elem:'.lazy'});
            //添加按钮
            $("#net-add").click(function () {
                var theInput=$("#net-input");
                var val=theInput.val();
                if(val==''){
                    layer.msg('请先输入图片的url地址');
                    theInput.focus();
                    return false;
                }
                $("#net-container").append(getItem({id:0,uri:val},'js_cancel'));
                theInput.val('');
            });
        });

        function getItem(item,funcName,cancelText,ischeked=true) {
            cancelText=cancelText || '取消';
            var checkit= ischeked ? ' checked disabled' : '';
            return '<div class="image-item">' +
                '<div class="inner"><div class="image-file">'+
                '<a href="javascript:;"><img class="lazy" src="'+item.uri+'" id="attachment-'+item.id+'"></a>'+
                '</div></div>' +
                '<div class="image-check">' +
                '<input type="checkbox" name="images[]" id="img_'+item.id+'" data-uri="'+item.uri+'" data-id="'+item.id+'"'+checkit+'>'+
                '</div>'+
                '<div class="image-check2">' +
                '<a href="#" onclick="'+funcName+'(this);return false;" data-id="'+item.id+'">'+cancelText+'</a>'+
                '</div>'+
                '</div>';
        }
        //网络图片的取消
        js_cancel=function(_this) {
            $(_this).parent().parent().remove();
        };
        //本地图片的取消
        js_del=function (_this) {
            layer.confirm("真的要删除吗？<br>会删除已经上传到服务器上对应的图片", function(index){
                var thisObj=$(_this);
                var id=thisObj.attr('data-id');
                $.get("<?=url('admin/upload/del')?>?id="+id,function (res) {
                    if(res.status==0){
                        thisObj.parent().parent().remove();
                    }else {
                        layer.msg(res.msg);
                    }
                });
                layer.close(index);
            });
        };
    });
</script>
</body>
</html>