<?php
/**
 * Quitenicebooking coupon post type
 *
 * @package quitenicebooking
 * @author Quite Nice Stuff
 * @copyright Copyright (c) 2013 Quite Nice Stuff
 * @link http://quitenicestuff.com
 * @version 2.5.9
 * @since 2.5.0
 */
class Quitenicebooking_Coupon_Post {
	/**
	 * Properties ==============================================================
	 */

	/**
	 * @var array The global settings
	 */
	public $settings;

	/**
	 * @var array Key definitions; see constructor
	 */
	public $keys;

	/**
	 * Methods =================================================================
	 */

	/**
	 * Constructor
	 *
	 * Registers post type, actions, filters
	 */
	public function __construct() {
		// settings should be poked by the invoker during init

		$this->keys = array();
		$this->keys['coupon_code']['meta_key'] = 'quitenicebooking_coupon_code';
		$this->keys['coupon_discount_type']['meta_key'] = 'quitenicebooking_coupon_discount_type';
		$this->keys['coupon_discount_flat']['meta_key'] = 'quitenicebooking_coupon_discount_flat';
		$this->keys['coupon_discount_percentage']['meta_key'] = 'quitenicebooking_coupon_discount_percentage';
		$this->keys['coupon_discount_duration_requirement']['meta_key'] = 'quitenicebooking_coupon_discount_duration_requirement';
		$this->keys['coupon_discount_duration_units']['meta_key'] = 'quitenicebooking_coupon_discount_duration_units';
		$this->keys['coupon_discount_duration_mode']['meta_key'] = 'quitenicebooking_coupon_discount_duration_mode';
		$this->keys['coupon_stackable']['meta_key'] = 'quitenicebooking_coupon_stackable';
		$this->keys['coupon_usage_limit']['meta_key'] = 'quitenicebooking_coupon_usage_limit';
		$this->keys['coupon_expiry_date']['meta_key'] = 'quitenicebooking_coupon_expiry_date';
		$this->keys['coupon_minimum_total']['meta_key'] = 'quitenicebooking_coupon_minimum_total';
		
		add_action('init', array($this, 'register_post_type'));
		// add meta box
		add_action('add_meta_boxes', array($this, 'add_meta_box'));
		// enqueue scripts
		add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
		// save meta
		add_action('save_post', array($this, 'save_meta'));
	}

	/**
	 * Registers the Coupons post type
	 */
	public function register_post_type() {
		register_post_type(
			'coupon',
			array(
				'public' => TRUE,
				'supports' => array(
					'title',
					'editor'
				),
				'labels' => array(
					'name' => __('Coupons', 'quitenicebooking'),
					'singular_name' => __('Coupon', 'quitenicebooking'),
					'add_new' => _x('Add Coupon', 'coupon', 'quitenicebooking'),
					'add_new_item' => __('Add New Coupon', 'quitenicebooking')
				),
				'menu_position' => 5,
			)
		);
	}

	/**
	 * Enqueues scripts
	 *
	 * @global WP_Post $post
	 */
	public function admin_enqueue_scripts() {
		global $post;
		if (is_object($post) && get_post_type($post->ID) == 'coupon') {
			// js
			wp_register_script('quitenicebooking-coupon-admin', plugins_url('assets/js/admin/coupon_meta.js', plugin_dir_path(__FILE__)));
			wp_enqueue_script('quitenicebooking-coupon-admin');
			wp_enqueue_script('jquery-ui-datepicker');
			// css
			wp_register_style('quitenicebooking-coupon-admin', plugins_url('assets/css/admin/coupon_meta.css', plugin_dir_path(__FILE__)));
			wp_enqueue_style('quitenicebooking-coupon-admin');
			wp_localize_script('quitenicebooking-coupon-admin', 'quitenicebooking_premium', array(
				'js_date_format' => $this->settings['date_format_strings'][$this->settings['date_format']]['js'],
				'currency_thousands_separator' => $this->settings['currency_thousands_separator'],
				'currency_decimal_separator' => $this->settings['currency_decimal_separator']
			));
			wp_enqueue_style('quitenicebooking-admin-shared', QUITENICEBOOKING_URL.'assets/css/admin/shared.css');
			wp_enqueue_style('jquery-ui-core', QUITENICEBOOKING_URL.'assets/css/admin/jquery.ui.core.css');
			wp_enqueue_style('jquery-ui-theme', QUITENICEBOOKING_URL.'assets/css/admin/jquery.ui.theme.css');
			wp_enqueue_style('jquery-ui-datepicker', QUITENICEBOOKING_URL.'assets/css/admin/jquery.ui.datepicker.css');
		}
	}

