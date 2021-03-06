<?php
/**
 * Quitenicebooking price filters class
 * 
 * @package quitenicebooking
 * @author Quite Nice Stuff
 * @copyright Copyright (c) 2013 Quite Nice Stuff
 * @link http://quitenicestuff.com
 * @version 2.5.9
 * @since 2.4.0
 */
class Quitenicebooking_Price_Filters {
	
	/**
	 * @var Quitenicebooking_Settings An instance of the settings class
	 */
	public $settings_class;
	
	/**
	 * @var array The global plugin settings
	 */
	public $settings;
	
	/**
	 * @var Quitenicebooking_Prices_* The price class
	 */
	public $price_class;
	
	/**
	 * Constructor
	 * 
	 * @param array $settings The global plugin settings
	 */
	public function __construct($settings) {
		add_action('admin_init', array($this, 'admin_init'));
		add_filter('calc_unit_price', array($this, 'filter_base_price'));
		$this->settings = $settings;
		
		// NOTE! this class is NOT attached to a post ID
		// alternatively, since it's dynamic, save_meta should only save meta that matches a regex, such as
		// /quitenicebooking_price_filters_(\d+)_(.+)_(.+)/
	}
	
	/**
	 * Admin hooks
	 */
	public function admin_init() {
		add_action('quitenicebooking_add_price_filters_meta_box', array($this, 'add_price_filters_meta_box'));
		add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
		
		// instantiate the price class
		$price_class = 'Quitenicebooking_Prices_'.ucfirst($this->settings['pricing_scheme']);
		$this->price_class = new $price_class($this->settings['entity_scheme']);

//		add_action('posts_selection', array($this, 'init'));
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
		$quitenicebooking_prices = $this->price_class;

		$num_filters = $wpdb->get_var($wpdb->prepare(
			"SELECT COUNT(*) FROM $wpdb->postmeta WHERE post_id = %d AND meta_key LIKE %s",
			$id,
			'quitenicebooking_price_filter_%_startdate'
		));

		$filter_keys = array();

		for ($f = 1; $f <= $num_filters; $f ++) {
			$filter_keys[] = "quitenicebooking_price_filter_{$f}_startdate";
			$filter_keys[] = "quitenicebooking_price_filter_{$f}_enddate";

			foreach ($quitenicebooking_prices->keys['composed'] as $composed_key) {
				$filter_keys[] = "quitenicebooking_price_filter_{$f}_".$composed_key['meta_part'];
			}
		}
		return $filter_keys;
	}
	
	/**
	 * Adds meta boxes to Accommodation post admin
	 */
	public function add_price_filters_meta_box() {
		add_meta_box(
			'price_filters_meta', // id
			__('Seasonal Price Filters', 'quitenicebooking'), // title
			array($this, 'show_price_filters_meta_box'), // callback
			'accommodation', // post_type
			'normal' // context
		);
	}
	
	/**
	 * Displays the Price Filters meta box
	 * 
	 * @global WP_Post $post
	 */
	public function show_price_filters_meta_box() {
		global $post;
		
		$quitenicebooking_prices = $this->price_class;
		
		$quitenicebooking_currency_unit = $this->settings['currency_unit'];
		
		$quitenicebooking_num_price_filters = $this->get_num_filters($post->ID);
		$quitenicebooking_num_price_filter_rules = $this->get_num_rules($post->ID);
		$meta = get_post_meta($post->ID);
		for ($f = 1; $f <= $quitenicebooking_num_price_filters; $f ++) {
			${"quitenicebooking_price_filter_{$f}_startdate"} = Quitenicebooking_Utilities::to_datestring($meta["quitenicebooking_price_filter_{$f}_startdate"][0], $this->settings);
			${"quitenicebooking_price_filter_{$f}_enddate"} = Quitenicebooking_Utilities::to_datestring($meta["quitenicebooking_price_filter_{$f}_enddate"][0], $this->settings);
			foreach ($quitenicebooking_prices->keys['composed'] as $composed_key) {
				${"quitenicebooking_price_filter_{$f}_".$composed_key['meta_part']} = isset($meta["quitenicebooking_price_filter_{$f}_".$composed_key['meta_part']][0]) ? $meta["quitenicebooking_price_filter_{$f}_".$composed_key['meta_part']][0] : 0;
			}
			if (is_a($quitenicebooking_prices->entity_scheme, 'Quitenicebooking_Entity_Per_Person')) {
				$em = array('adult_weekday', 'adult_weekend', 'child_weekday', 'child_weekend');
				for ($r = 1; $r <= $quitenicebooking_num_price_filter_rules[$f]; $r ++) {
					foreach ($em as $e) {
						${"quitenicebooking_price_filter_{$f}_price_rule_{$r}_{$e}"} = isset($meta["quitenicebooking_price_filter_{$f}_price_rule_{$r}_{$e}"][0]) ? Quitenicebooking_Utilities::float_to_user_price($meta["quitenicebooking_price_filter_{$f}_price_rule_{$r}_{$e}"][0], $this->settings, FALSE) : 0;
					}
				}

			}
		}
		include plugin_dir_path(__FILE__) . '../views/admin/accommodation_price_filters_meta.htm.php';
	}
	
