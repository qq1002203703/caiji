
layui.define(['layer', 'element', 'form', 'util','laydate'], function(exports){
    var $ = layui.jquery
        ,layer = layui.layer
        ,form = layui.form
        ,util = layui.util
        , laydate= layui.laydate
        ,device = layui.device()
        ,element = layui.element;
    //阻止IE9以下访问
    if(device.ie && device.ie < 9){
        layer.alert('你使用的IE浏览器版本过低，你使用chrome、firefox、360极速版等现代浏览器，如果您非得使用ie浏览器，那么请使用ie9+');
    }
    //当前导航的选中
    $('#'+navHeader).addClass('layui-this');
    if('undefined' != typeof navSidebar){
        var _this=$('#'+navSidebar).addClass('layui-this');
        if(!_this.hasClass('layui-nav-item')){
            var curentParent=_this.parent();
            if(curentParent.hasClass('layui-nav-item')){
                curentParent.addClass('layui-nav-itemed');
            }else {
                curentParent.parent().addClass('layui-nav-itemed');
            }
        }
    }
    //element.render('nav');


    //dropdown
    $('.dropdown-toggle').click(function(){
        $(this).next('.dropdown-menu').toggle(100);
    });
    //全部选中
    form.on('checkbox(checkall)',function(data){
        if (data.elem.checked) {
            $("input[id^='id_']").each(function(i, obj) {
                $(obj).prop('checked', 'checked');
            });
        } else {
            $("input[id^='id_']").each(function(i, obj) {
                $(obj).prop('checked', false);
            });
        }
        form.render();
    });
    var fly = {
        loadIndex: ''
        //Ajax
        ,json: function(url, data, success, options){
            this.loadIndex = layer.load(1);
            var that = this;
            options = options || {};
            data = data || {};
            return $.ajax({
                type: options.type || 'post',
                dataType: options.dataType || 'json',
                data: data,
                url: url,
                success: function(res){
                    layer.close(that.loadIndex);
                    if(res.code === 0) {
                        success && success(res);
                    } else {
                        layer.confirm(res.msg||res.code, {title:'提示'}, function(index){
                            layer.close(index);
                        });
                    }
                }, error: function(){
                    layer.close(that.loadIndex);
                    options.error || layer.msg('请求异常，请重试', {shift: 6});
                }
            });
        }

        //将普通对象按某个key排序
        ,sort: function(data, key, asc){
            var obj = JSON.parse(JSON.stringify(data));
            var compare = function (obj1, obj2) {
                var value1 = obj1[key];
                var value2 = obj2[key];
                if (value2 < value1) {
                    return -1;
                } else if (value2 > value1) {
                    return 1;
                } else {
                    return 0;
                }
            };
            obj.sort(compare);
            if(asc) obj.reverse();
            return obj;
        }

        //计算字符长度
        ,charLen: function(val){
            var arr = val.split(''), len = 0;
            for(var i = 0; i <  val.length ; i++){
                arr[i].charCodeAt(0) < 299 ? len++ : len += 2;
            }
            return len;
        }
        //打印变量
        ,dump:function (theVar,useToSource) {
            if(useToSource===undefined)
                useToSource=true;
            if(useToSource && theVar !==undefined)
                window.console.log(theVar.toSource());
            else
                window.console.log(theVar);
        }
        //插入右下角悬浮bar
        ,rbar: function(){
            var style = $('head').find('.fly-style'),
                skin = {stretch: 'charushuipingxian'};
            var str = '<ul class="fly-rbar">';
            str += '<li id="F_topbar" class="iconfont icon-top" method="top"><i style="font-size: 44px" class="layui-icon">&#xe604;</i></li>';
            str +=  '</ul>';
            var html = $(str);
            $('body').append(html);
            //事件
            html.find('li').on('click', function(){
                var othis = $(this), method = othis.attr('method');
                dict[method].call(this, othis);
            });
            //滚动
            var log = 0, topbar = $('#F_topbar'), scroll = function(){
                var stop = $(window).scrollTop();
                if(stop >= 200){
                    if(!log){
                        topbar.show();
                        log = 1;
                    }
                } else {
                    if(log){
                        topbar.hide();
                        log = 0;
                    }
                }
                return arguments.callee;
            }();
            $(window).on('scroll', scroll);
        },
        //弹出帮助提示信息
        help:function (msg) {
            layer.open({
                type:1,
                title:'提示',
                content:'<div class="text-box1">'+msg+'</div>'
            });
        },
        /**
         * 点击一个按钮或链接，进行ajax访问
         * @param {string} elem  选择器
         * @param {function} func ajax成功提交后的回调函数
         */
        ajaxClick:function (elem,func) {
            var _this=$(elem),
                action = _this.attr('ac'),
                alert = _this.attr('alert') || false,
                msg   = _this.attr('msg') || '你确实要删除吗?不可恢复',
                confirm=true;
            func=func || function(res){
                alert && layer.alert(res.msg, {time: 3000});
                _this.parent().parent().parent().remove();
            };
            if(_this.attr('confirm')==='false'){
                confirm=false;
            }
            if(confirm){//弹出确认，再ajax提交
                layer.confirm(msg, {icon: 0, title:'提示'}, function(index){
                    fly.json(action, {}, function(res){
                        func(res,_this);
                    });
                    layer.close(index);
                });
            }else {//直接ajax提交
                fly.json(action, {}, function(res){
                    func(res);
                });
            }

        }
    };

    //表单提交
    form.on('submit(ajax)', function(data){
        var action = $(data.form).attr('action'), button = $(data.elem);
        fly.json(action, data.field, function(res){
            var end=function(){if(res.action){location.href = res.action;}};
            //默认弹出返回的信息，如果不想弹出，设置button的'alert'属性为'false'
            (button.attr('alert')==='false') ? end() : layer.alert(res.msg, {
                icon: 1,
                time: 10*1000,
                end: end
            });
        });
        return false;
    });
    //监控批量操作提交的表单
    form.on('submit(multi)',function (data) {
        /*console.log(data.elem) //被执行事件的元素DOM对象，一般为button对象
        console.log(data.form) //被执行提交的form对象，一般在存在form标签时才会返回
        console.log(data.field)//页面所有的数据*/
        var bach=$("#batch").val() || $("#batch2").val();
        if(!bach){
            layer.msg('请先选中要操作的方法');
            return false;
        }
        var action=data.form.action+'&ac='+bach;
        var button=$(data.elem);
        switch (bach) {
            case 'del':
                layer.confirm('你确实要删除吗?不可恢复', {btn: ['确认删除','取消']}, function(){
                    fly.json(action, data.field, function(res){
                        var end=function(){if(res.action){location.href = res.action;}};
                        //默认弹出返回的信息，如果不想弹出，设置button的'alert'属性为'false'
                        (button.attr('alert')==='false') ? end() : layer.alert(res.msg, {
                            icon: 1,
                            time: 10*1000,
                            end: end
                        });
                    });
                });
                return false;
            default:
                layer.msg('非法的操作方法');
        }
        return false;
    });
    //日期时间选择器
    if($('.datetime-input').length){
        var renderData;
        $('.datetime-input').each(function(i, item){
            renderData = {
                elem: item
                ,type: 'datetime'
                ,format: 'yyyy-MM-dd HH:mm:ss'
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
            //console.log(renderData);
            laydate.render(renderData);
        });
    }
    //回退来路
    $("#go-back-btn").click(function () {
        window.history.go(-1);
    });
    //固定Bar
    util.fixbar({
        bar1: false
        ,bgcolor: '#009688'
        ,click: function(type){
            if(type === 'bar1'){
                layer.msg('打开 index.js，开启发表新帖的路径');
                //location.href = 'jie/add.html';
            }
        }
    });
    exports('fly', fly);
});