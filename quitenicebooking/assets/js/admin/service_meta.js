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
	 * 'Check all' button
	 * Checks all room checkboxes
	 */
	$('#check_all').on('click', function(e) {
		e.preventDefault();
		$('input[name^="quitenicebooking_service_id_"]').each(function() {
			$(this).prop('checked', true);
		});
	});

	/**
	 * 'Uncheck all' button
	 * Unchecks all room checkboxes
	 */
	$('#uncheck_all').on('click', function(e) {
		e.preventDefault();
		$('input[name^="quitenicebooking_service_id_"]').each(function() {
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

		// validate price


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