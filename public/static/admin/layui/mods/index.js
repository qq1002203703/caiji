/**

 @Name: Fly社区主入口

 */
layui.define(['layer', 'laytpl', 'form', 'upload', 'util', 'laydate'], function(exports){
  
  var $ = layui.jquery
  ,layer = layui.layer
  ,laytpl = layui.laytpl
  ,form = layui.form
  ,util = layui.util
  ,device = layui.device()
  ,upload = layui.upload
  ,laydate = layui.laydate;

  //阻止IE9以下访问
  if(device.ie && device.ie < 9){
    layer.alert('你使用的IE浏览器版本过低，你使用chrome、firefox、360极速版等现代浏览器，如果您非得使用ie浏览器，那么请使用ie9+');
  }
  //当前导航的选中
  $('#'+navHeader).addClass('layui-this');
  if('undefined' != typeof navSidebar)
	$('#'+navSidebar).addClass('layui-this');
  //
  layui.focusInsert = function(obj, str){
    var result, val = obj.value;
    obj.focus();
    if(document.selection){ //ie
      result = document.selection.createRange(); 
      document.selection.empty(); 
      result.text = str; 
    } else {
      result = [val.substring(0, obj.selectionStart), str, val.substr(obj.selectionEnd)];
      obj.focus();
      obj.value = result.join('');
    }
  };
  //dropdown
    $('.dropdown-toggle').click(function(){
        $(this).next('.dropdown-menu').toggle(100);

    });

  //需求等
    var dict = {
        /*user_xuqiu: function(othis){
            layer.open({
                type: 2,
                title: '提交需求',
                shadeClose: false,
                shade: 0.8,
                area: ['700px', '80%'],
                content: 'index.php?m=site&c=help&a=xuqiu_quick_form&from='+encodeURIComponent(location.href)
            });
        }
        ,user_bug: function(othis){
            layer.open({
                type: 2,
                title: '提交BUG',
                shadeClose: false,
                shade: 0.8,
                area: ['700px', '80%'],
                content: 'index.php?m=site&c=help&a=bug_quick_form&from='+encodeURIComponent(location.href)
            });
        }
        ,*/top: function(othis){
            $('html,body').animate({scrollTop: 0}, 100, function(){
                othis.hide();
            });
        }
    };
  
  var gather = {
     loadIndex: ''
    //Ajax
    ,json: function(url, data, success, options){
          gather.loadIndex = layer.load(1);
      var that = this;
      options = options || {};
      data = data || {};
      return $.ajax({
        type: options.type || 'post',
        dataType: options.dataType || 'json',
        data: data,
        url: url,
        success: function(res){
            layer.close(gather.loadIndex);
            if(res.status === 0)
            {
                success && success(res);
            }
            else
            {
                layer.msg(res.msg||res.code, {shift: 6});
            }
        }, error: function(e){
              layer.close(gather.loadIndex);
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
    
    ,form: {}

    ,cookie: function(e,o,t){
      e=e||"";var n,i,r,a,c,p,s,d,u;if("undefined"==typeof o){if(p=null,document.cookie&&""!=document.cookie)for(s=document.cookie.split(";"),d=0;d<s.length;d++)if(u=$.trim(s[d]),u.substring(0,e.length+1)==e+"="){p=decodeURIComponent(u.substring(e.length+1));break}return p}t=t||{},null===o&&(o="",t.expires=-1),n="",t.expires&&("number"==typeof t.expires||t.expires.toUTCString)&&("number"==typeof t.expires?(i=new Date,i.setTime(i.getTime()+864e5*t.expires)):i=t.expires,n="; expires="+i.toUTCString()),r=t.path?"; path="+t.path:"",a=t.domain?"; domain="+t.domain:"",c=t.secure?"; secure":"",document.cookie=[e,"=",encodeURIComponent(o),n,r,a,c].join("");
    }

    //插入右下角悬浮bar
    ,rbar: function(){
      var style = $('head').find('.fly-style'), skin = {
        stretch: 'charushuipingxian'
      };
      
      var str = '<ul class="fly-rbar">';
      
      /*if(gid == 1)
      {
        str = str + '<li method="user_xuqiu"><a href="javascript://" title="提交需求" style="color:#FFFFFF;font-weight:bold;font-size:9px;">提交需求</a></li>'
            +'<li method="user_bug"><a href="javascript://" title="提交BUG" style="color:#FFFFFF;font-weight:bold;font-size:9px;">发现bug</a></li>';
      }*/
      
      str = str + '<li id="F_topbar" class="iconfont icon-top" method="top"><i style="font-size: 44px" class="layui-icon">&#xe604;</i></li>';
      str = str + '</ul>';
      
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
    }
  };

    //mod下会这么执行
    $('.partial-mod a').click(function(){
        var method = $(this).attr('method');
        if(method){
            dict[method].call(this, $(this));
        }
    });
  //搜索
  $('.fly-search').submit(function(){
    var input = $(this).find('input'), val = input.val();
    if(val.replace(/\s/g, '') === ''){
      return false;
    }
    
    input.val(input.val());
  });
  
  $('.icon-sousuo').on('click', function(){
    $('.fly-search').submit();
  });
    
    gather.check_pay_status_user = function(){
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {},
            url: 'index.php?m=user&c=pay&a=weixin_check_pay_status&id='+order_id,
            success: function(res){
                var end = function(){
                    location.href = res.url;
                };
        
                if(res.status === 0)
                {
                    location.href = res.url;
                }
            }
          });
    };
    
    gather.check_pay_status_site = function(){
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {},
            url: 'index.php?m=site&c=pay&a=weixin_check_pay_status&id='+order_id,
            success: function(res){
                if(res.status === 0)
                {
                    location.href = res.url;
                }
            }
          });
    };
    
    if(location.href.indexOf("m=user")>=0 && location.href.indexOf("paytype=weixin")>=0)
    {
        var interval = setInterval(function(){
            gather.check_pay_status_user();
        }, 1000);
    }
    else if(location.href.indexOf("m=site")>=0 && location.href.indexOf("paytype=weixin")>=0)
    {
        var interval = setInterval(function(){
            gather.check_pay_status_site();
        }, 1000);
    }
  //发送激活邮件
  gather.activate = function(email){
    gather.json('/api/activate/', {}, function(res){
      if(res.status === 0){
        layer.alert('已成功将激活链接发送到了您的邮箱，接受可能会稍有延迟，请注意查收。', {
          icon: 1
        });
      };
    });
  };
  
  $('#LAY-activate').on('click', function(){
    gather.activate($(this).attr('email'));
  });
    
  //表单提交
  form.on('submit(*)', function(data){
    var value = $(this).attr('val');
    var update = $(this).attr('update');
    var jump = $(this).attr('jump');
    var batch = $(this).attr('batch');
    var action = $(data.form).attr('action'), button = $(data.elem);
    if(value)
    {
        action = action + '&value='+value;
    }
    
    if(!jump)
    {
        jump = 1;
    }
    
    if(update == '1')
    {
        data.field.content = editor.getValue();
        //alert($('#content').val());
    }
    var is_confirm = false;
    if(batch == '1')
    {
        if($("#batch").val() == 'del' || $("#batch").val() == 'delok' || $("#batch2").val() == 'del' || $("#batch2").val() == 'delok')
        {
            is_confirm = true;
        }
        else if($("#batch").val() == 'move_article' || $("#batch2").val() == 'move_article')
        {
            $("#form2").attr('action','index.php?m=site&c=content&a=move_content&type=move_article');
            $("#form2").submit();
            return false;
        }
        else if($("#batch").val() == 'move_product' || $("#batch2").val() == 'move_product')
        {
            $("#form2").attr('action','index.php?m=site&c=content&a=move_content&type=move_product');
            $("#form2").submit();
            return false;
        }
		else if($("#batch").val() == 'time' || $("#batch2").val() == 'time')
		{
			$("#form2").attr('action','index.php?m=site&c=content&a=move_content&type=time');
            $("#form2").submit();
            return false;
		}
    }

    if(is_confirm)
    {
        msg = '你确实要删除吗?不可恢复';
        layer.confirm(msg, {
          btn: ['确认删除','取消']
        }, function(){
            gather.json(action, data.field, function(res){
                var end = function(){
                    if(res.jump)
                    {
                        window.location.href = res.action;
                    }
                    else
                    {
                        parent.location.reload();
                    }
                };
				
                if(res.status == 0)
                {
                    button.attr('alert') ? layer.alert(res.msg, {
                      //icon: 1,
                      time: 1000,
                      end: end
                    }) : end();
                };
            });
        }, function(){
			
        });
    }
    else
    {
        gather.json(action, data.field, function(res){
            var end = function(){
                if(jump == 1)
                {	
                    if(res.jump)
                    {
                        location.href = res.action;
                    }
                    else
                    {
                        parent.location.reload();
                    }
                }
            };

            if(res.status == 0)
            {
                button.attr('alert') ? layer.alert(res.msg, {
                  time: 1000,
                  end: end,
                  shadeClose:true
                }) : end();
            };
        });
    }
    
    return false;
  });

  
  form.on('checkbox(checkall)',function(data){
		if (data.elem.checked)
        {
			$("input[id^='id_']").each(function(i, obj) {
				$(obj).prop('checked', 'checked');
			});
		}
        else
        {
			$("input[id^='id_']").each(function(i, obj) {
				$(obj).prop('checked', false);
			});
		}
		
        form.render();
    });
    
  //添加按钮
  $('#openbutton').on('click', function(){
        var action = $(this).attr('ac');
        var title = $(this).attr('title');
        layer.open({
          type: 2,
          title: title,
          shadeClose: true,
          shade: 0.8,
          area: ['500px', '90%'],
          content: action //iframe的url
        }); 
    });
    
    //编辑
    $("a[id^='edita']").on('click', function(){
        var action = $(this).attr('ac');
        var title = $(this).attr('title');
        layer.open({
          type: 2,
          title: title,
          shadeClose: true,
          shade: 0.8,
          area: ['500px', '90%'],
          content: action //iframe的url
        }); 
    });

    //删除
    $("a[id^='deletea']").on('click', function(){
        var action = $(this).attr('ac');
        var alert = $(this).attr('alert');
        var msg   = $(this).attr('msg');
        if(!msg){
            msg = '你确实要删除吗?不可恢复';
        }
        
        if(!alert)
        {
            alert = 1;
        }
        
        if(alert == 1)
        {
            layer.confirm(msg, {icon: 0, title:'提示'}, function(index){
                gather.json(action, {}, function(res){
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
                            //icon: 1,
                            time: 1000,
                            end: end
                        });
                    };
                });

                layer.close(index);
            });
        }
        else if(alert == 2)
        {
            gather.json(action, {}, function(res){
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
                        //icon: 1,
                        time: 1000,
                        end: end
                    });
                };
            });
        }
        
        return false;
    });
    
    $("a[id^='zan']").on('click', function(){
        var action = $(this).attr('ac');

        gather.json(action, {}, function(res){
            if(res.status == 0)
            {
                location.reload();
            };
        });
        
        return false;
    });
    
    $("input[id^='deletea']").on('click', function(){
        var action = $(this).attr('ac');
        var alert = $(this).attr('alert');
        var msg   = $(this).attr('msg');
        if(!msg){
            msg = '你确实要删除吗?不可恢复';
        }
        if(!alert)
        {
            alert = 1;
        }

        if(alert == 1){
            layer.confirm(msg, {icon: 0, title:'提示'}, function(index){
                gather.json(action, {}, function(res){
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
                            time: 3000,
                            end: end
                        });
                    };
                });

                layer.close(index);
            });
        }
        else if(alert == 2)
        {
            gather.json(action, {}, function(res){
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
                        time: 3000,
                        end: end
                    });
                };
            });
        }
        
        return false;
    });
    
    $("input[id^='seltpl']").on('click', function(){
        if(!$('#tpl').val())
        {
            alert('请选择模板');
        }
        else
        {
            location.href = 'index.php?m=site&c=tpl&a=ad&id=' + $('#tpl').val() + '#image';
        }
    });
    
    $("input[id^='seltplindex']").on('click', function(){
        if(!$('#tpl').val())
        {
            alert('请选择模板');
        }
        else
        {
            location.href = 'index.php?m=site&c=tpl&a=index&id=' + $('#tpl').val();
        }
    });  

    if($("#filename").length){
      upload.render({
        elem: ".upload-img #filename"
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
              time: 3000,
              end: end
            });
          }
          else
          {
            layer.msg(res.msg, {icon: 5});
          }

        }
      });
    };
    
    if($("#cfilename").length){
      upload.render({
        elem: ".upload-img #cfilename"
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
              time: 3000,
              end: end
            });
          }
          else
          {
            layer.msg(res.msg, {icon: 5});
          }

        }
      });
    };
    
    //展开栏目
    $("div[id^='qie_1']").on('click', function(){
        var fid = $(this).attr('fid');
        var status = $(this).attr('status');
        
        if(status == 'off')
        {
            $("tr[id^='child_1_"+fid+"']").show();
            $(this).attr('status', 'on');
        }
        else
        {
            $("tr[id^='child_1_"+fid+"']").hide();
            $(this).attr('status', 'off');
        }
        
        form.render();
    });
    
    $("div[id^='qie_2']").on('click', function(){
        var fid = $(this).attr('fid');
        var status = $(this).attr('status');
        
        if(status == 'off')
        {
            $("tr[id^='child_2_"+fid+"']").show();
            $(this).attr('status', 'on');
        }
        else
        {
            $("tr[id^='child_2_"+fid+"']").hide();
            $(this).attr('status', 'off');
        }
        
        form.render();
    });
    
  //监听tab
  /* form.on('select(cat_id_1_1)', function(data){
        $.getJSON("index.php?m=site&c=content&a=ajax_get_child_category&fid="+data.value, function(data){
            var optionstring = "";
            $.each(data.data, function(i,item){
                optionstring += "<option value=\"" + item.id + "\" >" + item.name + "</option>";
            });
            $("#cat_id_1_2").html("<option value=\"\"></option>"+optionstring);
            form.render('select'); //这个很重要
        });
    });
   
  form.on('select(cat_id_2_1)', function(data){
        $.getJSON("index.php?m=site&c=content&a=ajax_get_child_category&fid="+data.value, function(data){
            var optionstring = "";
            $.each(data.data, function(i,item){
                optionstring += "<option value=\"" + item.id + "\" >" + item.name + "</option>";
            });
            $("#cat_id_2_2").html(optionstring);
            form.render('select'); //这个很重要
        });
    }); */
  
  /*
  form.on('select(storage_cat_id)', function(data){
        $.getJSON("index.php?m=user&c=site&a=ajax_get_storage_category&cat_id="+data.value, function(data){
            $("#storage_2").html(data.data);
            $("#storage_1").show();
            form.render('select'); //这个很重要
        });
    });
    */
   
  //监听radio
  form.on('select(page_id_1)', function(data){
      //console.log(data.value);
      if(data.value == 2 || data.value == 3)
      {
          $("#seo").show();
      }
      else
      {
          $("#seo").hide();
      }
      
      if(data.value != 1)
      {
          $("#subject").show();
      }
      else
      {
          $("#subject").hide();
      }
//新加的
    $('#nav-name').val($(data.elem).find("option:selected").text());
  });
  
  form.on('radio(type)', function(data){
        if(data.value == 1)
        {
            var page_id_1 = $("#page_id_1").val();
            
            $("#neizhi").show();
            if(page_id_1 == 2 || page_id_1 == 3)
            {
                $("#seo").show();
            }
            else
            {
                $("#seo").hide();
            }
            
            if(page_id_1 != 1)
            {
                $("#subject").show();
            }
            else
            {
                $("#subject").hide();
            }
            
            $("#product").hide();
            $("#article").hide();
            $("#page").hide();
            $("#wailian").hide();
            
            form.render();
        }
        else if(data.value == 2)
        {
            $("#product").show();
            $("#neizhi").hide();
            $("#article").hide();
            $("#page").hide();
            $("#wailian").hide();
            $("#subject").hide();
            $("#seo").hide();
            
            form.render();
        }
        else if(data.value == 3)
        {
            $("#article").show();
            $("#neizhi").hide();
            $("#product").hide();
            $("#page").hide();
            $("#wailian").hide();
            $("#subject").hide();
            $("#seo").hide();
            
            form.render();
        }
        else if(data.value == 4)
        {
            $("#page").show();
            $("#neizhi").hide();
            $("#article").hide();
            $("#product").hide();
            $("#wailian").hide();
            $("#subject").hide();
            $("#seo").hide();
            
            form.render();
        }
        else if(data.value == 5)
        {
            $("#seo").hide();
            $("#wailian").show();
            $("#neizhi").hide();
            $("#article").hide();
            $("#page").hide();
            $("#product").hide();
            $("#subject").hide();
            
            form.render();
        }
    });
    
    form.on('radio(domain_type)', function(data){
        if(data.value == 1)
        {
            $("#product").show();
            $("#article").hide();
            $("#page").hide();
            
            form.render();
        }
        else if(data.value == 2)
        {
            $("#product").hide();
            $("#article").show();
            $("#page").hide();
            
            form.render();
        }
        else if(data.value == 3)
        {
            $("#article").hide();
            $("#product").hide();
            $("#page").show();
            
            form.render();
        }
    });
    
    $("#submitp").on('click', function(){ 
        if(!$("#title").val()){
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请填写产品名称！'
            });
            
             $("#title").focus();
             return false;
        }
        
        var is_sel = false;
         $('input:checkbox[id=cat_id]:checked').each(function(i){
             is_sel = true;
         });
      
         if(!is_sel)
         {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请选择产品分类！'
            });

             layer.open({
                 type: 1
                 ,title: '提示消息'
                 ,area: '300px;'
                 ,btn: ['继续发布', '重新修改']
                 ,moveType: 1
                 ,content: '<div style="padding: 20px;">您没有选择任何分类, 如果这篇内容确实不需要分类, 请点击继续发布, 否则点击重新修改。</div>'
                 ,yes: function(index, layero){
                     $("#content_form").submit();
                 }
             });
            
             $("#cat_id").focus();
             return false;
         }
         
        var html = ue.getContent();
        if(!html)
        {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请填写产品内容！'
            });
            
            return false;
        }
        
        //console.log(op_mod);
        
        var status = 0;
        var id = $('#content_id_field').val();
        var url = "index.php?m="+op_mod+"&c=content&a=ajax_check_title&type=2&title="+$("#title").val()+"&id="+id+"&filename="+$("#filename").val();
        $.post(url, {}, function (res) {
            if(res.status)
            {
                status = 1;
                //layer.alert(res.msg);
                if(res.status == 1)
                {
                    content = '<div style="padding-left:20px;padding-top:10px;">已经存在重复标题：'+$("#title").val()+"<br>重复内容；<a href='"+res.url+"' target='_blank'>"+$("#title").val()+"</a></div>";
                }
                else if(res.status == 2)
                {
                    content = '<div style="padding-left:20px;padding-top:10px;">已经存在重复自定义URL：'+$("#filename").val()+"<br>重复内容；<a href='"+res.url+"' target='_blank'>"+$("#filename").val()+"</a></div>";
                }
                
                layer.open({
                    type: 1
                    ,title: '提示消息'
                    ,closeBtn: false
                    ,area: '300px;'
                    ,btn: ['继续发布', '重新修改']
                    ,moveType: 1
                    ,content: content
                    ,yes: function(index, layero){
                        $("#content_form").submit();
                      }
                });
                
                return false;
            }
            else
            {
                $("#content_form").submit();
            }
            
        }, 'json');
        
        if(!status)
        {
            return false;
        }
    });
    $("#submitp_a").on('click', function(){ 
        if(!$("#title").val())
        {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请填写产品名称！'
            });
            
             $("#title").focus();
             return false;
        }
        
        var is_sel = false;
         $('input:checkbox[id=cat_id]:checked').each(function(i){
             is_sel = true;
         });
      
         if(!is_sel)
         {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请选择产品分类！'
            });

             layer.open({
                 type: 1
                 ,title: '提示消息'
                 ,area: '300px;'
                 ,btn: ['继续发布', '重新修改']
                 ,moveType: 1
                 ,content: '<div style="padding: 20px;">您没有选择任何分类, 如果这篇内容确实不需要分类, 请点击继续发布, 否则点击重新修改。</div>'
                 ,yes: function(index, layero){
                     $("#content_form").submit();
                 }
             });
            
             $("#cat_id").focus();
             return false;
         }
         
        var html = ue.getContent();
        if(!html)
        {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请填写产品内容！'
            });
            
            return false;
        }
        
        //console.log(op_mod);
        
        var status = 0;
        var id = $('#content_id_field').val();
        var url = "index.php?m="+op_mod+"&c=content&a=ajax_check_title&type=2&title="+$("#title").val()+"&id="+id+"&filename="+$("#filename").val();
        $.post(url, {}, function (res) {
            if(res.status)
            {
                status = 1;
                //layer.alert(res.msg);
                if(res.status == 1)
                {
                    content = '<div style="padding-left:20px;padding-top:10px;">已经存在重复标题：'+$("#title").val()+"<br>重复内容；<a href='"+res.url+"' target='_blank'>"+$("#title").val()+"</a></div>";
                }
                else if(res.status == 2)
                {
                    content = '<div style="padding-left:20px;padding-top:10px;">已经存在重复自定义URL：'+$("#filename").val()+"<br>重复内容；<a href='"+res.url+"' target='_blank'>"+$("#filename").val()+"</a></div>";
                }
                
                layer.open({
                    type: 1
                    ,title: '提示消息'
                    ,closeBtn: false
                    ,area: '300px;'
                    ,btn: ['继续发布', '重新修改']
                    ,moveType: 1
                    ,content: content
                    ,yes: function(index, layero){
                        $("#content_form").submit();
                      }
                });
                
                return false;
            }
            else
            {
                $("#content_form").submit();
            }
            
        }, 'json');
        
        if(!status)
        {
            return false;
        }
    });
    $("#submita").on('click', function(){
        if(!$("#title").val())
         {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请填写文章标题！'
            });
             $("#title").focus();
             return false;
         }
         
         var is_sel = false;
         $('input:checkbox[id=cat_id]:checked').each(function(i){
             is_sel = true;
         });
      
         if(!is_sel)
         {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请选择文章分类'
            });
             $("#cat_id").focus();
             return false;
         }
         
        var html = ue.getContent();
        if(!html)
        {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请填写文章内容！'
            });
            
            return false;
        }
        
        var status = 0;
        var id = $('#content_id_field').val();
        var url = "index.php?m="+op_mod+"&c=content&a=ajax_check_title&type=1&title="+$("#title").val()+"&id="+id+"&filename="+$("#filename").val();
        $.post(url, {}, function (res) {
            if(res.status)
            {
                status = 1;
                if(res.status == 1)
                {
                    content = '<div style="padding-left:20px;padding-top:10px;">已经存在重复标题：'+$("#title").val()+"<br>重复内容；<a href='"+res.url+"' target='_blank'>"+$("#title").val()+"</a></div>";
                }
                else if(res.status == 2)
                {
                    content = '<div style="padding-left:20px;padding-top:10px;">已经存在重复自定义URL：'+$("#filename").val()+"<br>重复内容；<a href='"+res.url+"' target='_blank'>"+$("#filename").val()+"</a></div>";
                }
                //layer.alert(res.msg);
                layer.open({
                    type: 1
                    ,title: '提示消息'
                    ,closeBtn: false
                    ,area: '300px;'
                    ,shade: 0.8
                    ,id: 'LAY_layuipro'
                    ,btn: ['继续发布', '重新修改']
                    ,moveType: 1
                    ,content: content
                    ,yes: function(index, layero){
                        $("#content_form").submit();
                      }
                });
                return false;
            }
            else
            {
                $("#content_form").submit();
            }
            
        }, 'json');
        
        if(!status)
        {
            return false;
        }
    });
     $("#submita_a").on('click', function(){
        if(!$("#title").val())
         {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请填写文章标题！'
            });
             $("#title").focus();
             return false;
         }
         
         var is_sel = false;
         $('input:checkbox[id=cat_id]:checked').each(function(i){
             is_sel = true;
         });
      
         if(!is_sel)
         {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请选择文章分类'
            });
             $("#cat_id").focus();
             return false;
         }
         
        var html = ue.getContent();
        if(!html)
        {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请填写文章内容！'
            });
            
            return false;
        }
        
        var status = 0;
        var id = $('#content_id_field').val();
        var url = "index.php?m="+op_mod+"&c=content&a=ajax_check_title&type=1&title="+$("#title").val()+"&id="+id+"&filename="+$("#filename").val();
        $.post(url, {}, function (res) {
            if(res.status)
            {
                status = 1;
                if(res.status == 1)
                {
                    content = '<div style="padding-left:20px;padding-top:10px;">已经存在重复标题：'+$("#title").val()+"<br>重复内容；<a href='"+res.url+"' target='_blank'>"+$("#title").val()+"</a></div>";
                }
                else if(res.status == 2)
                {
                    content = '<div style="padding-left:20px;padding-top:10px;">已经存在重复自定义URL：'+$("#filename").val()+"<br>重复内容；<a href='"+res.url+"' target='_blank'>"+$("#filename").val()+"</a></div>";
                }
                //layer.alert(res.msg);
                layer.open({
                    type: 1
                    ,title: '提示消息'
                    ,closeBtn: false
                    ,area: '300px;'
                    ,shade: 0.8
                    ,id: 'LAY_layuipro'
                    ,btn: ['继续发布', '重新修改']
                    ,moveType: 1
                    ,content: content
                    ,yes: function(index, layero){
                        $("#content_form").submit();
                      }
                });
                return false;
            }
            else
            {
                $("#content_form").submit();
            }
            
        }, 'json');
        
        if(!status)
        {
            return false;
        }
    });
    $("#submitpage").on('click', function(){
        var ctype = $(this).attr('ctype');
        if(!$("#name").val())
        {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请填写页面名称！'
            });
            
             $("#name").focus();
             return false;
        }
        
        if(ctype == 'page')
        {
            var html = ue.getContent();
            if(!html)
            {
                 layer.open({
                  title: "提示",
                  skin: 'layui-layer-rim',
                  content: '请填写页面内容！'
                });
                
                return false;
            }
            
            var type=3;
        }
        else if(ctype == 'article')
        {
            var type=4;
        }
        else if(ctype == 'product')
        {
            var type=5;
        }
        
        
        var status = 0;
        var id = $('#content_id_field').val();
        var url = "index.php?m="+op_mod+"&c=content&a=ajax_check_title&type="+type+"&title="+$("#name").val()+"&id="+id+"&filename="+$("#filename").val();
        $.post(url, {}, function (res) {
            if(res.status)
            {
                status = 1;
                if(res.status == 1)
                {
                    content = '<div style="padding-left:20px;padding-top:10px;">已经存在重复标题：'+$("#title").val()+"<br>重复内容；<a href='"+res.url+"' target='_blank'>"+$("#title").val()+"</a></div>";
                }
                else if(res.status == 2)
                {
                    content = '<div style="padding-left:20px;padding-top:10px;">已经存在重复自定义URL：'+$("#filename").val()+"<br>重复内容；<a href='"+res.url+"' target='_blank'>"+$("#filename").val()+"</a></div>";
                }
                //layer.alert(res.msg);
                layer.open({
                    type: 1
                    ,title: '提示消息'
                    ,closeBtn: false
                    ,area: '300px;'
                    ,shade: 0.8
                    ,id: 'LAY_layuipro'
                    ,btn: ['继续发布', '重新修改']
                    ,moveType: 1
                    ,content: content
                    ,yes: function(index, layero){
                        $("#content_form").submit();
                      }
                });
                return false;
            }
            else
            {
                $("#content_form").submit();
            }
            
        }, 'json');
        
        if(!status)
        {
            return false;
        }
    });
    
    $("#submitinfo").on('click', function(){
        if(!$("#title").val())
        {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请填写名称！'
            });
            
             $("#title").focus();
             return false;
        }
        
        var is_sel = false;
         $('input:checkbox[id=cat_id]:checked').each(function(i){
             is_sel = true;
         });
      
         if(!is_sel)
         {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请选择产品分类！'
            });
            
             $("#cat_id").focus();
             return false;
         }
         
        var html = ue.getContent();
        if(!html)
        {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请填写产品内容！'
            });
            
            return false;
        }
        
        var status = 0;
        var id = $('#content_id_field').val();
        var url = "index.php?m=wxapp&c=content&a=ajax_check_title&type=6&title="+$("#title").val()+"&id="+id;
        $.post(url, {}, function (res) {
            if(res.status)
            {
                status = 1;
                //layer.alert(res.msg);
                if(res.status == 1)
                {
                    content = '<div style="padding-left:20px;padding-top:10px;">已经存在重复标题：'+$("#title").val()+"<br>重复内容；<a href='"+res.url+"' target='_blank'>"+$("#title").val()+"</a></div>";
                }
                else if(res.status == 2)
                {
                    content = '<div style="padding-left:20px;padding-top:10px;">已经存在重复自定义URL：'+$("#filename").val()+"<br>重复内容；<a href='"+res.url+"' target='_blank'>"+$("#filename").val()+"</a></div>";
                }
                
                layer.open({
                    type: 1
                    ,title: '提示消息'
                    ,closeBtn: false
                    ,area: '300px;'
                    ,shade: 0.8
                    ,id: 'LAY_layuipro'
                    ,btn: ['继续发布', '重新修改']
                    ,moveType: 1
                    ,content: content
                    ,yes: function(index, layero){
                        $("#content_form").submit();
                      }
                });
                
                return false;
            }
            else
            {
                $("#content_form").submit();
            }
            
        }, 'json');
        
        if(!status)
        {
            return false;
        }
    });
    $("#caogaop").on('click', function(){
        if(!$("#title").val())
         {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请填写产品名称！'
            });
            
             $("#title").focus();
             return false;
         }              
         
         var is_sel = false;
         $('input:checkbox[id=cat_id]:checked').each(function(i){
             is_sel = true;
         });
      
         if(!is_sel)
         {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请选择产品分类！'
            });
            
             $("#cat_id").focus();
             return false;
         }
         
        var html = ue.getContent();
        if(!html)
        {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请填写产品内容！'
            });
            
            return false;
        }
         
         $("#content_form").submit();
    });
    $("#caogaop_a").on('click', function(){
        if(!$("#title").val())
         {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请填写产品名称！'
            });
            
             $("#title").focus();
             return false;
         }              
         
         var is_sel = false;
         $('input:checkbox[id=cat_id]:checked').each(function(i){
             is_sel = true;
         });
      
         if(!is_sel)
         {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请选择产品分类！'
            });
            
             $("#cat_id").focus();
             return false;
         }
         
        var html = ue.getContent();
        if(!html)
        {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请填写产品内容！'
            });
            
            return false;
        }
         
         $("#content_form").submit();
    });
    $("#caogaoa").on('click', function(){
        if(!$("#title").val())
         {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请填写文章标题！'
            });
             $("#title").focus();
             return false;
         }
         
         var is_sel = false;
         $('input:checkbox[id=cat_id]:checked').each(function(i){
             is_sel = true;
         });
      
         if(!is_sel)
         {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请选择文章分类'
            });
             $("#cat_id").focus();
             return false;
         }
         
        var html = ue.getContent();
        if(!html)
        {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请填写文章内容！'
            });
            
            return false;
        }
         
         $("#content_form").submit();
    });
   
    $("#caogaoa_a").on('click', function(){
        if(!$("#title").val())
         {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请填写文章标题！'
            });
             $("#title").focus();
             return false;
         }
         
         var is_sel = false;
         $('input:checkbox[id=cat_id]:checked').each(function(i){
             is_sel = true;
         });
      
         if(!is_sel)
         {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请选择文章分类'
            });
             $("#cat_id").focus();
             return false;
         }
         
        var html = ue.getContent();
        if(!html)
        {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请填写文章内容！'
            });
            
            return false;
        }
         
         $("#content_form").submit();
    });
    $("#timesendp").on('click', function(){
        var html = "";
        if(!$("#title").val())
         {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请填写产品名称！'
            });
             $("#title").focus();
             return false;
         }              
         
         var is_sel = false;
         $('input:checkbox[id=cat_id]:checked').each(function(i){
             is_sel = true;
         });
      
         if(!is_sel)
         {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请选择产品分类！'
            });
             $("#cat_id").focus();
             return false;
         }
         
         layer.open({
              type: 1,
              title: "定时发送",
              skin: 'layui-layer-rim',
              area: ['420px', '240px'],
              content: $('#time_send'),
              btn: ['发送', '取消'],
              yes: function(index, layero){
                 $("#time-send-input").val($("#time-send-select").val());
                 $("#content_form").submit();
              }
            });
    });
    
    $("#timesenda,#timesenda_a").on('click', function(){
        var html = "";
        if(!$("#title").val())
         {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请填写文章标题！'
            });
             $("#title").focus();
             return false;
         }              
         
         var is_sel = false;
         $('input:checkbox[id=cat_id]:checked').each(function(i){
             is_sel = true;
         });
      
         if(!is_sel)
         {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请选择文章分类'
            });
             $("#cat_id").focus();
             return false;
         }
         
         layer.open({
              type: 1,
              title: "定时发送",
              skin: 'layui-layer-rim',
              area: ['420px', '240px'],
              content: $('#time_send'),
              btn: ['发送', '取消'],
              yes: function(index, layero){
                  $("#time-send-input").val($("#time-send-select").val());

                 $("#content_form").submit();
              }
            });
    });
    
    $("a[id^='picture_info']").on('click', function(){
         var filename = $(this).attr('filename');
         layer.open({
              type: 2,
              title: "图片详情",
              skin: 'layui-layer-rim',
              area: ['90%', '90%'],
              shadeClose:true,
              content: 'index.php?m=site&c=setting&a=picture_info&filename='+filename
            });
    });
    
    $("a[id^='wxapp_picture_info']").on('click', function(){
         var filename = $(this).attr('filename');
         layer.open({
              type: 2,
              title: "图片详情",
              skin: 'layui-layer-rim',
              area: ['90%', '90%'],
              shadeClose:true,
              content: 'index.php?m=wxapp&c=setting&a=picture_info&filename='+filename
            });
    });
    
    $("a[id^='cpicture_info']").on('click', function(){
         var filename = $(this).attr('filename');
         layer.open({
              type: 2,
              title: "图片详情",
              skin: 'layui-layer-rim',
              area: ['90%', '90%'],
              shadeClose:true,
              content: 'index.php?m=site&c=guanli&a=picture_info&filename='+filename
            });
    });
    
    $("#picture_add").on('click', function(){
         var status = $("#add").attr('status');
         if(status == 'off')
         {
            $("#add").show();
            $("#add").attr('status','on')
         }
         else
         {
             $("#add").hide();
             $("#add").attr('status','off')
         }
    });
    
    $("#close_iframe").on('click', function(){
         parent.layer.closeAll();
    });
    
    $("a[id^='tag_lib']").on('click', function(){
        var status = $(this).attr('status');
        if(status == 'off')
        {
            $("#tag_libs").show();
            $(this).attr('status', 'on');
        }
        else
        {
            $("#tag_libs").hide();
            $(this).attr('status', 'off');
        }
        
        form.render();
    });
    
    $("#tagsend").on('click', function(){
        var html = "";
        if(!$("#tag_str").val())
         {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请填写Tag！'
            });
             $("#tag_str").focus();
             return false;
         }              
         
        var tag_str = $("#tag_str").val();
        var tagtype = $(this).attr('tagtype');
        var index = layer.load(1);
        if(tagtype == 1)
        {
            var url = "index.php?m=mod&c=tag_article&a=ajax_form&tag_str="+tag_str;
        }
        else
        {
            var url = "index.php?m=mod&c=tag_product&a=ajax_form&tag_str="+tag_str;
        }
        
        $.post(url, {}, function (res) {
            layer.close(index);
            
            if(res.status == 0)
            {
                $("#tag_str").val("");

                var str = $('#tag_sel').html();
                var tag_content_str = Array();
                var tagvar = '';
                if(res.names.length)
                {
                    for(var i=0;i<res.names.length;i++)
                    {
                        tag_value = res.names[i];
                        tag_id = res.ids[i];
                        tag_content_str.push(tag_id);
                        str += '<span id="tag_'+tag_id+'">';
                        str += '<i class="layui-icon" style="font-size:16px;color:#009e94;">';
                        str += '<a dataid="'+tag_id+'" id="tag_del_'+tag_id+'" href="javascript://">';
                        str += '&#x1007;</a></i>'+tag_value+'</span>';
                    }
                }
                
                $('#tag_sel').html(str);
                
                var str1 = $('#tag_content').val();
                var str2 = tag_content_str.join(',');
                var str3 = str1 + ',' + str2;
                $('#tag_content').val(str3);
            }
            else
            {
                layer.msg(res.msg, {shift: 6});
            }
        }, 'json');
    });
    
    $(document).on('click', 'a[id^=tag_del]', function(){
        var dataid = $(this).attr('dataid');
        $('#tag_'+dataid).hide();
        var str = $('#tag_content').val();
        str = str.split(',');
        if(str.length)
        {
            for(var i=0;i<str.length;i++)
            {
                if(str[i] == dataid)
                {
                    str.splice(i,1);
                }
            }
        }
        
        var str1 = str.join(',');
        $('#tag_content').val(str1);
    });
    
    $("a[id^='tag_all']").on('click', function(){
        var tag_content_str = Array();
        var tag_id = $(this).attr('tagid');
        var tagname = $(this).attr('tagname');
        var str = '<span id="tag_'+tag_id+'">';
        str += '<i class="layui-icon" style="font-size:16px;color:#009e94;">';
        str += '<a dataid="'+tag_id+'" id="tag_del_'+tag_id+'" href="javascript://">';
        str += '&#x1007;</a></i>'+tagname+'</span>';
        
        tag_content_str.push(tag_id);
        
        var str1 = $('#tag_content').val();
        var str2 = tag_content_str.join(',');
        var str3 = str1 + ',' + str2;
		if(str1)
		{
			var str1_arr = str1.split(',');
			for(var i=0;i<str1_arr.length;i++)
			{
				if(str1_arr[i] == str2)
				{
					layer.alert("不能重复添加tag标签！");
					return false;
				}
			}
		}
        $('#tag_content').val(str3);
                
        var html = $('#tag_sel').html();
        html += str;
        $('#tag_sel').html(html);
    });

    //控制左边菜单的滚动
    /*if($('.left-aside .layui-nav-tree').length){
        var aside_top;
        var nav_height = $('.left-aside .layui-nav-tree').height();
        var client_height = $(window).height();
        var main_height   = $('.fly-user-main .fly-panel').height();
        if(main_height < nav_height){
            $('.fly-user-main').css('height', nav_height);
        }

        //fix .layui-this
        var active_height;
        if($('.left-aside .layui-nav-tree .layui-this').length) {
            active_height = $('.layui-nav-tree .layui-this').offset().top - client_height;
        }
        if(active_height > 0){
            $('.left-aside .layui-nav-tree').css('top', -active_height-53);
        }

        $(window).on('scroll', function(){
            aside_top = $('.fly-user-main').offset().top - $(window).scrollTop();

            if(aside_top < 53){
                $('.left-aside .layui-nav-tree').addClass('aside-nav-fixed');
            }
            if(client_height < nav_height){

                if(aside_top < (client_height - nav_height)){
                    aside_top = client_height - nav_height;
                }
                $('.left-aside .layui-nav-tree').css('top', aside_top);
            }
        });
    }*/
    $('.layui-side .layui-nav-item').click(function(e){
        $(this).siblings().removeClass('layui-nav-itemed');
    });

    //文件上传bugifx
    $('.file>input[type=file]').change(function(){
        if($(this).parent('.file').next('.add-file-name').length){
            $(this).parent('.file').next('.add-file-name').text($(this).val());
        }else{
            $(this).parent('.file').after('<span class="add-file-name">'+$(this).val()+'</span>');
        }
    });

    //a标签的title,鼠标经过马上显示
    /*$("a[title]").hover(function(){
        $(this).css('position', 'relative').append('<span class="append-title">'+$(this).attr('title')+'</span>');
    },function(){
        $(this).find('.append-title').remove();
    });*/
    
    form.on('select(year)', function(data){
            var year = data.value;
            var allmoney = money * year;
            $("#sum").html(allmoney+"元");
        });
    
    /*
    $("span[id^='xup']").on('click', function(){
        var order = $(this).attr('order');
        var action = $(this).attr('action');
        location.href = action+"&order="+order;
    });
    
    $("span[id^='xdown']").on('click', function(){
        var order = $(this).attr('order');
        var action = $(this).attr('action');
        location.href = action+"&order="+order;
    });
    */
    
  /* $("a[id^='menu']").on('click', function(){
      console.log(site_id);
      if(site_id == 309)
      {
          layer.open({
              type: 2,
              title: "图片详情",
              skin: 'layui-layer-rim',
              area: ['90%', '90%'],
              shadeClose:true,
              content: 'index.php?m=site&c=setting&a=picture_info&filename='+filename
            });

            return false;
      }
      
  }); */
  
    $("#replace_maowenben_p").on('click', function(){
        if(!$("#title").val())
        {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请填写产品名称！'
            });
            
             $("#title").focus();
             return false;
        }
        
        var is_sel = false;
         $('input:checkbox[id=cat_id]:checked').each(function(i){
             is_sel = true;
         });
      
         if(!is_sel)
         {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请选择产品分类！'
            });
            
             $("#cat_id").focus();
             return false;
         }
         
        var html = ue.getContent();
        if(!html)
        {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请填写产品内容！'
            });
            
            return false;
        }
        
        var action = $("#content_form").attr('action');
        action = action + '&op=keywords_replace_maowenben';
        $("#content_form").attr('action',action);
        $("#content_form").submit();
    });
    
    $("#replace_maowenben_a").on('click', function(){
        if(!$("#title").val())
         {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请填写文章标题！'
            });
             $("#title").focus();
             return false;
         }
         
         var is_sel = false;
         $('input:checkbox[id=cat_id]:checked').each(function(i){
             is_sel = true;
         });
      
         if(!is_sel)
         {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请选择文章分类'
            });
             $("#cat_id").focus();
             return false;
         }
         
        var html = ue.getContent();
        if(!html)
        {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请填写文章内容！'
            });
            
            return false;
        }
        
        var action = $("#content_form").attr('action');
        action = action + '&op=keywords_replace_maowenben';
        $("#content_form").attr('action',action);
        $("#content_form").submit();
    });
    
    $("#replace_maowenben_page").on('click', function(){
        if(!$("#name").val())
        {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请填写页面名称！'
            });
            
             $("#name").focus();
             return false;
        }
        
        var html = ue.getContent();
        if(!html)
        {
             layer.open({
              title: "提示",
              skin: 'layui-layer-rim',
              content: '请填写页面内容！'
            });
            
            return false;
        }
        
        var action = $("#content_form").attr('action');
        action = action + '&op=keywords_replace_maowenben';
        $("#content_form").attr('action',action);
        $("#content_form").submit();
    });

    //编辑器自动保存,每隔15秒
    var auto_save_interval, last_md5, current_content, current_md5;
    if($('#content_form').length && $('#content_id_field').length && typeof (md5) != 'undefined'){
        auto_save_interval = setInterval(function(){
            current_content = $('#content_form').serialize();
            current_md5 = md5(current_content);
            //console.log(current_md5);
            if(current_md5 != last_md5){
                last_md5 = current_md5;
                $.post($('#content_form').attr('action')+'&auto_save=true', current_content, function(result){
                    if(result.id){
                        $('#content_id_field').val(result.id);
                        $('.last-update-time').html("最近保存: " + result.updatetime_friendly);
                        $('.last-update-time').attr("title", "最后一次保存时间: " + result.updatetime);
                        $('#previce-item').removeClass('layui-hide');
                    }
                }, 'json');

                //console.log('different');
            }else{
                //console.log('same');
            }
        }, 60000);
    }

    //预览按钮
    if($('#preview-item').length){
        $("#preview-item").click(function(){
            var type = $(this).data('type');
            if($('#content_id_field').val()){
                var url = 'index.php?m=web&c=index&a='+type+'&id=' + $('#content_id_field').val() + '&preview=' + Math.random();
                $(this).attr('href', url).attr('data-url', url);
            }else{
                //没有id,不能预览
                layer.alert('您尚未填写内容或者内容尚未提交,请稍后再预览');
                return false;
            }
        });
    }
     if($('#preview-item_i').length){
        $("#preview-item_i").click(function(){
            var type = $(this).data('type');
            if($('#content_id_field').val()){
                var url = 'index.php?m=web&c=index&a='+type+'&id=' + $('#content_id_field').val() + '&preview=' + Math.random();
                $(this).attr('href', url).attr('data-url', url);
            }else{
                //没有id,不能预览
                layer.alert('您尚未填写内容或者内容尚未提交,请稍后再预览');
                return false;
            }
        });
    }
    //计算layui-form-label,根据最长的确定左边宽度
    function calcFormLabel()
    {
        var label_width = 0, last_width, input_block_width;
        $('.layui-form-label').each(function (i, item){
            $(item).append('<span class="fixed-label">'+$(item).html()+'</span>');
            last_width = $(item).find('.fixed-label').width();
            if(last_width > label_width){
                label_width = last_width;
            }
            $(item).find('.fixed-label').remove();
        });
        if(label_width < 65){
            label_width = 65;
        }
        input_block_width = label_width + 15;
        $('head').append('<style>.layui-form-label{width: '+label_width+'px;min-width: '+label_width+'px;}.layui-input-block{margin-left: '+input_block_width +'px;}</style>');
    }
  calcFormLabel();

    //取消按钮返回上一步
    $("#go-back-btn,#go-back-btn_a").on('click', function(){
        window.history.go(-1);
    });
  //加载特定模块
  if(layui.cache.page && layui.cache.page !== 'index'){
    var extend = {};
    extend[layui.cache.page] = layui.cache.page;
    layui.extend(extend);
    layui.use(layui.cache.page);
  }
  
  if(location.href.indexOf('m=site') >= 0 && location.href.indexOf('c=help') < 0 && location.href.indexOf('c=index') < 0 && location.href.indexOf('a=picture_info') < 0)
  {
    //插入右下角bar
    gather.rbar();
  }
  
  //手机设备的简单适配
  var treeMobile = $('.site-tree-mobile')
  ,shadeMobile = $('.site-mobile-shade')

  treeMobile.on('click', function(){
    $('body').addClass('site-mobile');
  });

  shadeMobile.on('click', function(){
    $('body').removeClass('site-mobile');
  });

  //图片懒加载
  /*
  layui.use('flow', function(){
      var flow = layui.flow;
      //当你执行这样一个方法时，即对页面中的全部带有lay-src的img元素开启了懒加载（当然你也可以指定相关img）
      flow.lazyimg(); 
    });
  */

  //列表快速编辑
  $('.quick-edit').click(function(){
    //$(this).parents('tr').siblings("[id^='edit']").remove().end().after($('#quick-edit-form').html());
    //$(this).parents('tr').after($('#quick-edit-form').html());
    var item = $(this).parents('tr');
    var datas = [];
    item.find('[data-column]').each(function(i, val){
      datas[$(val).attr('data-column')] = $(val).text();
    });
    laytpl($('#quick-edit-form').html()).render(
      datas,
      function(string){
        var tplString = $(string);
        item.hide().after(tplString);
        //调整页面元素
        calcFormLabel();
        tplString.find('.cancel').click(function(){
          item.show().next().remove();
        });
      });
  });
  //列表快速编辑提交
  form.on('submit(quick-save)', function(data){
    var item = $(this).parents('tr');
    $.post($(data.form).attr('action')+'&quick_save=true', data.field, function(result){
      //对表格数据进行替换
      if(result.status == 0){
        $.each(result.data, function(key, val){
          item.prev().find('[data-column='+key+']').text(val);
        });
        item.prev().show();
        item.remove();
      }else{
        layer.alert(result.msg);
      }
    }, 'json');
    return false;
  });
    //输入数字翻页
    if($('#goto-page').length){
        $('#goto-page').click(function(){
            var jump_page = parseInt($('#page-number').val());
            var max_page  = parseInt($('#page-number').attr('max'));
            var jump_url  = $(this).data('prefix');
            if(jump_page < 1 || jump_page > max_page){
                layer.alert('输入的页数无效');
                return false;
            }
            window.location.href = jump_url + jump_page;
        });
    }
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
  
  exports('fly', gather);

});