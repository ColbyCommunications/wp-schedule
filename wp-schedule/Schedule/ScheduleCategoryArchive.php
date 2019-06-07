<?php
/**
 * ScheduleCategoryArchive.php
 *
 * @package colbycomms/wp-schedule
 */

namespace ColbyComms\Schedules\Schedule;

use ColbyComms\Schedules\Utils\WpFunctions as WP;
use ColbyComms\Schedules\Event\EventMeta;

/**
 * Methods for the display of schedule category term archive pages.
 */
class ScheduleCategoryArchive {
	/**
	 * Add hooks.
	 */
	public function __construct() {
		WP::add_action( 'pre_get_posts', [ __CLASS__, 'query_all_event_posts' ] );
		WP::add_action( 'pre_get_posts', [ __CLASS__, 'include_posts_from_parent_in_query' ] );
		WP::add_action( 'pre_get_posts', [ __CLASS__, 'order_events_by_date_and_time' ] );
	}

	/**
	 * Whether we're showing a term archive for this category.
	 *
	 * @param \WP_Query $query A query instance.
	 * @return boolean Yes or no.
	 */
	public static function doing_schedule_category_archive( \WP_Query $query = null ) : bool {
		$query = $query ?: $GLOBALS['wp_query'];
		return $query->is_main_query() && $query->is_tax( Schedule::CATEGORY_NAME );
	}

	/**
	 * Tells the main query to get all posts.
	 *
	 * @param \WP_Query $query A WP_Query instance.
	 * @return void
	 */
	public static function query_all_event_posts( \WP_Query $query ) : void {
		if ( ! self::doing_schedule_category_archive( $query ) ) {
			return;
		}

		$query->set( 'posts_per_page', -1 );
		$query->set( 'post_type', 'event' );
	}

	/**
	 * If the main query is for a schedule_category and the term is a child, include the parent
	 * term's posts in the query.
	 *
	 * @param \WP_Query $query The WP_Query object.
	 * @return void
	 */
	public static function include_posts_from_parent_in_query( \WP_Query $query ) {
		if ( ! self::doing_schedule_category_archive( $query ) ) {
			return;
		}

		$term = WP::get_term_by(
			'slug',
			WP::get_query_var( Schedule::CATEGORY_NAME ),
			Schedule::CATEGORY_NAME
		);
		$parents = WP::get_ancestors( $term->term_id, Schedule::CATEGORY_NAME, 'taxonomy' ) ?: [];

		$term_parent_queries = array_map(
			function( $term_id ) {
				$the_term = WP::get_term_by( 'id', $term_id, Schedule::CATEGORY_NAME );

				return [
					'taxonomy' => Schedule::CATEGORY_NAME,
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
				$term_parent_queries,
			]
		);
	}

	/**
	 * Order events in a schedule term by time and date meta fields.
	 *
	 * @param \WP_Query $query A query.
	 * @return void
	 */
	public static function order_events_by_date_and_time( \WP_Query $query ) : void {
		if ( ! self::doing_schedule_category_archive( $query ) ) {
			return;
		}

		$query->set(
			'meta_query',
			[
				'relation'     => 'AND',
				'schedule_date' => [
					'key'     => '_' . EventMeta::DATE_KEY,
					'value'   => date( 'Y-m-d' ),
					'compare' => '>=',
				],
				'schedule_time' => [
					'key'     => '_' . EventMeta::START_TIME_KEY,
					'compare' => 'EXISTS',
				],
			]
		);

		$query->set(
			'orderby',
			[
				'schedule_date' => 'ASC',
				'schedule_time' => 'ASC',
			]
		);
	}
}
