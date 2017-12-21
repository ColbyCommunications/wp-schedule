<?php
/**
 * EventPost.php
 *
 * @package colby-wp-schedule
 */

namespace Colby\Schedules;

/**
 * Setup 'event' custom post type.
 */
class EventPost extends CustomPostType {
	/**
	 * Constructor function.
	 */
	public function __construct() {
		parent::__construct( 'event' );

		add_action( 'init', [ $this, 'register_event_post_type' ] );
		add_action( 'init', [ $this, 'register_custom_taxonomies' ] );
	}

	/**
	 * Create 'event' custom post type.
	 */
	public function register_event_post_type() {
		register_post_type( $this->post_type, $this->post_type_args );
	}

	/**
	 * Generate labels array for specified taxonomy.
	 *
	 * @param string $taxonomy Name of the taxonomy.
	 */
	private function get_taxonomy_label_args( $taxonomy ) {
		return [
			'labels' => [
				'name'          => "{$taxonomy}s",
				'singular_name' => "{$taxonomy}",
				'add_new_item'  => "Add New {$taxonomy}",
				'search_items'  => "Search {$taxonomy}s",
			],
		];
	}

	/**
	 * Register taxonomies for 'event' post type.
	 */
	public function register_custom_taxonomies() {
		$schedule_taxonomy_args = array_merge(
			$this->get_taxonomy_label_args( 'Schedule' ), [ 'hierarchical' => true ]
		);

		register_taxonomy( 'schedule', $this->post_type, $schedule_taxonomy_args );
		register_taxonomy( 'event_tag', $this->post_type, $this->get_taxonomy_label_args( 'Event Tag' ) );

		register_taxonomy_for_object_type( 'schedule', $this->post_type );
		register_taxonomy_for_object_type( 'event_tag', $this->post_type );
	}

}
