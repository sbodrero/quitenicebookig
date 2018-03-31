(function($) {
$(document).ready(function() {
	'use strict';

	/**
	 * Step 3 - Form validation ===============================================
	 */
	$('.booking-fields-form').on('submit', function(e) {
		var validationErrors = new Array();

		var requiredfield_error = false;

		// check for missing fields
		if ($('#guest_first_name').val().length == 0
			|| $('#guest_last_name').val().length == 0
			|| $('#guest_email').val().length == 0 ) {
			requiredfield_error = true;
		}

		$('input[type=text][data-required], textarea[data-required]').each(function(){
			if ($(this).val().length == 0) {
				requiredfield_error = true;
			}
		});

		// check for required checkboxes/radios/selects
		var checkbox_arr = new Array;
		// get the names of all checkbox groups
		$('input[type=checkbox][data-required], input[type=radio][data-required]').each(function(){
			if ($.inArray($(this).attr('name'), checkbox_arr) == -1) {
				checkbox_arr[$(this).attr('name')] = false;
			}
		});
		// determine whether at least one checkbox is checked within the group
		for (var i in checkbox_arr) {
			$('input[name="'+i+'"]').each(function() {
				if ($(this).is(':checked')) {
					checkbox_arr[i] = true;
				}
			});
		}
		// get the names of all select groups
		var select_arr = new Array;
		$('select[data-required]').each(function(){
			if ($.inArray($(this).attr('name'), checkbox_arr) == -1) {
				select_arr[$(this).attr('name')] = false;
			}
		});
		// determine whether at least one selection is made within the group
		for (var i in select_arr) {
			$('select[name="'+i+'"] option').each(function() {
				if ($(this).is(':selected')) {
					select_arr[i] = true;
				}
			});
		}
		// determine if any of checkbox_arr or select_arr are false
		for (var i in checkbox_arr) {
			if (checkbox_arr[i] == false) {
				requiredfield_error = true;
			}
		}
		for (var i in select_arr) {
			if (select_arr[i] == false) {
				requiredfield_error = true;
			}
		}

		if (requiredfield_error == true) {
			validationErrors.push(quitenicebooking.validationerror_requiredfield);
		}

		// check specific fields
		if ($('#guest_email').val().match(/.+@.+\..+/) == null) {
			validationErrors.push(quitenicebooking.validationerror_email);
		}
		if ($('input[name=payment_method]').length > 0 && $('input[name="payment_method"]:checked').length == 0) {
			validationErrors.push(quitenicebooking.validationerror_paymentmethod);
		}
		if ($('#terms_check').length == 1 && !$('#terms_check').is(':checked')) {
			validationErrors.push(quitenicebooking.validationerror_tos);
		}

		if (validationErrors.length > 0) {
			$('.booking-form-notice').html('');
			for (var i = 0; i < validationErrors.length; i ++) {
				$('.booking-form-notice').append('<p>'+validationErrors[i]+'</p>');
			}
			$('html, body').animate( { scrollTop: $('.booking-main').offset().top }, 1000);
			// animation
			$('.booking-form-notice').delay(1000).effect('pulsate', { times:2 }, 1200);
			$('.booking-form-notice').delay(1000).fadeIn(1200, function() { } );

			return false;
		}
	});

	/**
	 * Step 3 - Skip validation if coupon being applied
	 */
	$('button[name="apply_coupon"], button[name="remove_coupon"]').on('click', function(e) {
		$('.booking-fields-form').off('submit');
	});

	/**
	 * Step 3 - Payment method accordion
	 */
	$('div.payment_method').accordion({event: 'mouseup', heightStyle: 'content'});
	$('div.payment_method h3').on('click', function() {
		$('input', this).prop('checked', true);
	});
	$('div.payment_method h3 input').on('click', function() {
		$(this).prop('checked', true);
	});
	// check first radio by default
	$('div.payment_method h3 input').first().prop('checked', true);

	/**
	 * Step 3 - Services checkboxes
	 */
	$('input[name="services[]"]').on('change', function() {
		calculate_services();
	});

	/**
	 * Calculate services on first load
	 */
	calculate_services();

	/**
	 * Calculate service prices and update total, breakdown
	 */
	function calculate_services() {
		// for each of the checked services, add to array
		var services = $('input[name="services[]"]');
		if (services.length == 0) {
			return;
		}
		var services_arr = [];
		for (var i = 0; i < services.length; i ++) {
			if (services.eq(i).prop('checked')) {
				services_arr.push(services.eq(i).val());
			}
		}
		// for each of the coupons inserted, add to array
		var coupons = $('.coupon-applied input[name^="coupon_code_"]');
		var coupons_arr = [];
		for (var i = 0; i < coupons.length; i ++) {
			coupons_arr.push(coupons.eq(i).val());
		}
		// send ajax request
		var request = {
			action: 'quitenicebooking_ajax_step_3_breakdown',
			services: services_arr,
			coupons: coupons_arr
		};
		$.post(quitenicebooking.ajax_url, request, function(response) {
			$('#price_break_hotel_room_all .page-content').html(response.breakdown_html);
			$('.deposit-price').html(response.deposit);
			$('.total-price').html(response.total);
		}, 'json');
	}
});
})(jQuery);
