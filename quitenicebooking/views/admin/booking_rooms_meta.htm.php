<div id="validation_errors" class="error hidden"></div>

<?php for ($i = 1; $i <= $quitenicebooking_num_rooms; $i ++) { ?>
<div class="room">
	<h3><?php printf(__('Room %d', 'quitenicebooking'), $i); ?></h3>
	<div class="field-wrapper field-padding clearfix">
		<div class="one-half">
			<label for="quitenicebooking_room_booking_<?php echo $i; ?>_checkin"><?php _e('Check in date', 'quitenicebooking'); ?> (<?php echo $this->settings['date_format_strings'][$this->settings['date_format']]['display'];?>)</label>
			<input type="text" name="quitenicebooking_room_booking_<?php echo $i; ?>_checkin" id="quitenicebooking_room_booking_<?php echo $i; ?>_checkin" value="<?php echo isset(${'quitenicebooking_room_booking_'.$i.'_checkin'}) ? ${'quitenicebooking_room_booking_'.$i.'_checkin'} : ''; ?>" class="full-width datepicker" disabled="">
		</div>
		<div class="one-half">
			<label for="quitenicebooking_room_booking_<?php echo $i; ?>_checkout"><?php _e('Check out date', 'quitenicebooking'); ?> (<?php echo $this->settings['date_format_strings'][$this->settings['date_format']]['display'];?>)</label>
			<input type="text" name="quitenicebooking_room_booking_<?php echo $i; ?>_checkout" id="quitenicebooking_room_booking_<?php echo $i; ?>_checkout" value="<?php echo isset(${'quitenicebooking_room_booking_'.$i.'_checkout'}) ? ${'quitenicebooking_room_booking_'.$i.'_checkout'} : ''; ?>" class="full-width datepicker" disabled="">
		</div>
	</div>
	<div class="field-wrapper field-padding-below clearfix">
		<div class="one-half">
			<label for="quitenicebooking_room_booking_<?php echo $i; ?>_type"><?php _e('Room type', 'quitenicebooking'); ?></label>
			<select name="quitenicebooking_room_booking_<?php echo $i; ?>_type" id="quitenicebooking_room_booking_<?php echo $i; ?>_type" class="full-width" disabled="">
				<?php foreach ($quitenicebooking_all_rooms as $room) { ?>
					<option value="<?php echo $room['id']; ?>" <?php selected(${'quitenicebooking_room_booking_'.$i.'_type'}, $room['id']); ?> <?php if (isset($room['translated_ids']) && count($room['translated_ids']) > 0) { foreach ($room['translated_ids'] as $tid) { selected(${'quitenicebooking_room_booking_'.$i.'_type'}, $tid); } } ?>><?php echo $room['title']; ?></option>
				<?php } ?>
			</select>
		</div>
		<div class="one-half">
			<label for="quitenicebooking_room_booking_<?php echo $i; ?>_bed"><?php _e('Bed type', 'quitenicebooking'); ?></label>
			<select name="quitenicebooking_room_booking_<?php echo $i; ?>_bed" id="quitenicebooking_room_booking_<?php echo $i; ?>_bed" class="full-width" disabled="">
				<?php foreach ($quitenicebooking_beds->keys['beds'] as $bed => $defs) { ?>
					<?php if ( $quitenicebooking_all_rooms[${'quitenicebooking_room_booking_'.$i.'_type'}][$defs['meta_key']] == 1 ) { ?>
					<option value="<?php echo $bed; ?>" <?php selected(${'quitenicebooking_room_booking_'.$i.'_bed'}, $bed); ?>><?php echo $defs['description']; ?></option>
					<?php } ?>
				<?php } ?>
					<option value="0" <?php selected(${'quitenicebooking_room_booking_'.$i.'_bed'}, 0); ?>><?php echo $quitenicebooking_beds->keys['disabled']['description']; ?></option>
			</select>
		</div>
	</div>
	<div class="field-wrapper field-padding-below clearfix">
		<div class="one-half">
			<label for="quitenicebooking_room_booking_<?php echo $i; ?>_adults"><?php _e('Adults', 'quitenicebooking'); ?></label>
			<select name="quitenicebooking_room_booking_<?php echo $i; ?>_adults" id="quitenicebooking_room_booking_<?php echo $i; ?>_adults" class="full-width" disabled="">
				<?php for ($r = 0; $r <= 50; $r ++) { ?>
					<option value="<?php echo $r; ?>" <?php selected(${'quitenicebooking_room_booking_'.$i.'_adults'}, $r); ?>><?php echo $r; ?></option>
				<?php } ?>
			</select>
		</div>
		<div class="one-half">
			<label for="quitenicebooking_room_booking_<?php echo $i; ?>_children"><?php _e('Children', 'quitenicebooking'); ?></label>
			<select name="quitenicebooking_room_booking_<?php echo $i; ?>_children" id="quitenicebooking_room_booking_<?php echo $i; ?>_children" class="full-width" disabled="">
				<?php for ($r = 0; $r <= 50; $r ++) { ?>
					<option value="<?php echo $r; ?>" <?php selected(${'quitenicebooking_room_booking_'.$i.'_children'}, $r); ?>><?php echo $r; ?></option>
				<?php } ?>
			</select>
		</div>	
	</div>
	<div class="field-wrapper field-padding-below clearfix">
		<button class="edit_room button"><?php _e('Edit Room', 'quitenicebooking'); ?></button>
		<button type="button" class="remove_room button"><?php _e('Remove Room', 'quitenicebooking'); ?></button>
		<span class="ajax_messages"></span>
	</div>
</div>
<?php } ?>

