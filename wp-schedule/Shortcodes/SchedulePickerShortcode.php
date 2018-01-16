<?php
/**
 * Creates a shortcode which renders a list of schedules to pick from.
 *
 * @package colby-wp-schedule
 */

namespace ColbyComms\Schedules\Shortcodes;

use ColbyComms\Schedules\WpFunctions as WP;

/**
 * Shortcode [schedule-picker].
 */
class SchedulePickerShortcode {
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
	 * @param array $atts Shortcode attributes.
	 * @return string The shortcode output.
	 */
	public static function schedule_picker( $atts = [] ) : string {
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

		$extra = '
			<h4 class="mt-4">
				<a href="' . WP::get_term_link( $parent_term->term_id ) . '">View all events</a>
			</h4>';
		$output = '
			<section class="text-center">
				<h2 class="section-title">Choose Your Class Year</h2>
				<ul class="schedule-picker">';

		$output .= array_reduce(
			$terms,
			function( $output, $term ) {
				$term = WP::get_term( $term, 'schedule_category' );
				$link = WP::get_term_link( $term->term_id );
				return "$output<li><a href=\"$link\">$term->name</a></li>";
			},
			''
		);

		return "$output</ul>$extra</section>";
	}
}
