<?php
/**
 * Quitenicebooking booking block class
 *
 * @package quitenicebooking
 * @author Quite Nice Stuff
 * @copyright Copyright (c) 2013 Quite Nice Stuff
 * @link http://quitenicestuff.com
 * @version 2.5.9
 * @since 2.4.0
 */
class Quitenicebooking_Booking_Block_Post {
	/**
	 * Properties ==============================================================
	 */

	/**
	 * @var array The global settings
	 */
	public $settings;

	/**
	 * @var array All of the accommodations
	 */
	public $accommodations;

	/**
	 * Methods =================================================================
	 */

	/**
	 * Constructor
	 * 
	 * Registers post type, actions, filters
	 */
	public function __construct() {
		// settings and accommodation_post should be poked by the invoker during init
		// register post type
		add_action('init', array($this, 'register_post_type'));
		// add admin menu
		add_action('admin_menu', array($this, 'add_admin_menu'));
		// add meta box
		add_action('add_meta_boxes', array($this, 'add_meta_box'));
		// enqueue scripts
		add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
		// save meta
		add_action('save_post', array($this, 'save_meta'));
	}

	/**
	 * Registers the Booking_Block post type
	 */
	public function register_post_type() {
		register_post_type(
			'booking_block',
			array(
				'public' => TRUE, // implies: show_ui => true, publicy_queryable => true, exclude_from_search => true
				'supports' => array(
					'title'
				),
				'labels' => array(
					'name' => __('Blocked Dates', 'quitenicebooking'),
					'singular_name' => __('Blocked Dates', 'quitenicebooking'),
					'add_new' => _x('Add New', 'block dates', 'quitenicebooking'),
					'add_new_item' => __('Add New Blocked Dates', 'quitenicebooking')
				),
				'menu_position' => 5,
				'show_in_menu' => 'edit.php?post_type=booking',
				'show_in_menu' => FALSE,
				'show_in_nav_menus' => FALSE
			)
		);
	}

	public function add_admin_menu() {
		add_submenu_page(
			'edit.php?post_type=booking',
			__('Blocked Dates', 'quitenicebooking'),
			__('Blocked Dates', 'quitenicebooking'),
			'manage_options',
			'edit.php?post_type=booking_block'
		);
		add_submenu_page(
			'edit.php?post_type=booking',
			__('Blocked Dates', 'quitenicebooking'),
			__('Add Blocked Dates', 'quitenicebooking'),
			'manage_options',
			'post-new.php?post_type=booking_block'
		);
	}


	/**
	 * Returns the dynamic keys for this post
	 *
	 * @global WPDB $wpdb
	 * @param int $id The post ID
	 * @return array An array of keys
	 */
	public function get_dynamic_keys($id) {
		global $wpdb;
		$room_keys = $wpdb->get_results($wpdb->prepare(
			"SELECT * FROM $wpdb->postmeta WHERE post_id = %d AND meta_key LIKE %s",
			$id,
			'quitenicebooking_booking_block_id_%'
		), ARRAY_A);
		$dynamic_keys = array();
		$dynamic_keys[] = 'quitenicebooking_booking_block_startdate';
		$dynamic_keys[] = 'quitenicebooking_booking_block_enddate';
		foreach ($room_keys as $r) {
//			$dynamic_keys[] = 'quitenicebooking_booking_block_id_'.$r['meta_key'][0];
//			$dynamic_keys[] = $r;
		}
		return $dynamic_keys;
	}

	/**
	 * Enqueues scripts
	 *
	 * @global WP_Post $post
	 */
	public function admin_enqueue_scripts() {
		global $post;
		if (is_object($post) && get_post_type($post->ID) == 'booking_block') {
			// datepicker
			wp_enqueue_script('jquery-ui-datepicker');

			// js
			wp_register_script('quitenicebooking-booking-block-admin', plugins_url('assets/js/admin/booking_block_meta.js', plugin_dir_path(__FILE__)));
			wp_enqueue_script('quitenicebooking-booking-block-admin');
			wp_localize_script('quitenicebooking-booking-block-admin', 'quitenicebooking_premium', array(
				'js_date_format' => $this->settings['date_format_strings'][$this->settings['date_format']]['js']
			));

			// css
			wp_enqueue_style('jquery-ui-core', QUITENICEBOOKING_URL.'assets/css/admin/jquery.ui.core.css');
			wp_enqueue_style('jquery-ui-theme', QUITENICEBOOKING_URL.'assets/css/admin/jquery.ui.theme.css');
			wp_enqueue_style('jquery-ui-datepicker', QUITENICEBOOKING_URL.'assets/css/admin/jquery.ui.datepicker.css');
			wp_enqueue_style('quitenicebooking-admin-shared', QUITENICEBOOKING_URL.'assets/css/admin/shared.css');
			wp_enqueue_style('quitenicebooking-booking-block-admin', plugins_url('assets/css/admin/booking_block_meta.css', plugin_dir_path(__FILE__)));
		}
	}

