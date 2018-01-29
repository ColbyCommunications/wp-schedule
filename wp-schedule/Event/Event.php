<?php
/**
 * EventPost.php
 *
 * @package wp-schedule
 */

namespace ColbyComms\Schedules\Event;

use ColbyComms\SVG\SVG;
use ColbyComms\Schedules\Utils\WpFunctions as WP;

/**
 * Setup 'event' custom post type.
 */
class Event {
	/**
	 * Post type slug.
	 *
	 * @var string
	 */
	const POST_TYPE_NAME = 'event';

	/**
	 * Add hooks.
	 */
	public function __construct() {

		WP::add_action( 'init', [ __CLASS__, 'register_event_post_type' ] );
	}

	/**
	 * Create 'event' custom post type.
	 */
	public static function register_event_post_type() {
		WP::register_post_type(
			self::POST_TYPE_NAME,
			self::get_post_type_args( ucfirst( self::POST_TYPE_NAME ) )
		);
	}

	/**
	 * Generate labels and args that will be used when registering the post type.
	 *
	 * @param string $post_type Name of post type (first letter capitalized).
	 */
	private static function get_post_type_args( $post_type ) {
		$labels = [
			'name'          => "{$post_type}s",
			'singular_name' => "{$post_type}",
			'add_new_item'  => "Add New {$post_type}",
			'new_item'      => "New {$post_type}",
			'edit_item'     => "Edit {$post_type}",
			'view_item'     => "View {$post_type}s",
			'all_items'     => "All {$post_type}s",
			'search_items'  => "Search {$post_type}s",
			'not_found'     => "No {$post_type}s found.",
		];

		return [
			'labels'       => $labels,
			'show_ui'      => true,
			'supports'     => [ 'title', 'editor', 'thumbnail' ],
			'show_in_rest' => true,
		];
	}

	/**
	 * Returns formatted version of the event time.
	 *
	 * @param string $start_time A start time.
	 * @param string $end_time An end time.
	 * @param int|string $id Post ID.
	 * @return string The formatted time.
	 */
	public static function get_formatted_event_time( string $start_time = '', string $end_time = '', $id = null ) : string {
		$id = $id ?: WP::get_the_id();

		$start_time = $start_time ?: EventMeta::get( EventMeta::START_TIME_KEY );
		$end_time = $end_time ?: EventMeta::get( EventMeta::END_TIME_KEY );

		if ( $start_time ) {
			$start_time = date_format( date_create( $start_time ), 'g:i a' );
		}

		if ( $end_time ) {
			$end_time = date_format( date_create( $end_time ), 'g:i a' );
		} else {
			// No end time. Simple.
			return ucfirst( self::time_string_replacements( $start_time ) );
		}

		$time = "<span>$start_time -</span> <span>$end_time</span>";
		if ( strpos( $time, 'am' ) !== false && strpos( $time, 'pm' ) !== false ) {
			// Both the a.m. and the p.m. need to stay.
			return self::time_string_replacements( $time );
		}

		// The start time's a.m./p.m. is unnecessary.
		$start_time = ucfirst(
			str_replace(
				[ 'a.m.', 'p.m.' ],
				'',
				self::time_string_replacements( $start_time )
			)
		);

		return self::time_string_replacements( "$start_time - $end_time" );
	}

	/**
	 * Reformat a time string for Colby style.
	 *
	 * @param string $string The input string.
	 * @return string The reformated string.
	 */
	public static function time_string_replacements( $string ) : string {
		return str_replace(
			[ '12:00 pm', '12:00 am', 'am', 'pm', ':00' ],
			[ 'noon', 'midnight', 'a.m.', 'p.m.', '' ],
			$string
		);
	}

	/**
	 * Echos calendar data on a span as data attributes.
	 *
	 * @return string HTML.
	 */
	public static function get_calendar_data() : string {
		$start_time = EventMeta::get( EventMeta::START_TIME_KEY );
		$end_time = EventMeta::get( EventMeta::END_TIME_KEY );
		$date = EventMeta::get( EventMeta::DATE_KEY );

		$title = esc_attr( get_the_title() );
		$description = esc_attr( get_the_content() );
		$location = esc_attr( EventMeta::get( EventMeta::LOCATION_KEY ) );
		$start_time = esc_attr( date_format( date_create( "$date $start_time" ), 'Y-m-d\\TH:i:00-04:00' ) );
		$end_time = esc_attr( date_format( date_create( "$date $end_time" ), 'Y-m-d\\TH:i:00-04:00' ) );

		return "<span
			data-add-to-calendar
			data-title=\"$title\"
			data-description=\"$description\"
			data-location=\"$location\"
			data-start-time=\"$start_time\"
			data-end-time=\"$end_time\">
			</span>";
	}
}
