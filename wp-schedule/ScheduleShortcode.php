<?php
/**
 * Creates a shortcode which renders a schedule.
 *
 * @package colby-wp-schedule
 */

namespace Colby\Schedules;

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
	 * Gets a query for posts with the event post_type.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return WP_Query
	 */
	private function get_events_query( $atts ) {
		$query_params = [
			'post_type'             => 'event',
			'posts_per_page'    => 99,
			'orderby'               => 'name',
			'order'                     => 'ASC',
			'tax_query'             => [
				'relation' => 'AND',
				[
					'taxonomy'  => 'schedule_category',
					'field'         => 'name',
					'terms'         => [ "{$atts['name']}" ],
				],
				[
					'taxonomy'  => 'event_tag',
					'field'         => 'name',
					'terms'         => explode( ',', $atts['tags'] ),
				],
			],
		];

		// TODO: Add params for when $atts['include-past-events'] is set to true.
		return new \WP_Query( $query_params );
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
			'name'                              => null,
			'tags'                              => null,
			'include-past-events' => false,
		];
		return shortcode_atts( $default_atts, $atts );
	}
}
