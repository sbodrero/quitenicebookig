<div id="validation_errors" class="error hidden"></div>

<div class="field-wrapper field-padding clearfix">
	<p class="description"><?php _e('A block will make the selected rooms unavailable for the defined date range', 'quitenicebooking'); ?></p>
</div>

<div class="field-wrapper field-padding-below clearfix">

	<div class="one-fifth empty-label"></div>

	<div class="one-fifth"><?php _e('From', 'quitenicebooking'); ?> (<?php echo $date_format_strings[$date_format]['display']; ?>)</div>

	<div class="one-fifth"><?php _e('To', 'quitenicebooking'); ?> (<?php echo $date_format_strings[$date_format]['display']; ?>)</div>

</div>

<div class="field-wrapper field-padding-below clearfix">

	<div class="one-fifth"><?php _e('Date range', 'quitenicebooking'); ?></div>

	<div class="one-fifth">
		<input type="text" name="quitenicebooking_booking_block_startdate" id="quitenicebooking_booking_block_startdate" value="<?php echo $quitenicebooking_booking_block_startdate; ?>" class="datepicker full-width">
	</div>

	<div class="one-fifth">
		<input type="text" name="quitenicebooking_booking_block_enddate" id="quitenicebooking_booking_block_enddate" value="<?php echo $quitenicebooking_booking_block_enddate; ?>"  class="datepicker full-width">
	</div>

</div>

<hr class="space1">

<div class="field-wrapper field-padding clearfix">

	<div class="one-fifth empty-label"></div>

	<div class="two-fifths">
		<a href="#" id="check_all"><?php _ex('Check All', 'checkboxes', 'quitenicebooking'); ?></a>&nbsp;
		<a href="#" id="uncheck_all"><?php _ex('Uncheck All', 'checkboxes', 'quitenicebooking'); ?></a>
	</div>

</div>

<div class="field-wrapper field-padding-below clearfix">

	<div class="one-fifth"><?php _e('Rooms', 'quitenicebooking'); ?></div>

	<div class="two-fifths">
		<?php foreach ($this->accommodations as $room) { ?>
			<?php ${'quitenicebooking_booking_block_id_'.$room['id']} = !empty(${'quitenicebooking_booking_block_id_'.$room['id']}) ? 1 : ''; ?>
			<div class="room-row clearfix">
				<input type="hidden" name="quitenicebooking_booking_block_id_<?php echo $room['id']; ?>" value="">
				<div class="check-wrapper"><input type="checkbox" name="quitenicebooking_booking_block_id_<?php echo $room['id']; ?>" id="quitenicebooking_booking_block_id_<?php echo $room['id']; ?>" value="1" <?php checked(${'quitenicebooking_booking_block_id_'.$room['id']}, 1); ?>></div><label for="quitenicebooking_booking_block_id_<?php echo $room['id']; ?>"><?php echo $room['title']; ?></label>
			</div>
		<?php } ?>
	</div>

</div>