	/**
	 * Get the number of price filters for this post
	 *
	 * @param int $id The post ID
	 * @global WPDB $wpdb
	 */
	protected function get_num_filters($id) {
		global $wpdb, $post;
		
		return $wpdb->get_var($wpdb->prepare(
			"SELECT COUNT(*) FROM $wpdb->postmeta WHERE post_id = %d AND meta_key LIKE %s",
			$id,
			'quitenicebooking_price_filter_%_startdate'
		));
	}

	/**
	 * Get the number of price rules for each price filter in this post
	 *
	 * @param int $id The post ID
	 * @global WPDB $wpdb
	 */
	protected function get_num_rules($id) {
		global $wpdb;

		$num_filters = $wpdb->get_var($wpdb->prepare(
			"SELECT COUNT(*) FROM $wpdb->postmeta WHERE post_id = %d AND meta_key LIKE %s",
			$id,
			'quitenicebooking_price_filter_%_startdate'
		));
		
		$num_rules = array();
		for ($i = 1; $i <= $num_filters; $i ++) {
			$num_rules[$i] = 0;
		}

		foreach ($num_rules as $key => &$val) {
			$val = $wpdb->query($wpdb->prepare(
				"SELECT * FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key LIKE %s",
				$id,
				'quitenicebooking_price_filter_'.$key.'_price_rule_%_adult_weekday'
			));
		}
		unset($val);
		
		return $num_rules;
	}
	
	/**
	 * Enqueues scripts for Accommodation post admin
	 * 
	 * @global	WP_Post	$post
	 */
	public function admin_enqueue_scripts() {
		global $post;
		if (is_object($post) && get_post_type($post->ID) == 'accommodation') {
			wp_enqueue_script('jquery-ui-sortable');
			
			wp_register_script('quitenicebooking-price-filters-admin', plugins_url('assets/js/admin/price_filters_meta.js', plugin_dir_path(__FILE__)));
			wp_enqueue_script('quitenicebooking-price-filters-admin');
			
			wp_localize_script('quitenicebooking-price-filters-admin', 'quitenicebooking_premium', array(
				'num_price_filters' => $this->get_num_filters($post->ID),
				'num_price_filter_price_rules' => $this->get_num_rules($post->ID)
			));
		}
	}
	
	/**
	 * Filters the base price of an accommodation
	 * 
	 * @param array $args Parameters
	 *		array(
	 *			'base_price' => float // the base price
	 *			'meta_name' => string // the meta name, e.g. 'room_weekday', 'room_weekend', etc.
	 *			'date' => int // unix timestamp of the date being queried
	 *			'id' => int // accommodation post ID
	 *		)
	 * @global WPDB $wpdb
	 * @return float The filtered base price
	 */
	public function filter_base_price($args) {
		global $wpdb;
		
		// 1. load the accommodation's meta
		$meta = get_post_meta($args['id']);
		
		// 1.1. get number of filters
		$num_filters = $wpdb->get_var($wpdb->prepare(
			"SELECT COUNT(*) FROM $wpdb->postmeta WHERE post_id = %d AND meta_key LIKE %s",
			$args['id'],
			'quitenicebooking_price_filter_%_startdate'
		));

		// 2. for each of the filters, if $args['date'] falls between _startdate and _enddate,
		//    set it as the filtered price, replacing any previous values
		for ($f = 1; $f <= $num_filters; $f ++) {
			if ($args['date'] >= $meta["quitenicebooking_price_filter_{$f}_startdate"][0]
				&& $args['date'] < $meta["quitenicebooking_price_filter_{$f}_enddate"][0]) {
				if (isset($meta["quitenicebooking_price_filter_{$f}_{$args['meta_name']}"][0])) {
					$filtered_price = $meta["quitenicebooking_price_filter_{$f}_{$args['meta_name']}"][0];
				}
			}
		}
		// 3. return the filtered price
		if (isset($filtered_price)) {
			return $filtered_price;
		}
		
		return $args['base_price'];
	}
	
