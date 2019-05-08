<?php

// The poorest reccomended security I ever saw. But let it be here.
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function simple_lineup__create_planning_metabox(){
	// Can only be used on a single post type (ie. page or post or a custom post type).
	// Must be repeated for each post type you want the metabox to appear on.

	$serialized_option_value = get_option(SIMPLE_LINEUP__CONTENT_TYPES_FIELD);

	add_meta_box(
		SIMPLE_LINEUP__PLANNING_METABOX_ID, // Metabox ID
		'Shows', // Title to display
		'simple_lineup__render_planning_metabox', // Function to call that contains the metabox content
		json_decode($serialized_option_value), // Post type to display metabox on
		'side', // Where to put it (normal = main colum, side = sidebar, etc.)
		'low' // Priority relative to other metaboxes
	);

}

add_action( 'add_meta_boxes', 'simple_lineup__create_planning_metabox' );


/*
 * Render the metabox markup
 * This is the function called in `_namespace_create_metabox()`
 */
function simple_lineup__render_planning_metabox() {
	// Variables
	global $post; // Get the current post data
	global $wpdb;
	$post_id = $post->ID;

	/*
		Security field - This validates that submission came from the
		actual dashboard and not the front end or a remote server.
	*/
	wp_nonce_field(SIMPLE_LINEUP__PLANNING_NONCE_FIELD_ID, SIMPLE_LINEUP__PLANNING_NONCE_FIELD_PROCESS);

	// This is horrible. Find way to make use to some templating language.

	$current_year = (int)date('Y');
	$min_start_year = 2015;
	$max_start_year = $current_year + 1;

	$current_month = (int)date('m');

	$days_in_current_month_count = (int)date('t');
	$current_day_in_current_month = (int)date('j');

	$current_hour = (int)date('G');
	$current_minute = (int)date('i');   
	$granularized_minute = $current_minute - $current_minute % 5;

	$db_table = simple_lineup__get_db_table_name();

	// TODO: get_column should be used instead.
	// If no items = no records in database.
	$sheduled_shows = $wpdb->get_results(
		"SELECT id, start, duration, show_on_program_page
			FROM $db_table
			WHERE node_id = $post_id AND node_type = '$post->post_type'
			ORDER BY start DESC;"
	);

	?>

		<div class="sheduling-widget js-sheduling-widget">
			<!-- <strong class="sheduling-widget--heading">Add new show</strong> -->
			<div class="sheduling-widget--new-item js-sheduling-widget--new-item">
				<div class="sheduling-widget-container">

					<div class="sheduling-widget-input-group sheduling-widget-w-100 sheduling-widget--new-item--heading">
						Start date
					</div>

					<div class="sheduling-widget-input-group sheduling-widget-w-33">
						<label for="sheduling-widget--start-day">Day</label>
						<select id="sheduling-widget--start-day" class="components-text-control__input js-sheduling-widget--new-item--field" data-name="start__day">
							<?php for($day=1; $day <= $days_in_current_month_count; $day++): ?>
								<?php $selected = $day === $current_day_in_current_month ? ' selected': ''; ?>
								<option value="<?php echo $day; ?>"<?php echo $selected; ?>>
									<?php echo $day; ?>
								</option>
							<?php endfor; ?>
						</select>
					</div>

					<div class="sheduling-widget-input-group sheduling-widget-w-33">
						<label for="sheduling-widget--start-month">Month</label>
						<select id="sheduling-widget--start-month" class="components-text-control__input js-sheduling-widget--new-item--field" data-name="start__month">
							<?php for($month=1; $month <= 12; $month++): ?>
								<?php $selected = $month === $current_month  ? ' selected': ''; ?>
								<option value="<?php echo $month; ?>"<?php echo $selected; ?>>
									<?php echo $month; ?>
								</option>
							<?php endfor; ?>
						</select>
					</div>

					<div class="sheduling-widget-input-group sheduling-widget-w-33">
						<label for="sheduling-widget--start-year">Year</label>
						<select id="sheduling-widget--start-year" class="components-text-control__input js-sheduling-widget--new-item--field" data-name="start__year">
							<?php for($year=$min_start_year; $year <= $max_start_year; $year++): ?>
								<?php $selected = $year === $current_year  ? ' selected': ''; ?>
								<option value="<?php echo $year; ?>"<?php echo $selected; ?>>
									<?php echo $year; ?>
								</option>
							<?php endfor; ?>
						</select>
					</div>

					<div class="sheduling-widget-input-group sheduling-widget-w-100 sheduling-widget--new-item--heading sheduling-widget--space-top">
						Start time
					</div>

					<div class="sheduling-widget-input-group sheduling-widget-w-50">
						<label for="sheduling-widget--start-hour">Hour</label>
						<select id="sheduling-widget--start-hour" class="components-text-control__input js-sheduling-widget--new-item--field" data-name="start__hour">
							<?php for($hour=0; $hour < 24; $hour++): ?>
								<?php $selected = $hour === $current_hour  ? ' selected': ''; ?>
								<option value="<?php echo $hour; ?>"<?php echo $selected; ?>>
									<?php echo $hour; ?>
								</option>
							<?php endfor; ?>
						</select>
					</div>

					<div class="sheduling-widget-input-group sheduling-widget-w-50">
						<label for="sheduling-widget--start-minutes">Minute</label>
						<select id="sheduling-widget--start-minutes" class="components-text-control__input js-sheduling-widget--new-item--field" data-name="start__minute">
							<?php for($minute=0; $minute < 60; $minute += SIMPLE_LINEUP__MINUTE_GRANULARITY): ?>
								<?php $selected = $minute === $granularized_minute  ? ' selected': ''; ?>
								<option value="<?php echo $minute; ?>"<?php echo $selected; ?>><?php echo $minute; ?></option>
							<?php endfor; ?>
						</select>
					</div>

					<div class="sheduling-widget-input-group sheduling-widget-w-100 sheduling-widget--new-item--heading sheduling-widget--space-top">
						Show length
					</div>

					<div class="sheduling-widget-input-group sheduling-widget-w-33">
						<label for="sheduling-widget--length-days">Days</label>
						<select id="sheduling-widget--length-days" class="components-text-control__input js-sheduling-widget--new-item--field" data-name="duration__days">
							<?php for($day=0; $day < 5; $day++): ?>
								<option value="<?php echo $day; ?>"><?php echo $day; ?></option>
							<?php endfor; ?>
						</select>
					</div>

					<div class="sheduling-widget-input-group sheduling-widget-w-33">
						<label for="sheduling-widget--length-hours">Hours</label>
						<select id="sheduling-widget--length-hours" class="components-text-control__input js-sheduling-widget--new-item--field" data-name="duration__hours">
							<?php for($hour=0; $hour < 24; $hour++): ?>
								<option value="<?php echo $hour; ?>"><?php echo $hour; ?></option>
							<?php endfor; ?>
						</select>
					</div>

					<div class="sheduling-widget-input-group sheduling-widget-w-33">
						<label for="sheduling-widget--length-minutes">Minutes</label>
						<select id="sheduling-widget--length-minutes" class="components-text-control__input js-sheduling-widget--new-item--field" data-name="duration__minutes">
							<?php for($minute=0; $minute < 60; $minute += SIMPLE_LINEUP__MINUTE_GRANULARITY): ?>
								<?php  $selected = $minute === SIMPLE_LINEUP__DEFAULT_MINUTE_LENGTH  ? ' selected': ''; ?>
								<option value="<?php echo $minute; ?>"<?php echo $selected; ?>>
									<?php echo $minute; ?>
								</option>
							<?php endfor; ?>
						</select>
					</div>

					<div class="sheduling-widget-input-group sheduling-widget-w-100 sheduling-widget--new-item--heading sheduling-widget--space-top">
						Program page
					</div>

					<div class="sheduling-widget-input-group sheduling-widget-w-100">
						<input type="checkbox" id="sheduling-widget--in-program" class="js-sheduling-widget--new-item--field" data-name="show_on_program_page">
						<label for="sheduling-widget--in-program">Show on program page</label>
					</div>

				</div>

				<button type="button" class="sheduling-widget--add-button components-button editor-post-publish-button is-button is-default is-primary is-large dashicons-before dashicons-plus js-sheduling-widget--add-button">
					Add new show
				</button>

			</div>
			<strong class="sheduling-widget--heading">Shows</strong>
			<ul class="sheduling-widget--item-wrapper js-sheduling-widget--item-wrapper">
				<?php
					foreach($sheduled_shows as $key => $sheduled_show){
						simple_lineup__showbox_template(
							simple_lineup__showbox_start_format($sheduled_show->start),
							simple_lineup__showbox_duration_format($sheduled_show->duration),
							$sheduled_show->show_on_program_page,
							$sheduled_show->id,
							$key
						);
					}
				?> 
			</ul>
			<ul class="hidden js-sheduling-widget--hidden-container">
				<?php simple_lineup__showbox_template('HH:MM - D.M.YYYY', 'DDd HHh MMm', false); ?>
			</ul>
		</div>
	<?php
}


