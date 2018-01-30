<?php
/**
 * Creates a shortcode which renders a list of schedules to pick from.
 *
 * @package wp-schedule
 */

namespace ColbyComms\Schedules\Blocks;

use ColbyComms\Schedules\Utils\WpFunctions as WP;

/**
 * Block [schedule-picker].
 */
class SchedulePickerBlock {
	/**
	 * Add shortcode.
	 */
	public function __construct() {
		if ( ! WP::shortcode_exists( 'schedule-picker' ) ) {
			WP::add_shortcode( 'schedule-picker', [ __CLASS__, 'schedule_picker' ] );
		}
	}

	/**
	 * The shortcode callback.
	 *
	 * @param array $atts Block attributes.
	 * @param string $content The shortcode content.
	 * @return string The shortcode output.
	 */
	public static function schedule_picker( $atts = [], $content = '' ) : string {
		if ( ! $atts['schedule'] ) {
			return '';
		}

		$parent_term = WP::get_term_by( 'name', $atts['schedule'], 'schedule_category' );

		if ( empty( $parent_term ) ) {
			return '';
		}

		$terms = WP::get_term_children( $parent_term->term_id, 'schedule_category' );

		if ( empty( $terms ) ) {
			return '';
		}

		$output = "
			$content
			<section class=\"text-center\">
				<ul class=\"schedule-picker\">";

		$output .= array_reduce(
			$terms,
			function( $output, $term ) {
				$term = WP::get_term( $term, 'schedule_category' );
				$link = WP::get_term_link( $term->term_id );
				return "$output<li><a href=\"$link\">$term->name</a></li>";
			},
			''
		);

		return "$output</ul></section>";
	}
}
