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
