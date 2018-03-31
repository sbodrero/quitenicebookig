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
	 * Fix the admin menu not opening
	 */
	$('#menu-posts-booking, #menu-posts-booking > a').removeClass('wp-not-current-submenu');
	$('#menu-posts-booking, #menu-posts-booking > a').addClass('wp-has-current-submenu wp-menu-open');

	/**
	 * Add datepicker to date fields
	 */
	$(document).on('focusin', 'input[name="quitenicebooking_booking_block_startdate"]', function() {
		$(this).datepicker({
			minDate: 0,
			firstDay: 1,
			dateFormat: quitenicebooking_premium.js_date_format
		});
	});

	$(document).on('focusin', 'input[name="quitenicebooking_booking_block_enddate"]', function() {
		$(this).datepicker({
			minDate: calc_minDate(),
			firstDay: 1,
			dateFormat: quitenicebooking_premium.js_date_format
		});
	});

	/**
	 * Make the datepicker fields read-only
	 */
	$('.datepicker').attr('readonly', true);

	/**
	 * Set datepicker minDate
	 */
	$('input[name="quitenicebooking_booking_block_startdate"]').on('change', function() {
		if ($('input[name="quitenicebooking_booking_block_enddate"]').hasClass('hasDatepicker')) {
			// datepicker already instantiated, change option
			$('input[name="quitenicebooking_booking_block_enddate"]').datepicker('option', 'minDate', calc_minDate());
		} else {
			// new datepicker instance
			$('input[name="quitenicebooking_booking_block_enddate"]').datepicker({minDate: calc_minDate(), firstDay: 1, dateFormat: quitenicebooking_premium.js_date_format});
			if ($.datepicker.parseDate(quitenicebooking_premium.js_date_format, $('input[name="quitenicebooking_booking_block_enddate"]').val()) <= calc_minDate()) {
				$('input[name="quitenicebooking_booking_block_enddate"]').val($.datepicker.formatDate(quitenicebooking_premium.js_date_format, calc_minDate()));
			}
		}
	});

	/**
	 * Calculate endDate minDate
	 */
	function calc_minDate() {
		if ($('input[name="quitenicebooking_booking_block_startdate"]').val().length > 0) {
			var d = new Date($.datepicker.parseDate(quitenicebooking_premium.js_date_format, $('input[name="quitenicebooking_booking_block_startdate"]').val()));
			d.setDate(d.getDate() + 1);
			return d;
		}
		return 0;
	}

	/**
	 * 'Check all' button
	 * Checks all room checkboxes
	 */
	$('#check_all').on('click', function(e) {
		e.preventDefault();
		$('input[name^="quitenicebooking_booking_block_id_"]').each(function() {
			$(this).prop('checked', true);
		});
	});

	/**
	 * 'Uncheck all' button
	 * Unchecks all room checkboxes
	 */
	$('#uncheck_all').on('click', function(e) {
		e.preventDefault();
		$('input[name^="quitenicebooking_booking_block_id_"]').each(function() {
			$(this).prop('checked', false);
		});
	});

	/**
	 * Form submission and validation
	 */
	$('form').on('submit', function(e) {
		$('#validation_errors').addClass('hidden');
		$('#validation_errors').html('');

		var validationErrors = new Array();

		// validate date fields
		var startdate = Date.parse($.datepicker.parseDate(quitenicebooking_premium.js_date_format, $('input[name$=startdate]').val()));
		var enddate =  Date.parse($.datepicker.parseDate(quitenicebooking_premium.js_date_format, $('input[name$=enddate]').val()));
		if (isNaN(startdate) || isNaN(enddate) || (startdate >= enddate)) {
			validationErrors.push('Please enter a valid date range');
		}

		if (validationErrors.length > 0) {
			// validation failed, show errors
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
});
})(jQuery);