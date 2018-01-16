<?php
/**
 * EventPost.php
 *
 * @package colby-wp-schedule
 */

namespace ColbyComms\Schedules;

use \WP_Query;
use ColbyComms\Schedules\WpFunctions as WP;

/**
 * Setup 'event' custom post type.
 */
class EventPost extends CustomPostType {
	/**
	 * Constructor function.
	 */
	public function __construct() {
		parent::__construct( 'event' );

		WP::add_action( 'init', [ $this, 'register_event_post_type' ] );
		WP::add_action( 'init', [ $this, 'register_custom_taxonomies' ] );
		WP::add_filter( "rest_{$this->post_type}_query", [ $this, 'handle_rest_taxonomies' ], 10, 2 );
		WP::add_action( 'pre_get_posts', [ __CLASS__, 'include_terms_from_parent_in_query' ] );
	}

	/**
	 * Create 'event' custom post type.
	 */
	public function register_event_post_type() {
		WP::register_post_type( $this->post_type, $this->post_type_args );
	}

	/**
	 * Generate labels array for specified taxonomy.
	 *
	 * @param string $taxonomy Name of the taxonomy.
	 */
	private static function get_taxonomy_label_args( $taxonomy ) {
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
			self::get_taxonomy_label_args( 'Schedule' ),
			[
				'hierarchical' => true,
				'rewrite' => [
					'slug' => 'schedule',
				],
			]
		);

		WP::register_taxonomy( 'schedule_category', $this->post_type, $schedule_taxonomy_args );
		WP::register_taxonomy( 'event_tag', $this->post_type, $this->get_taxonomy_label_args( 'Event Tag' ) );

		WP::register_taxonomy_for_object_type( 'schedule_category', $this->post_type );
		WP::register_taxonomy_for_object_type( 'event_tag', $this->post_type );
	}

	/**
	 * Use category and tag slugs rather than IDs.
	 *
	 * @param array           $args Key value array of query var to query value.
	 * @param WP_REST_Request $request The request used.
	 * @return array REST args.
	 */
	public function handle_rest_taxonomies( array $args = [], WP_REST_Request $request ) : array {
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

	/**
	 * If the main query is for a schedule_category and the term is a child, include the parent
	 * term's terms in the query. This is because child terms narrow the intended audience.
	 *
	 * @param WP_Query $query The WP_Query object.
	 * @return void
	 */
	public static function include_terms_from_parent_in_query( WP_Query $query ) {
		if ( ! $query->is_main_query() ) {
			return;
		}

		$slug = WP::get_query_var( 'schedule_category' );

		if ( ! $slug ) {
			return;
		}

		$query->set( 'post_type', 'event' );
		$query->set( 'posts_per_page', -1 );

		$term = WP::get_term_by( 'slug', $slug, 'schedule_category' );
		$parents = WP::get_ancestors( $term->term_id, 'schedule_category', 'taxonomy' ) ?: [];
		$term_parents_query = array_map(
			function( $term_id ) {
				$the_term = WP::get_term_by( 'id', $term_id, 'schedule_category' );

				return [
					'taxonomy' => 'schedule_category',
					'field' => 'slug',
					'terms' => $the_term->slug,
				];
			},
			$parents
		);

		$query->set(
			'tax_query',
			[
				'relation' => 'OR',
				$term_parents_query,
			]
		);
	}
}