<div id="dynamic_add_room"></div>
<div class="field-padding">
	<button type="button" id="add_room" class="button-primary"><?php _e('Add Room', 'quitenicebooking'); ?></button>
</div>

<div id="room_template" class="hidden">
	<h3><?php _e('Room', 'quitenicebooking'); ?> -room_num-</h3>
	<div class="field-wrapper field-padding clearfix">
		<div class="one-half">
			<label for="quitenicebooking_room_booking_-room_num-_checkin"><?php _e('Check in date', 'quitenicebooking'); ?> (<?php echo $this->settings['date_format_strings'][$this->settings['date_format']]['display'];?>)</label>
			<input type="text" name="quitenicebooking_room_booking_-room_num-_checkin" id="quitenicebooking_room_booking_-room_num-_checkin" class="full-width datepicker">
		</div>
		<div class="one-half">
			<label for="quitenicebooking_room_booking_-room_num-_checkout"><?php _e('Check out date', 'quitenicebooking'); ?> (<?php echo $this->settings['date_format_strings'][$this->settings['date_format']]['display'];?>)</label>
			<input type="text" name="quitenicebooking_room_booking_-room_num-_checkout" id="quitenicebooking_room_booking_-room_num-_checkout" class="full-width datepicker">
		</div>
	</div>
	<div class="field-wrapper field-padding-below clearfix">
		<div class="one-half">
			<label for="quitenicebooking_room_booking_-room_num-_type"><?php _e('Room type', 'quitenicebooking'); ?></label>
			<select name="quitenicebooking_room_booking_-room_num-_type" id="quitenicebooking_room_booking_-room_num-_type" class="full-width">
				<?php foreach ($quitenicebooking_all_rooms as $room) { ?>
					<option value="<?php echo $room['id']; ?>"><?php echo $room['title']; ?></option>
				<?php } ?>
			</select>
		</div>
		<div class="one-half">
			<label for="quitenicebooking_room_booking_-room_num-_bed"><?php _e('Bed type', 'quitenicebooking'); ?></label>
			<select name="quitenicebooking_room_booking_-room_num-_bed" id="quitenicebooking_room_booking_-room_num-_bed" class="full-width"><!-- bedstr --></select>
		</div>
		</div>
	<div class="field-wrapper field-padding-below clearfix">
		<div class="one-half">
			<label for="quitenicebooking_room_booking_-room_num-_adults"><?php _e('Adults', 'quitenicebooking'); ?></label>
			<select name="quitenicebooking_room_booking_-room_num-_adults" id="quitenicebooking_room_booking_-room_num-_adults" class="full-width">
				<?php for ($r = 0; $r <= 50; $r ++) { ?>
					<option value="<?php echo $r; ?>"><?php echo $r; ?></option>
				<?php } ?>
			</select>
		</div>
		<div class="one-half">
			<label for="quitenicebooking_room_booking_-room_num-_children"><?php _e('Children', 'quitenicebooking'); ?></label>
			<select name="quitenicebooking_room_booking_-room_num-_children" id="quitenicebooking_room_booking_-room_num-_children" class="full-width">
				<?php for ($r = 0; $r <= 50; $r ++) { ?>
					<option value="<?php echo $r; ?>"><?php echo $r; ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
	<div class="field-wrapper field-padding-below clearfix">
		<button type="button" class="check_availability button"><?php _e('Check Availability', 'quitenicebooking'); ?></button>
		<button type="button" class="remove_room button"><?php _e('Remove Room', 'quitenicebooking'); ?></button>
		<span class="ajax_messages"></span>
	</div>
</div>

<div id="translations" class="hidden">
	<span class="t_check_availability"><?php _e('Check Availability', 'quitenicebooking'); ?></span>
	<span class="t_edit_room"><?php _e('Edit Room', 'quitenicebooking'); ?></span>
	<span class="t_fully_booked"><?php _e('Room unavailable - fully booked', 'quitenicebooking'); ?></span>
	<span class="t_too_many_guests"><?php _e('Room unavailable - too many guests', 'quitenicebooking'); ?></span>
	<span class="t_dates_blocked"><?php _e('Room unavailable - dates blocked', 'quitenicebooking'); ?></span>
	<span class="t_fill_in_dates"><?php _e('Please fill in check in/check out dates', 'quitenicebooking'); ?></span>
	<span class="t_room_added"><?php _e('Room added', 'quitenicebooking'); ?></span>
	<span class="t_checking_availability"><?php _e('Checking availability, please wait...', 'quitenicebooking'); ?></span>
	<span class="t_checkout_after"><?php _e('Check out date must be after check in date', 'quitenicebooking'); ?></span>
	<span class="t_room"><?php _e('Room', 'quitenicebooking'); ?></span>

	<span class="t_validation_required_guest"><?php _e('Please enter required guest information', 'quitenicebooking'); ?></span>
	<span class="t_validation_guest_email"><?php _e('Please enter a valid guest email address', 'quitenicebooking'); ?></span>
	<span class="t_validation_deposit_amount"><?php _e('Please enter a valid deposit amount', 'quitenicebooking'); ?></span>
	<span class="t_validation_total_price"><?php _e('Please enter a valid total price', 'quitenicebooking'); ?></span>
	<span class="t_validation_add_room"><?php _e('Please add a room to this booking', 'quitenicebooking'); ?></span>
	<span class="t_validation_room_info"><?php _e('Please enter all information for room %s', 'quitenicebooking'); ?></span>
	<span class="t_validation_room_guests"><?php _e('Number of guests in room %s must be 0 or higher', 'quitenicebooking'); ?></span>
	<span class="t_post_changed"><?php _e('The changes you made will be lost if you navigate away from this page', 'quitenicebooking'); ?></span>
</div>
