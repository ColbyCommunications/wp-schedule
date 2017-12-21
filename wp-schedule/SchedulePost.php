<?php
/**
 * SchedulePost.php
 *
 * @package colby-wp-schedule
 */

namespace Colby\Schedules;

/**
 * Setup 'schedule' custom post type.
 */
class SchedulePost extends CustomPostType {
	/**
	 * Call parent constructor. Add all hooks.
	 */
	public function __construct() {
		parent::__construct( 'schedule' );

		add_action( 'init', [ $this, 'register_schedule_post_type' ] );
		add_filter( 'manage_schedule_posts_columns', [ $this, 'revealid_add_id_column' ], 5 );
		add_action( 'manage_schedule_posts_custom_column', [ $this, 'revealid_id_column_content' ], 5, 2 );
	}

	/**
	 * Create 'schedule' custom post type.
	 */
	public function register_schedule_post_type() {
		register_post_type( $this->post_type, $this->post_type_args );
	}

	/**
	 * Add column to schedule posts for displaying IDs.
	 *
	 * @param array $columns Array of data columns
	 * on display when viewing list of all posts.
	 */
	public function revealid_add_id_column( $columns ) {
		// Make IDs display in the second column, after the post's title.
		$checkbox = array_slice( $columns, 0, 2 );
		$columns = array_slice( $columns, 1 );

		$id['revealid_id'] = 'ID';

		$columns = array_merge( $checkbox, $id, $columns );
		return $columns;
	}

	/**
	 * Display IDs for 'schedule' custom posts.
	 *
	 * @param string  $column Name of column.
	 * @param integer $id Post id.
	 */
	public function revealid_id_column_content( $column, $id ) {
		if ( 'revealid_id' === $column ) {
			echo $id;
		}
	}

}
