<?php
/**
 * Creates a shortcode which renders a schedule.
 *
 * @package colby-wp-schedule
 */

namespace ColbyComms\Schedules;

/**
 * Shortcode [schedule].
 */
class ScheduleShortcode {
	/**
	 * Registers the shortcode callback.
	 */
	public function __construct() {
		if ( ! shortcode_exists( 'schedule' ) ) {
			add_shortcode( 'schedule', [ $this, 'render_schedule' ] );
		}
		add_filter( 'query_vars', [ $this, 'add_url_query_vars' ] );
	}

	/**
	 * The [schedule] shortcode callback.
	 *
	 * @param array  $atts Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @return string The shortcode output.
	 */
	public function render_schedule( $atts = [], $content = '' ) {
		$attributes = $this->handle_shortcode_attributes( $atts );
		$events_query = $this->get_events_query( $attributes );
		if ( ! $events_query->have_posts() ) {
			return '';
		}
		$items = $this->get_items_html( $events_query );
		return $items;
	}

	/**
	 * Add 'event-tag' to query vars.
	 *
	 * @param array $qvars Query variables.
	 * @return array Updated query variables.
	 */
	public function add_url_query_vars( $qvars ) {
		$qvars[] = 'event-tag';
		return $qvars;
	}

	/**
	 * Gets a query for posts with the event post_type.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return WP_Query
	 */
	private function get_events_query( $atts ) {
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

		$query_params = $this->add_params_from_shortcode_atts( $query_params, $atts );
		$query_params = $this->query_params_from_url_params( $query_params );

		return new \WP_Query( $query_params );
	}

	/**
	 * Create query parameters from shortcode attributes.
	 *
	 * @param array $query_params Query parameters.
	 * @param array $atts Shortcode attributes.
	 * @return array Parameters for the WP_Query.
	 */
	private function add_params_from_shortcode_atts( $query_params, $atts ) {
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
		if ( $atts['include-past-events'] ) {
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
	private function query_params_from_url_params( $query_params ) {
		if ( empty( $event_tag = get_query_var( 'event-tag' ) ) ) {
			return $query_params;
		}

		if ( ! isset( $query_params['tax_query'] ) ) {
			$query_params['tax_query'] = [];
		}

		$query_params['tax_query'][] = [
			'taxonomy' => 'event_tag',
			'field'    => 'name',
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
	private function get_items_html( \WP_Query $events_query ) {
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

	/**
	 * Merge user-provided attributes with default attributes.
	 *
	 * @param array $atts Shortcode attributes.
	 */
	private function handle_shortcode_attributes( $atts ) {
		$default_atts = [
			'name'                => null,
			'tags'                => null,
			'include-past-events' => false,
		];
		return shortcode_atts( $default_atts, $atts );
	}
}
