<?php
/**
 * Options.php
 *
 * @package colbycomms/wp-schedule
 */

namespace ColbyComms\Schedules;

use Carbon_Fields\Container;
use Carbon_Fields\Field;
use Carbon_Fields\Carbon_Fields;

/**
 * Sets up an options page using Carbon Fields.
 */
class Options {
	/**
	 * Adds hooks.
	 */
	public function __construct() {
		add_action( 'carbon_fields_register_fields', [ $this, 'create_container' ] );
		add_action( 'carbon_fields_register_fields', [ $this, 'add_plugin_options' ] );
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
