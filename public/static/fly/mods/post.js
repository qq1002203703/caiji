layui.define(['fly','spinner','flow'], function(exports){
    var $ = layui.jquery;
    var layer = layui.layer;
    var form = layui.form;
    var fly = layui.fly;
    var flow = layui.flow;
    layer.ready(function(){
        $('.spinner-input').spinner({min:1, value:1,step:1});
    });
    //马上购买
    $('#shop-buy').on('click',function () {
        layer.open({
            type: 1,
            title :'购买',
            area: '300px',
            btn:'提交',
            content: $('#form-buy'), //这里content是一个普通的String
            success: function(layero, index){
                layero.find('#form-buy').show();
            },
            yes:function (index,layero) {
                var form=layero.find('#form-buy');
                var email=form.find('#form-buy-email').val();
                if(email=='' || /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(email)==false){
                    layer.msg('接收邮箱格式不正确！');
                }else{
                    layer.confirm('已经核对正确了吗？',{icon: 3, title:'提示'},function () {
                        form.submit();
                        layer.close(index);
                    });
                }
            }
        });
        return false;
    });
    //加入购物车
    $('#shop-cart').on('click',function () {
        $.ajax({
            type: "POST",
            url: currentData.shopCartJson,
            data: { id: currentData.id},
            success: function(data){
            if(data.msg){
                layer.msg( data.msg );
                layer.open({
                    type:1,
                    content:'<div class="layui-field-box">'+data.msg+'</div>',
                    btn:['查看购物车', '关闭'],
                    yes:function () {
                        //跳转到购物车
                        window.location.href=currentData.shopCartUrl;
                    }
                });
            }else {
                layer.msg('服务器出错了');
            }
        },
            error:function () {
                layer.msg( '连接超时');
            }
        });
    });
    //批量回复
    $("#ctrl-multi-reply").on('click',function () {
        layer.open({
            type:1,
            title:"批量回复",
            area: ['500px', '360px'],
            btn:["马上提交",'取消'],
            content:'<div id="layer-box" style="padding:0 15px 10px;">' +
                '    <div class="layui-form">' +
                '        <div class="layui-form-item">' +
                '            <div class="layui-form-mid layui-word-aux"><div class="layui-input-inline"><input class="layui-input" id="input_set" value="3"></div><div class="layui-input-inline"><input class="layui-btn" type="button" value="获取格式" onclick="getReplyFormat();"></div></div>' +
                '            <textarea class="layui-textarea text-edit" name="multi_reply" id="multi_reply"></textarea>' +
                '        </div>' +
                '    </div>' +
                '</div>',
            success:function(){
                getReplyFormat();
            },
            yes:function (index, layero) {
                var multi_reply=layero.find("#multi_reply").val();
                fly.json(currentData.commentCtrlUrl+'?c=reply_m',{oid:currentData.id,table_name:currentData.table,multi_reply:multi_reply},function (res) {
                    layer.msg(res.msg,function () {
                        window.location.reload();
                    });
                });
            }
        });
    });
    //评论回复
    $(".comment-list-reply").on("click",function () {
        var _this=$(this);
        var data={};
        data.do=_this.attr('data-do');
        data.id=_this.attr('data-id');
        data.pid=_this.attr('data-pid');
        //console.log(data.pid);
        if(data.do==="1"){
            $("#comment-list-form").remove();
            _this.attr("data-do","0").html("回复");
        }else {
            var html=$("#comment-form").html();
            _this.parent().after('<div class="layui-form layui-form-pane" id="comment-list-form">'+html+'</div>');
            _this.attr("data-do","1").html("取消");
            $("#comment-list-form").find("input[name='pid']").val(data.id);
        }
    });
    //提交评论
    form.on('submit(post)', function(data){
        var action = $(data.form).attr('action');
        fly.json(action, data.field, function(res){
            //默认弹出返回的信息，如果不想弹出，设置button的'alert'属性为'false'
            layer.alert(res.msg, {
                icon: 1,
                time: 10*1000,
                end: function () {
                    window.location.reload();
                }
            });
        },{
            error:function (res) {
                if(res.code===99){
                    fly.json(currentData.commentCtrlUrl+'?c=token',function (ret) {
                        $("#token").val(ret.msg);
                    });
                }
            }
        });
        return false;
    });
    //删除评论
    $(".comment-list-del").on("click",function () {
        var _this=$(this);
        var id=_this.attr('data-id');
        layer.confirm("你真的要删除吗？",{icon: 3, title:'提示'},function (index) {
            fly.json(currentData.commentCtrlUrl+"?c=del&id="+id, {}, function(res){
                layer.msg(res.msg);
                $("#comment-"+id).remove();
                layer.close(index);
            },{type:"get"});
        });
    });
    //常用操作入口
    $(".comment-list-ctrl").on("click",function () {
        var _this=$(this);
        var id=_this.attr('data-id');
        var name=_this.attr('data-name');
        var value=_this.attr('data-value');
        var confirm=_this.attr('data-confirm')==='true';
        var method=_this.attr('data-method')||'get';
        var remove_box=_this.attr('data-remove')||'';
        if(confirm){
            var msg=_this.attr('data-msg')||'你真的要删除吗？';
            layer.confirm(msg,{icon: 3, title:'提示'},function (index) {
                fly.json(currentData.commentCtrlUrl, {c:name,id:id,v:value}, function(res){
                    layer.msg(res.msg);
                    if(remove_box!=='')
                        $("#"+remove_box).remove();
                    layer.close(index);
                },{type:method});
            });
        }else {
            fly.json(currentData.commentCtrlUrl, {c:name,id:id,v:value}, function(res){
                layer.msg(res.msg);
                if(remove_box!=='')
                    $("#"+remove_box).remove();
                layer.close(index);
            },{type:method});
        }
    });
    //管理员编辑评论
    $(".comment-list-edit").on("click",function () {
        var _this=$(this);
        var id=_this.attr('data-id');
        var user =  $("#user-"+id).html(),
            content = $("#content-text-"+id).html();
        layer.open({
            type: 1,
            title: "编辑",
            area: ['500px', '280px'],
            btn: ["提交", '取消'],
            content: '<div id="layer-box" style="padding:10px 15px 10px;">' +
                '    <div class="layui-form">' +
                '        <div class="layui-form-item">' +
                '            <div class="layui-form-label">用户名</div>' +
                '            <div class="layui-input-inline"><input class="layui-input" name="user" value="' + user + '"></div>' +
                '        </div>' +
                '        <div class="layui-form-item">' +
                '            <textarea class="layui-textarea" name="content">' + content + '</textarea>' +                         '         </div>' +
                '    </div>' +
                '</div>',
            yes:function (index, layero) {
                var newContent=layero.find("textarea[name='content']").val();
                var newUser=layero.find("input[name='user']").val();
                console.log(newContent);
                fly.json(currentData.commentCtrlUrl+"?c=edit", {id:id,content:newContent,username:newUser}, function(res){
                    layer.msg(res.msg);
                    //window.location.reload();
                    $("#user-"+id).html(newUser);
                    $("#content-text-"+id).html(newContent);
                    //$("#comment-"+id).remove();
                    layer.close(index);
                });
            }
        });
    });
    //点赞和踩
    window.clickLikes=function (tp,id,ele) {
        var counter=$(ele).find("cite");
        var counterNum=parseInt(counter.html());
        //console.log(counterNum);
        fly.json(currentData.commentCtrlUrl+"?c=like&id="+id+"&type="+tp,function (res) {
            layer.msg(res.msg);
            counter.html(counterNum+1);
        });
    };
    //批量回复时，获取格式化的内容
    window.getReplyFormat=function () {
        var num=$("#input_set").val();
        fly.json(currentData.commentCtrlUrl+"?c=format&id="+currentData.id+"&table="+currentData.table+"&p="+num,function (res) {
            $("#multi_reply").val(res.data);
        });
    };
    //流加载评论
    $("#comment-list-more").click(function () {
        flow.load({
            elem: '#comment-list' //指定列表容器
            ,isAuto:false
            ,done: function(page, next){
                $.get(currentData.commentCtrlUrl+"?c=flow&id="+currentData.id+"&table="+currentData.table+"&page="+page, function(res){
                    next(res.data, page < res.pages);
                });
            }
        });
        $(this).remove();
    });
    //更新时间
    window.renewDate=function(){
        fly.json(currentData.commentCtrlUrl+"?c=renew_date&id="+currentData.id+"&table="+currentData.table,function (res) {
            layer.msg(res.msg);
            window.location.reload();
        })
    };

    exports('post', null);
});