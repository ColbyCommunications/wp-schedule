<?php
/**
 * Plugin Name: Colby Schedule
 *
 * Description: Schedule table creator for Colby College.
 * Version: 0.0.1
 * Author: Iavor Dekov
 * Author Email: ivdekov@gmail.com
 * Text Domain: colby-schedule
 *
 * @package colby-wp-schedule
 */

// Add schedule custom post type
add_action( 'init', function() {
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
		'public'             => true,
		'supports'           => array( 'title' )
	);

	register_post_type( 'schedule', $args );
} );

// Display IDs for schedule custom posts
add_filter( 'manage_schedule_posts_columns', 'revealid_add_id_column', 5 );
add_action( 'manage_schedule_posts_custom_column', 'revealid_id_column_content', 5, 2 );

// Make IDs display in the second column, after the post's title
function revealid_add_id_column( $columns ) {
  $checkbox = array_slice( $columns , 0, 2 );
  $columns = array_slice( $columns , 1 );

  $id['revealid_id'] = 'ID';

  $columns = array_merge( $checkbox, $id, $columns );
  return $columns;
}

function revealid_id_column_content( $column, $id ) {
 if( 'revealid_id' == $column ) {
   echo $id;
 }
}

add_shortcode( 'schedule', 'handle_schedule_shortcode' );

function handle_schedule_shortcode( $atts ) {
  $schedule_id = $atts['id'];

  // check if the repeater field has rows of data
  if ( have_rows('event', $schedule_id) ) {
    echo '<table>';
    // loop through the rows of data
    while ( have_rows('event', $schedule_id) ) {
      echo '<tr style="border: 1px solid black;">';

      the_row();

      echo '<td style="border: 1px solid black;">';
      the_sub_field('start_time');
      echo ' - ';
      the_sub_field('end_time');
      echo '</td>';

      echo '<td style="border: 1px solid black;">';
      the_sub_field('event_details');
      echo '</td>';

      echo '</tr>';
    }

    echo '</table>';
  } else {
    // no rows found
  }
}
