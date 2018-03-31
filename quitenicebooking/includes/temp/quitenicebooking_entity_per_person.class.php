<?php
/**
 * Defines the 'per_person' entity
 */
class Quitenicebooking_Entity_Per_Person {
	/**
	 * Properties ==============================================================
	 */

	/**
	 * @var array Key definitions; see constructor
	 */
	public $keys;

	/**
	 * Methods =================================================================
	 */

	/**
	 * Constructor
	 * Creates a new instance of this class, defines keys
	 */
	public function __construct() {
		$this->keys = array();
		$this->keys['adult']['meta_part'] = 'adult';
		$this->keys['adult']['description'] = __('Per Adult', 'quitenicebooking');
		$this->keys['child']['meta_part'] = 'child';
		$this->keys['child']['description'] = __('Per Child', 'quitenicebooking');
	}

	/**
	 * Calculate unit price
	 *
	 * @param float $base_price The base price
	 * @param string $key Either 'adult' or 'child'
	 * @param array $args Parameters
	 *		array('adults' => int, 'children' => int)
	 * @return float The unit price
	 */
	public function calc($base_price, $key, $args) {
//		echo 'calling='.$key.'<br>';
		$total = 0;

		if ($key == 'adult') {
			if (!empty($this->price_rules)) {
				for ($adults = 0; $adults < $args['adults']; $adults ++) {
					if ($adults == 0) {
						$total += $base_price;
						continue;
					}
					$this->next_price_rule();
//					echo 'loop'.$adults.'adult current price rule='.$this->current_price_rule.'<br>';
//					echo 'current rule='; print_r($this->price_rules[$this->current_price_rule]['adult'][$args['daily_meta']]); echo '<br>';
					$total += $this->price_rules[$this->current_price_rule]['adult'][$args['daily_meta']];
				}
				$this->reset_price_rule();
//				echo 'total adult='.$total;
				// return price filtered with rules
				return $total;
			}
//			echo 'base adult='.$base_price * $args['adults'];
			// return original calculation when no price rules exist
			return $base_price * $args['adults'];
		}
		if ($key == 'child') {
			if (!empty($this->price_rules)) {
				for ($children = 0; $children < $args['children']; $children ++) {
					if ($children == 0) {
						$total += $base_price;
						continue;
					}
					$this->next_price_rule();
//					echo 'children current price rule='.$this->current_price_rule.'<br>';
					$total += $this->price_rules[$this->current_price_rule]['child'][$args['daily_meta']];
				}
				$this->reset_price_rule();
//				echo 'total child='.$total;
				// return price filtered with rules
				return $total;
			}
//			echo 'base child='.$base_price * $args['children'];
			// return original calculation when no price rules exist
			return $base_price * $args['children'];
		}
//		$adult = $args['adults'];
//		$child = $args['children'];
//		return $base_price * ${$key};
	}

	/**
	 * Load price rules
	 */
	public function load_price_rules($type, $date) {
		$this->price_rules = array();

		$meta = get_post_meta($type);
		$filter = array();
		$num_filters = 0;
		$active_filter = NULL;
		if (has_filter('calc_unit_price')) {
			// load price filter
			foreach ($meta as $k => $v) {
				if (preg_match('/quitenicebooking_price_filter_(\d)_startdate/', $k)) {
					$num_filters ++;
				}
			}
//			echo 'num_filters='.$num_filters.'<br>';
			if ($num_filters > 0) {
				for ($i = 1; $i <= $num_filters; $i ++) {
					if ($meta["quitenicebooking_price_filter_{$i}_startdate"][0] <= $date && $date <= $meta["quitenicebooking_price_filter_{$i}_enddate"][0]) {
						$active_filter = $i;
					}
				}
//				echo 'active_filter='.$active_filter.'<br>';
			}
		}
		if ($active_filter != NULL) {
			// filter is active, load its price rules
			foreach ($meta as $k => $v) {
				if (preg_match('/quitenicebooking_price_filter_'.$active_filter.'_price_rule_(\d+)_(.+)_(.+)/', $k, $m)) {
					$this->price_rules[$m[1]][$m[2]][$m[3]] = $v[0];
				}
			}
//			echo 'filter date='.$date;
//			echo 'filter price rules=';
//			print_r($this->price_rules);
		} else {
			// if $active_filter == NULL, do the below
			foreach ($meta as $k => $v) {
				if (preg_match('/^quitenicebooking_price_rule_(\d+)_(.+)_(.+)/', $k, $m)) {
					$this->price_rules[$m[1]][$m[2]][$m[3]] = $v[0];
				}
			}
//			echo 'date='.$date;
//			echo 'price rules=';
//			print_r($this->price_rules);
		}
	}

	/**
	 * Return the current price rule
	 */
	public function next_price_rule() {
		if (empty($this->price_rules)) {
			return;
		}
		if (!isset($this->current_price_rule)) {
			$this->current_price_rule = 1;
		} else {
			$this->current_price_rule = count($this->price_rules) > $this->current_price_rule ? $this->current_price_rule + 1 : $this->current_price_rule;
		}
	}
	/**
	 * Reset price rule
	 */
	public function reset_price_rule() {
		if (empty($this->price_rules)) {
			return;
		}
		unset($this->current_price_rule);
	}
}
