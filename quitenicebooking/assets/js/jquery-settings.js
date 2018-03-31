/**
 * Settings for the jQuery scripts
 *
 * Dependencies: jqueryui.datepicker, jqueryui.pulsate, prettyphoto
 *
 * global string quitenicebooking.date_format The date format from the plugin's settings: dd/mm/yy, mm/dd/yy, or yy/mm/dd
 * global string quitenicebooking.validationerror_requiredfield Validation error message
 * global string quitenicebooking.validationerror_email Validation error message
 * global string quitenicebooking.validationerror_paymentmethod Validation error message
 * global string quitenicebooking.validationerror_tos Validation error message
 */

// dictionary of unavailable dates
var blocked = {};

jQuery(document).ready(function() {
	
	'use strict';

	jQuery('.home-reservation-box').hide();
	jQuery('.widget-reservation-box').hide();
	jQuery('#bookingFormOverlay').addClass('spinner');
	
	
	/**
	 * PrettyPhoto ============================================================
	 */
	jQuery('a[rel^="prettyPhoto"]').prettyPhoto({social_tools:false});
	
	// Calendar Message
	jQuery('.datepicker2').click(function(e){
        jQuery('.ui-datepicker-calendar').effect('pulsate', { times:2 }, 1000);
		jQuery('.calendar-notice').fadeIn(1200, function() {
			// Animation complete
		});
		e.stopPropagation();
    });

	/**
	 * Toggle rooms ===========================================================
	 */
	
	// initial procedures

	// hide all rooms except for room-1
	jQuery('.rooms-wrapper div[class^="room-"]:not(div.room-1)').hide();
	jQuery('div.room-1 p.label').hide();

	// if the form was reloaded and already populated, run once to prevent rooms from being hidden
	toggle_rooms(jQuery('#room_qty').val());

	// event listener for when the room quantity drop-down changes
	jQuery('#room_qty').on('change', function(e) {
		e.preventDefault();
		toggle_rooms(jQuery('#room_qty').val());
	});

	/**
	 * Shows the appropriate number of rooms depending how many are selected in the drop-down
	 *
	 * param int room_qty The number of rooms to show; pass in the value from #room_qty
	 */
	function toggle_rooms(room_qty) {
		jQuery('.rooms-wrapper div[class^="room-"]').hide(); // hide everything at first
		for (var i = 1; i <= room_qty; i++) {
			jQuery('div[class^=room-'+ i +']').show(); // show all rooms up to room_qty
		}
		// case for if room_qty == 1, hide its p label
		if (room_qty == 1) {
			jQuery('div.room-1 p.label').hide();
		} else {
			jQuery('div.room-1 p.label').show();
		}
	}

	/**
	 * Datepicker =============================================================
	 *
	 * Note: Since the availability checker AJAX is synchronous (to reduce server load), Datepicker should run last, to prevent holding up other functions
	 */

	/**
	 * Add a custom event for datepicker to fire after it renders
	 */
	jQuery(function() {
		jQuery.datepicker._updateDatepicker_original = jQuery.datepicker._updateDatepicker;
		jQuery.datepicker._updateDatepicker = function(inst) {
			jQuery.datepicker._updateDatepicker_original(inst);
			var afterShow = this._get(inst, 'afterShow');
			if (afterShow)
				afterShow.apply((inst.input ? inst.input[0] : null));  // trigger custom callback
		}
	});

	// Attach Datepicker to widget
	jQuery('.datepicker').on('focusin', function() {
		var fday = (quitenicebooking.saturday_only == 1) ? 6 : 1; // Setting up saturday_to_saturday option.
		jQuery(this).datepicker({
			numberOfMonths: 2,
			minDate: 0,
			firstDay: fday,
			dateFormat: quitenicebooking.date_format,
			beforeShowDay: open_datepicker_widget,
			onSelect: function(val, inst) { jQuery(this).data('datepicker').inline = true; widget_select_dates(val, inst, this); },
			onChangeMonthYear: show_throbber_widget,
			afterShow: hide_throbber_widget,
			onClose: function() { jQuery(this).data('datepicker').inline = false; hide_throbber_widget(); }
		});
	});
	jQuery('.datepicker')
	
	// Make Datepicker Fields Read Only
	jQuery('#datefrom').attr('readonly', true);
	jQuery('#dateto').attr('readonly', true);

	// Attach Datepicker to step 1
	var fday = (quitenicebooking.saturday_only == 1) ? 6 : 1; // Setting up saturday_to_saturday option.
    jQuery('#open_datepicker').datepicker({
        dateFormat: quitenicebooking.date_format,
        numberOfMonths: 2,
        minDate: 0,
		firstDay: fday,
        beforeShowDay: (quitenicebooking.enable_calendar_availability == '1') ? open_datepicker : open_datepicker2,
		onChangeMonthYear: show_throbber,
		afterShow: hide_throbber,
        onSelect: select_dates
    });
	// hide the throbber after calendar loads
	hide_throbber();

	// show the throbber when changing month/year
	function show_throbber() {
		jQuery('#datepicker-loading-spinner').css('display', '');
	}

	// hide the throbber after calendar loads
	function hide_throbber() {
		jQuery('#datepicker-loading-spinner').css('display', 'none');
	}

	/**
	 * Wrapper function when opening datepicker via widget
	 * @param {Date} date The queried date
	 * @returns {Array} The styling of the date
	 *		[true, ''] if date is available
	 *		[true, 'dp-highlight'] if the date is in the selected range
	 *		[true, 'dp-half-begin'] if the date is available and another booking block begins on that day
	 *		[true, 'dp-half-end'] if the date is available and another booking block ends on the previous day
	 *		[false] if date is unavailable
	 */
	function open_datepicker_widget(date) {
		if (quitenicebooking.enable_calendar_availability != '1') {
			if (widget_datefrom.length > 0) {
				var wf = jQuery.datepicker.parseDate(quitenicebooking.date_format, widget_datefrom);
				if (date.getTime() == wf.getTime()) {
					return [true, 'dp-highlight-begin'];
				}
			}
			if (widget_dateto.length > 0) {
				var wt = jQuery.datepicker.parseDate(quitenicebooking.date_format, widget_dateto);
				if (date.getTime() == wt.getTime()) {
					return [true, 'dp-highlight-end'];
				}
			}
			return [true, wf && ((date.getTime() == wf.getTime()) || (wt && date >= wf && date <= wt)) ? 'dp-highlight' : ''];
		}

		if (widget_datefrom.length > 0) {
			var wf = jQuery.datepicker.parseDate(quitenicebooking.date_format, widget_datefrom);
			if (date.getTime() == wf.getTime()) {
				// if previous date is blocked, set this css class to dp-highlight-begin-blocked
				var prevdate = new Date(date.getTime());
				prevdate.setDate(prevdate.getDate() - 1);
				if (!blockedDates(prevdate)) {
					return [true, 'dp-highlight-begin-blocked'];
				}
				// if date is blocked, set it to dp-highlight-end-blocked
				if (!blockedDates(date)) {
					return [true, 'dp-highlight-end-blocked'];
				}
				// else set to dp-highlight-begin
				return [true, 'dp-highlight-begin'];
			}
			if (widget_dateto.length > 0) {
				var wt = jQuery.datepicker.parseDate(quitenicebooking.date_format, widget_dateto);
				if (date.getTime() == wt.getTime()) {
					// if date is blocked, set this css class to dp-highlight-end-blocked
					if (!blockedDates(date)) {
						return [true, 'dp-highlight-end-blocked'];
					}
					return [true, 'dp-highlight-end'];
				}
				if (wf.getTime() <= date.getTime() && date.getTime() <= wt.getTime()) {
					return [true, 'dp-highlight'];
				}
			}
		}

		if (!blockedDates(date)) {
			// if previous date is not blocked, set this css class to half-begin
			var prevdate = new Date(date.getTime());
			prevdate.setDate(prevdate.getDate() - 1);
			if (blockedDates(prevdate)) {
				return [true, 'dp-half-begin'];
			}
			// else return a full blocked css class date
			return [false];
		}
		// if previous date is blocked, set this css class to half-end
		var prevdate = new Date(date.getTime());
		prevdate.setDate(prevdate.getDate() - 1);
		if (!blockedDates(prevdate)) {
			return [true, 'dp-half-end'];
		}
		// else just return it
		return [true];
	}

	var widget_datefrom = '';
	var widget_dateto = '';

	function widget_select_dates(val, inst, jq) {
		var clicked_date = jQuery.datepicker.parseDate(quitenicebooking.date_format, val);

		if (widget_datefrom.length == 0 && widget_dateto.length == 0) {
			// both fields are empty
			widget_datefrom = val;
			widget_dateto = '';
		} else if (widget_datefrom.length > 0 && widget_dateto.length == 0) {
			// from field is filled, to is empty, validate whether from < to, and whether any date in between are blocked
			var wf = jQuery.datepicker.parseDate(quitenicebooking.date_format, widget_datefrom);
			var day = new Date(wf);
			var end = new Date(clicked_date);
			var dayblocked = false;
			if (quitenicebooking.enable_calendar_availability == '1') {
				while (day < end) {
					if (jQuery.inArray(day.getDate(), blocked['y'+day.getFullYear()]['m'+(day.getMonth() + 1)]) != -1) {
						dayblocked = true;
						break;
					}
					day.setDate(day.getDate() + 1);
				}
			}

			if (clicked_date <= wf || dayblocked) {
				// validation failed, set this as the new from date
				widget_datefrom = val;
				widget_dateto = '';
			} else {
				// set this as the new to date
				widget_dateto = val;
			}
		} else if (widget_datefrom.length > 0 && widget_dateto.length > 0) {
			// both fields are filled
			widget_datefrom = val;
			widget_dateto = '';
		}

		// close the datepicker, wait 1 second before removing the dp-flip class so it does not show when fading out
		if (widget_dateto.length > 0) {
			jQuery(jq).data('datepicker').inline = false;
			setTimeout(function() { jQuery('#ui-datepicker-div').removeClass('dp-flip'); }, 1000);
		}

		if (widget_datefrom.length > 0 && widget_dateto.length == 0) {
			jQuery('#ui-datepicker-div').addClass('dp-flip');
		}
//		else {
//			jQuery('#ui-datepicker-div').removeClass('dp-flip');
//		}
		
		// update the fields
		jQuery('#datefrom').val(widget_datefrom);
		jQuery('#dateto').val(widget_dateto);
	}

	jQuery('#ui-datepicker-div .ui-datepicker-calendar tbody tr td a').live('mouseenter', function() {
		widget_hover_select(this);
	});

	function widget_hover_select(hover_date) {
		var hd = new Date(parseInt(jQuery(hover_date).parent().attr('data-year')), parseInt(jQuery(hover_date).parent().attr('data-month')), parseInt(jQuery(hover_date).html()));

		if (widget_datefrom.length > 0 && widget_dateto.length == 0) {
			// clear previous highlight
			jQuery('#ui-datepicker-div .ui-datepicker-calendar td.dp-highlight').removeClass('dp-highlight');
			jQuery('#ui-datepicker-div .ui-datepicker-calendar td.dp-highlight-end-blocked').removeClass('dp-highlight-end-blocked');

			var day = new Date(jQuery.datepicker.parseDate(quitenicebooking.date_format, widget_datefrom));

			// check if first day is blocked
			if (quitenicebooking.enable_calendar_availability == '1' && !blockedDates(day)) {
				jQuery('#ui-datepicker-div .ui-datepicker-calendar td[data-year="'+day.getFullYear()+'"][data-month="'+day.getMonth()+'"] a').filter(function() { return jQuery(this).html() == day.getDate(); }).parent().addClass('dp-highlight-end-blocked');
				return;
			}

			day.setDate(day.getDate() + 1);

			while (day < hd) {
				if (quitenicebooking.enable_calendar_availability == '1' && !blockedDates(day)) {
					// if blocked date is in between date1 and date3, stop highlighting
					jQuery('#ui-datepicker-div .ui-datepicker-calendar td[data-year="'+day.getFullYear()+'"][data-month="'+day.getMonth()+'"] a').filter(function() { return jQuery(this).html() == day.getDate(); }).parent().addClass('dp-highlight-end-blocked');
					return;
				}

				jQuery('#ui-datepicker-div .ui-datepicker-calendar td[data-year="'+day.getFullYear()+'"][data-month="'+day.getMonth()+'"] a').filter(function() { return jQuery(this).html() == day.getDate(); }).parent().addClass('dp-highlight');
				day.setDate(day.getDate() + 1);
			}
		}
	}

	// save the original background image before swapping out with the throbber
	var date_input_background = jQuery('#datefrom').css('background-image');
	var date_input_position = jQuery('#datefrom').css('background-position');

	// put the throbber in the input field if datepicker is being loaded for the first time, hence a delay in opening
	jQuery('#datefrom, #dateto').on('mousedown', function() {
		if (jQuery('#open_datepicker').length == 0) {
			jQuery(this).css({
				'background-image': 'url(\''+quitenicebooking.plugin_url+'assets/images/calendar_widget_loading.gif\')',
				'background-position': '95% 50%'
			});
		}
	});

	function show_throbber_widget() {
		jQuery('#ui-datepicker-div').css({
			'width': '217px',
			'height': '218px',
			'background-image': 'url(\''+quitenicebooking.plugin_url+'assets/images/calendar_loading.gif\')',
			'background-repeat': 'no-repeat',
			'background-position': '50% 50%'
		});
	}

	function hide_throbber_widget() {
		jQuery('#ui-datepicker-div').css({
			'width': '',
			'height': '',
			'background-image': ''
		});

		// restore the input field throbber
		if (jQuery('#datefrom').css('background-image') != date_input_background
			|| jQuery('#dateto').css('background-image') != date_input_background) {
			jQuery('#datefrom, #dateto').css({
				'background-image': date_input_background,
				'background-position': date_input_position
			});
		}
	}

	/**
	 * Wrapper function when opening datepicker
	 * @param {Date} date The queried date
	 * @returns {Array} The styling of the date
	 *		[true, ''] if date is available
	 *		[true, 'dp-highlight'] if the date is in the selected range
	 *		[true, 'dp-highlight-begin'] if the date is the first of the selected range
	 *		[true, 'dp-highlight-end'] if the date is the last of the selected range
	 *		[true, 'dp-half-begin'] if the date is available and another booking block begins on that day
	 *		[true, 'dp-half-end'] if the date is available and another booking block ends on the previous day
	 *		[false] if date is unavailable
	 */
	function open_datepicker(date) {
		// check if dates blocked
//		if (blockedDates(date)) {
			// if dates not blocked
			// highlight selected dates
			var date1 = jQuery.datepicker.parseDate(quitenicebooking.date_format, jQuery('#datefrom').val());
			var date2 = jQuery.datepicker.parseDate(quitenicebooking.date_format, jQuery('#dateto').val());

			if (date1 && !date2) {
				jQuery('#open_datepicker').addClass('dp-flip');
			} else {
				jQuery('#open_datepicker').removeClass('dp-flip');
			}

			if (date1 && ((date.getTime() == date1.getTime()) || (date2 && date >= date1 && date <= date2))) {
				if (date.getTime() == date1.getTime()) {
					// if previous date is blocked, set this css class to dp-highlight-begin-blocked
					var prevdate = new Date(date.getTime());
					prevdate.setDate(prevdate.getDate() - 1);
					if (!blockedDates(prevdate)) {
						return [true, 'dp-highlight-begin-blocked'];
					}
					// if date is blocked, set it to dp-highlight-end-blocked
					if (!blockedDates(date)) {
						return [true, 'dp-highlight-end-blocked'];
					}
					// else set to dp-highlight-begin
					return [true, 'dp-highlight-begin'];
				}
				if (date.getTime() == date2.getTime()) {
					// if date is blocked, set this css class to dp-highlight-end-blocked
					if (!blockedDates(date)) {
						return [true, 'dp-highlight-end-blocked'];
					}
					// else set to dp-highlight-end
					return [true, 'dp-highlight-end'];
				}

				return [true, 'dp-highlight'];
			} else if (!blockedDates(date)) {
				// if previous date is not blocked, set this css class to half-begin
				var prevdate = new Date(date.getTime());
				prevdate.setDate(prevdate.getDate() - 1);
				if (blockedDates(prevdate)) {
					return [true, 'dp-half-begin'];
				}
				// else return a full blocked css class date
				return [false];
			} else {
				// if previous date is blocked, set this css class to half-end
				var prevdate = new Date(date.getTime());
				prevdate.setDate(prevdate.getDate() - 1);
				if (!blockedDates(prevdate)) {
					return [true, 'dp-half-end'];
				}
				// else just return it
				return [true, ''];
			}
//			return [true, date1 && ((date.getTime() == date1.getTime()) || (date2 && date >= date1 && date <= date2)) ? 'dp-highlight' : ''];
//		}
//		else {
//			return [true, 'dp-unavailable'];
//		}
	}

	/**
	 * Attach handler to highlight dates when date1 has been selected but not date 2
	 */
	jQuery('#open_datepicker .ui-datepicker-calendar td a').live('mouseenter', function() {
		hover_select(this);
	});

	/**
	 * Highlight date between date1 and mouse
	 * 
	 * @param {jQuery} hover_date The jQuery object being hovered over
	 */
	function hover_select(hover_date) {
		var date1 = jQuery.datepicker.parseDate(quitenicebooking.date_format, jQuery('#datefrom').val());
		var date2 = jQuery.datepicker.parseDate(quitenicebooking.date_format, jQuery('#dateto').val());

		var date3 = new Date(parseInt(jQuery(hover_date).parent().attr('data-year')), parseInt(jQuery(hover_date).parent().attr('data-month')), parseInt(jQuery(hover_date).html()));
		if (date1 && !date2) {
			// clear previous highlight
			jQuery('.ui-datepicker-calendar td.dp-highlight').removeClass('dp-highlight');
			jQuery('.ui-datepicker-calendar td.dp-highlight-end-blocked').removeClass('dp-highlight-end-blocked');
			var current_date = new Date(date1);
			
			// check if the first date is blocked
			if (quitenicebooking.enable_calendar_availability == '1' && !blockedDates(current_date)) {
				jQuery('.ui-datepicker-calendar td[data-year="'+current_date.getFullYear()+'"][data-month="'+current_date.getMonth()+'"] a').filter(function() { return jQuery(this).html() == current_date.getDate(); }).parent().addClass('dp-highlight-end-blocked');
				return;
			}

			current_date.setDate(current_date.getDate() + 1);

			while (current_date.getTime() < date3.getTime()) {
				if (quitenicebooking.enable_calendar_availability == '1' && !blockedDates(current_date)) {
					// if blocked date is in between date1 and date3, stop highlighting
					jQuery('.ui-datepicker-calendar td[data-year="'+current_date.getFullYear()+'"][data-month="'+current_date.getMonth()+'"] a').filter(function() { return jQuery(this).html() == current_date.getDate(); }).parent().addClass('dp-highlight-end-blocked');
					return;
				}
				// set current date to dp-highlight if current time < date3
				jQuery('.ui-datepicker-calendar td[data-year="'+current_date.getFullYear()+'"][data-month="'+current_date.getMonth()+'"] a').filter(function() { return jQuery(this).html() == current_date.getDate(); }).parent().addClass('dp-highlight');
				current_date.setDate(current_date.getDate() + 1);
			}
		}
	}

	/**
	 * Wrapper function when opening datepicker and live availability check is disabled
	 * @param {Date} The queried date
	 * @returns {Array} The styling of the date
	 *		[true, ''] if date is available
	 *		[true, 'dp-highlight'] if the date is in the selected range
	 */
	function open_datepicker2(date) {
		// highlight selected dates
		var date1 = jQuery.datepicker.parseDate(quitenicebooking.date_format, jQuery('#datefrom').val());
		var date2 = jQuery.datepicker.parseDate(quitenicebooking.date_format, jQuery('#dateto').val());

		if (date1 && !date2) {
			jQuery('#open_datepicker').addClass('dp-flip');
		} else {
			jQuery('#open_datepicker').removeClass('dp-flip');
		}
		if (date1 && (date.getTime() == date1.getTime())) {
			return [true, 'dp-highlight-begin'];
		}
		if (date2 && (date.getTime() == date2.getTime())) {
			return [true, 'dp-highlight-end'];
		}
		return [true, date1 && ((date.getTime() == date1.getTime()) || (date2 && date >= date1 && date <= date2)) ? 'dp-highlight' : ''];
	}

	// select dates
	// inserts the clicked date into the appropriate datefrom/dateto field
	function select_dates(dateText, inst) {
		var dateTextForParse = (inst.currentMonth + 1) + '/' + inst.currentDay + '/' + inst.currentYear;
		var date1 = jQuery.datepicker.parseDate(quitenicebooking.date_format, jQuery('#datefrom').val());
		var date2 = jQuery.datepicker.parseDate(quitenicebooking.date_format, jQuery('#dateto').val());

		// if date1 .. date2 crosses over blocked dates, don't set date2
		// 1. for each day starting from date1 until date2:
		//    2. check if day is in blocked
		//       3. if blocked, set #dateto to ''
		
		var day = new Date(date1);
		var end = new Date(dateTextForParse);
		var dayblocked = false;
		if (quitenicebooking.enable_calendar_availability == '1') {
			if (date1) {
				while (day < end) {
					if (jQuery.inArray(day.getDate(), blocked['y'+day.getFullYear()]['m'+(day.getMonth() + 1)]) != -1) {
						dayblocked = true;
						break;
					}
					day.setDate(day.getDate() + 1);
				}
			}
		}
		
		if (!date1 || date2 || dayblocked) {
			jQuery('#datefrom').val(dateText);
			jQuery('#dateto').val('');
		} else {
			if (Date.parse(dateTextForParse) <= Date.parse(date1))
			{
				jQuery('#datefrom').val(dateText);
				jQuery('#dateto').val('');
			}
			else
			{
				jQuery('#dateto').val(dateText);
			}
		}
	};

	/**
	 * Datepicker availability check
	 *
	 * @param Date date
	 * @return boolean true if date available, false if not
	 */
	function blockedDates(date) {
		var yy = 'y'+date.getFullYear();
		var mm = 'm'+(parseInt(date.getMonth())+1);

		var type = jQuery('input[name="highlight"]').val() || '';

		if (!blocked.hasOwnProperty(yy) || !blocked[yy].hasOwnProperty(mm)) {
			getBlockedDates(date.getFullYear(), parseInt(date.getMonth())+1, type);
		}

		if (jQuery.inArray(date.getDate(), blocked[yy][mm]) != -1) {
			return false;
		}

		return true;
	}

	/**
	 * Ajax call to get unavailable dates
	 *
	 * Adds array of blocked days to blocked
	 *
	 * @global object quitenicebooking
	 * @global object blocked
	 * @param int year
	 * @param int month
	 * @param int type or empty string
	 */
	function getBlockedDates(year, month, type, async=false) {
		if (typeof(Storage) !== "undefined") {
    		if (sessionStorage.getItem('SessionblockedDates') !== null) {
 				var SessionblockedDates = sessionStorage.getItem('SessionblockedDates');
 				console.log('parsed' + JSON.parse(SessionblockedDates));
 				jQuery.extend(true, blocked, JSON.parse(SessionblockedDates));
				jQuery('#bookingFormOverlay').removeClass('spinner');
				jQuery('.home-reservation-box').show();
				jQuery('.widget-reservation-box').show();
 				return;
			}
		} 
		jQuery.ajax({
			async: async, // wait for the function to finish before executing again
			type: 'GET',
			url: quitenicebooking.ajax_url,
			data: {action: 'quitenicebooking_ajax_calendar_availability', year: year, month: month, type: type},
			/*
			success: function(response) { // response is a json-encoded array of unavailable dates
				if (!blocked.hasOwnProperty('y'+year)) {
					blocked['y'+year] = {};
				}
				if (!blocked['y'+year].hasOwnProperty('m'+month)) {
					blocked['y'+year]['m'+month] = response;
				}
			},
			*/
			success: function(response) {
				jQuery.extend(true, blocked, response);
				jQuery('#bookingFormOverlay').removeClass('spinner');
				jQuery('.home-reservation-box').show();
				jQuery('.widget-reservation-box').show();
			if (typeof(Storage) !== "undefined") {
    			if (sessionStorage.getItem("SessionblockedDates") === null) {
 				sessionStorage.setItem('SessionblockedDates', JSON.stringify(response));
			}
		}
			},
			dataType: 'json'
		});
	}
	
	var today = new Date();
	getBlockedDates(today.getFullYear(), today.getMonth(), '', true);	

	//loadCalendarDates();

	/*setTimeout(function(){
		var today = new Date();
		getBlockedDates(today.getFullYear(), today.getMonth(), '');	
	}, 3000)$/

	
	/**
	 * Widget, step 1, step 2 - Form validation ===============================
	 */
	
	// event listener for form submission
	// validate all data here.  if JS is disabled, WP's form handler will have a fallback
	jQuery('.booking-form').on('submit', function() {
	
		// validate dates
		// check for default or no values
		if (jQuery('#datefrom').val() == quitenicebooking.input_checkin || jQuery('#dateto').val() == quitenicebooking.input_checkout || jQuery('#datefrom').val() == '' || jQuery('#dateto').val() == '') {
			alert(quitenicebooking.alert_select_dates);
			jQuery('#datefrom').effect('pulsate', { times:2 }, 800);
			jQuery('#dateto').effect('pulsate', { times:2 }, 800);
			return false;
		}
		// check whether dates are the same
		if (jQuery('#datefrom').val() == jQuery('#dateto').val()) {
			alert(quitenicebooking.alert_cannot_be_same_day);
			jQuery('#datefrom').effect('pulsate', { times:2 }, 800);
			jQuery('#dateto').effect('pulsate', { times:2 }, 800);
			return false;
		}
		
		var dateFrom = jQuery.datepicker.parseDate(quitenicebooking.date_format, jQuery('#datefrom').val());
		var dateTo = jQuery.datepicker.parseDate(quitenicebooking.date_format, jQuery('#dateto').val());
		
		if(quitenicebooking.saturday_only == 1) {
			if(dateFrom.getDay() != 6 || dateTo.getDay() != 6) {
				jQuery('#datefrom').effect('pulsate', { times:3 }, 800);
				jQuery('#dateto').effect('pulsate', { times:3 }, 800);
				alert(quitenicebooking.alert_day_not_saturday);
				return false;
			}			
		}
		// check whether checkout is before checkin
		if (dateTo < dateFrom) {
			jQuery('#datefrom').effect('pulsate', { times:3 }, 800);
			jQuery('#dateto').effect('pulsate', { times:3 }, 800);
			alert(quitenicebooking.alert_checkin_before_checkout);
			return false;
		}

		// check minimum stay requirement
		if ((dateTo - dateFrom) < quitenicebooking.minimum_stay * (1000 * 60 * 60 * 24)) {
			alert(quitenicebooking.alert_minimum_stay);
			return false;
		}

		// validate guests
		
		// each room must have at least 1 guest
		if (!jQuery('#room_qty').length) {
			// if on the front page form, or on step 2's "edit reservation" form, get the single n from room_n_adults
			var num_guests = jQuery('select[id$="_adults"]').first().get(0).attributes[0].nodeValue.match(/room_(\d)+_adults/)[1];
			// notes: get(0) gets the DOM of the element, attributes[0] gets the attribute, nodeValue gets the 'name', and match(...)[1] is the regex returning the captured match
		} else {
			var num_guests = jQuery('#room_qty').val();
		}
		
		for (var r = 1; r <= num_guests; r ++) {
			if (jQuery('#room_'+r+'_adults').val() + jQuery('#room_'+r+'_children').val() < 1) {
				alert(quitenicebooking.alert_at_least_1_guest+' '+r);
				jQuery('#room_'+r+'_adults').effect('pulsate', { times:2 }, 800);
				jQuery('#room_'+r+'_children').effect('pulsate', { times:2 }, 800);
				return false;
			}
		}
		
		// for step 1
		if (jQuery('#room_qty').length) {
			// entire booking must have at least 1 adult
			var totalAdults = 0;
			for (var r = 1; r <= jQuery('#room_qty').val(); r ++) {
				totalAdults += jQuery('#room_'+r+'_adults').val();
			}
			if (totalAdults < 1) {
				alert(quitenicebooking.alert_at_least_1_adult);
				for (var r = 1; r <= jQuery('#room_qty').val(); r ++) {
					jQuery('#room_'+r+'_adults').effect('pulsate', { times:2 }, 800);
				}
				return false;
			}
		}

	});
	
	// Booking Form Edit
	jQuery('.edit-reservation').on('click', function() {
		jQuery('.display-reservation').hide();
		jQuery('.display-reservation-edit').show();
		jQuery('.room-1').show();
		return false;
	});

});



