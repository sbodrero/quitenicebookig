<div id="validation_errors" class="error hidden"></div>

<?php if (!empty($error_coupon_not_unique)) { ?>
<div class="error">
	<p><?php _e('This coupon code is already in use.  Please change it or delete the other one(s).', 'quitenicebooking'); ?></p>
</div>
<?php } ?>
<div class="field-wrapper field-padding clearfix">

	<div class="one-fifth"><?php _e('Coupon Code', 'quitenicebooking'); ?></div>

	<div class="two-fifths"><input type="text" name="coupon_code" id="coupon_code" value="<?php echo !empty($coupon_code) ? $coupon_code : ''; ?>"></div>

	<div class="two-fifths"></div>

</div>
<hr class="space1" />
<div class="field-wrapper field-padding-above clearfix">

	<div class="one-fifth"><?php _e('Discount Type', 'quitenicebooking'); ?></div>

	<div class="two-fifths">
		<div class="field-padding-none clearfix">
			<div class="check-wrapper"><input type="radio" name="coupon_discount_type" value="flat" id="coupon_discount_type_flat" <?php checked($coupon_discount_type, 'flat'); ?>></div><label for="coupon_discount_type_flat"><?php _e('Flat discount', 'quitenicebooking'); ?></label>
		</div>
		<div class="clearfix">
			<div class="field-padding-none check-wrapper"><input type="radio" name="coupon_discount_type" value="percentage" id="coupon_discount_type_percentage" <?php checked($coupon_discount_type, 'percentage'); ?>></div><label for="coupon_discount_type_percentage"><?php _e('Percentage off', 'quitenicebooking'); ?></label>
		</div>
		<div class="clearfix">
			<div class="field-padding-none check-wrapper"><input type="radio" name="coupon_discount_type" value="duration" id="coupon_discount_type_duration" <?php checked($coupon_discount_type, 'duration'); ?>></div><label for="coupon_discount_type_duration"><?php _e('By length of stay', 'quitenicebooking'); ?></label>
		</div>

	</div>

	<div class="two-fifths"></div>

</div>
<hr class="space1" id="discount_details_separator" />
<div class="field-wrapper field-padding clearfix" id="discount_flat_div">

	<div class="one-fifth"><?php _e('Discount Amount', 'quitenicebooking'); ?> (<?php echo $this->settings['currency_unit']; ?>)</div>

	<div class="two-fifths"><input type="text" name="coupon_discount_flat" id="coupon_discount_flat" value="<?php echo !empty($coupon_discount_flat) ? Quitenicebooking_Utilities::float_to_user_price($coupon_discount_flat, $this->settings) : ''; ?>"></div>

	<div class="two-fifths"></div>

</div>

<div class="field-wrapper field-padding clearfix" id="discount_percentage_div">

	<div class="one-fifth"><?php _e('Discount Amount', 'quitenicebooking'); ?> (%)</div>

	<div class="two-fifths"><input type="text" name="coupon_discount_percentage" id="coupon_discount_percentage" value="<?php echo !empty($coupon_discount_percentage) ? $coupon_discount_percentage : ''; ?>"></div>

	<div class="two-fifths"></div>

</div>

<div class="field-wrapper field-padding-above clearfix" id="discount_duration_div">

	<div class="one-fifth"><?php _e('Discount Amount', 'quitenicebooking'); ?></div>

	<div class="four-fifths">
		<div class="field-padding-below-noindent clearfix">
		<?php ob_start(); ?>
		<input type="text" name="coupon_discount_duration_requirement" id="coupon_discount_duration_requirement" class="small-text" value="<?php echo !empty($coupon_discount_duration_requirement) ? $coupon_discount_duration_requirement : ''; ?>">
		<?php $coupon_discount_duration_requirement = ob_get_clean(); ?>
		<?php ob_start(); ?>
		<input type="text" name="coupon_discount_duration_units" id="coupon_discount_duration_units" class="small-text" value="<?php echo !empty($coupon_discount_duration_units) ? $coupon_discount_duration_units : ''; ?>">
		<?php $coupon_discount_duration_units = ob_get_clean(); ?>
		<?php printf(__('Book %s night(s), get %s night(s) free'), $coupon_discount_duration_requirement, $coupon_discount_duration_units); ?>
		</div>
		<div class="clearfix"><div class="check-wrapper"><input type="radio" name="coupon_discount_duration_mode" id="coupon_discount_duration_mode_deduct_lowest" value="deduct_lowest" <?php checked($coupon_discount_duration_mode, 'deduct_lowest'); ?>></div><label for="coupon_discount_duration_mode_deduct_lowest"><?php _e('Deduct least expensive nights', 'quitenicebooking'); ?></label></div>
		<div class="clearfix"><div class="check-wrapper"><input type="radio" name="coupon_discount_duration_mode" id="coupon_discount_duration_mode_deduct_highest" value="deduct_highest" <?php checked($coupon_discount_duration_mode, 'deduct_highest'); ?>></div><label for="coupon_discount_duration_mode_deduct_highest"><?php _e('Deduct most expensive nights', 'quitenicebooking'); ?></label></div>
	</div>

</div>
