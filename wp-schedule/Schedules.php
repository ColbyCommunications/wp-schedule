<?php
/**
 * Plugin class
 *
 * @package colbycomms/wp-schedule
 */

namespace ColbyComms\Schedules;

use ColbyComms\Schedules\Utils\WpFunctions as WP;
use ColbyComms\Schedules\Schedule\ScheduleCategoryArchive;
use ColbyComms\Schedules\Blocks\EventBlock;

/**
 * Add general hooks for this plugin.
 */
class Schedules {
	/**
	 * This plugin's package name.
	 *
	 * @var string
	 */
	const PACKAGE_NAME = 'colbycomms/wp-schedule';

	/**
	 * The plugin text domain.
	 *
	 * @var string
	 */
	const TEXT_DOMAIN = 'wp-schedule';

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	const VERSION = '1.0.1';

	/**
	 * Whether this is a production environment.
	 *
	 * @var bool
	 */
	const PROD = false;

	/**
	 * A string prepended to all filter names.
	 *
	 * @var string
	 */
	const FILTER_NAMESPACE = 'colbycomms__wp_schedule__';

	const BEFORE_SCHEDULE_ARCHIVE_FILTER = self::FILTER_NAMESPACE . 'before_schedule_archive';
	const AFTER_SCHEDULE_ARCHIVE_FILTER = self::FILTER_NAMESPACE . 'after_schedule_archive';
	const USE_SCHEDULE_CATEGORY_TEMPLATE_FILTER = self::FILTER_NAMESPACE . 'use_schedule_category_template';
	const DO_EVENT_SHORTCODE_FILTER = self::FILTER_NAMESPACE . 'add_' . EventBlock::SHORTCODE_TAG . '_shortcode';
	const ENQUEUE_SCRIPT_FILTER = self::FILTER_NAMESPACE . 'enqueue_script';
	const ENQUEUE_STYLE_FILTER = self::FILTER_NAMESPACE . 'enqueue_style';
	const ENQUEUE_PRINT_STYLE_FILTER = self::FILTER_NAMESPACE . 'enqueue_print_style';
	const DIST = self::FILTER_NAMESPACE . 'dist';

	/**
	 * Add action hooks.
	 */
	public function __construct() {
		new ThemeOptions();
		new Event\EventMeta();
		new Schedule\ScheduleMeta();

		WP::add_action( 'after_setup_theme', [ '\\Carbon_Fields\\Carbon_Fields', 'boot' ] );
		WP::add_filter( 'carbon_fields_map_field_api_key', [ __CLASS__, 'get_google_maps_api_key' ] );
		WP::add_action( 'init', [ __CLASS__, 'maybe_run' ], 8 );
		WP::add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue_google_script' ], 1 );
		WP::add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue_scripts_and_styles' ] );
		WP::add_filter( 'query_vars', [ __CLASS__, 'add_print_query_var' ] );
		WP::add_filter( 'template_include', [ __CLASS__, 'use_schedule_category_template' ] );
	}

	/**
	 * Add 'print' to accepted query vars.
	 *
	 * @param array $query_vars Query vars array.
	 * @return array Modified array.
	 */
	public static function add_print_query_var( array $query_vars ) : array {
		$query_vars[] = 'print';

		return $query_vars;
	}

	/**
	 * Returns the google maps api key set through the plugin options.
	 *
	 * @return string The key.
	 */
	public static function get_google_maps_api_key() : string {
		return ThemeOptions::get( ThemeOptions::GOOGLE_MAPS_API_KEY_KEY ) ?: '';
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
			new Event\Event();
			new Schedule\Schedule();
			new Blocks\ScheduleBlock();
			new Blocks\SchedulePickerBlock();
			new Blocks\EventBlock();
		}
	}

	/**
	 * Enqueues the Google Maps javascript file.
	 *
	 * @return void
	 */
	public static function enqueue_google_script() {
		$key = ThemeOptions::get( ThemeOptions::GOOGLE_MAPS_API_KEY_KEY );

		if ( ! $key ) {
			return;
		}

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
		$min = self::PROD ? '.min' : '';

		/**
		 * Filters whether to enqueue this plugin's script.
		 *
		 * @param bool Yes or no.
		 */
		if ( apply_filters( self::ENQUEUE_SCRIPT_FILTER, true ) === true ) {
			wp_die();
			wp_enqueue_script(
				self::TEXT_DOMAIN,
				"{$dist}wp-schedule$min.js",
				[],
				self::VERSION,
				true
			);
		}

		/**
		 * Filters whether to enqueue this plugin's stylesheet.
		 *
		 * @param bool Yes or no.
		 */
		if ( apply_filters( self::ENQUEUE_STYLE_FILTER, true ) ) {
			wp_enqueue_style(
				self::TEXT_DOMAIN,
				"{$dist}wp-schedule$min.css",
				[],
				self::VERSION
			);
		}

		/**
		 * Filters whether to enqueue this plugin's print stylesheet.
		 *
		 * @param bool Yes or no.
		 */
		if ( apply_filters( self::ENQUEUE_PRINT_STYLE_FILTER, true ) ) {
			wp_enqueue_style(
				self::TEXT_DOMAIN . '-print',
				"{$dist}wp-schedule-print$min.css",
				[],
				self::VERSION,
				get_query_var( 'print' ) ? 'all' : 'print'
			);
		}
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
		$dist = apply_filters( self::DIST, '' );
		if ( ! empty( $dist ) ) {
			return $dist;
		}

		if ( file_exists( dirname( __DIR__, 3 ) . '/plugins' ) ) {
			return plugin_dir_url( dirname( __DIR__ ) . '/index.php' ) . '/dist/';
		}

		return get_template_directory_uri() . '/vendor/colbycomms/wp-schedule/dist/';
	}

	/**
	 * Maybe use the schedule_category archive template provided by this plugin.
	 *
	 * @param string $template_path The template path provided by WordPress.
	 * @return string The possibly modified template path.
	 */
	public static function use_schedule_category_template( string $template_path ) : string {
		if ( ! ScheduleCategoryArchive::doing_schedule_category_archive() ) {
			return $template_path;
		}

		/**
		 * Filters whether to use the schedule_category archive template this plugin provides.
		 *
		 * @param bool True by default.
		 */
		if ( ! apply_filters( self::USE_SCHEDULE_CATEGORY_TEMPLATE_FILTER, true ) ) {
			return $template_path;
		}

		return __DIR__ . '/taxonomy-schedule_category.php';
	}
}
