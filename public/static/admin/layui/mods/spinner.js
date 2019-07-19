/*url:https://github.com/vsn4ik/jquery.spinner*/
layui.define(['jquery'], function(exports){
	jQuery=layui.jquery; 
	(function($) {
		$.fn.spinner = function(opts) {
			return this.each(function() {
				var defaults = {
					value: 0,
					min: 0,
					step:1
				}
				var options = $.extend(defaults, opts)
				var keyCodes = {
					up: 38,
					down: 40
				}
				var container = $('<div></div>');
				container.addClass('spinner');
				var textField=$(this);
				var textValue=parseFloat(textField.val());
				if(isNaN(textValue)){
					textField.val(options.value);
				}
				textField.addClass('value').attr('maxlength', '2').bind('keyup paste change', function(e) {
					var field = $(this)
					if (e.keyCode == keyCodes.up) changeValue(options.step)
					else if (e.keyCode == keyCodes.down) changeValue(-options.step)
					else if (getValue(field) != container.data('lastValidValue')) validateAndTrigger(field)
				});
				textField.wrap(container);
				var increaseButton = $('<button class="spinner-increase">+</button>').click(function() {
					changeValue(options.step);
					return false;
				});
				var decreaseButton = $('<button class="spinner-decrease">-</button>').click(function() {
					changeValue(-options.step);
					return false;
				});
				validate(textField);
				container.data('lastValidValue', options.value);
				textField.before(decreaseButton);
				textField.after(increaseButton);
				function changeValue(delta) {
					textField.val(getValue() + delta);
					validateAndTrigger(textField);
				}
				function validateAndTrigger(field) {
					clearTimeout(container.data('timeout'));
					var value = validate(field);
					if (!isInvalid(value)) {
						textField.trigger('update', [field, value]);
					}
				}
				function validate(field) {
					var value = getValue();
					if (value <= options.min) 
						decreaseButton.attr('disabled', 'disabled');
					else 
						decreaseButton.removeAttr('disabled');
					field.toggleClass('invalid', isInvalid(value)).toggleClass('passive', value === 0);
					if (isInvalid(value)) {
						var timeout = setTimeout(function() {
							textField.val(container.data('lastValidValue'))
							validate(field)
						}, 500);
						container.data('timeout', timeout);
					} else {
						container.data('lastValidValue', value);
					}
					return value;
				}

				function isInvalid(value) {
					return isNaN(+value) || value < options.min;
				}

				function getValue(field) {
					field = field || textField;
					return parseInt(field.val() || 0, 10);
				}
			});
		}
	})(jQuery);
	exports('spinner', null);
});