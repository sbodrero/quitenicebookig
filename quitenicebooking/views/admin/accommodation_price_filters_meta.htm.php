<div class="field-wrapper field-padding-above clearfix">
	<p class="description"><?php _e('Price filters will be applied from top to bottom (prices below will override the ones above if their date ranges overlap). Drag the price filter boxes to rearrange them', 'quitenicebooking'); ?></p>
</div>
<div id="dynamic_add_price_filter">
<?php for ($i = 1; $i <= $quitenicebooking_num_price_filters; $i ++) {  ?>
<div class="price_filter" id="<?php echo "price_filter_{$i}"; ?>">
<h3><?php printf(__('Filter %d', 'quitenicebooking'), $i); ?></h3>
<div class="field-wrapper field-padding clearfix">

	<div class="one-fifth empty-label"></div>

	<div class="one-fifth">
		<label><?php _e('From', 'quitenicebooking'); ?> (<?php echo $this->settings['date_format_strings'][$this->settings['date_format']]['display']; ?>)</label>
	</div>

	<div class="one-fifth">
		<label><?php _e('To', 'quitenicebooking'); ?> (<?php echo $this->settings['date_format_strings'][$this->settings['date_format']]['display']; ?>)</label>
	</div>

</div>
<div class="field-wrapper field-padding-below clearfix">

	<div class="one-fifth">
		<label><?php _e('Date range', 'quitenicebooking'); ?></label>
	</div>

	<div class="one-fifth">
		<input type="text" name="<?php echo "quitenicebooking_price_filter_{$i}_startdate"; ?>" id="<?php echo "quitenicebooking_price_filter_{$i}_startdate"; ?>" value="<?php echo ${"quitenicebooking_price_filter_{$i}_startdate"}; ?>" class="full-width datepicker">
	</div>

	<div class="one-fifth">
		<input type="text" name="<?php echo "quitenicebooking_price_filter_{$i}_enddate"; ?>" id="<?php echo "quitenicebooking_price_filter_{$i}_enddate"; ?>" value="<?php echo ${"quitenicebooking_price_filter_{$i}_enddate"}; ?>" class="full-width datepicker">
	</div>

</div>
<hr class="space1" />

<div class="field-wrapper field-padding clearfix">

	<div class="one-fifth empty-label"></div>

	<?php foreach ($quitenicebooking_prices->keys['prices'] as $key) { ?>
	<div class="one-fifth">
		<label><?php echo $key['description']; ?></label>
	</div>
	<?php } ?>
	
</div>

<?php foreach ($quitenicebooking_prices->entity_scheme->keys as $entity_key) { // rows ?>
<div class="field-wrapper field-padding-below clearfix">

	<div class="one-fifth"><?php echo $entity_key['description'].' ('.$quitenicebooking_currency_unit.')'; ?></div>

	<?php foreach ($quitenicebooking_prices->keys['prices'] as $price_key) { // columns ?>
	<div class="one-fifth">
		<input type="text" name="<?php echo 'quitenicebooking_price_filter_'.$i.'_'.$entity_key['meta_part'].'_'.$price_key['meta_part']; ?>" id="<?php echo 'quitenicebooking_price_filter_'.$i.'_'.$entity_key['meta_part'].'_'.$price_key['meta_part']; ?>" value="<?php echo isset(${'quitenicebooking_price_filter_'.$i.'_'.$entity_key['meta_part'].'_'.$price_key['meta_part']}) ? Quitenicebooking_Utilities::float_to_user_price(${'quitenicebooking_price_filter_'.$i.'_'.$entity_key['meta_part'].'_'.$price_key['meta_part']}, $this->settings, FALSE) : ''; ?>" class="full-width">
		
	</div>
	<?php } ?>

</div>
<?php } ?>
<hr class="space1">

<?php if (is_a($quitenicebooking_prices->entity_scheme, 'Quitenicebooking_Entity_Per_Person')) { ?>
	<div class="dynamic_price_filter_price_rules" data-id="<?php echo $i; ?>">
		<?php for ($r = 1; $r <= $quitenicebooking_num_price_filter_rules[$i]; $r ++) { ?>

			<div class="price_filter_price_rule" data-id="<?php echo $r; ?>">
				<h3><?php _e('Filter', 'quitenicebooking'); ?> <?php echo $i; ?> &gt; <?php _e('Price rule', 'quitenicebooking'); ?> <?php echo $r; ?></h3>
				<div class="field-wrapper field-padding clearfix">
					<div class="one-fifth empty-label"></div>
					<div class="one-fifth"><label><?php _e('Weekdays (Mon-Thu)', 'quitenicebooking'); ?></label></div>
					<div class="one-fifth"><label><?php _e('Weekends (Fri-Sun)', 'quitenicebooking'); ?></label></div>
				</div>
				<div class="field-wrapper field-padding-below clearfix">
					<div class="one-fifth"><label class="additional_adult"><?php echo ($r == $quitenicebooking_num_price_filter_rules[$i]) ? __('Per additional adult', 'quitenicebooking') : sprintf(__('For adult #%d', 'quitenicebooking'), $r + 1); echo ' ('.$this->settings['currency_unit'].')'; ?></label></div>
					<div class="one-fifth"><input type="text" class="full-width" name="quitenicebooking_price_filter_<?php echo $i; ?>_price_rule_<?php echo $r; ?>_adult_weekday" value="<?php echo ${"quitenicebooking_price_filter_{$i}_price_rule_{$r}_adult_weekday"}; ?>"></div>
					<div class="one-fifth"><input type="text" class="full-width" name="quitenicebooking_price_filter_<?php echo $i; ?>_price_rule_<?php echo $r; ?>_adult_weekend" value="<?php echo ${"quitenicebooking_price_filter_{$i}_price_rule_{$r}_adult_weekend"}; ?>"></div>
				</div>
				<div class="field-wrapper field-padding-below clearfix">
					<div class="one-fifth"><label class="additional_child"><?php echo ($r == $quitenicebooking_num_price_filter_rules[$i]) ? __('Per additional child', 'quitenicebooking') : sprintf(__('For child #%d', 'quitenicebooking'), $r + 1); echo ' ('.$this->settings['currency_unit'].')'; ?></label></div>
					<div class="one-fifth"><input type="text" class="full-width" name="quitenicebooking_price_filter_<?php echo $i; ?>_price_rule_<?php echo $r; ?>_child_weekday" value="<?php echo ${"quitenicebooking_price_filter_{$i}_price_rule_{$r}_child_weekday"}; ?>"></div>
					<div class="one-fifth"><input type="text" class="full-width" name="quitenicebooking_price_filter_<?php echo $i; ?>_price_rule_<?php echo $r; ?>_child_weekend" value="<?php echo ${"quitenicebooking_price_filter_{$i}_price_rule_{$r}_child_weekend"}; ?>"></div>
				</div>
				<div class="field-wrapper field-padding-below clearfix">
					<button type="button" class="remove_price_filter_price_rule button"><?php _e('Remove price rule', 'quitenicebooking'); ?></button>
				</div>
			</div>

		<?php } ?>
	</div>
	<div class="field-padding-below">
		<button type="button" class="button-primary add_price_filter_price_rule"><?php _e('Add price rule', 'quitenicebooking'); ?></button>
	</div>
	<div class="field-padding-below">
		<p class="description"><?php _e('Sets different prices for additional guests -- e.g. for the 2nd, 3rd, 4th guests'); ?></p>
	</div>
	<hr class="space1">
<?php } ?>

<div class="field-wrapper field-padding clearfix">
	<button type="button" class="remove_price_filter button"><?php _e('Remove filter', 'quitenicebooking'); ?></button>
</div>
</div>
<?php } // end for ?>
</div><!-- #dynamic_add_price_filter -->
<div class="field-padding">
	<button type="button" id="add_price_filter" class="button-primary"><?php _e('Add Filter', 'quitenicebooking'); ?></button>
