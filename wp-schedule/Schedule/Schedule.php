<?php
/**
 * EventPost.php
 *
 * @package wp-schedule
 */

namespace ColbyComms\Schedules\Schedule;

use ColbyComms\Schedules\Event\Event;
use ColbyComms\Schedules\Utils\WpFunctions as WP;

/**
 * Setup 'event' custom post type.
 */
class Schedule {
	/**
	 * The category taxonomy slug.
	 *
	 * @var string
	 */
	const CATEGORY_NAME = 'schedule_category';

	/**
	 * Args to use when registering the category taxonomy.
	 *
	 * @var array
	 */
	const CATEGORY_ARGS = [
		'hierarchical' => true,
		'rewrite' => [
			'slug' => 'schedule',
		],
	];

	/**
	 * The tag taxonomy slug.
	 *
	 * @var string
	 */
	const TAG_NAME = 'event_tag';

	/**
	 * Add hooks.
	 */
	public function __construct() {
		WP::add_action( 'init', [ __CLASS__, 'register_custom_taxonomies' ] );

		new ScheduleCategoryArchive();
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
	 * Gets a label-friendly version of the post type slug.
	 *
	 * @param string $slug A slug.
	 * @return string The formatted version.
	 */
	public static function get_singular_name( string $slug ) : string {
		return ucwords(
			str_replace(
				[ '_category', '_' ],
				[ '', ' ' ],
				$slug
			)
		);
	}

	/**
	 * Register taxonomies for 'event' post type.
	 */
	public static function register_custom_taxonomies() {
		WP::register_taxonomy(
			self::CATEGORY_NAME,
			Event::POST_TYPE_NAME,
			array_merge(
				self::get_taxonomy_label_args( self::get_singular_name( self::CATEGORY_NAME ) ),
				self::CATEGORY_ARGS
			)
		);

		WP::register_taxonomy(
			self::TAG_NAME,
			Event::POST_TYPE_NAME,
			self::get_taxonomy_label_args( self::get_singular_name( self::TAG_NAME ) )
		);

		WP::register_taxonomy_for_object_type( self::CATEGORY_NAME, Event::POST_TYPE_NAME );
		WP::register_taxonomy_for_object_type( self::TAG_NAME, Event::POST_TYPE_NAME );
	}
}