	/**
	 * Adds the meta box
	 */
	public function add_meta_box() {
		add_meta_box(
			'coupon_details_meta',
			__('Coupon Details', 'quitenicebooking'),
			array($this, 'show_details_meta_box'),
			'coupon',
			'normal'
		);

		add_meta_box(
			'coupon_restrictions_meta',
			__('Coupon Restrictions', 'quitenicebooking'),
			array($this, 'show_restrictions_meta_box'),
			'coupon',
			'normal'
		);
	}

	/**
	 * Shows the meta box
	 * @global WP_Post $post
	 */
	public function show_details_meta_box() {
		global $post;
		$meta = get_post_meta($post->ID);

		foreach ($this->keys as $key => $defs) {
			if (isset($meta[$defs['meta_key']])) {
				${$key} = $meta[$defs['meta_key']][0];
			} else {
				${$key} = '';
			}
		}

		if (!$this->validate_unique_coupon_code($coupon_code)) {
			$error_coupon_not_unique = TRUE;
		}

		include plugin_dir_path(__FILE__) . '../views/admin/coupon_details_meta.htm.php';
	}

	public function show_restrictions_meta_box() {
		global $post;
		$meta = get_post_meta($post->ID);

		foreach ($this->keys as $key => $defs) {
			if (isset($meta[$defs['meta_key']])) {
				if ($key == 'coupon_expiry_date' && !empty($meta[$defs['meta_key']][0])) {
					${$key} = Quitenicebooking_Utilities::to_datestring($meta[$defs['meta_key']][0], $this->settings);
				} else {
					${$key} = $meta[$defs['meta_key']][0];
				}
			} else {
				${$key} = '';
			}
		}

		include plugin_dir_path(__FILE__) . '../views/admin/coupon_restrictions_meta.htm.php';
	}

	/**
	 * Saves the meta defined in this class
	 *
	 * @param int $id The post ID
	 */
	public function save_meta($id) {
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}

		// check whether this is a coupon post type
		if (!isset($_REQUEST['post_type']) || $_REQUEST['post_type'] != 'coupon') {
			return;
		}

		// check permissions
		if (!current_user_can('edit_post', $id)) {
			return;
		}

		// update meta
		$input_post = filter_input_array(INPUT_POST);
		if ($input_post === NULL) {
			return;
		}

