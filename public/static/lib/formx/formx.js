/**
 * @name form validate widget
 * @auth sole
 * @time 2015年8月3日
 * @verson 1.0
 * @use $('#form').formx();
*/
;(function($){
	$.fn.getForm = function(type){
		var _this = $(this),
			aTxt = ['input', 'select', 'textarea'],
			_children = _this.find(aTxt.join(',')),
			_res = {},
			str = '';
		function json2str(json){
			var res = ''
			for( str in json ){
				res += '&'+str+'='+json[str];
			}
			return res.substring(1);
		};
		
		_children.each(function(i, ele){
			if( $(ele).attr('name') ){
				_res[ $(ele).attr('name') ] = $(ele).val();
			}
		});
		if( type == 'str' ){
			_res = json2str(_res);
		}
		return _res;
	};
})(jQuery);

;(function($){

	var noop = function(res){ return true; };

	//default options
	var def = {
		errClass : 'err',
		okClass : 'success',
		errElem : '',
		blured : noop, 
		eleClick : noop,
		docClick : noop,
		regexped : noop, 
		move : noop, 
		keyup : noop,
		urled : noop,
		saved : noop,
		debug : false,
		saveBefore : noop
	};
	
	$.fn.formx = function(o){
		var opt = $.fn.extend({}, def, o);

		return this.each(function(){

			var oForm = $(this),
				oRequired = oForm.find('input[required], textarea[required]'),
				oTxt = oForm.find('input, textarea'),
				oBtnSave = oForm.find('input[type="submit"],button[type="submit"]'),
				reTxt = {},
				thisForm=this.id ? ("#"+this.id):("."+(this.getAttribute('class').replace(/ /g,'.')));

			//init and get regexps, 防止用户恶意篡改正则
			oTxt.each(function(i, ele){
				var re = $.trim( $(ele).attr('pattern') );
				if ( re ) {
					reTxt[ $(ele).attr('name') ] = re;
					$(ele).removeAttr('pattern');
				}
			});

			function setSaveBtn(){
				var arr = [];
				var iErr = oForm.find('.'+opt.errClass).length;
				oRequired = oForm.find('input[required], textarea[required]');
				data = oForm.getForm();
				oRequired.each(function(i, ele){
					if( $.trim(data[ $(ele).attr('name') ]) ){
						arr.push( $(ele).attr('name') );
					}
				});

				(oRequired.length==arr.length && !iErr) ? oBtnSave.removeAttr('disabled') : oBtnSave.attr('disabled', 'disabled');
			}
			function v_empty(_this){
				var _val = $.trim( _this.val() ),
					_parent = _this.parents(opt.errElem);
				_parent[ (!_val ? 'add' : 'remove') + 'Class' ](opt.errClass);
				_parent[ (!_val ? 'remove' : 'add') + 'Class' ](opt.okClass);
				return !_val ? false : true;
			}

			function v_pattern(_this){
				var _val = $.trim( _this.val() ),
					_parent = _this.parents(opt.errElem),
					_name = _this.attr('name'),
					_re = reTxt[ _name ];
				var reg = new RegExp(_re);
				_parent[ (!reg.test(_val) ? 'add' : 'remove') + 'Class' ](opt.errClass);
				_parent[ (!reg.test(_val) ? 'remove' : 'add') + 'Class' ](opt.okClass);
				if( reg.test(_val) ){
					opt.regexped( _this );
				}
			}

			function v_url(_this){
				var _val = $.trim( _this.val() ),
					_parent = _this.parents(opt.errElem),
					_url = _this.data('url'),
					_name = _this.attr('name');
				var data = {};
				data[_name] = _val;
				$.ajax({
					async: false,
					type: 'post',
					dataType: 'json',
					data: data,
					url:_url,
					success:function(res){
						_parent[ (!res.code ? 'add' : 'remove') + 'Class' ](opt.errClass);
						_parent[ (!reg.code ? 'remove' : 'add') + 'Class' ](opt.okClass);
						if( res.code == 1 ){
							_parent.removeClass(opt.errClass).addClass(opt.okClass);
						}else{
							_parent.addClass(opt.errClass).removeClass(opt.okClass);
						}
						opt.urled(_this, res);
					}
				});
			}

			function v_group(_this){
				var _group = _this.data('group');
				//console.log(_group);
				var oo=$(thisForm+ ' input[name="'+_group+'"]');
				//console.log(oo.val());
				if(oo.val()===_this.val())
					_this.parents(opt.errElem).removeClass(opt.errClass).addClass(opt.okClass);
				else
					_this.parents(opt.errElem).addClass(opt.errClass).removeClass(opt.okClass);
			}
			function v_cgroup(_this){
				var _cgroup = _this.data('cgroup');
				var oo=$(thisForm+ ' input[name="'+_cgroup+'"]');
				if(oo.val()===_this.val())
					oo.parents(opt.errElem).removeClass(opt.errClass).addClass(opt.okClass);
				else
					oo.parents(opt.errElem).addClass(opt.errClass).removeClass(opt.okClass);
			}

			function v_event(_this, eventName){
					_parent = _this.parents(opt.errElem),
					_url = _this.data('url'),
					_group = _this.data('group'),
					_cgroup=_this.data('cgroup'),
					_name = _this.attr('name'),
					_re = reTxt[ _name ];

				//errElem
				_parent = opt.errElem ? _this.parents(opt.errElem) : _parent;

				if ( v_empty(_this) ){
					_re && v_pattern(_this);
					_group && v_group(_this);
					_cgroup && v_cgroup(_this);
					if ( eventName == 'blur' ) {
						_url && v_url(_this);
					}
				}
			}
			$(document).on({
				blur:function(){
					v_event($(this), 'blur');
					setSaveBtn();
				},
				keyup:function(){
					v_event($(this), 'keyup');
					setSaveBtn();
				}
			},thisForm + ' input[required],'+ thisForm + ' textarea[required]');

			$(document).on({
				click:function(e){opt.eleClick($(this));e.stopPropagation();},
				keyup:function(){opt.keyup( $(this));},
				blur:function(){opt.blured($(this));}
			}, thisForm +' input,'+ thisForm + ' textarea');

			$(document).on('click', function(e){
				opt.docClick( $(e.target) );
			});

			oForm.on('mousemove', function(){
				setSaveBtn();

				opt.move( data );
			});/*.on('submit', function(){
				var url = oForm.attr('action'),
					method = oForm.attr('method'),
					data = oForm.getForm();

				opt.saveBefore && opt.saveBefore(data);

				if (opt.debug) { window.console && console.log(url, method, data) };

				if( !oBtnSave.attr('disabled') ){
					$.ajax({
						async: false,
						type:method,
						dataType: 'json',
						data: data,
						url:url,
						success:function(res){
							opt.saved(res);
						}
					});
				}
				return false;
			});*/
			$(document).on('submit',thisForm,function(){
				var url = oForm.attr('action'),
					method = oForm.attr('method'),
					data = oForm.getForm();

				opt.saveBefore && opt.saveBefore(data);

				if (opt.debug) { window.console && console.log(url, method, data) };

				if( !oBtnSave.attr('disabled') ){
					$.ajax({
						async: false,
						type:method,
						dataType: 'json',
						data: data,
						url:url,
						success:function(res){
							opt.saved(res);
						}
					});
				}
				return false;
			});

			
		});

	}
	
})(jQuery);
