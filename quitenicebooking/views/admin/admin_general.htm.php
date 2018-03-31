<div class="wrap">
	<?php include 'admin_header.htm.php'; ?>
	<form method="POST" action="options.php" id="admin_settings_form">
		<?php settings_fields('quitenicebooking_settings'); ?>
		
		<table class="form-table">
			<tbody>
				<tr>
					<td colspan="2"><h3><?php _e('Public contact information', 'quitenicebooking'); ?></h3></td>
				</tr>
				<tr>
					<th scope="row"><label for="quitenicebooking[email_address]"><?php _e('Email address', 'quitenicebooking'); ?></label></th>
					<td>
						<input type="text" class="regular-text" name="quitenicebooking[email_address]" id="quitenicebooking[email_address]" value="<?php echo $this->settings['email_address']; ?>">
						<p class="description"><?php _e('If provided, notifications will be sent to and from this address instead of the one specified in Settings &gt; General', 'quitenicebooking'); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="quitenicebooking[phone_number]"><?php _e('Phone number', 'quitenicebooking'); ?></label></th>
					<td><input type="text" class="regular-text" name="quitenicebooking[phone_number]" id="quitenicebooking[phone_number]" value="<?php echo $this->settings['phone_number']; ?>"></td>
				</tr>
				<tr>
					<th scope="row"><label for="quitenicebooking[fax_number]"><?php _e('Fax number', 'quitenicebooking'); ?></label></th>
					<td><input type="text" class="regular-text" name="quitenicebooking[fax_number]" id="quitenicebooking[fax_number]" value="<?php echo $this->settings['fax_number']; ?>"></td>
				</tr>

				<tr>
					<td colspan="2"><h3><?php _e('Booking settings', 'quitenicebooking'); ?></h3></td>
				</tr>
				
				<tr>
					<th scope="row"><label for="quitenicebooking[manually_confirm_bookings]"><?php _e('Manually confirm bookings', 'quitenicebooking'); ?></label></th>
					<td>
						<input type="hidden" name="quitenicebooking[manually_confirm_bookings]" value="0">
						<input type="checkbox" name="quitenicebooking[manually_confirm_bookings]" id="quitenicebooking[manually_confirm_bookings]" value="1" <?php checked(isset($this->settings['manually_confirm_bookings']) ? $this->settings['manually_confirm_bookings'] : 0, 1); ?>>
						<p class="description"><?php _e('Bookings will initially be unconfirmed and require manual confirmation/cancellation', 'quitenicebooking'); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="quitenicebooking[saturday_to_saturday_bookings]"><?php _e('Saturday to Saturday bookings only', 'quitenicebooking'); ?></label></th>
					<td>
						<input type="hidden" name="quitenicebooking[saturday_to_saturday_bookings]" value="0">
						<input type="checkbox" name="quitenicebooking[saturday_to_saturday_bookings]" id="quitenicebooking[saturday_to_saturday_bookings]" value="1" <?php checked(isset($this->settings['saturday_to_saturday_bookings']) ? $this->settings['saturday_to_saturday_bookings'] : 0, 1); ?>>					
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="quitenicebooking[disable_database]"><?php _e('Do not record bookings into database', 'quitenicebooking'); ?></label></th>
					<td>
						<input type="hidden" name="quitenicebooking[disable_database]" value="0">
						<input type="checkbox" name="quitenicebooking[disable_database]" id="quitenicebooking[disable_database]" value="1" <?php checked(isset($this->settings['disable_database']) ? $this->settings['disable_database'] : 0, 1); ?>>
						<p class="description"><?php _e('The availability checker will not work if you check this option', 'quitenicebooking'); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="quitenicebooking[hide_booking_system]"><?php _e('Hide booking system', 'quitenicebooking'); ?></label></th>
					<td>
						<input type="hidden" name="quitenicebooking[hide_booking_system]" value="0">
						<input type="checkbox" name="quitenicebooking[hide_booking_system]" id="quitenicebooking[hide_booking_system]" value="1" <?php checked(isset($this->settings['hide_booking_system']) ? $this->settings['hide_booking_system'] : 0, 1); ?>>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="quitenicebooking[entity_scheme]"><?php _e( 'Pricing scheme', 'quitenicebooking' ); ?></label></th>
					<td>
						<select name="quitenicebooking[entity_scheme]">
							<option value="per_person" <?php selected( isset( $this->settings['entity_scheme'] ) ? $this->settings['entity_scheme'] : '', 'per_person' ); ?>><?php _e( 'Charge per person', 'quitenicebooking' ); ?></option>
							<option value="per_room" <?php selected( isset( $this->settings['entity_scheme'] ) ? $this->settings['entity_scheme'] : '', 'per_room' ); ?>><?php _e( 'Charge per room', 'quitenicebooking' ); ?></option>
						</select>
						<input type="hidden" name="quitenicebooking[pricing_scheme]" value="daily">
						<p class="description"><?php _e('Notice: If you already have prices and filters set for the current pricing scheme, changing the pricing scheme will not transfer those values to the new scheme, and you will need to re-enter that data', 'quitenicebooking'); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="quitenicebooking[remove_children]"><?php _e('Remove children from booking forms', 'quitenicebooking'); ?></label></th>
					<td><input type="hidden" name="quitenicebooking[remove_children]" value="0"><input type="checkbox" name="quitenicebooking[remove_children]" id="quitenicebooking[remove_children]" value="1" <?php checked(isset($this->settings['remove_children']) ? $this->settings['remove_children'] : 0, 1); ?>></td>
				</tr>
				<tr>
					<th scope="row"><label for="quitenicebooking[multiroom_link]"><?php _e('Show multi-room link on booking widgets', 'quitenicebooking'); ?></label></th>
					<td><input type="hidden" name="quitenicebooking[multiroom_link]" value="0"><input type="checkbox" name="quitenicebooking[multiroom_link]" id="quitenicebooking[multiroom_link]" value="1" <?php checked(isset($this->settings['multiroom_link']) ? $this->settings['multiroom_link'] : 0, 1); ?>><p class="description"><?php _e('Booking widgets assume a single-room booking and will proceed to step 2. This option will display a link to step 1', 'quitenicebooking'); ?></p></td>
				</tr>
				<tr>
					<th scope="row"><label for="quitenicebooking[max_persons_in_form]"><?php _e('Maximum persons to show on booking form', 'quitenicebooking'); ?></label></th>
					<td>
						<select name="quitenicebooking[max_persons_in_form]" id="quitenicebooking[max_persons_in_form]">
							<?php foreach (range(1, 50) as $r) { ?>
								<option value="<?php echo $r; ?>" <?php selected($this->settings['max_persons_in_form'], $r); ?>><?php echo $r; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="quitenicebooking[max_rooms]"><?php _e('Maximum rooms per booking', 'quitenicebooking'); ?></label></th>
					<td>
						<select name="quitenicebooking[max_rooms]" id="quitenicebooking[max_rooms]">
							<?php foreach (range(1, 5) as $r) { ?>
								<option value="<?php echo $r; ?>" <?php selected($this->settings['max_rooms'], $r); ?>><?php echo $r; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="quitenicebooking[minimum_stay]"><?php _e('Minimum stay', 'quitenicebooking'); ?></label></th>
					<td>
						<select name="quitenicebooking[minimum_stay]" id="quitenicebooking[minimum_stay]">
							<?php $minimum_stay = !empty($this->settings['minimum_stay']) ? $this->settings['minimum_stay'] : 1; ?>
							<?php foreach (range(1, 30) as $r) { ?>
								<option value="<?php echo $r; ?>" <?php selected($minimum_stay, $r); ?>><?php printf(_n('%d night', '%d nights', $r, 'quitenicebooking'), $r); ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				
				<tr>
					<td colspan="2"><h3><?php _e('Display settings', 'quitenicebooking'); ?></h3></td>
				</tr>
				<tr>
					<th scope="row"><label for="quitenicebooking[rooms_per_page]"><?php _e('Rooms displayed per page', 'quitenicebooking'); ?></label></th>
					<td>
						<select name="quitenicebooking[rooms_per_page]" id="quitenicebooking[rooms_per_page]">
							<?php foreach (range(1, 30) as $r) { ?>
								<option value="<?php echo $r; ?>" <?php selected($this->settings['rooms_per_page'], $r); ?>><?php echo $r; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="quitenicebooking[rooms_order]"><?php _e('Rooms display order', 'quitenicebooking'); ?></label></th>
					<td>
						<select name="quitenicebooking[rooms_order]" id="quitenicebooking[rooms_order]">
							<option value="newest" <?php selected($this->settings['rooms_order'], 'newest'); ?>><?php _e('Newest first', 'quitenicebooking'); ?></option>
							<option value="oldest" <?php selected($this->settings['rooms_order'], 'oldest'); ?>><?php _e('Oldest first', 'quitenicebooking'); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="quitenicebooking[date_format]"><?php _e('Date format', 'quitenicebooking'); ?></label></th>
					<td>
						<select name="quitenicebooking[date_format]" id="quitenicebooking[date_format]">
							<option value="dd/mm/yy" <?php selected($this->settings['date_format'], 'dd/mm/yy'); ?>><?php _e('DD/MM/YYYY', 'quitenicebooking'); ?></option>
							<option value="mm/dd/yy" <?php selected($this->settings['date_format'], 'mm/dd/yy'); ?>><?php _e('MM/DD/YYYY', 'quitenicebooking'); ?></option>
							<option value="yy/mm/dd" <?php selected($this->settings['date_format'], 'yy/mm/dd'); ?>><?php _e('YYYY/MM/DD', 'quitenicebooking'); ?></option>
						</select>
					</td>
				</tr>

				<tr>
					<th scope="row"><label><?php _e('Live availability calendars', 'quitenicebooking'); ?></label></th>
					<td>
						<fieldset>
							<div>
								<input type="hidden" name="quitenicebooking[enable_live_calendar_widget]" value="0">
								<input type="checkbox" name="quitenicebooking[enable_live_calendar_widget]" id="quitenicebooking[enable_live_calendar_widget]" value="1" <?php checked(isset($this->settings['enable_live_calendar_widget']) ? $this->settings['enable_live_calendar_widget'] : 0, 1); ?>><label for="quitenicebooking[enable_live_calendar_widget]"><?php _e('Enable on homepage and reservation widget', 'quitenicebooking'); ?></label>
							</div>
							<div>
								<input type="hidden" name="quitenicebooking[enable_live_calendar_step_1]" value="0">
								<input type="checkbox" name="quitenicebooking[enable_live_calendar_step_1]" id="quitenicebooking[enable_live_calendar_step_1]" value="1" <?php checked(isset($this->settings['enable_live_calendar_step_1']) ? $this->settings['enable_live_calendar_step_1'] : 0, 1); ?>><label for="quitenicebooking[enable_live_calendar_step_1]"><?php _e('Enable on booking step 1', 'quitenicebooking'); ?></label>
							</div>
							<div>
								<input type="hidden" name="quitenicebooking[enable_live_calendar_single]" value="0">
								<input type="checkbox" name="quitenicebooking[enable_live_calendar_single]" id="quitenicebooking[enable_live_calendar_single]" value="1" <?php checked(isset($this->settings['enable_live_calendar_single']) ? $this->settings['enable_live_calendar_single'] : 0, 1); ?>><label for="quitenicebooking[enable_live_calendar_single]"><?php _e('Enable on single accommodation template', 'quitenicebooking'); ?></label>
							</div>
							<p class="description"><?php _e('If the live availability calendar is taking too long to load, uncheck the above options', 'quitenicebooking'); ?></p>
						</fieldset>
					</td>
				</tr>
			</tbody>
		</table><!-- .form-table -->
		<?php submit_button(__('Save Changes', 'quitenicebooking')); ?>
	</form>
</div>
