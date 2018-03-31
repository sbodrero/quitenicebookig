<div class="field-wrapper field-padding-above clearfix">

	<div class="one-fifth"><?php _e('Allow Stacking', 'quitenicebooking'); ?></div>

	<div class="four-fifths">
		<div class="clearfix">
			<div class="check-wrapper"><input type="hidden" name="coupon_stackable" value=""><input type="checkbox" name="coupon_stackable" value="1" id="coupon_stackable" <?php if (!empty($coupon_stackable)) { checked($coupon_stackable, 1); } ?>></div><label for="coupon_stackable"><?php _e('Can be used in conjunction with other coupons', 'quitenicebooking'); ?></label>
		</div>
	</div>
	
</div>
<hr class="space1" />
<div class="field-wrapper field-padding-above clearfix">

	<div class="one-fifth"><?php _e('Usage Limit', 'quitenicebooking'); ?></div>

	<div class="four-fifths">
		<input type="text" name="coupon_usage_limit" id="coupon_usage_limit" value="<?php echo isset($coupon_usage_limit) ? $coupon_usage_limit : ''; ?>">
		<p class="description"><?php _e('Leave empty for unlimited uses', 'quitenicebooking'); ?></p>
	</div>

</div>
<hr class="space1" />
<div class="field-wrapper field-padding clearfix">

	<div class="one-fifth empty-label"></div>

	<div class="one-fifth">(<?php echo $this->settings['date_format_strings'][$this->settings['date_format']]['display']; ?>)</div>

	<div class="one-fifth"></div>

</div>
<div class="field-wrapper field-padding-below clearfix">
	
	<div class="one-fifth"><?php _e('Expiry Date', 'quitenicebooking'); ?></div>
	
	<div class="one-fifth">
		<input type="text" name="coupon_expiry_date" id="coupon_expiry_date" class="datepicker" value="<?php echo !empty($coupon_expiry_date) ? $coupon_expiry_date : ''; ?>">
	</div>

	<div class="one-fifth"></div>
	
</div>
<hr class="space1" />
<div class="field-wrapper field-padding clearfix">

	<div class="one-fifth"><?php _e('Minimum Total Payment'); ?> (<?php echo $this->settings['currency_unit']; ?>)</div>

	<div class="four-fifths">
		<input type="text" name="coupon_minimum_total" id="coupon_minimum_total" value="<?php echo !empty($coupon_minimum_total) ? Quitenicebooking_Utilities::float_to_user_price($coupon_minimum_total, $this->settings) : ''; ?>">
	</div>

</div>
