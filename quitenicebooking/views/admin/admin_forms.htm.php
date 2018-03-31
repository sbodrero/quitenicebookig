<div id="validation_errors" class="error hidden"></div>
<div class="wrap">
	<?php include QUITENICEBOOKING_PATH . 'views/admin/admin_header.htm.php'; ?>
	<div id="form_generator_errors"></div>
	<form method="POST" action="options.php" id="admin_settings_form">
		<?php settings_fields('quitenicebooking_settings'); ?>

		<table class="form-table">
			<tbody>
				<tr>
					<td colspan="2"><h3><?php _e('Reservation Form', 'quitenicebooking'); ?></h3></td>
				</tr>
				<tr>
					<th scope="row"><label for="quitenicebooking[reservation_form]"><?php _e('Guest details', 'quitenicebooking'); ?> *</label></th>
					<td>
						<div class="instructions">The form must contain the following 3 fields: <code>[guest_first_name]</code>, <code>[guest_last_name]</code>, and <code>[guest_email]</code>.  You can generate additional custom fields by clicking the "Generate Custom Field" drop-down below.</div>
						<div class="clearfix">
						<div id="form_generator">
							<div id="selector">
								<select id="element_type">
									<option value="" selected>Generate custom field</option>
									<option value="text">Text</option>
									<option value="textarea">Textarea</option>
									<option value="checkbox">Check box</option>
									<option value="radio">Radio button</option>
									<option value="select">Drop-down menu</option>
								</select>
							</div>
							<div id="element_params">
								<div><label for="element_required">Required</label><input type="checkbox" id="element_required"></div>
								<div><label for="element_id">ID *</label><input type="text" id="element_id"></div>
								<div><label for="element_label">Label *</label><input type="text" id="element_label"></div>
								<div id="element_value_div"><label for="element_value">Value</label><input type="text" id="element_value"></div>
								<div id="element_choices_div">
									<label for="element_choices">Choices *</label><textarea id="element_choices"></textarea>
									<p>Enter one choice per line</p>
									<div id="element_multiple_choices_div"><input type="checkbox" id="element_multiple_choices"><label for="element_multiple_choices">Allow multiple selections</label></div>
								</div>
								<div id="element_maxlength_div"><label for="element_maxlength">Max length</label><input type="text" id="element_maxlength"></div>
								<div><label for="element_class">Class</label><input type="text" id="element_class"></div>
								<div><button type="button" id="element_generate" class="button button-primary">Generate</button></div>
								Copy the following code into the form on the left
								<div><input type="text" id="generated"></div>
							</div>
						</div>

						<textarea name="quitenicebooking[reservation_form]" id="quitenicebooking[reservation_form]"><?php echo htmlentities($this->settings['reservation_form']); ?></textarea>
						</div><!-- .clearfix -->
					</td>
				</tr>
			</tbody>
		</table><!-- .form-table -->
		<?php submit_button(__('Save Changes', 'quitenicebooking')); ?>
	</form>
</div>