	/**
	 * Adds the meta box
	 */
	public function add_meta_box() {
		add_meta_box(
			'booking_block_meta',
			__('Block Details', 'quitenicestuff'),
			array($this, 'show_meta_box'),
			'booking_block',
			'normal'
		);
	}

	/**
	 * Shows the meta box
	 *
	 * @global WP_Post $post
	 */
	public function show_meta_box() {
		global $post;
		$meta = get_post_meta($post->ID);

		foreach ($meta as $meta_key => $meta_val) {
			if (preg_match('/^quitenicebooking_booking_block_/', $meta_key)) {
				${$meta_key} = $meta_val[0];
			}
		}
		$quitenicebooking_booking_block_startdate = !empty($quitenicebooking_booking_block_startdate) ? Quitenicebooking_Utilities::to_datestring($quitenicebooking_booking_block_startdate, $this->settings) : '';
		$quitenicebooking_booking_block_enddate = !empty($quitenicebooking_booking_block_enddate) ? Quitenicebooking_Utilities::to_datestring($quitenicebooking_booking_block_enddate, $this->settings) : '';

		$date_format = $this->settings['date_format'];
		$date_format_strings = $this->settings['date_format_strings'];
		include plugin_dir_path(__FILE__) . '../views/admin/booking_block_meta.htm.php';
	}

	/**
	 * Saves the meta defined in this class
	 *
	 * @param int $id The post ID
	 * @param array $post The $_POST array
	 */
	public function save_meta($id) {
		// check autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}

		// check whether this is a booking_block post type
		if (!isset($_REQUEST['post_type']) || $_REQUEST['post_type'] != 'booking_block') {
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
		foreach ($input_post as $post_key => $post_val) {
			if (preg_match('/^quitenicebooking_booking_block_/', $post_key)) {
				if (preg_match('/^quitenicebooking_booking_block_(start|end)date$/', $post_key)) {
					// convert dates to unix time and save
					$date = Quitenicebooking_Utilities::to_timestamp(sanitize_text_field($post_val), $this->settings);
					update_post_meta($id, $post_key, $date);
				} else {
					// save
					update_post_meta($id, $post_key, sanitize_text_field($post_val));
				}
			}
		}
	}

	/**
	 * Checks whether a date block is active for the given dates and accommodation
	 *
	 * @param int $checkin The checkin unix timestamp
	 * @param int $checkout The checkout unix timestamp
	 * @param int $accommodation_id The post ID of the accommodation
	 * @global WPDB $wpdb
	 * @return boolean TRUE if block is active, FALSE otherwise
	 */
	public function is_blocked($checkin, $checkout, $accommodation_id) {
		global $wpdb;
		// 1. get all booking_block IDs that affect this room
		$rows = $wpdb->get_results($wpdb->prepare(
"SELECT post_id FROM {$wpdb->postmeta}
INNER JOIN {$wpdb->posts}
ON {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
WHERE {$wpdb->posts}.post_status = %s AND meta_key = %s AND meta_value = %d",
			'publish',
			'quitenicebooking_booking_block_id_'.$accommodation_id,
			1
		), ARRAY_A);
		// 2. for each of these rows, check if they are blocking the checkin and checkout dates
		// the criteria is checkin < enddate or checkout > startdate
		// 2.1. create a pivot table of the startdate and enddates only
		foreach ($rows as $row) {
			$blocked = $wpdb->get_var($wpdb->prepare(
"SELECT COUNT(*) FROM
(SELECT
GROUP_CONCAT(IF(meta_key = 'quitenicebooking_booking_block_startdate', meta_value, NULL)) AS 'startdate',
GROUP_CONCAT(IF(meta_key = 'quitenicebooking_booking_block_enddate', meta_value, NULL)) AS 'enddate'
FROM {$wpdb->postmeta} WHERE post_id=%d
) AS a
WHERE startdate < %d AND enddate > %d",
				$row['post_id'],
				$checkout,
				$checkin
			));
			// if a block is found, break and return
			if ($blocked > 0) {
				return TRUE;
			}
		}
		return FALSE;
	}
}
