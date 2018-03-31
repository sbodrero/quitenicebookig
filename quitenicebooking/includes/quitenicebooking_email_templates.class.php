<?php
/**
 * Quitenicebooking_Email_Templates
 *
 * @package quitenicebooking
 * @author Quite Nice Stuff
 * @copyright Copyright (c) 2013 Quite Nice Stuff
 * @link http://quitenicestuff.com
 * @version 2.5.9
 * @since 2.5.0
 */
class Quitenicebooking_Email_Templates {

	/**
	 * @var array The global plugin settings
	 */
	public $settings;

	/**
	 * Constructor
	 *
	 * @param array $settings The global plugin settings
	 */
	public function __construct($settings) {
		$this->settings = $settings;
		add_action('quitenicebooking_settings_email_form', array($this, 'settings_email_form'));
		add_filter('mce_external_plugins', array($this, 'mce_external_plugin'));
		add_filter('mce_buttons_3', array($this, 'mce_buttons'));
	}

	/**
	 * Activation
	 *
	 * Adds a default email_user_template into settings
	 */
	public static function activate() {
		$settings = get_option('quitenicebooking');
		// don't overwrite existing setting if defined
		if (!empty($settings['email_user_template'])) {
			return;
		}
		$settings['email_user_template'] = 'Dear [guest_first_name] [guest_last_name],

Thank you choosing [hotel_name]! Your reservation has been accepted; details can be found below. We look forward to seeing you soon.

<strong>Booking ID:</strong> [booking_id]

[single_room_booking]
<strong>Lodging Info</strong>
<ul>
	<li><strong>Lodging Type:</strong> [room_type]</li>
	<li><strong>Date:</strong> [checkin] - [checkout]</li>
	<li><strong>Guests:</strong> [number_of_guests]</li>
</ul>
[/single_room_booking]

[multi_room_booking]
<strong>Lodging [room_number] Info</strong>
<ul>
	<li><strong>Lodging Type:</strong> [room_type]</li>
	<li><strong>Date:</strong> [checkin] - [checkout]</li>
	<li><strong>Guests:</strong> [number_of_guests]</li>
</ul>
[/multi_room_booking]

<strong>Services Requested:</strong> [services]

[guest_details]
<strong>Guest Info</strong>
<ul>
	<li><strong>Name:</strong> [guest_first_name] [guest_last_name]</li>
	<li><strong>Email:</strong> [guest_email]</li>
</ul>
[/guest_details]

<strong>Payment</strong>
<ul>
	<li><strong>Deposit Due:</strong> [payment_deposit]</li>
	<li><strong>Total:</strong> [payment_total]</li>
</ul>

Best regards,

[hotel_name]
';
		remove_all_filters('pre_update_option_quitenicebooking');
		update_option('quitenicebooking', $settings);
	}

	/**
	 * Display the email template form
	 *
	 * @param string $html The original html
	 */
	public function settings_email_form() {
		$form_fields = Quitenicebooking_Utilities::decode_reservation_form($this->settings);
		include plugin_dir_path(__FILE__) . '../views/admin/admin_email_form.htm.php';
	}

	/**
	 * Register the custom MCE external plugin
	 *
	 * @param array $args An array of plugins to load
	 * @return array An array of plugins to load plus this one
	 */
	public function mce_external_plugin($args) {
		$args['quitenicebooking_premium'] = plugin_dir_url(__FILE__) . '../assets/js/admin/html_email_mce.js';
		return $args;
	}

	/**
	 * Adds the MCE buttons to the editor
	 *
	 * @param array $args An array of buttons
	 * @return array An array of buttons plus these
	 */
	public function mce_buttons($args) {
		array_push($args, 'guest_first_name', 'guest_last_name', 'guest_email','hotel_name', 'booking_id', 'single_room', 'multi_room', 'room_number','room_type', 'checkin', 'checkout', 'num_guests', 'services', 'guest_details', 'payment_deposit', 'payment_total');

		return $args;
	}

