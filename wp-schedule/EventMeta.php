<?php
/**
 * EventMeta.php
 */

namespace Colby\Schedules;

/**
 * Add hooks to register meta fields for events posts.
 */
class EventMeta {
	public function __construct() {

		$this->titan = \TitanFramework::getInstance( 'colby-wp-schedule' );

		add_action( 'tf_create_options', [ $this, 'register_details_meta_box' ] );
		add_action( 'tf_create_options', [ $this, 'register_details_meta_fields' ] );
	}

	/**
	 * Creates the box that will contain meta fields.
	 */
	public function register_details_meta_box() {
		$this->details_box = $this->titan->createMetaBox(
			[
				'name' => 'Event Details',
				'post_type' => 'schedule',

			]
		);
	}

	/**
	 * Adds meta fields to the details box.
	 */
	public function register_details_meta_fields() {
		// Create the options fields.
		$this->details_box->createOption(
			[
				'name' => 'Location',
				'id' => 'event-location',
				'type' => 'text',
				'desc' => 'Enter the event location',
			]
		);
	}
}
