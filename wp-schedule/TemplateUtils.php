<?php
/**
 * TemplateUtils class.
 *
 * @package colbycomms/colby-wp-schedule
 */

namespace ColbyComms\Schedules;

use Carbon_Fields\Helper\Helper;
use ColbyComms\Schedules\WpFunctions as WP;

/**
 * Provides utilities for the template files.
 */
class TemplateUtils {
	/**
	 * Get a string of term slugs separated by spaces for use as CSS classes.
	 *
	 * @param array $terms Term objects.
	 * @return void
	 */
	public static function term_classes( array $terms = [] ) : void {
		echo $terms
			? ' ' . implode(
				' ',
				array_map(
					function( $term ) {
						return $term->slug;
					},
					$terms
				)
			)
			: '';
	}

	/**
	 * Get the term IDs separated by commas.
	 *
	 * @param array $terms Term objects.
	 * @return void
	 */
	public static function term_ids( array $terms = [] ) : void {
		echo $terms
			? implode(
				',',
				array_map(
					function( $term ) {
						return $term->term_id;
					},
					$terms
				)
			)
			: '0';
	}

	/**
	 * Outputs a formatted version of the event time.
	 *
	 * @param string $start_time A start time.
	 * @param string $end_time An end time.
	 * @return void
	 */
	public static function event_time( string $start_time = '', string $end_time = '' ) : void {
		$start_time = $start_time ?: Helper::get_the_post_meta( 'colby_schedule__start_time' );
		$end_time = $end_time ?: Helper::get_the_post_meta( 'colby_schedule__end_time' );

		if ( $start_time ) {
			$start_time = date_format( date_create( $start_time ), 'g:i a' );
		}

		if ( $end_time ) {
			$end_time = date_format( date_create( $end_time ), 'g:i a' );
		} else {
			echo ucfirst( self::time_string_replacements( $start_time ) );
			return;
		}

		$time = "<span>$start_time -</span> <span>$end_time</span>";
		if ( strpos( $time, 'am' ) !== false && strpos( $time, 'pm' ) !== false ) {
			echo self::time_string_replacements( $time );
			return;
		}

		$start_time = ucfirst(
			str_replace(
				[ 'a.m.', 'p.m.' ],
				'',
				self::time_string_replacements( $start_time )
			)
		);

		echo self::time_string_replacements( "$start_time - $end_time" );
	}

	/**
	 * Render the map fields as data attributes.
	 *
	 * @param array $map Map fields.
	 * @return void
	 */
	public static function google_map_attributes( $map ) {
		echo array_reduce(
			array_keys( $map ),
			function( $output, $key ) use ( $map ) {
				if ( ! $key || empty( $map[ $key ] ) ) {
					return $output;
				}

				$value = WP::esc_attr( $map[ $key ] );
				return $output .= " data-$key=\"$value\"";
			},
			''
		);
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
}