	/**
	 * Inserts the booking data into the email template
	 *
	 * @param array $booking The booking data
	 *		array(
	 *
	 *		)
	 * @return string The composed message
	 */
	public function generate_email($booking) {
		$composed = $this->settings['email_user_template'];

		$service_str = '';
		foreach ($booking['services'] as $s) {
			$service_str .= $s['title'].', ';
		}
		$service_str = substr($service_str, 0, -2);

		// replace hardcoded tags
		$composed = preg_replace(
			array(
				'/\[guest_first_name\]/i',
				'/\[guest_last_name\]/i',
				'/\[guest_email\]/',
				'/\[hotel_name\]/i',
				'/\[booking_id\]/',
				'/\[payment_deposit\]/',
				'/\[payment_total\]/',
				'/\[services\]/'
			),
			array(
				$booking['guest_first_name'],
				$booking['guest_last_name'],
				$booking['guest_email'],
				get_bloginfo('name'),
				$booking['booking_id'],
				str_replace('$', '\$', Quitenicebooking_Utilities::format_price(Quitenicebooking_Utilities::float_to_user_price($booking['deposit'], $this->settings, TRUE), $this->settings)),
				str_replace('$', '\$', Quitenicebooking_Utilities::format_price(Quitenicebooking_Utilities::float_to_user_price($booking['total'], $this->settings, TRUE), $this->settings)),
				$service_str
			),
			$composed
		);

		$content = array();
		// determine single or multi room booking
		if (count($booking['summary']) == 1) {
			// single room booking
			// discard multi room tag
			$composed = preg_replace(
				'/\[multi_room_booking\].*\[\/multi_room_booking\]/si',
				'',
				$composed
			);
			// capture the content inside the single room tag
			preg_match(
				'/\[single_room_booking\](.*)\[\/single_room_booking\]/si',
				$composed,
				$content
			);
		} else {
			// multi room booking
			// discard single room tag
			$composed = preg_replace(
				'/\[single_room_booking\].*\[\/single_room_booking\]/si',
				'',
				$composed
			);
			// capture the content inside the multi room tag
			preg_match(
				'/\[multi_room_booking\](.*)\[\/multi_room_booking\]/si',
				$composed,
				$content
			);
		}
		$content = $content[1];

		$composed_content = array();
		// replace each element inside the single/multi room tag
		$room_number = 1;
		foreach ($booking['summary'] as $room) {
			$composed_content[] = preg_replace(
				array(
					'/\[room_type\]/i',
					'/\[checkin\]/i',
					'/\[checkout\]/i',
					'/\[number_of_guests\]/i',
					'/\[room_number\]/i'
				),
				array(
					$room['type'],
					$room['checkin'],
					$room['checkout'],
					$room['guests'],
					$room_number ++
				),
				$content
			);
		}

		if (count($booking['summary']) == 1) {
			// strip the single room tag and replace it with the content
			$composed = preg_replace(
				'/\[single_room_booking\].*\[\/single_room_booking\]/si',
				implode($composed_content),
				$composed
			);
		} else {
			// strip the multi room tag and replace it with the content
			// strip the single room tag and replace it with the content
			$composed = preg_replace(
				'/\[multi_room_booking\].*\[\/multi_room_booking\]/si',
				implode($composed_content),
				$composed
			);
		}

		// replace guest details
		$content = array();
		// capture the content inside the guest details tag
		preg_match(
			'/\[guest_details\](.*)\[\/guest_details\]/si',
			$composed,
			$content
		);
		$content = $content[1];

		// replace each element inside the guest details tag
		$tags = array();
		preg_match_all('/\[(.+?)\]/i', $content, $tags);
		$tags = $tags[1];
		$guest_details = array();
		foreach ($booking['guest_details'] as $key => $field) {
			if (is_array($field['value'])) {
				// handle the multiple select inputs (select multiple, checkbox)
				$guest_details[$key] = implode(', ', $field['value']);
			} else {
				$guest_details[$key] = $field['value'];
			}
		}

		foreach ($tags as $tag) {
			// if the corresponding tag is found in the booking data, replace it with its contents
			if (in_array($tag, array_keys($guest_details))) {
				$content = preg_replace('/\['.$tag.'\]/', $guest_details[$tag], $content);
			}
		}
		// strip out the guest details tag and replace it with content
		$composed = preg_replace(
			'/\[guest_details\].*\[\/guest_details\]/si',
			$content,
			$composed
		);

		// convert double linebreaks into <p> tags
		$composed = wpautop($composed);

		// wrap html and body tags
		$composed = '<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>'.$composed.'</body>
</html>';
		return $composed;
	}
}
