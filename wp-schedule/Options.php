<?php
/**
 * Options.php
 *
 * @package colbycomms/wp-schedule
 */

namespace ColbyComms\Schedules;

use Carbon_Fields\{Container, Field};
use ColbyComms\Schedules\WpFunctions as WP;

/**
 * Sets up an options page using Carbon Fields.
 */
class Options {
	/**
	 * Adds hooks.
	 */
	public function __construct() {
		WP::add_action( 'carbon_fields_register_fields', [ $this, 'create_container' ] );
		WP::add_action( 'carbon_fields_register_fields', [ $this, 'add_plugin_options' ] );
	}

	/**
	 * Creates the options page.
	 */
	public function create_container() {
		$this->container = Container::make( 'theme_options', 'Schedule Options' )
			->set_page_parent( 'plugins.php' );
	}

	/**
	 * Adds the plugin options.
	 */
	public function add_plugin_options() {
		$this->container->add_fields(
			[
				Field::make( 'text', 'wp_schedule_google_maps_api_key', 'Google Maps API key.' )
					->set_help_text( 'An API key from <a href="https://developers.google.com/maps/documentation/javascript/get-api-key">Google</a>.' ),
			]
		);
	}
}
