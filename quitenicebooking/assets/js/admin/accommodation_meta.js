// get the existing number of price rules
var price_rule_num = parseInt(quitenicebooking.num_price_rules) + 1;

(function($) {

$(window).load(function(){
	// detect changes
	var changed = false;
	$('#poststuff input, #poststuff select, #poststuff textarea').on('change', function() {
		changed = true;
	});
	$('#save-post, #publish').on('click', function() {
		changed = false;
	});
	window.onbeforeunload = function() {
		if (changed == true) {
			return $('#translations .t_post_changed').html();
		}
	};
});

$(document).ready(function() {
	// form validation
	$('form').on('submit', function(e) {
		$('#message').addClass('hidden');
		$('#validation_errors').addClass('hidden');
		$('#validation_errors').html('');
		var validationErrors = new Array();
		if ($('#title').val().length == 0) {
			validationErrors.push($('#translations .t_validation_title').html());
		}
		var unicode_decimal_sep = (quitenicebooking.currency_decimal_separator.charCodeAt(0)).toString(16);
		unicode_decimal_sep = ('000'+unicode_decimal_sep).slice(-4);

		var invalid_price = false;
		// validate prices
		$('.validate_price').not('.hidden .validate_price').each(function() {
			if ($(this).val().trim().match('^\\d+\\u'+unicode_decimal_sep+'?\\d*$') == null) {
				invalid_price = true;
			}
		});
		// validate optional prices
		$('.validate_optional_price').each(function() {
			if ($(this).val().trim().length > 0) {
				if ($(this).val().trim().match('^\\d+\\u'+unicode_decimal_sep+'?\\d*$') == null) {
					invalid_price = true;
				}
			}
		});

		if (invalid_price) {
			validationErrors.push($('#translations .t_validation_room_price').html().replace(/%s/, quitenicebooking.currency_decimal_separator));
		}

		if (validationErrors.length > 0) {
			$('#validation_errors').removeClass('hidden');
			for (var i = 0; i < validationErrors.length; i ++) {
				$('#validation_errors').append('<p><strong>'+validationErrors[i]+'</strong></p>');
			}
			$('html, body').animate( { scrollTop: $('div#validation_errors').offset().top-50 }, 1000);

			$('#save-post').removeClass('button-disabled');
			$('#publish').removeClass('button-primary-disabled');
			$('.spinner').css('display', 'none');
			return false;
		}
	});

	/**
	 * Add jquery-ui-tabs to tabs
	 */
	$('#quitenicebooking_tabs').tabs().addClass('ui-tabs-vertical ui-helper-clearfix');
	$('#quitenicebooking_tabs').tabs().removeClass('ui-corner-top').addClass('ui-corner-left');

	/**
	 * Add price rule button
	 */
	$('button.add_price_rule').on('click', function(e) {
		e.preventDefault();

		var html = $('#price_rule_template').html().replace(/%price_rule_id%/g, price_rule_num);
		$('#dynamic_price_rules').append('<div class="price_rule" data-id="'+price_rule_num+'">'+html+'</div>');

		price_rule_num ++;
		rename_labels();
	});

	/**
	 * Remove price rule button
	 */
	$(document).on('click', 'button.remove_price_rule', function(e) {
		e.preventDefault();

		$(this).parent().parent().remove();
		price_rule_num --;

		// reorder existing rules
		var count = 1;
		for (var i = 0; i < price_rule_num - 1; i ++) {
			$('.price_rule').eq(i).attr('data-id', count);
			$('.price_rule').eq(i).find('h3').html($('#translations .t_price_rule').html() + ' ' + count);
			$('.price_rule').eq(i).find('input').each(function() {
				$(this).attr('name', function(index, value) {
					return value.replace(/price_rule_\d+/, 'price_rule_'+count);
				});
			});
			count ++;
		}
		rename_labels();
	});

	 /**
	  * Rename price rule labels
	  */
	 function rename_labels() {
		 $('.price_rule').each(function() {
			var rule_id = $(this).attr('data-id');
			if (rule_id < (price_rule_num - 1)) {
				$(this).find('.additional_adult').html($('#translations .t_for_adult_n').html().replace(/%d/, parseInt(rule_id)+1) + ' ('+quitenicebooking.currency_unit+')');
				$(this).find('.additional_child').html($('#translations .t_for_child_n').html().replace(/%d/, parseInt(rule_id)+1) + ' ('+quitenicebooking.currency_unit+')');
			} else {
				$(this).find('.additional_adult').html($('#translations .t_additional_adult').html() + ' ('+quitenicebooking.currency_unit+')');
				$(this).find('.additional_child').html($('#translations .t_additional_child').html() + ' ('+quitenicebooking.currency_unit+')');
			}
		 });
	 }

});

})(jQuery);