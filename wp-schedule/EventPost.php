<?php
/**
 * EventPost.php
 *
 * @package colby-wp-schedule
 */

namespace ColbyComms\Schedules;

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
		add_filter( "rest_{$this->post_type}_query", [ $this, 'handle_rest_taxonomies' ], 10, 2 );
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
			'show_in_rest' => true,
			'labels'       => [
				'name'          => "{$taxonomy}s",
				'singular_name' => "{$taxonomy}",
				'add_new_item'  => "Add New {$taxonomy}",
				'search_items'  => "Search {$taxonomy}s",
			],
			'show_admin_column' => true,
		];
	}

	/**
	 * Register taxonomies for 'event' post type.
	 */
	public function register_custom_taxonomies() {
		$schedule_taxonomy_args = array_merge(
			$this->get_taxonomy_label_args( 'Schedule' ), [ 'hierarchical' => true ]
		);

		register_taxonomy( 'schedule_category', $this->post_type, $schedule_taxonomy_args );
		register_taxonomy( 'event_tag', $this->post_type, $this->get_taxonomy_label_args( 'Event Tag' ) );

		register_taxonomy_for_object_type( 'schedule_category', $this->post_type );
		register_taxonomy_for_object_type( 'event_tag', $this->post_type );
	}

	/**
	 * Use category and tag slugs rather than IDs.
	 *
	 * @param array           $args Key value array of query var to query value.
	 * @param WP_REST_Request $request The request used.
	 */
	public function handle_rest_taxonomies( $args, $request ) {
		if ( $this->exists_in_request( 'schedule_category', $request ) ) {
			$args['schedule_category'] = $request['schedule_category'];
		}
		if ( $this->exists_in_request( 'event_tag', $request ) ) {
			$args['event_tag'] = $request['event_tag'];
		}
		return $args;
	}

	/**
	 * Check if value isset in request object and not empty.
	 *
	 * @param string          $key Array key to check.
	 * @param WP_REST_Request $request A request object.
	 */
	private function exists_in_request( $key, $request ) {
		return isset( $request[ $key ] ) && ! empty( $request[ $key ] );
	}

}
