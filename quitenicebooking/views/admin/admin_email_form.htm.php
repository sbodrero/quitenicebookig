<tr>
	<th scope="row"><label for="quitenicebooking[email_user_template]"><?php _e('Confirmation email (customer)', 'quitenicebooking'); ?> *</label></th>
	<td>
		<?php
		wp_editor(
			!empty($this->settings['email_user_template']) ? $this->settings['email_user_template'] : '',
			'quitenicebooking[email_user_template]',
				array(
				'textarea_name' => 'quitenicebooking[email_user_template]',
				'media_buttons' => FALSE,
				'textarea_rows' => 15,
				'tinymce' => array(
					'theme_advanced_disable' => 'wp_more'
				))
		);
		?>
		<p class="description">The current tags are allowed inside the [guest_details]...[/guest_details] tag (you may change them on the <a href="<?php echo admin_url('admin.php?page=quitenicebooking_settings&tab=forms'); ?>">forms</a> tab):</p>
		<?php foreach ($form_fields as $field) { ?>
			<?php if (in_array($field['type'], array('guest_first_name', 'guest_last_name', 'guest_email'))) {				continue; } ?>
			<?php echo '['.$field['id'].']'; ?>
		<?php } ?>
	</td>
</tr>