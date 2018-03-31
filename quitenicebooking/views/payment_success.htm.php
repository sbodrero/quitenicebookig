<style>
.pp_content td {
	width: 50%;
}
</style>
<!-- BEGIN .booking-step-wrapper -->
<div class="booking-step-wrapper clearfix">

	<div class="step-wrapper">
		<div class="step-icon-wrapper">
			<div class="step-icon">1.</div>
		</div>
		<div class="step-title"><?php _e('Choose Your Date','quitenicebooking'); ?></div>
	</div>

	<div class="step-wrapper">
		<div class="step-icon-wrapper">
			<div class="step-icon">2.</div>
		</div>
		<div class="step-title"><?php _e('Choose Your Lodging','quitenicebooking'); ?></div>
	</div>

	<div class="step-wrapper">
		<div class="step-icon-wrapper">
			<div class="step-icon">3.</div>
		</div>
		<div class="step-title"><?php _e('Place Your Reservation','quitenicebooking'); ?></div>
	</div>

	<div class="step-wrapper last-col">
		<div class="step-icon-wrapper">
			<div class="step-icon step-icon-current">4.</div>
		</div>
		<div class="step-title"><?php _e('Confirmation','quitenicebooking'); ?></div>
	</div>

	<div class="step-line"></div>

<!-- END .booking-step-wrapper -->
</div>

<!-- BEGIN .booking-main-wrapper -->
<div class="booking-main-wrapper">
	
	<!-- BEGIN .booking-main -->
	<div class="booking-main">
		
		<h4 class="title-style4"><?php _e('Payment Successful', 'quitenicebooking'); ?><span class="title-block"></span></h4>
		<p><?php echo $quitenicebooking_payment_success_message; ?></p>
		
		<ul class="contact_details_list contact_details_list_dark">
			<?php if (!empty($quitenicebooking_phone_number)) { ?>
				<li class="phone_list"><strong><?php _e('Phone', 'quitenicebooking'); ?>:</strong> <?php echo $quitenicebooking_phone_number; ?></li>
			<?php } ?>

			<?php if (!empty($quitenicebooking_fax_number)) { ?>
				<li class="fax_list"><strong><?php _e('Fax', 'quitenicebooking'); ?>:</strong> <?php echo $quitenicebooking_fax_number; ?></li>
			<?php } ?>

			<?php if (!empty($quitenicebooking_email_address)) { ?>
				<li class="email_list"><strong><?php _e('Email', 'quitenicebooking'); ?>:</strong> <a href="mailto:<?php echo $quitenicebooking_email_address; ?>"><?php echo $quitenicebooking_email_address; ?></a></li>
			<?php } ?>
		</ul>
		
	<!-- END .booking-main -->
	</div>
	
<!-- END .booking-main-wrapper -->
</div>

