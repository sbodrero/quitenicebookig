<?php
/**
 * Quitenicebooking_Reservation_Form
 *
 * @package quitenicebooking
 * @author Quite Nice Stuff
 * @copyright Copyright (c) 2013 Quite Nice Stuff
 * @link http://quitenicestuff.com
 * @version 2.5.7
 * @since 2.5.0
 */
class Quitenicebooking_Reservation_Form {

	/**
	 * @var array $settings The global plugin settings
	 */
	public $settings;

	/**
	 * Constructor
	 *
	 * @param array $settings The global plugin settings
	 */
	public function __construct($settings) {
		$this->settings = $settings;
		add_filter('quitenicebooking_reservation_form_save', array($this, 'settings_save'));
	}

	/**
	 * Saves the meta
	 *
	 * Performs validation and discards any fields that are invalid
	 * @param array $post The $_POST array
	 * @param array $errors A collection of errors
	 * @return array The filtered post array
	 */
	public function settings_save($post) {
		if (!isset($post['reservation_form'])) {
			return $post;
		}

		// get the reservation_form argument
		$user_format = $post['reservation_form'];

		// if it does not contain the 3 required fields
		if (strpos($post['reservation_form'], '[guest_first_name]') === FALSE
			|| strpos($post['reservation_form'], '[guest_last_name]') === FALSE
			|| strpos($post['reservation_form'], '[guest_email]') === FALSE) {
			$post['reservation_form'] = $this->settings['reservation_form'];
			$errors = 'Form is missing one or more of the required fields: [guest_first_name], [guest_last_name], [guest_email]';
			add_settings_error('quitenicebooking', esc_attr('settings_updated'), $errors, 'error');
			return $post;
		}

		// parse the user_format string and split it into [tag ...]
		$tags = array();
		preg_match_all('/\[(guest_first_name|guest_last_name|guest_email)\]|\[(text|textarea|checkbox|radio|select)\s+(required)?\s?id="([^"]+)"\s+label="([^"]+)"\s*(?:value="([^"]+)")?\s*(?:maxlength="(\d+)")?\s*(?:choices="([^"]+)")?\s*(multiple)?\s*(?:class="([^"]+)")?\s*\]/', $user_format, $tags, PREG_SET_ORDER);

		$reservation_form = '';
		foreach ($tags as $tag) {
			$reservation_form .= $tag[0].PHP_EOL;
		}
		$post['reservation_form'] = $reservation_form;

		return $post;
	}
	
}
