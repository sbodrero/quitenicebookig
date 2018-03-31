<?php
/**
 * Quitenicebooking services class
 *
 * @package quitenicebooking
 * @author Quite Nice Stuff
 * @copyright Copyright (c) 2014 Quite Nice Stuff
 * @link http://quitenicestuff.com
 * @version 2.5.9
 * @since 2.5.9
 */

class Quitenicebooking_Service_Post {
	/**
	 * Properties ==============================================================
	 */

	/**
	 * @var array The global plugin settings
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
		// settings and accommodations should be poked by the invoker during init

		// register post type
		add_action('init', array($this, 'register_post_type'));
		// add meta box
		add_action('add_meta_boxes', array($this, 'add_meta_box'));
		// enqueue scripts
		add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
		// save meta
		add_action('save_post', array($this, 'save_meta'));
	}

	/**
	 * Register the post type
	 */
	public function register_post_type() {
		register_post_type(
			'service',
			array(
				'public' => TRUE,
				'supports' => array(
					'title',
					'editor'
				),
				'labels' => array(
					'name' => __('Services', 'quitenicebooking'),
					'singular_name' => __('Services', 'quitenicebooking'),
					'add_new' => _x('Add Service', 'service', 'quitenicebooking'),
					'add_new_item' => __('Add New Service', 'quitenicebooking')
				),
				'menu_position' => 5
			)
		);
	}

	/**
	 * Adds meta box
	 */
	public function add_meta_box() {
		add_meta_box(
			'service_meta',
			__('Service Details', 'quitenicebooking'),
			array($this, 'show_meta_box'),
			'service',
			'normal'
		);
	}

	/**
	 * Show meta box
	 *
	 * @global WP_Post $post
	 */
	public function show_meta_box() {
		global $post;
		$meta = get_post_meta($post->ID);

		foreach ($meta as $meta_key => $meta_val) {
			if (preg_match('/^quitenicebooking_service_/', $meta_key)) {
				${$meta_key} = $meta_val[0];
			}
		}
		include plugin_dir_path(__FILE__) . '../views/admin/service_details_meta.htm.php';
	}

	/**
	 * Enqueue scripts
	 *
	 * @global WP_Post $post
	 */
	public function admin_enqueue_scripts() {
		global $post;
		if (is_object($post) && get_post_type($post->ID) == 'service') {
			wp_enqueue_style('quitenicebooking-admin-shared', QUITENICEBOOKING_URL.'assets/css/admin/shared.css');
			wp_enqueue_style('quitenicebooking-service-admin', QUITENICEBOOKING_URL.'assets/css/admin/service_meta.css');
			wp_enqueue_script('quitenicebooking-service-admin', QUITENICEBOOKING_URL.'assets/js/admin/service_meta.js');
		}
	}

	/**
	 * Save the meta
	 *
	 * @param int $id The post ID
	 */
	public function save_meta($post_id) {
		// check autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}

		// check whether this is a service post type
		if (!isset($_REQUEST['post_type']) || $_REQUEST['post_type'] != 'service') {
			return;
		}

		// check permissions
		if (!current_user_can('edit_post', $post_id)) {
			return;
		}

		// update meta
		$input_post = filter_input_array(INPUT_POST);

		if ($input_post === NULL) {
			return;
		}

		foreach ($input_post as $key => $val) {
			if (preg_match('/^quitenicebooking_service_/', $key)) {
				if (preg_match('/^quitenicebooking_service_price$/', $key)) {
					update_post_meta($post_id, $key, sanitize_text_field(Quitenicebooking_Utilities::user_price_to_float($val, $this->settings)));
				} else {
					update_post_meta($post_id, $key, sanitize_text_field($val));
				}
			}
		}

	}

	/**
	 * Gets services for the requested rooms
	 *
	 * @global WPDB $wpdb
	 * @param array $room_ids An array of room ids
	 * @return array The available services and their costs
	 *		array(
	 *			array('title' => (string) $title, 'description' => (string) $description, 'price' => (float) $price),
	 *			array(...)
	 *		)
	 */
	public function get_services($room_ids) {
		if (empty($room_ids)) {
			return array();
		}

		foreach ($room_ids as &$r) {
			$r = '\'quitenicebooking_service_id_'.$r.'\'';
		}
		unset($r);
		$room_ids_sql = implode(',', $room_ids);

		global $wpdb;
		
		// get the ids of the services that match the $room_ids
		$rows = $wpdb->get_results($wpdb->prepare(
"SELECT post_id FROM {$wpdb->postmeta}
INNER JOIN {$wpdb->posts}
ON {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
WHERE {$wpdb->posts}.post_status = %s and meta_key IN ({$room_ids_sql}) AND meta_value = %d
GROUP BY post_id",
			'publish',
			1
		), ARRAY_A);

		// get each service
		$services = array();
		foreach ($rows as $r) {
			$services[$r['post_id']] = get_post($r['post_id'], ARRAY_A);
			$meta = get_post_meta($r['post_id']);
			$services[$r['post_id']]['quitenicebooking_service_price'] = $meta['quitenicebooking_service_price'][0];
		}
		return $services;
	}

	/**
	 * Gets a service
	 *
	 * @param int $post_id The post ID of the service
	 * @return array An array containing the service post
	 */
	public function get_service($post_id) {
		if (empty($post_id)) {
			return array();
		}
		$service = get_post($post_id, ARRAY_A);
		$meta = get_post_meta($post_id);
		$service['quitenicebooking_service_price'] = isset($meta['quitenicebooking_service_price'][0]) ? $meta['quitenicebooking_service_price'][0] : 0;
		return $service;
	}

}
