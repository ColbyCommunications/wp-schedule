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
		WP::add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue_scripts_and_styles' ] );
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

	/**
	 * Enqueues plugin assets.
	 *
	 * @return void
	 */
	public static function enqueue_scripts_and_styles() : void {
		$dist = self::get_dist_directory();
		$min = defined( 'PROD' ) && PROD ? '.min' : '';
		/**
		 * Filters whether to enqueue this plugin's script.
		 *
		 * @param bool Yes or no.
		 */
		if ( apply_filters( 'colbycomms__wp_schedule__enqueue_script', true ) === true ) {
			wp_enqueue_script(
				TEXT_DOMAIN,
				"{$dist}colby-wp-schedule$min.js",
				[],
				VERSION,
				true
			);
		}

		/**
		 * Filters whether to enqueue this plugin's stylesheet.
		 *
		 * @param bool Yes or no.
		 */
		if ( apply_filters( 'colbycomms__wp_schedule__enqueue_style', true ) === true ) {
			wp_enqueue_style(
				TEXT_DOMAIN,
				"{$dist}colby-wp-schedule$min.css",
				[],
				VERSION
			);
		}

		wp_enqueue_style(
			TEXT_DOMAIN . '-print',
			"{$dist}colby-wp-schedule-print$min.css",
			[],
			VERSION,
			isset( $_GET['print'] ) ? 'all' : 'print'
		);
	}

	/**
	 * Gets the plugin's dist/ directory URL, whether this package is installed as a plugin
	 * or in a theme via composer. If the package is in neither of those places and the filter
	 * is not used, this whole thing will fail.
	 *
	 * @return string The URL.
	 */
	public static function get_dist_directory() : string {
		/**
		 * Filters the URL location of the /dist directory.
		 *
		 * @param string The URL.
		 */
		$dist = apply_filters( 'colbycomms__whos_coming__dist', '' );
		if ( ! empty( $dist ) ) {
			return $dist;
		}

		if ( file_exists( dirname( __DIR__, 3 ) . '/plugins' ) ) {
			return plugin_dir_url( dirname( __DIR__ ) . '/index.php' ) . '/dist/';
		}

		return get_template_directory_uri() . '/vendor/colbycomms/colby-wp-schedule/dist/';
	}
}
