<?php
/**
 * Initialize the main classes. This file autoloads.
 *
 * @package colby-wp-schedule
 */

namespace ColbyComms\Schedules;

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

add_action(
	'init', function() {
		/**
		 * Filters whether to run. Useful for theme contexts where users opt in to this feature.
		 *
		 * @var bool True to run.
		 */
		if ( apply_filters( 'colby_wp_schedule_run', true ) ) {
			new EventMeta();
			new SchedulePost();
			new EventPost();
			new ScheduleShortcode();
			new SchedulePickerShortcode();
		}
	}, 8
);


// phpcs:disable Squiz.Commenting.FunctionComment.Missing,WordPress.PHP.DevelopmentFunctions.error_log_print_r
if ( ! function_exists( 'pp' ) ) {
	function pp( $data, $die = false ) {
		echo '<pre>';
		print_r( $data );
		echo '</pre>';
		if ( $die ) {
			wp_die();
		}
	}
}
