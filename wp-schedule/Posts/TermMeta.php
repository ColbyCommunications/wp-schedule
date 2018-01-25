<?php
/**
 * EventMeta.php
 *
 * @package colby-wp-schedule
 */

namespace ColbyComms\Schedules\Posts;

use Carbon_Fields\{Field, Container};
use ColbyComms\Schedules\Utils\WpFunctions as WP;

/**
 * Add hooks to register meta fields for events posts.
 */
class TermMeta {
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
		$this->details_box = Container::make( 'term_meta', 'Event Details' )
			->where( 'term_taxonomy', '=', 'schedule_category' );
	}

	/**
	 * Provides an array of fields to add.
	 *
	 * @return array The fields.
	 */
	public static function get_fields() {
		return [
			Field::make( 'checkbox', 'colby_schedule__do_tag_list', 'Show tag list?' )
				->set_default_value( true )
				->set_help_text( 'Set to true to show a list of checkboxes to filter the events on this schedule\'s archive page.' ),
			Field::make( 'checkbox', 'colby_schedule__hide_description', 'Hide descriptions?' )
				->set_default_value( true )
				->set_help_text( 'Set to true to show the event descriptions in an expandable drawer. If this is set to false, events\' maps will not be shown.' )
		];
	}

	/**
	 * Adds the location field to the details box.
	 */
	public function register_fields() {
		$this->details_box->add_fields( self::get_fields() );
	}
}