/**
 * Save the metabox
 * @param  Number $post_id The post ID
 * @param  Array  $post    The post data
 */
function simple_lineup__save_planning_metabox($post_id, $post) {

	// Verify that our security field exists. If not, bail.
	if(!isset($_POST[SIMPLE_LINEUP__PLANNING_NONCE_FIELD_PROCESS])){
		return;
	}

	// Verify data came from edit/dashboard screen
	if(!wp_verify_nonce(
			$_POST[SIMPLE_LINEUP__PLANNING_NONCE_FIELD_PROCESS],
			SIMPLE_LINEUP__PLANNING_NONCE_FIELD_ID
	)){return $post->ID;}

	// Verify user has permission to edit post
	if(!current_user_can('edit_post', $post->ID)) {
		return $post->ID;
	}

	/*
	 * 	Sanitize the submitted data
	 * 	This keeps malicious code out of our database.
	 * 	`wp_filter_post_kses` strips our dangerous server values
	 * 	and allows through anything you can include a post.
	 */
	$sanitized = wp_filter_post_kses( $_POST[SIMPLE_LINEUP__PLANNING_FIELD_ID] ?? SIMPLE_LINEUP__PLANNING_CHOICES_DEFAULT);

	// Save our submissions to the database
	update_post_meta( $post->ID, SIMPLE_LINEUP__PLANNING_FIELD_ID, $sanitized );
}

add_action('save_post', 'simple_lineup__save_planning_metabox', 1, 2);
