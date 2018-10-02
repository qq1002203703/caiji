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
	///menu-dropdown
	//$(".menu-dropdown-click").click(function(){
		//$(this).siblings(".menu-dropdown-content").slideToggle();
	//});
	$(".dh-dropdown >.dh-dropdown-click").click(function(){
		$(this).siblings(".dh-dropdown-content").slideToggle();
	});
	//侧栏菜单
	asideMenu();

});

//侧栏菜单函数
var asideMenu=function(){
	var _this=$("#aside .dh-menu");
	var select=_this.find("li[router='"+router.module+'/'+router.ctrl+'/'+router.action+"']");
	if(select.length <1 ){
		select=_this.find("li[router='"+router.module+'/'+router.ctrl+"']");
		select.addClass("dh-menu-select").children(".dh-menu-children").show();
	}else{
		select.addClass("dh-menu-select").parents(".dh-menu-children").show();
	}
	_this.find(".dh-menu-has-children > a").click(function(){
		$(this).siblings(".dh-menu-children").slideToggle();
	});
}

//tab函数
var Jtab=function (tab){
	var tab=$(tab);
	tab.find('#' + tab.find('.tab-hander .tab-select').attr('data')).show();
	tab.find('.tab-hander-item').click(function(){
		tab.find('#'+tab.find('.tab-hander .tab-select').removeClass('tab-select').attr('data')).hide();
		tab.find('#'+$(this).addClass('tab-select').attr('data')).show();
	});
}

function showMsg(element){
	var $msg=$(element).html();
	if($msg != '')
		alert($msg);
}
/**
*点击一个连接，用ajax请求这个连接地址
*/
function ajaxLink(elme,msg){
	var msg = msg || '你真的要删除？';//确认提示信息
	$(elme).click(function(){
		var _this=$(this);
		if( confirm(msg)){
			var ajax=new Jajax();
			ajax.get(_this[0].href,'', function(data){
				_this.parents('tr').remove();
				return false;
			}, false, true,function(data){
				alert(data.msg);
				return false;
			});
		}
		return false;
	});
	return false;
}
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
							if(ret.code >= 1){//服务器处理失败
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
			'error': function(ret, textStatus, errorThrown){
				/*ret.status;//错误状态吗*/
				//layer.closeAll('loading');
				setTimeout(function () {
					if(ret.status == 404){
						ret.msg='请求失败，请求页面未找到,status:404';
					}else if(ret.status == 503){
						ret.msg='请求失败，服务器内部错误,status:503';
					}else {
						ret.msg='请求失败,错误信息：'+textStatus;
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
		if(cache==undefined) cache=true;
		form = $(form);
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
};


/**
 * 常用点击类
 */
var clickClass=function () {
	/**
	 * 点击一个a元素，用ajax访问这个元素的链接
	 * @param el string： a元素的选择器字符串
	 * @param success function:ajax访问成功后的回调函数，如果不提供，默认是弹出返回消息，并刷新当前页。这个函数接收两个参数：一个是ajax返回的数据，另一个是点击的a元素的jquery对象
	 * @param error function:ajax访问失败后的回调函数，如果不提供，默认是弹出返回的消息。同样，这个函数也接收两个参数：一个是ajax返回的数据，另一个是点击的a元素的jquery对象
     */
	this.ajaxLink=function (el, success, error) {
		if(success === undefined){success=function (data) {alert(data.msg);location.reload();}}
		if(error=== undefined){error=function (data) {alert(data.msg);}}
		$(el).click(function () {
			var _this=$(this);
			var url=_this[0].href;
			_this[0].href='#';
			var ajax=new Jajax();
			ajax.get(url,'', function(data){
				success(data,_this);
			}, false, true,function(data){
				_this[0].href=url;
				error(data,_this);
			});
			return false;
		});
	};
	/**
	 * 点击一个a按钮，触发ajax方式提交所有选中的checkbox中的值到按钮指向的url
	 * @param el string:a按钮的选择器字符串,如 '.delete-all'
	 * @param checkboxs string:checkbox的选择器字符串,例如 '#myform td >input[type="checkbox"]:checked'
	 * @param success function:ajax访问成功后的回调函数，如果不提供，默认是弹出返回消息，并刷新当前页。这个函数接收两个参数：一个是ajax返回的数据，另一个是点击的a元素的jquery对象
	 * @param error function:ajax访问失败后的回调函数，如果不提供，默认是弹出返回的消息。同样，这个函数也接收两个参数：一个是ajax返回的数据，另一个是点击的a元素的jquery对象
     */
	this.ajaxCheckbox=function (el,checkboxs,success,error) {
		if(success === undefined){success=function (data) {alert(data.msg);location.reload();}}
		if(error=== undefined){error=function (data) {alert(data.msg);}}
		var _this=$(el);
		_this.click(function(){
			var ids='';
			var checkbox=$(checkboxs);
			if(checkbox.length>0){
				if(confirm('你真的要这样做吗？')){
					checkbox.each(function(k){
						if(k==0)
							ids=$(this).val();
						else
							ids += ','+$(this).val();
					});
					var ajax=new Jajax();
					ajax.get($(this).children('a').attr('href'),{id:ids}, function(data){
						success(data,_this);
					}, false, true,function(data){
						error(data,_this);
					});
				}
			}else{
				alert('请先选中最少一项');
			}
			return false;
		});
	};
	/**
	 * 全选/不全选：点击el这个元素，对checkbox进行全选或不全选
	 * @param el：被点击的元素的选择器，如 '.js-check-all'
	 * @param checkboxs: 要选中的所有checkbox的选择器 如 '#myform td >input[type="checkbox"]'
	 */
	this.checkedAll=function(el,checkboxs) {
		/*绑定点击事件到 .js-check-all*/
		$(el).click(function(){
			if($(this).is(':checked')){
				$(checkboxs).prop('checked', true);
			}else{
				$(checkboxs).prop('checked', false);
			}
		});
	}

}
