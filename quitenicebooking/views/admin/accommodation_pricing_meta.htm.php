<div id="validation_errors" class="error hidden"></div>

<input type="hidden" name="quitenicebooking_update_accommodation" value="1">

<div class="field-wrapper field-padding clearfix">
	<div class="one-fifth empty-label"></div>
	
	<?php foreach ($quitenicebooking_prices->keys['prices'] as $key) { ?>
	<div class="one-fifth">
		<label><?php echo $key['description']; ?></label>
	</div>
	<?php } ?>
</div>


<?php foreach ($quitenicebooking_prices->entity_scheme->keys as $entity_key) { // rows ?>
<div id="<?php echo 'quitenicebooking_price_per'.$entity_key['meta_part']; ?>" class="field-wrapper field-padding-below clearfix">

	<div class="one-fifth"><?php echo $entity_key['description'].' ('.$this->settings['currency_unit'].')'; ?></div>
	
	<?php foreach ($quitenicebooking_prices->keys['prices'] as $price_key) { ?>
	<div class="one-fifth">
		<input type="text" name="<?php echo 'quitenicebooking_price_per_'.$entity_key['meta_part'].'_'.$price_key['meta_part']; ?>" id="<?php echo 'quitenicebooking_price_per_'.$entity_key['meta_part'].'_'.$price_key['meta_part']; ?>" value="<?php echo isset(${'quitenicebooking_price_per_'.$entity_key['meta_part'].'_'.$price_key['meta_part']}) ? Quitenicebooking_Utilities::float_to_user_price(${'quitenicebooking_price_per_'.$entity_key['meta_part'].'_'.$price_key['meta_part']}, $this->settings, FALSE) : ''; ?>" class="full-width validate_price">
	</div>
	<?php } ?>	

</div>
<?php } ?>

<hr class="space1" />
<div class="field-wrapper field-padding clearfix">
	<div class="one-fifth">
		<label for="quitenicebooking_room_surcharge"><?php _e('Surcharge', 'quitenicebooking'); ?> (<?php echo $this->settings['currency_unit']; ?>)</label>
	</div>
	<div class="two-fifths">
		<input type="text" name="quitenicebooking_surcharge" id="quitenicebooking_room_view" value="<?php echo isset($quitenicebooking_surcharge) ? Quitenicebooking_Utilities::float_to_user_price($quitenicebooking_surcharge, $this->settings, FALSE) : ''; ?>" class="full-width validate_optional_price">
	</div>
	<div class="two-fifths">
		<span class="description"><?php _e('Optional; if there are no additional booking costs for the room, leave this blank', 'quitenicebooking'); ?></span>
	</div>
</div>

