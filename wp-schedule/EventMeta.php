<?php
/**
 * EventMeta.php
 */

namespace Colby\Schedules;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

/**
 * Add hooks to register meta fields for events posts.
 */
class EventMeta {
	public function __construct() {
		add_action( 'after_setup_theme', [ $this, 'boot_carbon_fields' ] );
		add_action( 'carbon_fields_register_fields', [ $this, 'register_details_meta_box' ] );
		add_action( 'carbon_fields_register_fields', [ $this, 'register_fields' ] );
	}

	public function boot_carbon_fields() {
		\Carbon_Fields\Carbon_Fields::boot();
	}

	/**
	 * Creates the box that will contain meta fields.
	 */
	public function register_details_meta_box() {
		$this->details_box = Container::make( 'post_meta', 'Event Details' )
			->where( 'post_type', '=', 'event' );
	}

	/**
	 * Adds the location field to the details box.
	 */
	public function register_fields() {
		$this->details_box->add_fields(
			[
				Field::make( 'text', 'colby_schedule__location', 'Location' ),
			]
		);
	}
}
