<?php
/**
 * Creates a shortcode which renders a schedule.
 *
 * @package colby-wp-schedule
 */

namespace ColbyComms\Schedules;

use Carbon_Fields\Carbon_Fields;

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
		'include-past-events' => false,
		'active' => ''
	];

	/**
	 * Registers the shortcode callback.
	 */
	public function __construct() {
		if ( ! shortcode_exists( 'schedule' ) ) {
			add_shortcode( 'schedule', [ __CLASS__, 'schedule_shortcode' ] );
		}
		add_filter( 'query_vars', [ __CLASS__, 'add_url_query_vars' ] );
	}

	/**
	 * The [schedule] shortcode callback.
	 *
	 * @param array  $atts Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @return string The shortcode output.
	 */
	public static function schedule_shortcode( $atts = [], $content = '' ) {
		Carbon_Fields::boot();

		$attributes = shortcode_atts( self::$default_atts, $atts );
		$events_query = self::get_events_query( $attributes );

		if ( ! $events_query->have_posts() ) {
			return '';
		}
	
		$days = self::sort_posts_by_day( $events_query->posts );
		$tags = self::get_all_post_tag_ids( $events_query->posts );
		$active_tags = array_map( 'trim', explode( ',', $atts['active'] ) );

		// Sort by date.
		ksort( $days );

		ob_start();

		include 'templates/schedule.php';

		return ob_get_clean();
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
				$day = get_post_meta( $post->ID, '_colby_schedule__date', 1 );
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

				$terms = get_the_terms( $post->ID, 'event_tag' ) ?: [];

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
		$qvars[] = 'event-tag';
		return $qvars;
	}

	/**
	 * Gets a query for posts with the event post_type.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return WP_Query
	 */
	public static function get_events_query( $atts ) {
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

		$query_params = self::query_params_from_url_params( $query_params );

		// URL params take precedent.
		if ( empty( $query_params['tax_query'] ) ) {
			$query_params = self::add_params_from_shortcode_atts( $query_params, $atts );
		}

		return new \WP_Query( $query_params );
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

	/**
	 * Create query parameters from $_GET url parameters.
	 *
	 * @param array $query_params Query parameters.
	 * @return array Parameters for the WP_Query.
	 */
	private static function query_params_from_url_params( $query_params ) {
		$event_tag = get_query_var( 'event-tag' );
		if ( empty( $event_tag ) ) {
			return $query_params;
		}

		if ( ! isset( $query_params['tax_query'] ) ) {
			$query_params['tax_query'] = [];
		}

		$query_params['tax_query'][] = [
			'taxonomy' => 'event_tag',
			'field'    => 'slug',
			'terms'    => $event_tag,
		];

		return $query_params;
	}

	/**
	 * Renders all the events in a schedule.
	 *
	 * @param \WP_Query $events_query The wp_query to work with.
	 * @return string The rendered list.
	 */
	private static function get_items_html( \WP_Query $events_query ) {
		ob_start();
		while ( $events_query->have_posts() ) {
			$events_query->the_post();
			$permalink = get_the_permalink();
			the_title( "<h2><a href=$permalink>", '</a></h2>' );
			the_content();
			echo 'Location: ' . get_post_meta( get_the_ID(), '_colby_schedule__location', true ) . '<br>';
			echo 'Date: ' . get_post_meta( get_the_ID(), '_colby_schedule__date', true ) . '<br>';
			echo 'Start Time: ' . get_post_meta( get_the_ID(), '_colby_schedule__start_time', true ) . '<br>';
			echo 'End Time: ' . get_post_meta( get_the_ID(), '_colby_schedule__end_time', true ) . '<br>';
		}
		wp_reset_postdata();
		return ob_get_clean();
	}

}