	/**
	 * Saves the meta defined in this class
	 * 
	 * @param int $id The post ID
	 * @param array $post The $_POST array
	 * @global WPDB $wpdb
	 */
	public function save_meta($id, $post) {

		if (!isset($post)) {
			return;
		}
		
		// check if it is submitted by quick edit form
		if (isset($post['action']) && $post['action'] == 'inline-save') {
			return;
		}

		$num_filters = 0;
		$num_price_rules = array();
		foreach ($post as $post_key => $post_val) {
			if (preg_match('/^quitenicebooking_price_filter_\d+_/', $post_key)) {
				if (preg_match('/^quitenicebooking_price_filter_\d+_(start|end)date$/', $post_key)) {
					// update price filter dates, change to unix time
					$date = Quitenicebooking_Utilities::to_timestamp(sanitize_text_field($post_val), $this->settings);
					update_post_meta($id, $post_key, $date);
				} elseif (preg_match('/^quitenicebooking_price_filter_\d+_(?:adult|child|room)_week(?:day|end)$/', $post_key)) {
					// update price filter prices
					update_post_meta($id, $post_key, sanitize_text_field(Quitenicebooking_Utilities::user_price_to_float($post_val, $this->settings)));
				} elseif (preg_match('/^quitenicebooking_price_filter_\d+_price_rule_\d+_(?:adult|child)_week(?:day|end)$/', $post_key)) {
					// update price filter price rules
					update_post_meta($id, $post_key, sanitize_text_field(Quitenicebooking_Utilities::user_price_to_float($post_val, $this->settings)));
				}
				// increment fiter count
				if (preg_match('/^quitenicebooking_price_filter_\d+_startdate$/', $post_key)) {
					$num_filters ++;
				}
				if (preg_match('/^quitenicebooking_price_filter_(\d+)_price_rule_\d+_adult_weekday$/', $post_key, $m)) {
					if (empty($num_price_rules[$m[1]])) {
						$num_price_rules[$m[1]] = 0;
					}
					$num_price_rules[$m[1]] ++;
				}
			}
		}
		// delete all filters above $num_filters

		global $wpdb;
		// first, get the total number of filters for this post
		$fn = $this->get_num_filters($id); // cache result before deletion
		for ($i = $num_filters+1; $i <= $fn; $i ++) {
			// delete via wpdb, otherwise orphaned entries from another entity/pricing scheme won't be deleted
			$wpdb->query($wpdb->prepare(
				"DELETE FROM $wpdb->postmeta WHERE post_id = %d AND meta_key LIKE %s",
				$id,
				'quitenicebooking_price_filter_'.$i.'%'
			));
		}

		// if all price rules for a filter has been removed, POST values are not sent, so pre-populate the array with 0s
		for ($i = 1; $i <= $num_filters; $i ++) {
			if (empty($num_price_rules[$i])) {
				$num_price_rules[$i] = 0;
			}
		}

		// delete price rules above $num_price_rules
		$num_rules = $this->get_num_rules($id);

		foreach ($num_price_rules as $k => $pr) {
			for ($r = $num_price_rules[$k] + 1; $r <= $num_rules[$k]; $r ++) {
				$wpdb->query($wpdb->prepare(
					"DELETE FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key LIKE %s",
					$id,
					"quitenicebooking_price_filter_{$k}_price_rule_{$r}_%"
				));
			}
		}
	}

}
