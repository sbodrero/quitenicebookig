<?php get_header(); ?>

<?php
$current_theme = wp_get_theme();
$soho_theme_active = ($current_theme['Name'] == 'Soho Hotel') || ($current_theme['Template'] == 'sohohotel');
if ($soho_theme_active) {
	global $smof_data;
	global $post;
	// Get Header Image
	$header_image = page_header(get_post_meta($post->ID, 'qns_page_header_image_url', true));
	// Get Content ID/Class
	$content_id_class = content_id_class(get_post_meta($post->ID, 'qns_page_sidebar', true));
	if (strpos($content_id_class, 'full-width')) {
		$content_id_class = str_replace('full-width', 'left-main-content', $content_id_class);
	}
	// get sidebar position
	$sidebar_position = $smof_data['sidebar_position'];
	if ($sidebar_position == 'none') {
		$sidebar_position = 'right';
	}
	wp_reset_query();
}

global $quitenicebooking;
?>

<?php global $post;

$idheader = get_post_meta($post->ID,'_header_accommodation_name_image',true);
$url = wp_get_attachment_url( $idheader, full);
?>
<div id="page-header" style="background:url('<?php echo $url; ?>') !important; " >

	<span class=titleh1> <h1><?php the_title(); ?></h1></span>
</div>

