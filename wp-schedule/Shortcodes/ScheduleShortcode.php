<?php
/**
 * Creates a shortcode which renders a schedule.
 *
 * @package colby-wp-schedule
 */

namespace ColbyComms\Schedules\Shortcodes;

use ColbyComms\Schedules\{WpQuery, WpFunctions as WP};

/**
 * Shortcode [schedule].
 */
class ScheduleShortcode {
	/**
	 * Default shortcode attributes.
	 *
	 * @var array
	 */
	public static $default_atts = [
		'name'                => null,
		'tags'                => null,
		'include-past-events' => 'false',
		'active' => '',
		'tag-selector' => 'true',
		'show-description' => 'false',
	];

	/**
	 * Registers the shortcode callback.
	 */
	public function __construct() {
		if ( ! WP::shortcode_exists( 'schedule' ) ) {
			WP::add_shortcode( 'schedule', [ __CLASS__, 'schedule_shortcode' ] );
		}

		WP::add_filter( 'query_vars', [ __CLASS__, 'add_url_query_vars' ] );
	}

	/**
	 * Set up variables to send to the template.
	 *
	 * @param WpQuery $events_query A query for events posts.
	 * @param array $atts Shortcode attributes.
	 * @param mixed $term The term if we're on a term archive page.
	 * @return string The shortcode output.
	 */
	public static function render( WpQuery $events_query, array $atts = [], $term = null ) : string {
		$atts = WP::shortcode_atts( self::$default_atts, $atts );
		$days = self::sort_posts_by_day( $events_query->posts );
		$tags = self::get_all_post_tag_ids( $events_query->posts );

		// Make an array from the active tags passed to the shortcode.
		$active_tags = array_map( 'trim', explode( ',', isset( $atts['active'] ) ? $atts['active'] : '' ) );

		// Sort by date.
		ksort( $days );

		ob_start();

		include dirname( __DIR__ ) . '/templates/schedule.php';

		return ob_get_clean();
	}

	/**
	 * The [schedule] shortcode callback.
	 *
	 * @param array  $atts Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @return string The shortcode output.
	 */
	public static function schedule_shortcode( $atts = [], $content = '' ) {
		$events_query = self::get_events_query( $attributes );

		if ( ! $events_query->have_posts() ) {
			return '';
		}

		return self::render( $events_query, $atts );
	}

	/**
	 * Sort an array of posts into an associative array sorted by date.
	 *
	 * @param array $posts A set of WP_Post objects.
	 * @return array The resulting associative array.
	 */
	public static function sort_posts_by_day( $posts = [] ) {
		return array_reduce(
			$posts,
			function( $output, $post ) {
				$day = WP::get_post_meta( $post->ID, '_colby_schedule__date', 1 );
				$output[ $day ][] = $post;
				return $output;
			},
			[]
		);
	}

	/**
	 * Gets an array of unique event_tag term objects from a group of posts.
	 *
	 * @param array $posts WP_Post objects.
	 * @return array The tag ids.
	 */
	public static function get_all_post_tag_ids( $posts = [] ) {
		return array_reduce(
			$posts,
			function( $output, $post ) {
				static $term_ids_in_output;

				if ( empty( $term_ids_in_output ) ) {
					$term_ids_in_output = [];
				}

				$terms = WP::get_the_terms( $post->ID, 'event_tag' ) ?: [];

				if ( ! $terms && ! in_array( 0, $term_ids_in_output, true ) ) {
					$term_ids_in_output[] = 0;
					$output[] = (object) [
						'term_id' => 0,
						'name' => 'Uncategorized',
					];
				}

				foreach ( $terms as $term ) {
					// The term has not been captured yet.
					if ( ! in_array( $term->term_id, $term_ids_in_output, true ) ) {
						$term_ids_in_output[] = $term->term_id;
						$output[] = $term;
					}
				}

				return $output;
			},
			[]
		);
	}

	/**
	 * Add 'event-tag' to query vars.
	 *
	 * @param array $qvars Query variables.
	 * @return array Updated query variables.
	 */
	public static function add_url_query_vars( $qvars ) {
		$qvars[] = 'invitee-group';
		return $qvars;
	}

	/**
	 * Gets a query for posts with the event post_type.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return WpQuery The query wrapper.
	 */
	public static function get_events_query( array $atts = [] ) : WpQuery {
		$query_params = [
			'post_type'      => 'event',
			'posts_per_page' => 99,
			'meta_query'     => [
				'relation'     => 'AND',
				'schedule_date' => [
					'key'     => '_colby_schedule__date',
					'value'   => date( 'Y-m-d' ),
					'compare' => '>',
				],
				'schedule_time' => [
					'key'     => '_colby_schedule__start_time',
					'compare' => 'EXISTS',
				],
			],
			'orderby' => [
				'schedule_date' => 'ASC',
				'schedule_time' => 'ASC',
			],
		];

		// URL params take precedent.
		if ( empty( $query_params['tax_query'] ) ) {
			$query_params = self::add_params_from_shortcode_atts( $query_params, $atts );
		}

		return new WpQuery( $query_params );
	}

	/**
	 * Create query parameters from shortcode attributes.
	 *
	 * @param array $query_params Query parameters.
	 * @param array $atts Shortcode attributes.
	 * @return array Parameters for the WP_Query.
	 */
	private static function add_params_from_shortcode_atts( $query_params, $atts ) {
		if ( isset( $atts['name'] ) || isset( $atts['tags'] ) ) {
			$query_params['tax_query'] = [];
		}

		if ( isset( $atts['name'] ) ) {
			$query_params['tax_query'][] = [
				'taxonomy' => 'schedule_category',
				'field'    => 'name',
				'terms'    => [ "{$atts['name']}" ],
			];
		}

		if ( isset( $atts['tags'] ) ) {
			$query_params['tax_query'][] = [
				'taxonomy' => 'event_tag',
				'field'    => 'name',
				'terms'    => explode( ',', $atts['tags'] ),
			];
		}

		// Show events that have passed.
		if ( false !== $atts['include-past-events'] ) {
			unset( $query_params['meta_query']['schedule_date']['value'] );
			$query_params['meta_query']['schedule_date']['compare'] = 'EXISTS';

		}
		return $query_params;
	}
}
