<?php
/**
 * SchedulePost.php
 */

namespace Colby\Schedules;

class SchedulePost {
  public function __construct() {
    add_action( 'init', [ $this, 'register_schedule_post_type'] );
    add_filter( 'manage_schedule_posts_columns', [ $this, 'revealid_add_id_column' ], 5 );
    add_action( 'manage_schedule_posts_custom_column', [ $this, 'revealid_id_column_content' ], 5, 2 );
    add_shortcode( 'schedule', [ $this, 'handle_schedule_shortcode' ] );
  }

  /**
   * Create 'schedule' custom post type
   */
  public function register_schedule_post_type() {
    $labels = array(
			'name'               => _x( 'Schedules', 'post type general name', 'colby-wp-schedule' ),
			'singular_name'      => _x( 'Schedule', 'post type singular name', 'colby-wp-schedule' ),
			'add_new_item'       => __( 'Add New Schedule', 'colby-wp-schedule' ),
			'new_item'           => __( 'New Schedule', 'colby-wp-schedule' ),
			'edit_item'          => __( 'Edit Schedule', 'colby-wp-schedule' ),
			'view_item'          => __( 'View Schedules', 'colby-wp-schedule' ),
			'all_items'          => __( 'All Schedules', 'colby-wp-schedule' ),
			'search_items'       => __( 'Search Schedules', 'colby-wp-schedule' ),
			'not_found'          => __( 'No web docs found.', 'colby-wp-schedule' ),
		);

		$args = array(
			'labels'             => $labels,
			'show_ui'            => true,
			'supports'           => array( 'title' ),
		);

		register_post_type( 'schedule', $args );
  }

  /**
   * Add column to schedule posts for displaying IDs
   */
  public function revealid_add_id_column( $columns ) {
    // Make IDs display in the second column, after the post's title
  	$checkbox = array_slice( $columns , 0, 2 );
  	$columns = array_slice( $columns , 1 );

  	$id['revealid_id'] = 'ID';

  	$columns = array_merge( $checkbox, $id, $columns );
  	return $columns;
  }

  /**
  * Display IDs for 'schedule' custom posts
  */
  public function revealid_id_column_content( $column, $id ) {
  	if ( 'revealid_id' == $column ) {
  		echo $id;
  	}
  }

  public function handle_schedule_shortcode() {
    // TODO: implement this method
  }

}