		foreach ($this->keys as $key => $defs) {
			if (isset($input_post[$key])) {
				if ($key == 'coupon_expiry_date' && !empty($input_post[$key])) {
					update_post_meta($id, $defs['meta_key'], Quitenicebooking_Utilities::to_timestamp(sanitize_text_field($input_post[$key]), $this->settings));
				} elseif (($key == 'coupon_discount_flat' && !empty($input_post[$key])) || ($key == 'coupon_minimum_total' && !empty($input_post[$key]))) {
					update_post_meta($id, $defs['meta_key'], sanitize_text_field(Quitenicebooking_Utilities::user_price_to_float($input_post[$key], $this->settings)));
				} else {
					update_post_meta($id, $defs['meta_key'], sanitize_text_field($input_post[$key]));
				}
			}
		}
	}

	/**
	 * Validates a coupon
	 *
	 * @param string $coupon_code
	 * @return boolean TRUE if valid, FALSE if invalid
	 */
	public function validate_coupon($coupon_code) {
		// validate coupon code
		if ($this->get_coupon_id($coupon_code) === NULL) {
			return FALSE;
		}
		// validate expiry date
		$coupon = $this->get_coupon($coupon_code);
		if (!empty($coupon['coupon_expiry_date']) && time() >= $coupon['coupon_expiry_date']) {
			return FALSE;
		}
		// validate usage
		if (isset($coupon['coupon_usage_limit']) && $coupon['coupon_usage_limit'] != '' && $coupon['coupon_usage_limit'] <= 0) {
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Checks whether the coupon code is unique
	 *
	 * @global WPDB $wpdb
	 * @param string $coupon_code
	 * @return TRUE if unique; FALSE if not
	 */
	public function validate_unique_coupon_code($coupon_code) {
		if (empty($coupon_code)) {
			return TRUE;
		}
		global $wpdb;
		$count = $wpdb->get_var($wpdb->prepare(
"SELECT COUNT(post_id) FROM {$wpdb->postmeta}
INNER JOIN {$wpdb->posts}
ON {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
WHERE {$wpdb->posts}.post_status = 'publish' AND meta_key = 'quitenicebooking_coupon_code' AND meta_value = %s",
			$coupon_code
		));
		return $count <= 1;
	}

	/**
	 * Applies a discount to a single booking
	 *
	 * @param array $coupon_codes An array of coupon codes
	 * @param array $breakdown The breakdown
	 * @param float $total The grand total
	 * @param array $discount Describes the discount
	 *		array(
	 *			'description' => string,
	 *			'amount' => string
	 *		)
	 * @param array $errors Any error messages
	 * @return float The total discount amount
	 */
	public function apply_discount(&$coupon_codes, &$breakdown, &$total, &$discount, &$errors) {
		// collect the coupons into an array
		foreach ($coupon_codes as $cc) {
			$coupons[] = $this->get_coupon($cc);
		}
		// 1. sort the coupon types in this order:
		// duration, flat rate off, percentage off
		uasort($coupons, array($this, 'uasort_discount_types'));

		$total_discount = 0;
		// 2. apply the coupons in order
		foreach ($coupons as $coupon) {
			// 2.1. check minimum total requirement
			if (isset($coupon['coupon_minimum_total']) && $total < $coupon['coupon_minimum_total']) {
				$errors[] = sprintf(__('Coupon "%s" requires a minimum charge of %s', 'quitenicebooking'), $coupon['coupon_code'], Quitenicebooking_Utilities::format_price(Quitenicebooking_Utilities::float_to_user_price($coupon['coupon_minimum_total'], $this->settings, TRUE), $this->settings));
				unset($coupon_codes[array_search($coupon['coupon_code'], $coupon_codes)]);
				continue;
			}
			// 2.2. check stacking restriction
			if (empty($coupon['coupon_stackable']) && count($coupon_codes) > 1) {
				$errors[] = sprintf(__('Coupon "%s" cannot be used in conjunction with other coupons', 'quitenicebooking'), $coupon['coupon_code']);
				unset($coupon_codes[array_search($coupon['coupon_code'], $coupon_codes)]);
				continue;
			}

			// determine coupon type
			switch ($coupon['coupon_discount_type']) {
				case 'flat':
					$discount[] = array(
						'description' => sprintf(__('Discount: %s off', 'quitenicebooking'), Quitenicebooking_Utilities::format_price(Quitenicebooking_Utilities::float_to_user_price($coupon['coupon_discount_flat'], $this->settings, TRUE), $this->settings)),
						'amount' => '-'.Quitenicebooking_Utilities::format_price(Quitenicebooking_Utilities::float_to_user_price($coupon['coupon_discount_flat'], $this->settings, TRUE), $this->settings),
						'type' => 'flat',
						'calc' => $coupon['coupon_discount_flat']
					);
					$total_discount += $coupon['coupon_discount_flat'];
					$total -= $coupon['coupon_discount_flat'];
					$total = $total < 0 ? 0 : $total;
					break;
				case 'percentage':
					$discount[] = array(
						'description' => sprintf(__('Discount: %s%% off', 'quitenicebooking'), $coupon['coupon_discount_percentage']),
						'amount' => '-'.Quitenicebooking_Utilities::format_price(Quitenicebooking_Utilities::float_to_user_price(round(($coupon['coupon_discount_percentage'] / 100) * $total, 2), $this->settings, TRUE), $this->settings),
						'type' => 'percentage',
						'calc' => $coupon['coupon_discount_percentage']
					);
					$total_discount += ($coupon['coupon_discount_percentage'] / 100) * $total;
					$total = round((1 - ($coupon['coupon_discount_percentage'] / 100)) * $total, 2);
					$total = $total < 0 ? 0 : $total;
					break;
				case 'duration':
					foreach ($breakdown as &$room) {
						// build an array of the day prices
						$day_prices = array();
						foreach ($room['breakdown'] as $day => $vals) {
							$day_prices[$day] = $vals['subtotal'];
						}
						// check whether requirements are met
						if (count($day_prices) >= $coupon['coupon_discount_duration_requirement']) {
							asort($day_prices);
							// trim the array
							if ($coupon['coupon_discount_duration_mode'] == 'deduct_highest') {
								$day_prices = array_slice($day_prices, (0 - $coupon['coupon_discount_duration_units']), NULL, TRUE);
							} else {
								$day_prices = array_slice($day_prices, 0, $coupon['coupon_discount_duration_units'], TRUE);
							}
							// sum the x most/least expensive days
							$discount_amt = 0;
							foreach ($room['breakdown'] as $day => $vals) {
								if (in_array($day, array_keys($day_prices))) {
									$discount_amt += $room['breakdown'][$day]['subtotal'];
								}
							}
							$total_discount += $discount_amt;
							$discount[] = array(
								'description' => sprintf(_n('Discount: %s night free', 'Discount: %s nights free', $coupon['coupon_discount_duration_units'], 'quitenicebooking'), $coupon['coupon_discount_duration_units']),
								'amount' => '-'.Quitenicebooking_Utilities::format_price(Quitenicebooking_Utilities::float_to_user_price($discount_amt, $this->settings, TRUE), $this->settings),
								'type' => 'duration',
								'calc' => $discount_amt
							);
						}
						// don't popup "else" error message nor unset coupon because bookings can be of varying lengths
					}
					unset($room);
					$total -= $total_discount;
					$total = $total < 0 ? 0 : $total;
					break;
			} // end switch
		} // end foreach
		return $total_discount;
	}

	/**
	 * Callback for uasort() sorting coupon types
	 *
	 * In order of priority: duration, flat, percentage
	 *
	 * @param array $a The value in question
	 * @param array $b The value being compared to
	 * @return 0 if equal; 1 if $a > $b; -1 if $a < $b
	 */
	public function uasort_discount_types($a, $b) {
		if ($a['coupon_discount_type'] == 'duration') {
			if ($b['coupon_discount_type'] == 'duration') {
				return 0;
			}
			else {
				return -1;
			}
		} elseif ($a['coupon_discount_type'] == 'flat') {
			if ($b['coupon_discount_type'] == 'flat') {
				return 0;
			}
			if ($b['coupon_discount_type'] == 'duration') {
				return 1;
			}
			if ($b['coupon_discount_type'] == 'percentage') {
				return -1;
			}
		} elseif ($a['coupon_discount_type'] == 'percentage') {
			if ($b['coupon_discount_type'] == 'percentage') {
				return 0;
			}
			else {
				return 1;
			}
		}
		// catch any other cases
		return 0;
	}

	/**
	 * Gets a coupon id
	 *
	 * @global WPDB $wpdb
	 * @param string $coupon_code
	 * @return int The post ID of the coupon
	 */
	public function get_coupon_id($coupon_code) {
		global $wpdb;
		return $wpdb->get_var($wpdb->prepare(
"SELECT post_id FROM {$wpdb->postmeta}
INNER JOIN {$wpdb->posts}
ON {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
WHERE {$wpdb->posts}.post_status = 'publish' AND meta_key = 'quitenicebooking_coupon_code' AND meta_value = %s",
			$coupon_code
		));
	}

	/**
	 * Gets a coupon
	 *
	 * @global WPDB $wpdb
	 * @param string $coupon_code
	 * @return array An array containing the coupon data
	 */
	public function get_coupon($coupon_code) {
		global $wpdb;
		$coupon_id = $this->get_coupon_id($coupon_code);
		if ($coupon_id === NULL) {
			return array();
		}
		$meta = get_post_meta($coupon_id);

		$coupon = array();
		foreach ($this->keys as $key => $defs) {
			if (isset($meta[$defs['meta_key']][0])) {
				$coupon[$key] = $meta[$defs['meta_key']][0];
			}
		}
		return $coupon;
	}

	/**
	 * Uses coupons and decrement their usage limit
	 *
	 * @param array $coupons
	 */
	public function use_coupons($coupon_codes) {
		foreach ($coupon_codes as $coupon_code) {
			$coupon = $this->get_coupon($coupon_code);
			if (!empty($coupon['coupon_usage_limit'])) {
				update_post_meta($this->get_coupon_id($coupon_code), $this->keys['coupon_usage_limit']['meta_key'], $coupon['coupon_usage_limit'] - 1);
			}
		}
	}

}
