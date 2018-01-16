<?php
/**
 * EventMeta.php
 *
 * @package colby-wp-schedule
 */

namespace ColbyComms\Schedules;

use Carbon_Fields\{Field, Container};
use ColbyComms\Schedules\WpFunctions as WP;

/**
 * Add hooks to register meta fields for events posts.
 */
class EventMeta {
	/**
	 * Constructor function; add all hooks.
	 */
	public function __construct() {
		WP::add_action( 'carbon_fields_register_fields', [ $this, 'register_details_meta_box' ] );
		WP::add_action( 'carbon_fields_register_fields', [ $this, 'register_fields' ] );
	}

	/**
	 * Creates the box that will contain meta fields.
	 */
	public function register_details_meta_box() {
		$this->details_box = Container::make( 'post_meta', 'Event Details' )
			->where( 'post_type', '=', 'event' );
	}

	/**
	 * Provides an array of fields to add.
	 *
	 * @return array The fields.
	 */
	public static function get_fields() {
		return [
			Field::make( 'date', 'colby_schedule__date', 'Date' ),
			Field::make( 'time', 'colby_schedule__start_time', 'Start Time' ),
			Field::make( 'time', 'colby_schedule__end_time', 'End Time' ),
			Field::make( 'text', 'colby_schedule__location', 'Location' ),
			Field::make( 'checkbox', 'colby_schedule__do_map', 'Show map location?' )
				->set_default_value( false ),
			Field::make( 'map', 'colby_schedule__map', 'Location (Map)' )
				->set_position( 44.563869, -69.662636, 17 )
				->set_help_text( 'Drag and drop the pin on the map to select a location.' )
				->set_conditional_logic(
					[
						[
							'field' => 'colby_schedule__do_map',
							'value' => true,
						],
					]
				),
		];
	}

	/**
	 * Adds the location field to the details box.
	 */
	public function register_fields() {
		$this->details_box->add_fields( self::get_fields() );
	}
}