</div>

<div id="price_filter_template" class="hidden">
	<h3><?php _e('Filter', 'quitenicebooking'); ?> %price_filter_num%</h3>
	<div class="field-wrapper field-padding clearfix">
		<div class="one-fifth empty-label"></div>
		<div class="one-fifth">
			<label>From (<?php echo $this->settings['date_format_strings'][$this->settings['date_format']]['display']; ?>)</label>
		</div>
		<div class="one-fifth">
			<label>To (<?php echo $this->settings['date_format_strings'][$this->settings['date_format']]['display']; ?>)</label>
		</div>
	</div>
	<div class="field-wrapper field-padding-below clearfix">
		<div class="one-fifth">
			<label><?php _e('Date range', 'quitenicebooking'); ?></label>
		</div>
		<div class="one-fifth">
			<input type="text" name="quitenicebooking_price_filter_%price_filter_num%_startdate" class="full-width datepicker">
		</div>
		<div class="one-fifth">
			<input type="text" name="quitenicebooking_price_filter_%price_filter_num%_enddate" class="full-width datepicker">
		</div>
	</div>
	<hr class="space1">
	<div class="field-wrapper field-padding clearfix">
		<div class="one-fifth empty-label"></div>
		<?php foreach ($quitenicebooking_prices->keys['prices'] as $key) { ?>
		<div class="one-fifth">
			<label><?php echo $key['description']; ?></label>
		</div>
		<?php } ?>
	</div>
	<?php foreach ($quitenicebooking_prices->entity_scheme->keys as $entity_key) { // rows ?>
	<div class="field-wrapper field-padding-below clearfix">
		<div class="one-fifth"><?php echo $entity_key['description'].' ('.$quitenicebooking_currency_unit.')'; ?></div>
		<?php foreach ($quitenicebooking_prices->keys['prices'] as $price_key) { // columns ?>
		<div class="one-fifth">
			<input type="text" name="<?php echo 'quitenicebooking_price_filter_%price_filter_num%_'.$entity_key['meta_part'].'_'.$price_key['meta_part']; ?>" class="full-width">
		</div>
		<?php } ?>
	</div>
	<?php } ?>
	<hr class="space1">
	<?php if (is_a($quitenicebooking_prices->entity_scheme, 'Quitenicebooking_Entity_Per_Person')) { ?>
		<div class="dynamic_price_filter_price_rules" data-id="<?php echo $i; ?>"></div>
		<div class="field-padding-below">
			<button type="button" class="button-primary add_price_filter_price_rule"><?php _e('Add price rule', 'quitenicebooking'); ?></button>
		</div>
		<div class="field-padding-below">
			<p class="description"><?php _e('Sets different prices for additional guests -- e.g. for the 2nd, 3rd, 4th guests'); ?></p>
		</div>
		<hr class="space1">
	<?php } ?>
	<div class="field-wrapper field-padding clearfix">
		<button type="button" class="remove_price_filter button">Remove Filter</button>
	</div>
