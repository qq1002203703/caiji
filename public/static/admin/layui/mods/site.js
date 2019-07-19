/**
 @Name: 用户模块
 */
layui.define(['laypage', 'fly', 'element', 'form', 'upload'], function(exports){
    
    var $ = layui.jquery;
    var layer = layui.layer;
    var util = layui.util;
    var laytpl = layui.laytpl;
    var form = layui.form;
    var laypage = layui.laypage;
    var fly = layui.fly;
    var element = layui.element;
    var upload  = layui.upload;

    //Hash地址的定位
    var layid = location.hash.replace(/^#/, '');
    element.tabChange('user', layid);

    element.on('tab(user)', function(elem){
    location.hash = ''+ $(this).attr('lay-id');
    });

  //设置的几个图片上传
  if($('.setting-upload').length){
    upload.render({
      elem: ".setting-upload"
      ,done: function(res){
        if(res.status == 0)
        {
          $('#' + this.item.data('target')).attr('src', res.src);
        }
        else
        {
          layer.msg(res.msg, {icon: 5});
        }

      }
    });
  }

    if($('#addpic'))
    {
      upload.render({
        elem: ".upload-img #addpic"
        ,method: 'post'
        ,url: 'index.php?m=site&c=setting&a=addpic'
        ,done: function(res){
          var end = function(){
            if(res.jump)
            {
              location.href = res.action;
            }
            else
            {
              parent.location.reload();
            }
          };

          if(res.status == 0)
          {
            layer.alert(res.msg, {
              icon: 1,
              time: 10*1000,
              end: end
            });
          }
          else
          {
            layer.msg(res.msg, {icon: 5});
          }

        }
      });
    }
    
    exports('site', null);
});