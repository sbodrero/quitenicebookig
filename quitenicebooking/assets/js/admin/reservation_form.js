(function($){
$(document).ready(function() {

	/**
	 * Show the element_params div and relevant fields
	 */
	function show_generator_form() {
		if ($('#element_type').val() != '') {
			$('#element_params').css('display', 'block');
		} else {
			$('#element_params').css('display', 'none');
		}
		var input_text = ['text', 'textarea'];

		if ($.inArray($('#element_type').val(), input_text) !== -1) {
			$('#element_value_div, #element_maxlength_div').show();
			$('#element_choices_div').hide();
		} else {
			$('#element_value_div, #element_maxlength_div').hide();
			$('#element_choices_div').show();
			if ($('#element_type').val() == 'select') {
				$('#element_multiple_choices_div').show();
			} else {
				$('#element_multiple_choices_div').hide();
			}
		}
		// clear inputs
		$('#element_id, #element_label, #element_value, #element_maxlength, #element_class, #element_choices').val('');
		$('#element_required, #element_multiple_choices').prop('checked', false);
		$('#generated').val('');
		// clear errors
		$('#validation_errors').addClass('hidden');
		$('#validation_errors').html('');
	};
	
	/**
	 * Show form when when dropdown clicked/changed
	 */
	$('#element_type').on('change', function() {
		show_generator_form();
	});

	/**
	 * Show form on load
	 */
	show_generator_form();

	/**
	 * Generate a tag based on the parameters
	 */
	$('#element_generate').on('click', function(e) {
		e.preventDefault();

		$('#generated').val('');
		$('#validation_errors').addClass('hidden');
		$('#validation_errors').html('');

		var errors = new Array();

		// check if type selected
		if ($('#element_type').val() == '') {
			errors.push('Please select a field type');
		}

		var composed = '[';
		composed += $('#element_type').val();
		composed += $('#element_required').is(':checked') ? ' required' : '';

		// create a safe id consisting only of [a-z0-9-_]
		var element_id = $('#element_id').val().toLowerCase().replace(/[^a-z0-9_-]+/g, '');
		if (element_id.length == 0) {
			errors.push('Please enter an ID');
		} else if ($('#quitenicebooking\\\[reservation_form\\\]').val().match('id="'+element_id+'"')) { // check for duplicate IDs
			errors.push('ID is already in use; please enter a unique ID');
		} else {
			composed += ' id="'+element_id+'"';
		}
		// escape any double quotes in label
		var element_label = $('#element_label').val().trim().replace(/"/g, '&#34;');
		if (element_label.length == 0) {
			errors.push('Please enter a label');
		} else {
			composed += ' label="'+element_label+'"';
		}
		// text and textarea types
		var input_text = ['text', 'textarea'];
		if ($.inArray($('#element_type').val(), input_text) !== -1) {
			// escape any double quotes in value
			var element_value = $('#element_value').val().trim().replace(/"/g, '&#34;');
			if (element_value.length > 0) {
				composed += ' value="'+element_value+'"';
			}

			// validate maxlength
			var element_maxlength = $('#element_maxlength').val().trim();
			if (element_maxlength.match(/^\d*$/) == null) {
				errors.push('Please enter a valid max length value (number)');
			} else {
				if (element_maxlength.length > 0) {
					composed += ' maxlength="'+element_maxlength+'"';
				}
			}
		} else {
		// checkbox, radio, select types
			// parse choices by line.  escape any double quotes and commas
			var element_choices = $('#element_choices').val().trim().replace(/"/g, '&#34;').replace(/,/g, '&#44;').replace(/\n/g, ',');
			if (element_choices.length == 0) {
				errors.push('Please enter at least one choice');
			}
			composed += ' choices="'+element_choices+'"';
			if ($('#element_type').val() == 'select' && $('#element_multiple_choices').is(':checked')) {
				composed += ' multiple';
			}
		}

		// validate class
		var element_class = $('#element_class').val().trim();
		if (element_class.length > 0) {
			if (element_class.match(/-?[_a-zA-Z]+[_a-zA-Z0-9-]*/) == null) {
				errors.push('Please enter a valid CSS class name');
			} else {
				composed += ' class="'+element_class+'"';
			}
		}

		composed += ']';

		if (errors.length == 0) {
			$('#generated').val(composed);
		} else {
			$('#validation_errors').removeClass('hidden');
			for (var i = 0; i < errors.length; i ++) {
				$('#validation_errors').append('<p><strong>'+errors[i]+'</strong></p>');
			}
			$('html, body').animate( { scrollTop: $('div#validation_errors').offset().top-50 }, 1000);
		}
	});

	/**
	 * Select generated text
	 */
	$('#generated').on('click', function() {
		$(this).select();
	});


	/**
	 * Generate some hard-coded tags
	 */
	$('#generate_guest_first_name').on('click', function(e) {
		e.preventDefault();
		$('#generated').val('[guest_first_name]');
	});
	$('#generate_guest_last_name').on('click', function(e) {
		e.preventDefault();
		$('#generated').val('[guest_last_name]');
	});
	$('#generate_guest_email').on('click', function(e) {
		e.preventDefault();
		$('#generated').val('[guest_email]');
	});

	/**
	 * Form submission validation
	 */
	$('form').on('submit', function() {
		// check if the 3 required fields are present
		if ($('#quitenicebooking\\[reservation_form\\]').val().indexOf('[guest_first_name]') == -1
			|| $('#quitenicebooking\\[reservation_form\\]').val().indexOf('[guest_last_name]') == -1
			|| $('#quitenicebooking\\[reservation_form\\]').val().indexOf('[guest_email]') == -1) {
			$('#form_generator_errors').css('display', 'block');
			$('#form_generator_errors').addClass('error');
			$('#form_generator_errors').html('<p><strong>Form is missing one or more of the required fields: [guest_first_name], [guest_last_name], [guest_email]</strong></p>');
			return false;
		}
	});
});
})(jQuery);