</div>


<?php if (is_a($quitenicebooking_prices->entity_scheme, 'Quitenicebooking_Entity_Per_Person')) { ?>
<div id="price_filter_price_rule_template" class="hidden">
	<h3><?php _e('Filter', 'quitenicebooking'); ?> %price_filter_id% &gt; <?php _e('Price rule', 'quitenicebooking'); ?> %price_rule_id%</h3>
	<div class="field-wrapper field-padding clearfix">
		<div class="one-fifth empty-label"></div>
		<div class="one-fifth"><label><?php _e('Weekdays (Mon-Thu)', 'quitenicebooking'); ?></label></div>
		<div class="one-fifth"><label><?php _e('Weekends (Fri-Sun)', 'quitenicebooking'); ?></label></div>
	</div>
	<div class="field-wrapper field-padding-below clearfix">
		<div class="one-fifth"><label class="additional_adult"><?php echo __('Per additional adult', 'quitenicebooking').' ('.$this->settings['currency_unit'].')'; ?></label></div>
		<div class="one-fifth"><input type="text" class="full-width" name="quitenicebooking_price_filter_%price_filter_id%_price_rule_%price_rule_id%_adult_weekday"></div>
		<div class="one-fifth"><input type="text" class="full-width" name="quitenicebooking_price_filter_%price_filter_id%_price_rule_%price_rule_id%_adult_weekend"></div>
	</div>
	<div class="field-wrapper field-padding-below clearfix">
		<div class="one-fifth"><label class="additional_child"><?php echo __('Per additional child', 'quitenicebooking').' ('.$this->settings['currency_unit'].')'; ?></label></div>
		<div class="one-fifth"><input type="text" class="full-width" name="quitenicebooking_price_filter_%price_filter_id%_price_rule_%price_rule_id%_child_weekday"></div>
		<div class="one-fifth"><input type="text" class="full-width" name="quitenicebooking_price_filter_%price_filter_id%_price_rule_%price_rule_id%_child_weekend"></div>
	</div>
	<div class="field-wrapper field-padding-below clearfix">
		<button type="button" class="remove_price_filter_price_rule button"><?php _e('Remove price rule', 'quitenicebooking'); ?></button>
	</div>
</div>
<?php } ?>
