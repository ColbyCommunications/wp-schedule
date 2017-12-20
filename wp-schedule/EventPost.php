<?php
/**
 * EventPost.php
 */

namespace Colby\Schedules;

class EventPost {
  private $post_type = 'event';
  private $post_type_ucfirst;

  public function __construct() {
    $this->post_type_ucfirst = ucfirst( $this->post_type );
    add_action( 'init', [ $this, 'register_event_post_type'] );
    add_action( 'init', [ $this, 'register_custom_taxonomies' ] );
  }

  /**
   * Create 'event' custom post type
   */
  public function register_event_post_type() {
    $labels = [
			'name'               => _x( "{$this->post_type_ucfirst}s", 'post type general name', 'colby-wp-schedule' ),
			'singular_name'      => _x( "{$this->post_type_ucfirst}", 'post type singular name', 'colby-wp-schedule' ),
			'add_new_item'       => __( "Add New {$this->post_type_ucfirst}", 'colby-wp-schedule' ),
			'new_item'           => __( "New {$this->post_type_ucfirst}", 'colby-wp-schedule' ),
			'edit_item'          => __( "Edit {$this->post_type_ucfirst}", 'colby-wp-schedule' ),
			'view_item'          => __( "View {$this->post_type_ucfirst}s", 'colby-wp-schedule' ),
			'all_items'          => __( "All {$this->post_type_ucfirst}", 'colby-wp-schedule' ),
			'search_items'       => __( "Search {$this->post_type_ucfirst}s", 'colby-wp-schedule' ),
			'not_found'          => __( "No {$this->post_type_ucfirst}s found.", 'colby-wp-schedule' ),
		];

		$args = array(
			'labels'             => $labels,
			'show_ui'            => true,
			'supports'           => array( 'title' ),
		);

		register_post_type( $this->post_type, $args );
  }

  private function get_taxonomy_label_args( $taxonomy ) {
    return [
      'labels' => [
        'name'          => _x( "{$taxonomy}s", 'taxonomy general name' ),
        'singular_name' => _x( "{$taxonomy}", 'taxonomy singular name' ),
        'add_new_item'  => __( "Add New {$taxonomy}" ),
        'search_items'  => __( "Search {$taxonomy}s" )
      ]
    ];
  }

  public function register_custom_taxonomies() {
    $schedule_taxonomy_args = array_merge(
      $this->get_taxonomy_label_args( 'Schedule' ), [ 'hierarchical' => true ]
    );

    register_taxonomy( 'schedule', $this->post_type, $schedule_taxonomy_args );
    register_taxonomy( 'event_tag', $this->post_type, $this->get_taxonomy_label_args('Event Tag') );

    register_taxonomy_for_object_type( 'schedule', $this->post_type );
    register_taxonomy_for_object_type( 'event_tag', $this->post_type );
  }

}
