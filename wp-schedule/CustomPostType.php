<?php
/**
 * CustomPostType.php
 *
 * @package colby-wp-schedule
 */

namespace ColbyComms\Schedules;

/**
 * Base custom post type class.
 * Custom post types will inherit this class.
 */
abstract class CustomPostType {
	/**
	 * Name of post.
	 *
	 * @var string
	 */
	public $post_type;
	/**
	 * Array of post type args.
	 *
	 * @var array
	 */
	public $post_type_args;

	/**
	 * Store post type and its args for child classes to use.
	 *
	 * @param string $post_type Name of post type.
	 */
	public function __construct( $post_type ) {
		$this->post_type = $post_type;
		$post_type_ucfirst = ucfirst( $post_type );
		$this->post_type_args = $this->get_post_type_args( $post_type_ucfirst );
	}

	/**
	 * Generate labels and args that will be used when registering the post type.
	 *
	 * @param string $post_type Name of post type (first letter capitalized).
	 */
	private function get_post_type_args( $post_type ) {
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
}
