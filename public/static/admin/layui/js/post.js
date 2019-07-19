//富文本编辑器
/*var editor = new Simditor({
    textarea: $('#content'),
    toolbar: ['title','bold', 'italic', 'color','link', '|', 'ol', 'ul','code','|','html','fullscreen']
});*/
var ue = UE.getEditor('content');
layui.use(['form','upload', 'element','fly','layer'], function(){
    var $ = layui.jquery,form=layui.form,layer=layui.layer;
    var fly=layui.fly;
    //草稿1按钮点击
    $('#caogaop').click(function () {
        var  aaa=$("#status").val('2'); //设置status的值为2
        form.render('select',aaa.attr('lay-filter')); //重新渲染这个select元素
        $("#submitp").click(); //提交表单
        return false;
    });
    //草稿2按钮点击
    $('#caogaop_a').click(function () {
        $("#caogaop").click();
        return false;
    });
    //定时发布1
    $("#yuyue").click(function () {
        layer.open({
            type: 1,
            title: "定时发布",
            skin: 'layui-layer-rim',
            area: ['420px', '240px'],
            content: $('#time_send'),
            btn: ['发布', '取消'],
            yes: function(index, layero){
                $("#published_time").val($("#time-send-select").val());
                $("#submitp").click();
            }
        });
        return false;
    });
    //定时发布2
    $("#yuyue_a").click(function () {
        $("#yuyue").click();
        return false;
    });
    //-------网盘--------------------------
    var file_num=$('#num');
    /*绑定点击事件到.file_add*/
    $(document).on('click','.file_add',function(){
        $(this).parents('tr').after(getItem());
        file_num.html(parseInt(file_num.html())+1);
    });
    /*绑定点击事件到.file_cancel*/
    $(document).on('click','.file_cancel',function(){
        $(this).parents('tr').remove();
        file_num.html(parseInt(file_num.html())-1);
    });
    //-------网盘结束--------------------------
    //-------扩展项--------------------------
    var more_num=$('#num_more');
    /*绑定点击事件到.more_add*/
    $(document).on('click','.more_add',function(){
        $(this).parents('tr').after(getMoreItem());
        more_num.html(parseInt(more_num.html())+1);
    });
    /*绑定点击事件到.more_cancel*/
    $(document).on('click','.more_cancel',function(){
        $(this).parents('tr').remove();
        more_num.html(parseInt(more_num.html())-1);
    });
    //-------扩展项结束--------------------------
    //--------图片上传----------------------------
    $("#thumbnail").on('click',function() {
        layer.open({
            type: 2,
            title: '上传缩略图',
            maxmin: true,
            resize:false,
            scrollbar:false,
            shadeClose: true,
            area : ['800px' , '580px'],
            btn:['确认','取消'],
            content: currentData.uploader
            ,yes:function (index, layero) {
                var currentTab=layer.getChildFrame('.layui-tab > .layui-tab-content >.layui-show', index);
                var imageList=currentTab.find('.upload-list > .image-item input[type=checkbox]:checked');
                if(imageList.length<1){
                    layer.confirm('请先上传图片，或选中已有图片！');
                }else {
                    imageList.each(function (index2, domEle){
                        var id=domEle.getAttribute('data-id');
                        var uri=domEle.getAttribute('data-uri');
                        $("#pic_S").append(' <div class="image-item" style="width:61px; height:61px;" id="pic_item_'+id+'"><div class="inner" style="width:34px; height:34px;"><a id="pic_slt_'+id+'" data_id="'+id+'"><img class="lazy" style="width:100%;height:100%" src="'+uri+'" id="attachment-'+id+'"></a><input type="hidden" name="images_id[]" value="'+id+'"><input type="hidden" name="images_url[]" value="'+uri+'"></div></div>');
                        //console.log(id+'=>'+uri);
                    });
                    var counter=$("#upload-count");
                    counter.html(parseInt(counter.html())+imageList.length);
                    layer.close(index);
                }
            }
        });
    });
    //缩略图详情
    $("#pic_S").on('click',"a[id^='pic_slt_']", function() {
        var _this=$(this);
        var data_url=_this.find('img').attr('src');
        layer.open({
            type: 1,
            title: "图片详情",
            skin: 'layui-layer-rim',
            //area: ['90%', '90%'],
            shadeClose:true,
            btn:'取消',
            content: '<img src="'+data_url+'" style="width: 100%">',
            yes:function (index, layero) {
                _this.parent().parent().remove();
                var counter=$("#upload-count");
                counter.html(parseInt(counter.html())-1);
                layer.close(index);
            }
        });
    });
    window.toggle_pic_S = function() {
        $("#pic_S").toggle();
    };
    window.toggle_pic_q = function() {
        $("#pic_q").toggle();
    };
    //--------图片上传结束----------------------------
    /**---------------聚合----------------**/
    //上级聚合 点击后->弹出框架，输入关键词，搜索->在搜索结果选择->把选择的放到这里
    $('#juhe-parent-select').click(function () {
        var url=$(this).attr('data');
        layer.prompt({
            formType: 0,
            value: '',
            'title':'输入关键词',
            maxlength: 140,
        },function (value, index) {
            var pid=parseInt($('#pid').attr('value'));
            $.post(url,{key:value,id:currentData.id,pid:pid},function (data) {
                layer.open({
                    type:1,
                    title:'请选择下级：支持多个',
                    content:data.status==0 ? formatSearchResult(data.data):data.msg,
                    success: function(layero, sindex){
                        if(layero.find('.sul').length >0){
                            layer.close(index);
                        }else {
                            layer.msg(data.msg);
                            layer.close(sindex);
                        }
                    },
                    btn:['确定', '取消'],
                    yes:function (sindex,layero) {
                        var theCheckBox=layero.find('.scheck:checked');
                        var i=0;
                        data=data.data;
                        if(theCheckBox.length <1)
                            layer.msg('最少要选中一项');
                        else {
                            i=parseInt(theCheckBox.attr('value'));
                            $('#juhe-parent-container').html(data[i].id+'. '+data[i].title+'<i class="juhe-cancel">&#215;</i>');
                            $('#pid').attr('value',data[i].id);
                            layer.close(sindex);
                        }
                    }
                });
            });
        });
    });
    //下级聚合
    $('#juhe-children-select').click(function () {
        var url=$(this).attr('data');
        layer.prompt({
            formType: 0,
            value: '',
            'title':'输入关键词',
            maxlength: 140,
        },function (value, index) {
            var pid=parseInt($('#pid').attr('value'));
            $.post(url,{key:value,id:currentData.id,pid:pid},function (data) {
                layer.open({
                    type:1,
                    title:'请选择一个作为上级',
                    content:data.status==0 ? formatSearchResult(data.data,true):data.msg,
                    success: function(layero, sindex){
                        if(layero.find('.sul').length >0){
                            layer.close(index);
                        }else {
                            layer.msg(data.msg);
                            layer.close(sindex);
                        }
                    },
                    btn:['确定', '取消'],
                    yes:function (sindex,layero) {
                        var theCheckBox=layero.find('.scheck:checked');;
                        if(theCheckBox.length <1)
                            layer.msg('最少要选中一项');
                        else {
                            var html='';
                            data=data.data;
                            var childrenSelector=$('#children_id');
                            var children_id=childrenSelector.attr('value');
                            theCheckBox.each(function () {
                                var i=parseInt(this.value);
                                html+='<li id="children-data-'+data[i].id+'"><span>'+data[i].id+'</span>. '+data[i].title+'<i class="juhe-cancel" data="'+data[i].id+'">&#215;</i></li>';
                                children_id+=','+data[i].id;
                            });
                            $('#juhe-children-container').append(html);
                            childrenSelector.attr('value',children_id.replace(/^,/,''));
                            layer.close(sindex);
                        }
                    }
                });
            });
        });
    });

    //监控上级聚合的取消
    $('#juhe-parent-container').on('click','.juhe-cancel',function () {
        $('#juhe-parent-container').html('');
        $('#pid').attr('value',0);
    });
    //监控下级聚合的取消
    $('#juhe-children-container').on('click','.juhe-cancel',function () {
        var id=this.getAttribute('data');
        $("#juhe-children-container").children('#children-data-'+id).remove();
        var childrenSelector=$('#children_id');
        var children_ids=childrenSelector.attr('value');
        var reg=new RegExp('^'+id+'(,|$)|,'+id+'(,|$)');
        childrenSelector.attr('value',children_ids.replace(reg,''));
    });
    //监控已经存在的下级聚合的取消
    $('#juhe-children-existing').on('click','.juhe-cancel',function () {
        var id=this.getAttribute('data');
        var childrenExistingSelector=$("#juhe-children-existing");
        var url=childrenExistingSelector.attr('data');
        $.get(url+'?id='+id,function () {
            childrenExistingSelector.children('#children-data-'+id).remove();
        });
    });
    /**---------------聚合结束----------------**/
    /**函数区------------------------------**/
    //网盘添加的html
    function getItem(){
        var file_i=$('#num').html();
        return '<tr><td><input class="layui-input" type="text" name="files['+file_i+'][name]"></td>' +
            '<td><input value="百度网盘" class="layui-input" type="text" name="files['+file_i+'][type]"></td>' +
            '<td><input class="layui-input" type="text" name="files['+file_i+'][url]"></td>' +
            '<td><input value="提取密码:" class="layui-input" type="text" name="files['+file_i+'][remark]"></td>' +
            '<td><a href="javascript:;" class="layui-btn layui-btn-normal layui-btn-sm file_add">增加</a><a href="javascript:;" class="layui-btn layui-btn-danger layui-btn-sm file_cancel">取消</a></td></tr>';
    }
    //扩展项添加的html
    function getMoreItem () {
        var more_i=$('#num_more').html();
        var html='<tr>';
        html+='<td><input class="layui-input" type="text" name="more['+more_i+'][name]"></td>';
        html+='<td><input class="layui-input" type="text" name="more['+more_i+'][value]"></td>';
        html+='<td><a href="javascript:;" class="layui-btn layui-btn-normal layui-btn-sm more_add">增加</a><a href="javascript:;" class="layui-btn layui-btn-danger layui-btn-sm more_cancel">取消</a></td>';
        html+='</tr>';
        return html;
    }
    /***上下级函数----------------------**/
    function formatSearchResult(data,multi) {
        var onclickFunc;
        if(multi)
            onclickFunc='changSelectMulti(this)';
        else
            onclickFunc='changSelect(this)';
        var html='<table class="layui-table sul">';
        for(var i=0;i<data.length;i++){
            html+='<tr>';
            html+='<td class="std"><input type="checkbox" value="'+i+'" class="scheck" onclick="'+onclickFunc+'"></td>';
            html+='<td class="sid">'+data[i].id+'</td>';
            html+='<td class="stitle">'+data[i].title+'</td>';
            html+='</tr>';
        }
        return html+'</table>';
    }
    window.changSelect=function (current) {
        $('.scheck').prop('checked', false);
        $(current).prop('checked', true);
    }
    window.changSelectMulti=function(current) {
        var _this=$(current);
        if(_this.is(':checked')){
            _this.prop('checked', true);
        }else {
            _this.prop('checked', false);
        }
    }
    /***上下级函数结束----------------------**/
    /**--tag标签--------------------------------**/
    //'添加'按钮点击时
    $("#tagsend").on('click', function(){
        var tagstr=$("#tag_str"),
            tagstrValue=tagstr.val();
        if(!tagstrValue) {
            layer.open({title: "提示", skin: 'layui-layer-rim', content: '请填写Tag！'});
            tagstr.focus();
            return false;
        }
        var tagsel=$("#tag_sel");
        var tags = [];//已存在的tag
        var tagRepeat='';//重复的标签
        //var isEdit=currentData.isEdit || false;
        tagsel.find("input[name='tags[]']").each(function(i, elem) {
            tags[i] =elem.value;
        });
        $.each(tagstrValue.replace(/，/g,',').split(","),function (i,item) {
            if(tags.indexOf(item) >-1){ //标签已经在tags中存在
                tagRepeat+=','+item;
                return true;//跳出当次循环
            }
            tagsel.append('<span class="tag"><a class="tag-del"  href="javascript:;"><i class="layui-icon">&#x1007</i></a>'+item+' <input type="hidden" name="tags[]" value="'+item+'"></span>');
            tags.push(item);
        });
        tagstr.val('');
        if(tagRepeat !=='')
            layer.alert('标签："'+tagRepeat.replace(/^,/,'')+'" 重复了');
    });
    //监控标签删除
    $("#tag_sel").on('click','.tag-del',function () {
        $(this).parent().remove();
    });
    //监控标签更新帮助（在编辑页才有，添加加页没有）
    $("#tags-update-help").click(function () {
        fly.help('在编辑页面，对标签进行新添加/修改等操作,<br>需要在确定好标签后，点击下面的“更新标签”,<br>这样修改才会生效（会马上更新到数据库中）');
    });
    //监控标签更新（在编辑页才有，添加加页没有）
    $("#tags-update").click(function () {
        var _this=$(this);
        var allTags=[];
        $("#tag_sel").find("span.tag").each(function (i,e) {
            var eo=$(e).find("input[name='tags[]']").val();
            //eo.find('a').remove();
            allTags.push(eo);
        });
        if (allTags.length == 0) {
            layer.msg('请先添加tag');
            $("#tag_str").focus();
            return false;
        }
        fly.json(currentData.tagsEditUrl+'?type='+currentData.type,{tags:allTags,id:currentData.id},function (res) {
            layer.msg(res.msg);
        });
        return false;
    });
    /**--tag标签结束--------------------------------**/
});

