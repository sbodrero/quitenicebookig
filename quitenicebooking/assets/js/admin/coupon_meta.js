/**
 * Detect changes and prompt user before navigating away
 */
(function($) {
$(window).load(function(){
	var changed = false;
	$('#poststuff input, #poststuff select, #poststuff textarea').on('change', function() {
		changed = true;
	});
	$('#save-post, #publish').on('click', function() {
		changed = false;
	});
	window.onbeforeunload = function() {
		if (changed == true) {
			return 'The changes you made will be lost if you navigate away from this page.';
		}
	};
});
})(jQuery);


(function($) {
$(document).ready(function() {
	/**
	 * Add datepicker to date fields
	 */
	$('.datepicker').datepicker({
		minDate: 0,
		firstDay: 1,
		dateFormat: quitenicebooking_premium.js_date_format
	});

	/**
	 * Show/hide discount_type boxes
	 * @param string type
	 */
	function show_discount_type_box(type) {
		$('#discount_flat_div, #discount_percentage_div, #discount_duration_div').css('display', 'none');
		if (type != null) {
			$('#discount_details_separator').css('display', 'block');
		}
		$('#discount_'+type+'_div').css('display', 'block');
	}

	/**
	 * Open up discount_type box on page load
	 */
	show_discount_type_box($('input[name="coupon_discount_type"]:checked').val());

	/**
	 * Open up the discount_type box when selected
	 */
	$('input[name="coupon_discount_type"]').on('click', function() {
		show_discount_type_box($(this).val());
	});

	/**
	 * Form submission and validation
	 */
	$('form').on('submit', function(e) {
		$('#validation_errors').addClass('hidden');
		$('#validation_errors').html('');

		var errors = new Array();

		var unicode_decimal_sep = (quitenicebooking_premium.currency_decimal_separator.charCodeAt(0)).toString(16);
		unicode_decimal_sep = ('000'+unicode_decimal_sep).slice(-4);

		// check if code is empty
		if ($('#coupon_code').val().trim().length == 0) {
			errors.push('Please enter a coupon code');
		}

		// check if discount type is not selected
		var discount_type_selected = false;
		$('input[name="coupon_discount_type"]').each(function() {
			if ($(this).is(':checked')) {
				discount_type_selected = true;
			}
		});
		if (!discount_type_selected) {
			errors.push('Please select a discount type');
		}

		// check if the selected discount type parameters are valid
		if ($('#coupon_discount_type_flat').is(':checked')) {
			if ($('#coupon_discount_flat').val().match('^\\d+\\u'+unicode_decimal_sep+'?\\d*$') == null
				|| $('#coupon_discount_flat').val() <= 0) {
				errors.push('Please enter a valid discount amount (omit thousands separators '+quitenicebooking_premium.currency_thousands_separator+' and use '+quitenicebooking_premium.currency_decimal_separator+' as decimal point)');
			}
		} else if ($('#coupon_discount_type_percentage').is(':checked')) {
			if ($('#coupon_discount_percentage').val().match(/^\d+\.?\d*$/) == null
				|| $('#coupon_discount_percentage').val() == 0
				|| !($('#coupon_discount_percentage').val() < 100)) {
				errors.push('Please enter a valid discount percentage (use . as decimal point)');
			}
		} else if ($('#coupon_discount_type_duration').is(':checked')) {
			if ($('#coupon_discount_duration_requirement').val().match(/^\d+$/) == null) {
				var error_duration_requirement = true;
			}
			if ($('#coupon_discount_duration_units').val().match(/^\d+$/) == null
				|| $('#coupon_discount_duration_units').val() < 1) {
				var error_duration_requirement = true;
			}
			if (error_duration_requirement != null) {
				errors.push('Please enter a valid discount amount (number of nights)');
			}
			if (($('#coupon_discount_duration_requirement').val().length > 0 && $('#coupon_discount_duration_units').val().length > 0) && $('#coupon_discount_duration_requirement').val() <= $('#coupon_discount_duration_units').val()) {
				errors.push('Number of free nights must be less than number of booked nights');
			}
			var duration_mode_selected = false;
			$('input[name="coupon_discount_duration_mode"]').each(function(){
				if ($(this).is(':checked')) {
					duration_mode_selected = true;
				}
			});
			if (!duration_mode_selected) {
				errors.push('Please select a discount amount option (least/most expensive nights)');
			}
		}

		// check usage limit
		if ($('#coupon_usage_limit').val().trim().length > 0) {
			if ($('#coupon_usage_limit').val().match(/^\d+$/) == null) {
				errors.push('Please enter a valid usage limit (number)');
			}
		}

		// check expiration date
		if ($('#coupon_expiry_date').val().trim().length > 0) {
			if (isNaN(Date.parse($.datepicker.parseDate(quitenicebooking_premium.js_date_format, $('#coupon_expiry_date').val())))) {
				errors.push('Please enter a valid expiry date');
			}
		}

		// check minimum total
		if ($('#coupon_minimum_total').val().trim().length > 0) {
			if ($('#coupon_minimum_total').val().match('^\\d+\\u'+unicode_decimal_sep+'?\\d*$') == null) {
				errors.push('Please enter a valid minimum total payment (omit thousands separators '+quitenicebooking_premium.currency_thousands_separator+' and use '+quitenicebooking_premium.currency_decimal_separator+' as decimal point)');
			}
		}

		if (errors.length > 0) {
			$('#save-post').removeClass('button-disabled');
			$('#publish').removeClass('button-primary-disabled');
			$('.spinner').css('display', 'none');
			$('#validation_errors').removeClass('hidden');
			for (var i = 0; i < errors.length; i ++) {
				$('#validation_errors').append('<p><strong>'+errors[i]+'</strong></p>');
			}
			$('html, body').animate( { scrollTop: $('div#validation_errors').offset().top-50 }, 1000);
			return false;
		}
	});

});
})(jQuery);
