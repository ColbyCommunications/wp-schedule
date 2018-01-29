<?php
/**
 * EventMeta.php
 *
 * @package wp-schedule
 */

namespace ColbyComms\Schedules\Event;

use Carbon_Fields\{Field, Container};
use Carbon_Fields\Helper\Helper as Carbon;
use ColbyComms\Schedules\Utils\WpFunctions as WP;

/**
 * Add hooks to register meta fields for events posts.
 */
class EventMeta {
	/**
	 * Date meta key.
	 *
	 * @var string
	 */
	const DATE_KEY = 'colby_schedule__date';

	/**
	 * Start time meta key.
	 *
	 * @var string
	 */
	const START_TIME_KEY = 'colby_schedule__start_time';

	/**
	 * End time meta key.
	 *
	 * @var string
	 */
	const END_TIME_KEY = 'colby_schedule__end_time';

	/**
	 * Location meta key.
	 *
	 * @var string
	 */
	const LOCATION_KEY = 'colby_schedule__location';

	/**
	 * Do map meta key.
	 *
	 * @var string
	 */
	const DO_MAP_KEY = 'colby_schedule__do_map';

	/**
	 * Map meta key.
	 *
	 * @var string
	 */
	const MAP_KEY = 'colby_schedule__map';

	/**
	 * Constructor function; add all hooks.
	 */
	public function __construct() {
		WP::add_action(
			'carbon_fields_register_fields',
			[ $this, 'register_details_meta_box' ]
		);

		WP::add_action(
			'carbon_fields_register_fields',
			[ $this, 'register_fields' ]
		);
	}

	/**
	 * Creates the box that will contain meta fields.
	 */
	public function register_details_meta_box() {
		$this->details_box = Container::make( 'post_meta', 'Event Details' )
			->where( 'post_type', '=', Event::POST_TYPE_NAME );
	}

	/**
	 * Provides an array of fields to add.
	 *
	 * @return array The fields.
	 */
	public static function get_fields() {
		return [
			Field::make( 'date', self::DATE_KEY, 'Date' ),

			Field::make( 'time', self::START_TIME_KEY, 'Start Time' ),

			Field::make( 'time', self::END_TIME_KEY, 'End Time' ),

			Field::make( 'text', self::LOCATION_KEY, 'Location' ),

			Field::make( 'checkbox', self::DO_MAP_KEY, 'Show map location?' )
				->set_default_value( false ),

			Field::make( 'map', self::MAP_KEY, 'Location (Map)' )
				->set_position( 44.563869, -69.662636, 17 )
				->set_help_text( 'Drag and drop the pin on the map.' )
				->set_conditional_logic(
					[
						[
							'field' => self::DO_MAP_KEY,
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

	/**
	 * Get a meta field.
	 *
	 * @param string $key A meta key.
	 * @param int|string $id Post ID.
	 * @return mixed The retrieved value.
	 */
	public static function get( string $key, $id = null ) {
		static $cache;

		$cache = $cache ?: [];

		$id = $id ?: WP::get_the_id();

		if ( isset( $cache[ "$key$id" ] ) ) {
			return $cache[ "$key$id" ];
		}

		$value = Carbon::get_post_meta( $id, $key );

		$cache[ "$key$id" ] = $value;

		return $value;
	}
}