<?php if (is_a($quitenicebooking_prices->entity_scheme, 'Quitenicebooking_Entity_Per_Person')) { ?>
<hr class="space1">
<div id="dynamic_price_rules">
	<?php for ($i = 1; $i <= $quitenicebooking_num_price_rules; $i ++) { ?>
<div class="price_rule" data-id="<?php echo $i; ?>">
	<h3><?php _e('Price rule', 'quitenicebooking'); ?> <?php echo $i; ?></h3>
	<div class="field-wrapper field-padding clearfix">
		<div class="one-fifth empty-label"></div>
		<div class="one-fifth"><label><?php _e('Weekdays (Mon-Thu)', 'quitenicebooking'); ?></label></div>
		<div class="one-fifth"><label><?php _e('Weekends (Fri-Sun)', 'quitenicebooking'); ?></label></div>
	</div>
	<div class="field-wrapper field-padding-below clearfix">
		<div class="one-fifth"><label class="additional_adult"><?php echo ($i == $quitenicebooking_num_price_rules) ? __('Per additional adult', 'quitenicebooking') : sprintf(__('For adult #%d', 'quitenicebooking'), $i + 1); echo ' ('.$this->settings['currency_unit'].')'; ?></label></div>
	<div class="one-fifth"><input type="text" class="full-width validate_price" name="quitenicebooking_price_rule_<?php echo $i; ?>_adult_weekday" value="<?php echo Quitenicebooking_Utilities::float_to_user_price(${"quitenicebooking_price_rule_{$i}_adult_weekday"}, $this->settings, FALSE); ?>"></div>
		<div class="one-fifth"><input type="text" class="full-width validate_price" name="quitenicebooking_price_rule_<?php echo $i; ?>_adult_weekend" value="<?php echo Quitenicebooking_Utilities::float_to_user_price(${"quitenicebooking_price_rule_{$i}_adult_weekend"}, $this->settings, FALSE); ?>"></div>
	</div>
	<div class="field-wrapper field-padding-below clearfix">
		<div class="one-fifth"><label class="additional_child"><?php echo ($i == $quitenicebooking_num_price_rules) ? __('Per additional child', 'quitenicebooking') : sprintf(__('For child #%d', 'quitenicebooking'), $i + 1); echo ' ('.$this->settings['currency_unit'].')'; ?></label></div>
		<div class="one-fifth"><input type="text" class="full-width validate_price" name="quitenicebooking_price_rule_<?php echo $i; ?>_child_weekday" value="<?php echo Quitenicebooking_Utilities::float_to_user_price(${"quitenicebooking_price_rule_{$i}_child_weekday"}, $this->settings, FALSE); ?>"></div>
		<div class="one-fifth"><input type="text" class="full-width validate_price" name="quitenicebooking_price_rule_<?php echo $i; ?>_child_weekend" value="<?php echo Quitenicebooking_Utilities::float_to_user_price(${"quitenicebooking_price_rule_{$i}_child_weekend"}, $this->settings, FALSE); ?>"></div>
	</div>
	<div class="field-wrapper field-padding-below clearfix">
		<button type="button" class="remove_price_rule button"><?php _e('Remove price rule', 'quitenicebooking'); ?></button>
	</div>
</div>
	<?php } ?>
</div>

<div class="field-padding-below">
	<button type="button" class="button-primary add_price_rule"><?php _e('Add price rule', 'quitenicebooking'); ?></button>
</div>
<div class="field-padding-below">
	<p class="description"><?php _e('Sets different prices for additional guests -- e.g. for the 2nd, 3rd, 4th guests'); ?></p>
</div>

<div id="price_rule_template" class="hidden">
	<h3><?php _e('Price rule', 'quitenicebooking'); ?> %price_rule_id%</h3>
	<div class="field-wrapper field-padding clearfix">
		<div class="one-fifth empty-label"></div>
		<div class="one-fifth"><label><?php _e('Weekdays (Mon-Thu)', 'quitenicebooking'); ?></label></div>
		<div class="one-fifth"><label><?php _e('Weekends (Fri-Sun)', 'quitenicebooking'); ?></label></div>
	</div>
	<div class="field-wrapper field-padding-below clearfix">
		<div class="one-fifth"><label class="additional_adult"><?php echo __('Per additional adult', 'quitenicebooking').' ('.$this->settings['currency_unit'].')'; ?></label></div>
		<div class="one-fifth"><input type="text" class="full-width validate_price" name="quitenicebooking_price_rule_%price_rule_id%_adult_weekday"></div>
		<div class="one-fifth"><input type="text" class="full-width validate_price" name="quitenicebooking_price_rule_%price_rule_id%_adult_weekend"></div>
	</div>
	<div class="field-wrapper field-padding-below clearfix">
		<div class="one-fifth"><label class="additional_child"><?php echo __('Per additional child', 'quitenicebooking').' ('.$this->settings['currency_unit'].')'; ?></label></div>
		<div class="one-fifth"><input type="text" class="full-width validate_price" name="quitenicebooking_price_rule_%price_rule_id%_child_weekday"></div>
		<div class="one-fifth"><input type="text" class="full-width validate_price" name="quitenicebooking_price_rule_%price_rule_id%_child_weekend"></div>
	</div>
	<div class="field-wrapper field-padding-below clearfix">
		<button type="button" class="remove_price_rule button"><?php _e('Remove price rule', 'quitenicebooking'); ?></button>
	</div>
</div>
<?php } // end if ?>

<div id="translations" class="hidden">
	<span class="t_for_adult_n"><?php _e('For adult #%d', 'quitenicebooking'); ?></span>
	<span class="t_for_child_n"><?php _e('For child #%d', 'quitenicebooking'); ?></span>
	<span class="t_additional_adult"><?php _e('Per additional adult', 'quitenicebooking'); ?></span>
	<span class="t_additional_child"><?php _e('Per additional child', 'quitenicebooking'); ?></span>
	<span class="t_validation_room_price"><?php _e('Please enter a valid room price (omit thousands separators and use %s as decimal point)', 'quitenicebooking'); ?></span>
	<span class="t_validation_filter_price"><?php _e('Please enter a valid price filter price (omit thousands separators and use %s as decimal point)', 'quitenicebooking'); ?></span>
	<span class="t_validation_filter_date_range"><?php _e('Please enter a valid price filter date range', 'quitenicebooking'); ?></span>
	<span class="t_validation_title"><?php _e('Please enter a title', 'quitenicebooking'); ?></span>
	<span class="t_post_changed"><?php _e('The changes you made will be lost if you navigate away from this page', 'quitenicebooking'); ?></span>
	<span class="t_price_rule"><?php _e('Price rule', 'quitenicebooking'); ?></span>
	<span class="t_filter"><?php _e('Filter', 'quitenicebooking'); ?></span>
</div>
