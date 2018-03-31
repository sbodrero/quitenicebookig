<?php $i = 1; ?>
<?php foreach ($data['breakdown'] as $b) { ?>
	<?php if (count($data['breakdown']) > 1) { ?>
		<h4 class="room-title"><?php printf(__('Room %d: %s', 'quitenicebooking'), $i ++, $b['type']); ?></h4>
	<?php } ?>
	<table><tbody>
		<?php foreach ($b['breakdown'] as $day) { ?>
		<tr>
			<td data-title="<?php _e('Date', 'quitenicebooking'); ?>"><?php echo $day['date_string']; ?></td>
			<td data-title="<?php _e('Price', 'quitenicebooking'); ?>"><?php echo $day['price_string']; ?></td>
		</tr>
		<?php } ?>
		<?php if (!empty($b['surcharge'])) { ?>
		<tr>
			<td><?php _e('Surcharge', 'quitenicebooking'); ?></td>
			<td><?php echo Quitenicebooking_Utilities::format_price(Quitenicebooking_Utilities::float_to_user_price($b['surcharge'], $this->settings, TRUE), $this->settings); ?></td>
		</tr>
		<?php } ?>
		<tr>
			<td><?php _e('Room subtotal', 'quitenicebooking'); ?></td>
			<td class="breakdown-room-subtotal" data-price="<?php echo $b['total']; ?>"><?php echo Quitenicebooking_Utilities::format_price(Quitenicebooking_Utilities::float_to_user_price($b['total'], $this->settings, TRUE), $this->settings); ?></td>
		</tr>
	</tbody></table>
<?php } // end foreach ?>

<?php if (!empty($data['services'])) { ?>
<table><tbody>
	<?php foreach ($data['services'] as $s) { ?>
		<tr><td><?php echo $s['title']; ?></td><td><?php echo Quitenicebooking_Utilities::format_price(Quitenicebooking_Utilities::float_to_user_price($s['price'], $this->settings, TRUE), $this->settings); ?></td></tr>
	<?php } ?>
</tbody></table>
<?php } ?>

<?php if (!empty($data['discounts'])) { ?>
<table><tbody>
	<?php foreach ($data['discounts'] as $d) { ?>
		<tr>
			<td><?php echo $d['description']; ?></td>
			<td class="breakdown-discount" data-type="<?php echo $d['type']; ?>" data-calc="<?php echo $d['calc']; ?>"><?php echo $d['amount']; ?></td>
		</tr>
	<?php } ?>
</tbody></table>
<?php } ?>

<?php if (!empty($this->settings['tax_percentage'])) { ?>
<table>
	<tbody>
		<tr>
			<td><?php _e('Tax'); ?> (<?php echo $this->settings['tax_percentage']; ?>%)</td>
			<td class="breakdown-tax" data-tax-rate="<?php echo $this->settings['tax_percentage']; ?>"><?php echo Quitenicebooking_Utilities::format_price(Quitenicebooking_Utilities::float_to_user_price($data['tax'], $this->settings, TRUE), $this->settings); ?></td>
		</tr>
	</tbody>
</table>
<?php } ?>

<table><tbody>
	<tr>
		<td><?php _e('Total', 'quitenicebooking'); ?></td>
		<td class="breakdown-total"><?php echo Quitenicebooking_Utilities::format_price(Quitenicebooking_Utilities::float_to_user_price($data['total'], $this->settings, TRUE), $this->settings); ?></td>
	</tr>
</tbody></table>