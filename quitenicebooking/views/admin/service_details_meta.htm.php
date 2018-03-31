<div id="validation_errors" class="error hidden"></div>

<div class="field-wrapper field-padding clearfix">
	<p class="description"><?php _e('Services (e.g. extra beds, airport pickup, etc.) will be displayed on booking step 3 for the selected rooms.  Use the title field for the service name; the content area is for your own memo.', 'quitenicebooking'); ?></p>
</div>

<hr class="space1">

<div class="field-wrapper field-padding clearfix">
	<div class="one-fifth">
		<label for="quitenicebooking_service_price"><?php _e('Price', 'quitenicebooking'); ?> (<?php echo $this->settings['currency_unit']; ?>)</label>
	</div>
	<div class="two-fifths">
		<input type="text" name="quitenicebooking_service_price" id="quitenicebooking_service_price" value="<?php echo isset($quitenicebooking_service_price) ? Quitenicebooking_Utilities::float_to_user_price($quitenicebooking_service_price, $this->settings) : ''; ?>" class="full-width">
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
			<?php ${'quitenicebooking_service_id_'.$room['id']} = !empty(${'quitenicebooking_service_id_'.$room['id']}) ? 1 : 0; ?>
			<div class="room-row clearfix">
				<input type="hidden" name="quitenicebooking_service_id_<?php echo $room['id']; ?>" value="0">
				<div class="check-wrapper"><input type="checkbox" name="quitenicebooking_service_id_<?php echo $room['id']; ?>" id="quitenicebooking_service_id_<?php echo $room['id']; ?>" value="1" <?php checked(${'quitenicebooking_service_id_'.$room['id']}, 1); ?>></div><label for="quitenicebooking_service_id_<?php echo $room['id']; ?>"><?php echo $room['title']; ?></label>
			</div>
		<?php } ?>
	</div>

</div>
