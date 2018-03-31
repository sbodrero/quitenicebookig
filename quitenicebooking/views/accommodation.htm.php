<ul class="accommodation-listing clearfix">
	<?php foreach ($quitenicebooking_rooms as $room) { ?>
		<li class="col">
			<a href="<?php echo $room['permalink'] ?>" rel="bookmark" title="<?php echo $room['title_attribute']; ?>">
				<?php if (!empty($room['post_thumbnail'])) { ?>
					<img src="<?php echo $room['post_thumbnail']; ?>" alt="" class="accommodation-thumb" />
				<?php } ?>
			</a>
			<h3 class="title-style1">
				<a href="<?php echo $room['permalink']; ?>" rel="bookmark" title="<?php echo $room['title_attribute']; ?>"><?php echo $room['title']; ?></a>
				<span class="title-block"></span>
			</h3>
			
			<ul class="no-style-list">
				<?php
					$bed_count = FALSE;
					$bed_str = '';
					foreach ($room['beds_available'] as $bed) {
						if ($bed['available']) {
							$bed_str .= $bed['description'].', ';
							$bed_count = TRUE;
						}
					}
					$bed_str = substr($bed_str, 0, -2);
					if ($bed_count) { ?>
					<li>
						<strong><?php _e('Beds', 'quitenicebooking'); ?>:</strong> <?php echo $bed_str; ?>
					</li>
				<?php } ?>
				<?php if (!empty($room['num_bedrooms']) && $room['num_bedrooms'] > 1) { ?>
					<li>
						<strong><?php _e('Number of Bedrooms', 'quitenicebooking'); ?>:</strong> <?php echo $room['num_bedrooms']; ?>
					</li>
				<?php } ?>
				<?php if (!empty($room['max_occupancy'])) { ?>
					<li>
						<strong><?php _e('Occupancy', 'quitenicebooking'); ?>:</strong> <?php echo $room['max_occupancy']; ?>
					</li>
				<?php } ?>
				<?php if (!empty($room['room_size'])) { ?>
					<li>
						<strong><?php _e('Size', 'quitenicebooking'); ?>:</strong> <?php echo $room['room_size']; ?>
					</li>
				<?php } ?>
				<?php if (!empty($room['room_view'])) { ?>
					<li>
						<strong><?php _e('View', 'quitenicebooking'); ?>:</strong> <?php echo $room['room_view']; ?>
					</li>
				<?php } ?>
				<?php if (!empty($room['base_price'])) { ?>
					<li>
						<strong><?php _e('Rates From', 'quitenicebooking'); ?>:</strong> <?php echo Quitenicebooking_Utilities::format_price(Quitenicebooking_Utilities::float_to_user_price($room['base_price'], $this->settings, TRUE), $this->settings,true); echo ' / '; _e('Week', 'quitenicebooking'); ?>
					</li>
				<?php } ?>
			</ul>
			
			<a href="<?php echo $room['permalink']; ?>" class="button1 button-space1"><?php _e('Lodging Details', 'quitenicebooking'); ?></a>
			<?php if (empty($this->settings['hide_booking_system'])) { ?>
			<a class="button2" href="<?php echo add_query_arg(array('highlight' => $room['id']), $quitenicebooking_step_1_url); ?>"><?php _e('Check Availability', 'quitenicebooking'); ?></a>
			<?php } ?>
		</li>
	<?php } ?>
</ul>

<?php if (isset($quitenicebooking_errors)) { ?>
	<p><?php echo $quitenicebooking_errors[0]; ?></p>
<?php } ?>

<?php if ($quitenicebooking_max_num_pages > 1) { ?>
	<div class="clearboth"></div>
	<hr class="space2" />
	
	<?php if (is_plugin_active('wp-pagenavi/wp-pagenavi.php')) { ?>
		<div class="fr">
			<?php wp_pagenavi(array('query' => $quitenicebooking_query)); ?>
		</div>
		<div class="clearboth"></div>
	<?php } else { ?>
		<div class="pagination-wrapper">
			<p class="clearfix">
				<span class="fl prev-pagination"><?php echo $quitenicebooking_previous_posts_link; ?></span>
				<span class="fr next-pagination"><?php echo $quitenicebooking_next_posts_link; ?></span>
			</p>
		</div>
	<?php } ?>
<?php } ?>
