<?php
/**
 * Plugin class
 *
 * @package colbycomms/colby-wp-schedule
 */

namespace ColbyComms\Schedules;

use Carbon_Fields\Helper\Helper;
use ColbyComms\Schedules\WpFunctions as WP;

/**
 * Add general hooks for this plugin.
 */
class Plugin {
	/**
	 * Add action hooks.
	 */
	public function __construct() {
		WP::add_filter( 'carbon_fields_map_field_api_key', [ __CLASS__, 'get_google_maps_api_key' ] );
		WP::add_action( 'init', [ __CLASS__, 'maybe_run' ], 8 );
		WP::add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue_google_script' ], 1 );
	}

	/**
	 * Returns the google maps api key set through the plugin options.
	 *
	 * @return string The key.
	 */
	public static function get_google_maps_api_key() : string {
		return Helper::get_theme_option( 'wp_schedule_google_maps_api_key' ) ?: '';
	}

	/**
	 * Maybe initiate plugin classes.
	 *
	 * @return void
	 */
	public static function maybe_run() {
		/**
		 * Filters whether to run. Useful for theme contexts where users opt in to this feature.
		 *
		 * @param bool True to run.
		 */
		if ( WP::apply_filters( 'colby_wp_schedule_run', true ) ) {
			new EventPost();
			new Shortcodes\ScheduleShortcode();
			new Shortcodes\SchedulePickerShortcode();
		}
	}

	/**
	 * Enqueues the Google Maps javascript file.
	 *
	 * @return void
	 */
	public static function enqueue_google_script() {
		$key = Helper::get_theme_option( 'wp_schedule_google_maps_api_key' );
		WP::wp_enqueue_script(
			'google-maps',
			"https://maps.googleapis.com/maps/api/js?key=$key",
			[],
			false,
			true
		);
	}
}
