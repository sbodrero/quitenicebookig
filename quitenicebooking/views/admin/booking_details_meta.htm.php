<?php
$default_labels = array();
$default_labels['phone'] = __('Telephone Number', 'quitenicebooking');
$default_labels['address_1'] = __('Address Line 1', 'quitenicebooking');
$default_labels['address_2'] = __('Address Line 2', 'quitenicebooking');
$default_labels['city'] = __('City', 'quitenicebooking');
$default_labels['state'] = __('County/State/Province', 'quitenicebooking');
$default_labels['zip'] = __('ZIP/Postal Code', 'quitenicebooking');
$default_labels['country'] = __('Country', 'quitenicebooking');
$default_labels['special_requirements'] = __('Special Requirements', 'quitenicebooking');
?>
<div class="field-wrapper field-padding clearfix">
	<div class="one-whole">
		<?php _e('Booking ID', 'quitenicebooking'); ?>: <?php echo isset($quitenicebooking_booking_id) ? $quitenicebooking_booking_id : ''; ?>
		<input type="hidden" name="quitenicebooking_booking_id" id="quitenicebooking_booking_id" value="<?php echo isset($quitenicebooking_booking_id) ? $quitenicebooking_booking_id : ''; ?>" readonly class="full-width"> 
	</div>
</div>
<hr class="space1" />
<div class="field-wrapper field-padding clearfix">
	<div class="one-half">
		<label for="quitenicebooking_guest_first_name"><?php _e('First Name', 'quitenicebooking'); ?> *</label>
		<input type="text" name="quitenicebooking_guest_first_name" id="quitenicebooking_guest_first_name" value="<?php echo isset($quitenicebooking_guest_first_name) ? htmlspecialchars($quitenicebooking_guest_first_name) : ''; ?>" class="full-width">
	</div>
	<div class="one-half">
		<label for="quitenicebooking_guest_last_name"><?php _e('Last Name', 'quitenicebooking'); ?> *</label>
		<input type="text" name="quitenicebooking_guest_last_name" id="quitenicebooking_guest_last_name" value="<?php echo isset($quitenicebooking_guest_last_name) ? htmlspecialchars($quitenicebooking_guest_last_name) : ''; ?>" class="full-width">
	</div>
</div>

<div class="field-wrapper field-padding-below clearfix">
	<div class="one-whole">
		<label for="quitenicebooking_guest_email"><?php _e('Email Address', 'quitenicebooking'); ?> *</label>
		<input type="text" name="quitenicebooking_guest_email" id="quitenicebooking_guest_email" value="<?php echo isset($quitenicebooking_guest_email) ? htmlspecialchars($quitenicebooking_guest_email) : ''; ?>" class="full-width">
	</div>
</div>

<?php if (empty($quitenicebooking_guest_details)) { // new booking; use form fields from settings ?>
<?php $field_format = array(); ?>
<?php foreach ($form_fields as $field) { ?>
<?php if (in_array($field['type'], array('guest_first_name', 'guest_last_name', 'guest_email'))) { continue; } ?>
<div class="field-wrapper field-padding-below clearfix">
	<div class="one-whole">
		<label for="quitenicebooking_guest_details_<?php echo $field['id']; ?>"><?php echo empty($default_labels[$field['id']]) ? $field['label'] : $default_labels[$field['id']]; ?></label>
		<?php if ($field['type'] == 'textarea') { ?>
		<textarea class="full-width" name="quitenicebooking_guest_details_<?php echo $field['id']; ?>" id="quitenicebooking_guest_details_<?php echo $field['id']; ?>"></textarea>
		<?php } else { ?>
		<input type="text" class="full-width" name="quitenicebooking_guest_details_<?php echo $field['id']; ?>" id="quitenicebooking_guest_details_<?php echo $field['id']; ?>">
		<?php }  ?>
	</div>
</div>
<?php $field_format[$field['id']]['label'] = $field['label']; ?>
<?php $field_format[$field['id']]['type'] = $field['type']; ?>
<?php } // end foreach ?>
<input type="hidden" name="quitenicebooking_guest_fields" value="<?php echo base64_encode(json_encode($field_format)); ?>">
<?php } else { // existing booking, use form fields stored in meta ?>
<?php $form_fields = json_decode($quitenicebooking_guest_details, TRUE); ?>
<?php $field_format = array(); ?>
<?php foreach ($form_fields as $key => $field) { ?>
<div class="field-wrapper field-padding-below clearfix">
	<div class="one-whole">
		<label for="quitenicebooking_guest_details_<?php echo $key; ?>"><?php echo empty($default_labels[$key]) ? $field['label'] : $default_labels[$key]; ?></label>
		<?php if ($field['type'] == 'textarea') { ?>
		<textarea class="full-width" name="quitenicebooking_guest_details_<?php echo $key; ?>" id="quitenicebooking_guest_details_<?php echo $key; ?>"><?php echo $field['value']; ?></textarea>
		<?php } else { ?>
		<input type="text" class="full-width" name="quitenicebooking_guest_details_<?php echo $key; ?>" id="quitenicebooking_guest_details_<?php echo $key; ?>" value="<?php echo htmlspecialchars($field['value']); ?>">
		<?php } ?>
	</div>
</div>
<?php $field_format[$key]['label'] = $field['label']; ?>
<?php $field_format[$key]['type'] = $field['type']; ?>
<?php } // end foreach ?>
<input type="hidden" name="quitenicebooking_guest_fields" value="<?php echo base64_encode(json_encode($field_format)); ?>">
<?php } // end else ?>

<div class="field-wrapper field-padding-below clearfix">
	<div class="one-whole">
		<label for="services"><?php _e('Services Requested', 'quitenicebooking'); ?></label>
		<input type="text" name="services" id="services" value="<?php echo implode(', ', array_keys($services)); ?>" class="full-width">
	</div>
</div>
<input type="hidden" name="service_json" value="<?php echo base64_encode($quitenicebooking_services); ?>">
