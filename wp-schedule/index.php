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

require 'pp.php';

add_action( 'after_setup_theme', [ 'Carbon_Fields\\Carbon_Fields', 'boot' ] );

new Plugin();
new Options();
new EventMeta();

add_action(
	'init', function() {
		/**
		 * Filters whether to run. Useful for theme contexts where users opt in to this feature.
		 *
		 * @var bool True to run.
		 */
		if ( apply_filters( 'colby_wp_schedule_run', true ) ) {
			new EventPost();
			new ScheduleShortcode();
		}
	}, 8
);


add_action(
	'wp_enqueue_scripts', function() {
		$key = carbon_get_theme_option( 'wp_schedule_google_maps_api_key' );
		wp_enqueue_script(
			'google-maps',
			"https://maps.googleapis.com/maps/api/js?key=$key",
			[],
			false,
			true
		);
	}, 1
);
