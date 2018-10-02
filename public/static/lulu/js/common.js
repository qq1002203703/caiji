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
	$("#sidebar .pure-menu .pure-menu-select").parents(".pure-menu-children").show();
	$("#sidebar .pure-menu .pure-menu-has-children > a").click(function(){
		var aa=$(this).siblings(".pure-menu-children").slideToggle();
		aa.is(':hidden') && aa.find(".pure-menu-children").hide();
	});
});