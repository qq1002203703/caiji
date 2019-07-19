/**
 * 缩略图上传，主要是与页面的交互操作
 * 页面需要有三个地方
 *  id=thumb-button 上传按钮
 *  id=thumb-container 接收多图结果的
 *  id=thumb-logo 展示第一张图
 */
layui.use(['layer'], function() {
    var $=layui.jquery,
        layer=layui.layer;
    var nopic='/uploads/images/notpic.gif'; //没有图片的默认图
    //上传缩略图
    $("#thumb-button").on('click',function() {
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
                    var picContainer=$("#thumb-container");
                    var counter=$("#upload-count");
                    imageList.each(function (index2, domEle){
                        var id=domEle.getAttribute('data-id');
                        var uri=domEle.getAttribute('data-uri');
                        if(index2===0 && counter.html()==='0'){
                            $("#thumb-logo").attr('src',uri);
                        }
                        picContainer.append(' <div class="image-item" style="width:61px; height:61px;" id="pic_item_'+id+'"><div class="inner" style="width:34px; height:34px;"><a id="pic_slt_'+id+'" data_id="'+id+'" title="删除"><img class="lazy" style="width:100%;height:100%" src="'+uri+'" id="attachment-'+id+'"></a><input type="hidden" name="images_id[]" value="'+id+'"><input type="hidden" name="images_url[]" value="'+uri+'"></div></div>');
                    });
                    counter.html(parseInt(counter.html())+imageList.length);
                    layer.close(index);
                }
            }
        });
    });

    //缩略图详情
    $("#thumb-container").on('click',"a[id^='pic_slt_']", function() {
        var _this=$(this);
        var data_url=_this.find('img').attr('src');
        layer.open({
            type: 1,
            title: "图片详情",
            skin: 'layui-layer-rim',
            //area: ['90%', '90%'],
            shadeClose:true,
            btn:'删除',
            content: '<img src="'+data_url+'" style="width: 100%">',
            yes:function (index, layero) {
                _this.parent().parent().remove();
                var theFirst=$("input[name='images_url[]']").first();
                var newSrc= (theFirst.length<1)? nopic : theFirst.val();
                $("#thumb-logo").attr('src',newSrc);
                var counter=$("#upload-count");
                counter.html(parseInt(counter.html())-1);
                layer.close(index);
            }
        });
    });
});
