<?php
/**
 * Plugin class
 *
 * @package colbycomms/colby-wp-schedule
 */

namespace ColbyComms\Schedules;

/**
 * Add general hooks for this plugin.
 */
class Plugin {
	/**
	 * Add action hooks.
	 */
	public function __construct() {
		add_filter( 'carbon_fields_map_field_api_key', [ __CLASS__, 'get_google_maps_api_key' ] );
	}

	/**
	 * Returns the google maps api key set through the plugin options.
	 *
	 * @return string The key.
	 */
	public static function get_google_maps_api_key() : string {
		return carbon_get_theme_option( 'wp_schedule_google_maps_api_key' ) ?: '';
	}
}
