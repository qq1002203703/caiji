$(document).ready(function() {
	//移动菜单按钮
	$("#menu-button-click").click(function(){
		var mobile=$("#mobile-menu");
		if(mobile.length ==0){
			$("#header .pure-menu").removeClass("pure-menu-horizontal").clone().attr('id','mobile-menu').appendTo('#header').find(".pure-menu-list > li > .pure-menu-children").remove();
			$("#mobile-menu .pure-menu-allow-hover").removeClass("pure-menu-allow-hover pure-menu-has-children");
			mobile=$("#mobile-menu");
		}
		if(mobile.is(":hidden"))
			mobile.show();
		else
			mobile.hide();		
	});

	/**$(window).resize(function (){
		if($(window).width() >= 768 && $("#mobile-menu").length > 0){
			$("#mobile-menu").remove();
			$("#mobile-menu").remove();
			location.reload();
		}
	});**/
	///menu-dropdown
	$(".menu-dropdown-click").click(function(){
		$(this).siblings(".menu-dropdown-content").slideToggle();
	});
	//导航菜单
	$(".pure-menu-horizontal .pure-menu-select").parents("li.pure-menu-has-children").last().addClass("pure-menu-select");
	//侧栏菜单
	$("#aside .pure-menu .pure-menu-select").parents(".pure-menu-children").show();
	$("#aside .pure-menu .pure-menu-has-children > a").click(function(){
		var aa=$(this).siblings(".pure-menu-children").slideToggle();
		aa.is(':hidden') && aa.find(".pure-menu-children").hide();
	});
});

/**
*Jquery通过ajax方式提交整个表单数据到目标网址
* formJquery：表单的Jquery对象
* url:表单提交到的目标网址
* fn_sussce：接收到正确的json数据后，进行的最后处理函数
*/
function sendJSON(formJquery, url,fn_sussce) {
    //获取表单数据，并序列化
    var formData = formJquery.serializeArray();
    //将序列化数据转为对象
    var formObject = {};
    for (var item in formData) {
        formObject[formData[item].name] = formData[item].value;
    }
    var formJSON = JSON.stringify(formObject);
    //发送JSON到服务器
    $.ajax({
        type: 'POST',
        url: url,
        contentType: "application/json",  //一定要设置这一行，很关键
        data: formJSON,
        datatype: "json",
		success: function(ret){
			if(ret!== undefined && ret.code !== undefined){
				if(ret.code==1){
					alert(ret.msg);
					return false;
				}else
					fn_sussce();
			}else{
				alert("无法获取ajax结果");
				return false;
			}								
		},
    });
}

/**二次封装的Jquery ajax对象**/
var Jajax=function(status){
	//设置ajax当前状态(是否可以发送);
	this.status=(status===undefined) ? true: status;
	//是否成功提交
	this.isSucces=false;
	//储存信息
	this.msg='';
	this.isloading=false;
	//ajax封装
	this.ajax=function(url, data, success, cache, alone, type, error,dataType,async) {
		var type = type || 'post';//请求类型
		var dataType = dataType || 'json';//接收数据类型
		var async = async || true;//异步请求
		var alone = alone || false;//独立提交（一次有效的提交）
		var cache = cache || false;//浏览器历史缓存
		var _this=this;
		var success = success || function () {return true;};
		var error = error || function () {return false;};
		/*判断是否可以发送请求*/
		if(!this.status){
			return false;
		}
		_this.status = false;//禁用ajax请求
		/*正常情况下1秒后可以再次多个异步请求，为true时只可以有一次有效请求（例如添加数据）*/
		if(!alone){
			setTimeout(function () {
				_this.status = true;
			},1000);
		}
		$.ajax({
			'url': url,
			'data': data,
			'type': type,
			'dataType': dataType,
			'async': async,
			'success': function(ret){
					//setTimeout(function () {
						//layer.msg(ret.msg);//通过layer插件来进行提示信息
					//},500);
					setTimeout(function () {
						if(typeof(ret)=== undefined || typeof(ret.code)=== undefined){	
							_this.isSuccuse=false;
							_this.msg='成功提交，但服务器返回数据格式不对';
						}else{
							if(ret.code==1){//服务器处理失败
								_this.isSuccuse=false;
								_this.msg=ret.msg;
								error(ret);
							}else{//服务器处理成功
								_this.isSuccuse=true;
								_this.msg=ret.msg;
								success(ret);
							}
						}
						_this.isloading=false;
					},500);			
			},
			'error': function(ret){
				/*ret.status;//错误状态吗*/
				//layer.closeAll('loading');
				setTimeout(function () {
					if(ret.status == 404){
						ret.msg='请求失败，请求页面未找到,status:404';
					}else if(ret.status == 503){
						ret.msg='请求失败，服务器内部错误,status:503';
					}else {
						ret.msg='请求失败';
					}
					error(ret);
					_this.status = true;
					_this.isloading=false;
				},500);
			},
			'jsonpCallback': 'jsonp' + (new Date()).valueOf().toString().substr(-4),
			'beforeSend': function () {
				_this.isSuccess=false;
				_this.msg='';
				_this.isloading=true;
			},
		});
	};
	//submitAjax(post方式提交)
	this.submitAjax=function (form, success, cache, alone,error) {
		cache = cache || true;
		var form = $(form);
		var url = form.attr('action');
		var data = form.serialize();
		this.ajax(url, data, success, cache, alone,'post',error,'json',false);
	};
	// ajax提交(post方式提交)
	this.post=function(url, data, success, cache, alone,error) {
		this.ajax(url, data, success, cache, alone, 'post',error,'json',false);
	};
	//ajax提交(get方式提交)
	this.get=function(url,data, success, cache, alone,error) {
		this.ajax(url, data, success, cache,alone, 'get',error,'json',true);
	};
	// jsonp跨域请求(get方式提交)
	this.jsonp=function(url, success, cache, alone,error) {
		this.ajax(url, {}, success, cache, alone, 'get',error,'jsonp',true);
	}
}