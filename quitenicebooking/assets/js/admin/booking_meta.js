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

var room_num = parseInt(quitenicebooking.num_rooms) + 1;
$(document).ready(function() {
	// add datepicker to date fields
	$(document).on('focusin', 'input[name$="checkin"]', function() {
		$(this).datepicker({
			minDate: 0,
			firstDay: 1,
			dateFormat: quitenicebooking.js_date_format
		});
	});
	$(document).on('focusin', 'input[name$="checkout"]', function() {
		$(this).datepicker({
			minDate: calc_minDate($(this)),
			firstDay: 1,
			dateFormat: quitenicebooking.js_date_format
		});
	});

	// set checkout datepicker mindate when checkin changed
	$(document).on('change', 'input[name$="checkin"]', function() {
		if ($(this).parent().parent().find('input[name$="checkout"]').hasClass('hasDatepicker')) {
			// datepicker already instantiated, change option
			$(this).parent().parent().find('input[name$="checkout"]').datepicker('option', 'minDate', calc_minDate($(this)));
		} else {
			// new datepicker instance
			$(this).parent().parent().find('input[name$="checkout"]').datepicker({minDate: calc_minDate($(this)), firstDay: 1, dateFormat: quitenicebooking.js_date_format});
			if ($.datepicker.parseDate(quitenicebooking.js_date_format, $(this).parent().parent().find('input[name$="checkout"]').val()) <= calc_minDate($(this))) {
				$(this).parent().parent().find('input[name$="checkout"]').val($.datepicker.formatDate(quitenicebooking.js_date_format, calc_minDate($(this))));
			}
		}
	});

	/**
	 * Calculate checkout minDate
	 * @param t jQuery(this)
	 */
	function calc_minDate(t) {
		if (t.parent().parent().find('input[name$="checkin"]').val().length > 0) {
			var d = new Date($.datepicker.parseDate(quitenicebooking.js_date_format, t.parent().parent().find('input[name$="checkin"]').val()));
			d.setDate(d.getDate() + 1);
			return d;
		}
		return 0;
	}

	/**
	 * Add room button
	 */
	$('button#add_room').on('click', function(e) {
		e.preventDefault();

		var bedStr = '';
		// load bed_types for the first room only
		// because js converted the php array into an unordered object, here, get the "first" element
		var first_room = 0;
		for (var f in quitenicebooking.all_rooms) {
			first_room = f;
			break;
		}
		for (var k in quitenicebooking.bed_types) {
			if ( quitenicebooking.all_rooms[first_room]['quitenicebooking_beds_'+k] == 1 ) {
				bedStr += '<option value="'+k+'">'+quitenicebooking.bed_types[k].description+'</option>';
			}
		}
		bedStr += '<option value="0">'+$('#translations .t_room').html()+'</option>';
		var room_html = $('#room_template').html().replace(/-room_num-/g, room_num).replace(/<!-- bedstr -->/, bedStr);
		$('div#dynamic_add_room').append('<div class="room">'+room_html+'</div>');
		room_num++;
	});

	/**
	 * Remove room button
	 */
	$(document).on('click', 'button.remove_room', function(e) {
		e.preventDefault();
		$(this).parent().parent().remove();
		room_num --;
		// reorder existing rooms
		var count = 1;
		var rooms = $('div.room');
		for (var i = 0; i < rooms.length; i ++) {
			$('div.room').eq(i).children('h3').html('Room '+count);
			$('div.room').eq(i).find('input, select').each( function(){
				$(this).attr('name', function(index, value) {
					return value.replace(/room_booking_\d+/, 'room_booking_'+count);
				});
				$(this).attr('id', function(index, value) {
					return value.replace(/room_booking_\d+/, 'room_booking_'+count);
				});
			});
			$('div.room').eq(i).find('label').each(function() {
				$(this).attr('for', function(index, value) {
					return value.replace(/room_booking_\d+/, 'room_booking_'+count);
				});
			});
			count ++;
		}
	});

	/**
	 * Populate bed_types when room_type changes
	 */
	$(document).on('change', 'select[name$=_type]', function(e) {
		e.preventDefault();
		$(this).parentsUntil('.room').eq(1).find('select[name$=_bed] option').remove();
		for (var k in quitenicebooking.bed_types) {
			if ( quitenicebooking.all_rooms[$(this).val()]['quitenicebooking_beds_'+k] == 1 ) {
				$(this).parentsUntil('.room').eq(1).find('select[name$=_bed]').append('<option value="'+k+'">'+quitenicebooking.bed_types[k].description+'</option>');
			}
		}
		$(this).parentsUntil('.room').eq(1).find('select[name$=_bed]').append('<option value="0">Room</option>');
	});

	/**
	 * Availability checker ajax
	 */
	$(document).on('click', 'button.check_availability', function(e) {
		e.preventDefault();
		// validation
		// check that all fields have been filled before submitting
		if ($(this).parentsUntil('#dynamic_add_room').eq(1).find('input[name$="checkin"]').val().length == 0 || $(this).parentsUntil('#dynamic_add_room').eq(1).find('input[name$="checkout"]').val().length == 0) {
			$(this).siblings('.ajax_messages').html('<img src="'+quitenicebooking.assets_url+'images/no.png" alt="" />'+$('#translations .t_fill_in_dates').html());
			return false;
		}
		// validate checkin < checkout
		var checkin = Date.parse($.datepicker.parseDate(quitenicebooking.js_date_format, $(this).parentsUntil('#dynamic_add_room').eq(1).find('input[name$="checkin"]').val()));
		var checkout = Date.parse($.datepicker.parseDate(quitenicebooking.js_date_format, $(this).parentsUntil('#dynamic_add_room').eq(1).find('input[name$="checkout"]').val()));
		if (checkin >= checkout) {
			$(this).siblings('.ajax_messages').html('<img src="'+quitenicebooking.assets_url+'images/no.png" alt="" />'+$('#translations .t_checkout_after').html());
			return false;
		}

		// update the status
		$(this).siblings('.ajax_messages').html('<img src="'+quitenicebooking.assets_url+'images/wpspin_light.gif" alt="" />'+$('#translations .t_checking_availability').html());

		// create current_room object
		// get the button's parent and get all the values from the fields
		var current_room = new Object();
		current_room.checkin = $(this).parentsUntil('#dynamic_add_room').eq(1).find('input[name$="checkin"]').val();
		current_room.checkout = $(this).parentsUntil('#dynamic_add_room').eq(1).find('input[name$="checkout"]').val();
		current_room.adults = parseInt($(this).parentsUntil('#dynamic_add_room').eq(1).find('select[name$="adults"]').val());
		current_room.children = parseInt($(this).parentsUntil('#dynamic_add_room').eq(1).find('select[name$="children"]').val());
		current_room.type = $(this).parentsUntil('#dynamic_add_room').eq(1).find('select[name$="type"]').val();
		current_room.bed = $(this).parentsUntil('#dynamic_add_room').eq(1).find('select[name$="bed"]').val();

		// create the checked_rooms array of objects
		// each checked_room has an edit_room button, use its existence as the criteria
		var checked_rooms = new Array();
		var checked_rooms_selector = $('.edit_room');
		for (var i = 0; i < checked_rooms_selector.length; i ++) {
			checked_rooms[i] = new Object();
			checked_rooms[i].checkin = checked_rooms_selector.eq(i).parentsUntil('#dynamic_add_room').eq(1).find('input[name$="checkin"]').val();
			checked_rooms[i].checkout = checked_rooms_selector.eq(i).parentsUntil('#dynamic_add_room').eq(1).find('input[name$="checkout"]').val();
			checked_rooms[i].adults = parseInt(checked_rooms_selector.eq(i).parentsUntil('#dynamic_add_room').eq(1).find('select[name$="adults"]').val());
			checked_rooms[i].children = parseInt(checked_rooms_selector.eq(i).parentsUntil('#dynamic_add_room').eq(1).find('select[name$="children"]').val());
			checked_rooms[i].type = checked_rooms_selector.eq(i).parentsUntil('#dynamic_add_room').eq(1).find('select[name$="type"]').val();
			checked_rooms[i].bed = checked_rooms_selector.eq(i).parentsUntil('#dynamic_add_room').eq(1).find('select[name$="bed"]').val();
		}

		var formdata = {
			action: 'quitenicebooking_ajax_check_availability',
			current_room: current_room,
			checked_rooms: checked_rooms,
			post_id: quitenicebooking.post_id
		};
		var check_availability_button = $(this);
		$.post(quitenicebooking.ajax_url, formdata, function(response) {
			if (response == 'true') {
				// room available
				check_availability_button.siblings('.ajax_messages').html('<img src="'+quitenicebooking.assets_url+'images/yes.png" alt="" />'+$('#translations .t_room_added').html());
				// change all fields to read-only/disabled (if disabled, enable before submit)
				check_availability_button.parentsUntil('#dynamic_add_room').eq(1).find('input, select').prop('disabled', true);

				// swap check_availability button for edit_room button
				check_availability_button.after('<button class="edit_room button">'+$('#translations .t_edit_room').html()+'</button>');
				check_availability_button.remove();
			} else if (response == '"room_qty"') {
				check_availability_button.siblings('.ajax_messages').html('<img src="'+quitenicebooking.assets_url+'images/no.png" alt="" />'+$('#translations .t_fully_booked').html());
			} else if (response == '"guest_qty"') {
				check_availability_button.siblings('.ajax_messages').html('<img src="'+quitenicebooking.assets_url+'images/no.png" alt="" />'+$('#translations .t_too_many_guests').html());
			} else if (response == '"room_blocked"') {
				check_availability_button.siblings('.ajax_messages').html('<img src="'+quitenicebooking.assets_url+'images/no.png" alt="" />'+$('#translations .t_dates_blocked').html());
			}
		});

	});

	/**
	 * Edit room button
	 */
	$(document).on('click', 'button.edit_room', function(e) {
		e.preventDefault();

		// enable fields to be edited
		$(this).parentsUntil('#dynamic_add_room').eq(1).find('input, select').removeProp('disabled');

		// replace edit_room button with check_availability button
		$(this).siblings('.ajax_messages').html('');
		$(this).after('<button class="check_availability button">'+$('#translations .t_check_availability').html()+'</button>');
		$(this).remove();

	});

	/**
	 * Form submission and validation
	 */
	$('form').on('submit', function(e) {
		// re-enable all the fields
		$('.room').find('input, select').removeProp('disabled');

		// remove all the rooms that have a check_availability button
		// 1. get the number of unchecked rooms to remove
		var remove_rooms = $('.check_availability').parentsUntil('#dynamic_add_room').filter('.room');
		room_num -= remove_rooms.length;
		// 2. remove the unchecked rooms
		remove_rooms.remove();
		// 3. reorder existing rooms (same as above)
		var count = 1;
		var rooms = $('div.room');
		for (var i = 0; i < rooms.length; i ++) {
			$('div.room').eq(i).children('h3').html('Room '+count);
			$('div.room').eq(i).find('input, select').each( function(){
				$(this).attr('name', function(index, value) {
					return value.replace(/room_booking_\d+/, 'room_booking_'+count);
				});
				$(this).attr('id', function(index, value) {
					return value.replace(/room_booking_\d+/, 'room_booking_'+count);
				});
			});
			count ++;
		}

		$('#validation_errors').addClass('hidden');
		$('#validation_errors').html('');
		var validationErrors = new Array();

		if ($('#quitenicebooking_guest_last_name').val().trim().length == 0
			|| $('#quitenicebooking_guest_first_name').val().trim().length == 0
			|| $('#quitenicebooking_guest_email').val().trim().length == 0) {
			validationErrors.push($('#translations .t_validation_required_guest').html());
		}

		if ($('#quitenicebooking_guest_email').val().trim().match(/.+@.+\..+/) == null) {
			validationErrors.push($('#translations .t_validation_guest_email').html());
		}

		var unicode_decimal_sep = (quitenicebooking.currency_decimal_separator.charCodeAt(0)).toString(16);
		unicode_decimal_sep = ('000'+unicode_decimal_sep).slice(-4);

		if ($('#quitenicebooking_deposit_amount').val().trim().length > 0
			&& $('#quitenicebooking_deposit_amount').val().trim().match('^\\d+\\u'+unicode_decimal_sep+'?\\d*$') == null) {
			validationErrors.push($('#translations .t_validation_deposit_amount').html());
		}

		if ($('#quitenicebooking_total_price').val().trim().length > 0
			&& $('#quitenicebooking_total_price').val().trim().match('^\\d+\\u'+unicode_decimal_sep+'?\\d*$') == null) {
			validationErrors.push($('#translations .t_validation_total_price').html());
		}

		// count number of rooms
		if ($('input[name$="_checkin"]').not('input[name="quitenicebooking_room_booking_-room_num-_checkin"]').last().length == 0) {
			validationErrors.push($('#translations .t_validation_add_room').html());
		} else {
			var num_rooms = $('input[name$="_checkin"]').not('input[name="quitenicebooking_room_booking_-room_num-_checkin"]').last().get(0).name.match(/room_booking_(\d)+_checkin/)[1];

			for (var n = 1; n <= num_rooms; n++) {
				// check for empty fields
				if ($('#quitenicebooking_room_booking_'+n+'_checkin').val().trim().length == 0
					|| $('#quitenicebooking_room_booking_'+n+'_checkout').val().trim().length == 0
					|| $('#quitenicebooking_room_booking_'+n+'_type').val().trim().length == 0
					|| $('#quitenicebooking_room_booking_'+n+'_bed').val().trim().length == 0
					|| $('#quitenicebooking_room_booking_'+n+'_adults').val().trim().length == 0
					|| $('#quitenicebooking_room_booking_'+n+'_children').val().trim().length == 0) {
					validationErrors.push($('#translations .t_validation_room_info').html().replace(/%s/, n));
				}

				if ($('#quitenicebooking_room_booking_'+n+'_adults').val().trim().match(/\d+/) == null
					|| $('#quitenicebooking_room_booking_'+n+'_children').val().trim().match(/\d+/) == null) {
					validationErrors.push($('#translations .t_validation_room_guests').html().replace(/%s/, (n+1)));
				}
			}
		}

		if (validationErrors.length > 0) {
			// disable rooms
			$('.room').find('input, select').prop('disabled', true);

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

		// automatically generate a title for this post
		var title_str = '';
		// "n room booking for first_name last_name (first_checkin - last_checkout)"
		// 1. count number of rooms
		title_str += rooms.length + ' room booking for ' + $('#quitenicebooking_guest_first_name').val() + ' ' + $('#quitenicebooking_guest_last_name').val();
		// 2. get the first checkin
		// 2.1. get all the checkin dates and convert them to js time
		var checkins = new Array();
		$('.room').find('input[name$=_checkin]').each(function(index, elem){
			checkins.push(Date.parse($.datepicker.parseDate(quitenicebooking.js_date_format, $(elem).val())));
		});
		// 2.2. find the first checkin
		var first_checkin = checkins[0];
		for (var i = 0; i < checkins.length; i ++) {
			if (checkins[i] < first_checkin) {
				first_checkin = checkins[i];
			}
		}
		// 2.3. convert the first checkin back to js_date_format
		first_checkin = $.datepicker.formatDate(quitenicebooking.js_date_format, new Date(first_checkin));

		// 3. get the last checkout
		var checkouts = new Array();
		// 3.1. get all the checkout dates and convert them to js time
		$('.room').find('input[name$=_checkout]').each(function(index, elem){
			checkouts.push(Date.parse($.datepicker.parseDate(quitenicebooking.js_date_format, $(elem).val())));
		});
		// 3.2. find the last checkout
		var last_checkout = checkouts[0];
		for (var i = 0; i < checkouts.length; i ++) {
			if (checkouts[i] > last_checkout) {
				last_checkout = checkouts[i];
			}
		}
		// 3.3. convert the last checkout back into js_date_format
		last_checkout = $.datepicker.formatDate(quitenicebooking.js_date_format, new Date(last_checkout));

		// 4. put it all together
		title_str += ' (' + first_checkin + ' - ' + last_checkout + ')';

		$('#title-prompt-text').addClass('screen-reader-text');
		$('#title').val(title_str);

	});
});

})(jQuery);
