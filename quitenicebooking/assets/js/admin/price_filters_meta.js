// get the existing number of price filters
var price_filter_num = parseInt(quitenicebooking_premium.num_price_filters) + 1;
// an array containing the number of price rules per filter
var price_filter_price_rules = {};
if (quitenicebooking_premium.num_price_filter_price_rules != null) {
	price_filter_price_rules = quitenicebooking_premium.num_price_filter_price_rules;
}
for (var i in price_filter_price_rules) {
	price_filter_price_rules[i] ++;
}

(function($) {
$(document).ready(function() {
	
	/**
	 * Add datepicker to date fields (future)
	 */
	$(document).on('focusin', 'input[name$="startdate"]', function() {
		$(this).datepicker({
			minDate: 0,
			firstDay: 1,
			dateFormat: quitenicebooking.js_date_format
		});
	});
	
	$(document).on('focusin', 'input[name$="enddate"]', function() {
		$(this).datepicker({
			minDate: calc_minDate($(this)),
			firstDay: 1,
			dateFormat: quitenicebooking.js_date_format
		});
	});

	// set enddate mindate when startdate changed
	$(document).on('change', 'input[name$="startdate"]', function() {
		if ($(this).parent().parent().find('input[name$="enddate"]').hasClass('hasDatepicker')) {
			// datepicker already instantiated, change option
			jQuery(this).parent().parent().find('input[name$="enddate"]').datepicker('option', 'minDate', calc_minDate(jQuery(this)));
		} else {
			// new datepicker instance
			jQuery(this).parent().parent().find('input[name$="enddate"]').datepicker({minDate: calc_minDate(jQuery(this)), firstDay: 1, dateFormat: quitenicebooking.js_date_format});
			if (jQuery.datepicker.parseDate(quitenicebooking.js_date_format, jQuery(this).parent().parent().find('input[name$="enddate"]').val()) <= calc_minDate(jQuery(this))) {
				jQuery(this).parent().parent().find('input[name$="enddate"]').val(jQuery.datepicker.formatDate(quitenicebooking.js_date_format, calc_minDate(jQuery(this))));
			}
		}
	});
	
	/**
	 * Make the datepicker fields read-only
	 */
	$('.datepicker').attr('readonly', true);

	/**
	 * Calculate enddate minDate
	 * @param t jQuery(this
	 */
	function calc_minDate(t) {
		if (t.parent().parent().find('input[name$="startdate"]').val().length > 0) {
			var d = new Date($.datepicker.parseDate(quitenicebooking.js_date_format, t.parent().parent().find('input[name$="startdate"]').val()));
			d.setDate(d.getDate() + 1);
			return d;
		}
		return 0;
	}

	/**
	 * Make price filter divs sortable
	 * Reorder the price filter names and ids after sorting
	 */
	$('#dynamic_add_price_filter').sortable({
		stop: function(e) {
			var num_filters = $('.price_filter').length;
			for (var i = 0; i < num_filters; i ++) {
				$('.price_filter').eq(i).children('h3').html($('#translations .t_filter').html()+' '+(i+1));
				$('.price_filter').eq(i).find('input').each(function() {
					$(this).attr('name', function(index, value) {
						return value.replace(/price_filter_\d+/, 'price_filter_'+(i+1));
					});
				});
			}
		}
	});
	
	/**
	 * Add price filter button
	 */
	$('button#add_price_filter').on('click', function(e) {
		e.preventDefault();
		var price_filter_html = $('#price_filter_template').html().replace(/%price_filter_num%/g, price_filter_num);
		$('div#dynamic_add_price_filter').append('<div class="price_filter" id="price_filter_'+price_filter_num+'">'+price_filter_html+'</div>');
		$('.datepicker').attr('readonly', true);

		if (price_filter_price_rules[price_filter_num] == null) {
			price_filter_price_rules[price_filter_num] = 1;
		}

		price_filter_num ++;
	});
	
	/**
	 * Remove price filter button
	 */
	$(document).on('click', 'button.remove_price_filter', function(e) {
		e.preventDefault();
		var filter_id = $(this).parents('.price_filter').attr('id').match(/^price_filter_(\d+)$/);
		filter_id = filter_id[1];
		delete price_filter_price_rules[filter_id];
		// reorder objects
		var c = 0;
		for (var k in price_filter_price_rules) {
			c ++;
		}
		if (c > 0) {
			var new_price_filter_price_rules = {};
			for (var i in price_filter_price_rules) {
				if (parseInt(i) > filter_id) {
					new_price_filter_price_rules[parseInt(i) - 1] = price_filter_price_rules[i];
				} else {
					new_price_filter_price_rules[parseInt(i)] = price_filter_price_rules[i];
				}
			}
			price_filter_price_rules = new_price_filter_price_rules;
		} else {
			price_filter_price_rules = {};
		}

		$(this).parent().parent().remove();
		price_filter_num --;
		// reorder existing filters
		var count = 1;
		var price_filters = $('div.price_filter');
		for (var i = 0; i < price_filters.length; i ++) {
			$('div.price_filter').eq(i).children('h3').html($('#translations .t_filter').html()+' '+count);
			$('div.price_filter').eq(i).find('input').each( function() {
				$(this).attr('name', function(index, value) {
					return value.replace(/price_filter_\d+/, 'price_filter_'+count);
				});
			});
			count ++;
		}
	});
	
	/**
	 * Form submission and validation
	 */
	$('form').on('submit', function(e) {
		// get the total number of filters defined
		var num_filters = $('.price_filters').length;
		
		// create a new validationerrors array if it does not already exist
		if (typeof validationErrors == 'undefined') {
			var validationErrors = new Array();
		}
		
		// define error message booleans
		var invalidPrice = false;
		var invalidDateRange = false;
		
		// iterate through each price filter and validate their fields
		$('.price_filter').each(function() {
			var startdate = Date.parse($.datepicker.parseDate(quitenicebooking.js_date_format, $(this).find('input[name$=startdate]').val()));
			var enddate =  Date.parse($.datepicker.parseDate(quitenicebooking.js_date_format, $(this).find('input[name$=enddate]').val()));
			if (isNaN(startdate) || isNaN(enddate) || (startdate >= enddate)) {
				invalidDateRange = true;
			}

			var unicode_decimal_sep = (quitenicebooking.currency_decimal_separator.charCodeAt(0)).toString(16);
			unicode_decimal_sep = ('000'+unicode_decimal_sep).slice(-4);
			
			$(this).find('input').not('[name$=startdate],[name$=enddate]').each(function() {
				if ($(this).val().match('^\\d+\\u'+unicode_decimal_sep+'?\\d*$') == null) {
					invalidPrice = true;
				}
			});

		});
		// push error messages only once
		if (invalidPrice == true) {
			validationErrors.push($('#translations .t_validation_filter_price').html());
		}
		if (invalidDateRange == true) {
			validationErrors.push($('#translations .t_validation_filter_date_range').html());
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

	/**
	 * Add price rule button
	 */
	$(document).on('click', '.add_price_filter_price_rule', function(e) {
		e.preventDefault();

		// get the price filter id
		var price_filter_id = $(this).parents('.price_filter').attr('id').match(/^price_filter_(\d+)$/);
		price_filter_id = price_filter_id[1];

		// get the price rule id
		var price_rule_id = price_filter_price_rules[price_filter_id];

		// get the html and replace the names
		var html = $('#price_filter_price_rule_template').html().replace(/%price_filter_id%/g, price_filter_id).replace(/%price_rule_id%/g, price_rule_id);

		// append html
		$(this).parents('.price_filter').children('.dynamic_price_filter_price_rules').append('<div class="price_filter_price_rule" data-id="'+price_rule_id+'">'+html+'</div>');

		// increment price rule id
		price_filter_price_rules[price_filter_id] ++;
		price_filters_rename_price_rule_labels();
	});

	/**
	 * Remove price rule button
	 */
	$(document).on('click', '.remove_price_filter_price_rule', function(e) {
		e.preventDefault();

		var price_filter_parent = $(this).parents('.price_filter');
		var price_filter_id = price_filter_parent.attr('id').match(/^price_filter_(\d+)$/);
		price_filter_id = price_filter_id[1];
		
		$(this).parents('.price_filter_price_rule').remove();

		price_filter_price_rules[price_filter_id] --;

		// reorder existing rules
		var count = 1;
		for (var i = 0; i < price_filter_price_rules[price_filter_id] - 1; i ++) {
			price_filter_parent.find('.price_filter_price_rule').eq(i).attr('data-id', count);
			price_filter_parent.find('.price_filter_price_rule').eq(i).find('h3').html($('#translations .t_filter').html()+' '+price_filter_id+' &gt; '+$('#translations .t_price_rule').html()+' '+count);
			price_filter_parent.find('.price_filter_price_rule').eq(i).find('input').each(function() {
				$(this).attr('name', function(index, value) {
					return value.replace(/price_filter_\d+/, 'price_filter_'+price_filter_id).replace(/price_rule_\d+/, 'price_rule_'+count);
				});
			});
			count ++;
		}
		price_filters_rename_price_rule_labels();

	});

	/**
	 * Rename price rule labels
	 */
	function price_filters_rename_price_rule_labels() {
		$('.price_filter_price_rule').each(function() {
			var filter_id = $(this).parents('.price_filter').attr('id').match(/^price_filter_(\d+)$/);
			filter_id = filter_id[1];
			var rule_id = $(this).attr('data-id');
			var rule_num = price_filter_price_rules[filter_id];
			
			if (rule_id < (rule_num - 1)) {
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