<!-- BEGIN .content-wrapper -->
<div class="content-wrapper clearfix">

	<!-- BEGIN .main-content -->
	<div class="main-content <?php echo $soho_theme_active ? $content_id_class : 'left-main-content'; ?>">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<?php $attachments = get_post_meta($post->ID, '_slideshow_images', true); // get attachments ?>
			<?php if ( $attachments ) : ?>
				<!-- BEGIN .slider -->
				<div class="slider accommodation-slider">
					<!-- BEGIN .slides -->
					<ul class="slides">
						<?php $attachments_array = array_filter(explode( ',', $attachments )); ?>
						<?php if ($attachments_array) : // display attachments ?>
							<?php foreach ($attachments_array as $attachment_id) : ?>
								<?php $link = wp_get_attachment_link($attachment_id, 'accommodation-full', false); ?>
									<li>
										<?php echo $link; ?>
										<?php if (get_post_field('post_excerpt', $attachment_id) != '') : ?>
											<?php echo '<div class="flex-caption">' . get_post_field('post_excerpt', $attachment_id) . '</div>'; ?>
										<?php endif; ?>
									</li>
							<?php endforeach; ?>
						 <?php endif; ?>
					<!-- END .slides -->
					</ul>
				<!-- END .slider -->
				</div>
			<?php endif; // attachments ?>
				
			<!-- BEGIN .page-content -->
			<div class="page-content">
				<h2 class="title-style1"><?php _e('Room Description', 'quitenicebooking'); ?><span class="title-block"></span></h2>
				<?php the_content(); ?>
				<hr class="space1" />
				
				<!-- BEGIN .tabs -->
				<div class="tabs">
					<ul class="nav clearfix">
						<?php foreach (range(1, 5) as $r) : ?>
							<?php $tab_title[$r] = get_post_meta($post->ID, 'quitenicebooking_tab_'.$r.'_title', TRUE); ?>
							<?php $tab_content[$r] = get_post_meta($post->ID, 'quitenicebooking_tab_'.$r.'_content', TRUE); ?>
						<?php endforeach; ?>
						
						<?php foreach (range(1, 5) as $r) : ?>
							<?php if ($tab_title[$r]) : ?>
								<li><a href="#tabs-tab-title-<?php echo $r; ?>"><?php echo $tab_title[$r]; ?></a></li>
							<?php endif; ?>
						<?php endforeach; ?>
					</ul>
					
					<?php foreach (range(1, 5) as $r) : ?>
						<?php if ($tab_title[$r]) : ?>
							<div id="tabs-tab-title-<?php echo $r; ?>">
								<?php echo do_shortcode($tab_content[$r]); ?>
							</div>
						<?php endif; ?>
					<?php endforeach; ?>

				<!-- END .tabs -->
				</div>
				
			<!-- END .page-content -->
			</div>
			
		<?php endwhile; endif; ?>
	<!-- END .main-content -->
	</div>
	
	<!-- BEGIN .sidebar -->
	<div class="sidebar <?php echo $soho_theme_active ? $sidebar_position.'-sidebar' : 'right-sidebar'; ?>">

		<?php if (empty($quitenicebooking->settings['hide_booking_system'])) { ?>
		<!-- BEGIN .widget -->
		<div class="widget">
			<h3 class="title-style3"><?php _e('Reserve This Room', 'quitenicebooking'); ?><span class="title-block"></span></h3>
			
			<!-- BEGIN .widget-reservation-box -->
			<div class="widget-reservation-box">
				<form class="booking-form" action="<?php echo get_permalink($quitenicebooking->settings['step_1_page_id']); ?>" method="POST">
					<div class="room-price-widget">
						<p class="from"><?php _e('Room From', 'quitenicebooking'); ?></p>
						<h3 class="price">
							<?php
							$lowest_price = $quitenicebooking->accommodation_post->get_lowest_price($post->ID);
							echo Quitenicebooking_Utilities::format_price(Quitenicebooking_Utilities::float_to_user_price($lowest_price, $quitenicebooking->settings, TRUE), $quitenicebooking->settings,true);
							?>
						</h3>
						<p class="price-detail"><?php _e('Per Week', 'quitenicebooking') ?></p> 
					</div>
				
					<input type="text" id="datefrom" name="room_all_checkin" value="<?php _e('Check In', 'quitenicebooking'); ?>" class="datepicker">
					<input type="text" id="dateto" name="room_all_checkout" value="<?php _e('Check Out','quitenicebooking'); ?>" class="datepicker">
					
					<div class="select-wrapper">
						<select id="room_1_adults" name="room_1_adults">
							<option value="0" selected><?php _e('Adults', 'quitenicebooking'); ?></option>
							<?php foreach (range(0, $quitenicebooking->settings['max_persons_in_form']) as $r) { ?>
								<option value="<?php echo $r; ?>"><?php echo $r; ?></option>
							<?php } ?>
						</select>
					</div>
					
					<?php if (empty($quitenicebooking->settings['remove_children'])) { ?>
					<div class="select-wrapper">
						<select id="room_1_children" name="room_1_children">
							<option value="0" selected><?php _e('Children', 'quitenicebooking'); ?></option>
							<?php foreach (range(0, $quitenicebooking->settings['max_persons_in_form']) as $r) { ?>
								<option value="<?php echo $r; ?>"><?php echo $r; ?></option>
							<?php } ?>
						</select>
					</div>
					<?php } else { ?>
					<input type="hidden" name="room_1_children" value="0" />
					<?php } ?>
					
					<input type="hidden" name="room_qty" value="1" />
					<input type="hidden" name="highlight" value="<?php echo $post->ID; ?>" />
					<input class="bookbutton" name="booking_step_1_submit" type="submit" value="<?php _e('Check Availability', 'quitenicebooking'); ?>" />
				</form>
				<?php if (!empty($quitenicebooking->settings['multiroom_link'])) { ?>
				<p class="multiroom-link"><a href="<?php echo add_query_arg(array('highlight' => $post->ID), get_permalink($quitenicebooking->settings['step_1_page_id'])); ?>"><?php _e('Multi-room booking?', 'quitenicebooking'); ?></a></p>
				<?php } ?>
			<!-- END .widget-reservation-box -->
			</div>
		<!-- END .widget -->
		</div>
		<?php } // end if ?>
		
		<!-- BEGIN .widget -->
		<?php if ($quitenicebooking->settings['phone_number'] || $quitenicebooking->settings['fax_number'] || $quitenicebooking->settings['email_address']) { ?>
		<div class="widget">
			<h3 class="title-style3"><?php _e('Direct Reservations', 'quitenicebooking'); ?><span class="title-block"></span></h3>

			<ul class="contact_details_list">
				<?php if ($quitenicebooking->settings['phone_number']) { ?>
					<li class="phone_list"><strong><?php _e('Phone', 'quitenicebooking'); ?>:</strong> <?php echo $quitenicebooking->settings['phone_number']; ?></li>
				<?php } ?>

				<?php if ($quitenicebooking->settings['fax_number']) { ?>
					<li class="fax_list"><strong><?php _e('Fax', 'quitenicebooking'); ?>:</strong> <?php echo $quitenicebooking->settings['fax_number']; ?></li>
				<?php } ?>

				<?php if ($quitenicebooking->settings['email_address']) { ?>
					<li class="email_list"><strong><?php _e('Email', 'quitenicebooking'); ?>:</strong> <a href="mailto:<?php echo $quitenicebooking->settings['email_address']; ?>"><?php echo $quitenicebooking->settings['email_address']; ?></a></li>
				<?php } ?>
			</ul>
		<!-- END .widget -->
		</div>
		<?php } ?>
		
		<?php if ($soho_theme_active && !empty($quitenicebooking->settings['hide_booking_system'])) { ?>
			<?php dynamic_sidebar( 'primary-widget-area' ); ?>
		<?php } ?>
	<!-- END .sidebar -->
	</div>
	
<!-- END .content-wrapper -->
</div>

<?php get_footer(); ?>