<!-- BEGIN .booking-side-wrapper -->
<div class="booking-side-wrapper">

	<!-- BEGIN .booking-side -->
	<div class="booking-side clearfix">

		<?php $n = 1; ?>
		<?php foreach ($quitenicebooking_summary as $room) { ?>
			<?php if (count($quitenicebooking_summary) == 1) { ?>
				<h4 class="title-style4"><?php _e('Your Reservation', 'quitenicebooking'); ?><span class="title-block"></span></h4>
			<?php } else { ?>
				<h4 class="title-style4"><?php printf(__('Lodging %d of %d', 'quitenicebooking'), $n++, $quitenicebooking_total_rooms); ?><span class="title-block"></span></h4>
			<?php } ?>
			<ul>
				<li><span><?php _e('Lodging', 'quitenicebooking'); ?>:</span> <?php echo $room['type'];?> </li>
				<li><span><?php _e('Check In', 'quitenicebooking'); ?>:</span> <?php echo $room['checkin'];?> </li>
				<li><span><?php _e('Check Out', 'quitenicebooking'); ?>:</span> <?php echo $room['checkout'];?> </li>
				<li><span><?php _e('Guests', 'quitenicebooking'); ?>:</span> <?php echo $room['guests'];?> </li>
			</ul>
		<?php } // end foreach ?>
		
		<!-- BEGIN .price-details -->
		<div class="price-details">
			<?php $accept_deposit = !empty($quitenicebooking_deposit_type) && ((!empty($quitenicebooking_accept_paypal) && !empty($quitenicebooking_paypal_email_address)) || !empty($quitenicebooking_accept_bank_transfer)); ?>
			<?php if ($accept_deposit && $quitenicebooking_deposit_type == 'percentage') { ?>
				<p class="deposit"><?php printf(__('%d%% Deposit Paid', 'quitenicebooking'), $quitenicebooking_deposit_percentage); ?></p>
				<h3 class="price"><?php echo Quitenicebooking_Utilities::format_price($quitenicebooking_deposit, $this->settings); ?></h3>
				<hr class="total-line" />
				<p class="total"><?php _e('Total Price', 'quitenicebooking'); ?></p>
			<?php } elseif ($accept_deposit && ($quitenicebooking_deposit_type == 'flat' || $quitenicebooking_deposit_type == 'duration')) { ?>
				<p class="deposit"><?php _e('Deposit Paid', 'quitenicebooking'); ?></p>
				<h3 class="price"><?php echo Quitenicebooking_Utilities::format_price($quitenicebooking_deposit, $this->settings); ?></h3>
				<hr class="total-line" />
				<p class="total"><?php _e('Total Price', 'quitenicebooking'); ?></p>
			<?php } elseif ($accept_deposit && $quitenicebooking_deposit_type == 'full') { ?>
				<p class="full-payment"><?php _e('Total Paid', 'quitenicebooking'); ?></p>
			<?php } else { ?>
				<p class="total-only"><?php _e('Total Price', 'quitenicebooking'); ?></p>
			<?php } ?>
			<h3 class="total-price"><?php echo Quitenicebooking_Utilities::format_price($quitenicebooking_total, $this->settings); ?></h3>
			<p class="price-breakdown"><a rel="prettyPhoto" href="#price_break_hotel_room_all"><?php _e('View Price Breakdown', 'quitenicebooking'); ?></a></p>
			<div id="price_break_hotel_room_all" class="hide">
				
				<!-- BEGIN .lightbox-title -->
				<div class="lightbox-title">
					<h4 class="title-style4"><?php _e('Price Breakdown','quitenicebooking'); ?><span class="title-block"></span></h4>
				<!-- END .lightbox-title -->
				</div>
				
				<!-- BEGIN .page-content -->
				<div class="page-content">
					
					<?php $i = 1; ?>
					<?php foreach ($quitenicebooking_breakdown as $b) { ?>
						<?php if (count($quitenicebooking_breakdown) > 1) { ?>
							<h4 class="room-title"><?php printf(__('Lodging %d: %s', 'quitenicebooking'), $i ++, $b['type']); ?></h4>
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
							<tr>
								<td><?php _e('Lodging subtotal', 'quitenicebooking'); ?></td>
								<td><?php echo Quitenicebooking_Utilities::format_price(Quitenicebooking_Utilities::float_to_user_price($b['total'], $this->settings, TRUE), $this->settings); ?></td>
							</tr>
						</tbody></table>
					<?php } // end foreach ?>

					<?php if (!empty($quitenicebooking_services)) { ?>
					<table><tbody>
						<?php foreach ($quitenicebooking_services as $key => $val) { ?>
							<tr><td><?php echo $key; ?></td><td><?php echo Quitenicebooking_Utilities::format_price(Quitenicebooking_Utilities::float_to_user_price($val, $this->settings, TRUE), $this->settings); ?></td></tr>
						<?php } ?>
					</tbody></table>
					<?php } ?>
					
					<?php if (!empty($quitenicebooking_discounts)) { ?>
					<table><tbody>
						<?php foreach ($quitenicebooking_discounts as $d) { ?>
							<tr>
								<td><?php echo $d['description']; ?></td>
								<td><?php echo $d['amount']; ?></td>
							</tr>
						<?php } ?>
					</tbody></table>
					<?php } ?>

					<?php if (!empty($this->settings['tax_percentage'])) { ?>
					<table>
						<tbody>
							<tr>
								<td><?php _e('Tax'); ?> (<?php echo $this->settings['tax_percentage']; ?>%)</td>
								<td><?php echo Quitenicebooking_Utilities::format_price(Quitenicebooking_Utilities::float_to_user_price($quitenicebooking_tax, $this->settings, TRUE), $this->settings); ?></td>
							</tr>
						</tbody>
					</table>
					<?php } ?>

					<table><tbody>
						<tr>
							<td><?php _e('Total', 'quitenicebooking'); ?></td>
							<td><?php echo Quitenicebooking_Utilities::format_price($quitenicebooking_total, $this->settings); ?></td>
						</tr>
					</tbody></table>
				<!-- END .page-content -->
				</div>
			<!-- END #price_break_hotel_room_all -->
			</div>
		<!-- END .price-details -->
		</div>
		
		<hr class="space9" />
		
	<!-- END .booking-side -->
	</div>
	
<!-- END .booking-side-wrapper -->
</div>

<div class="clearboth"></div>
