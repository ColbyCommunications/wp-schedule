<?php
/**
 * Plugin Name: Colby Schedules
 *
 * Description: Schedule table creator for Colby College.
 * Version: 0.0.1
 * Author: Iavor Dekov
 * Author Email: ivdekov@gmail.com
 * Text Domain: colby-schedule
 *
 * @package colby-wp-schedule
 */

include 'vendor/autoload.php';

new Colby\Schedules\EventMeta();


add_action( 'admin_init', 'handle_missing_acf_plugin' );
function handle_missing_acf_plugin() {

	// Short-circuit this check for development.
	return;

	// If ACF is not activated...
	if ( ! class_exists( 'acf' ) ) {
		// Deactivate Colby Schedules
		deactivate_plugins( plugin_basename( __FILE__ ) );
		// Display an admin notice
		add_action( 'admin_notices', 'display_missing_acf_admin_notice' );
		// Remove "Plugin activated." admin notice
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}
}

// Display an admin notice advising the user to activate ACF
function display_missing_acf_admin_notice() {
	$plugin_name = get_plugin_data( __FILE__ )['Name'];
	?>
  <div class="notice notice-error">
	  <p><?php echo $plugin_name; ?> requires the Advanced Custom Fields plugin.
		Please activate it first before activating <?php echo $plugin_name; ?>.</p>
  </div>
	<?php
}

// Add schedule custom post type
add_action(
	'init', function() {
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
);

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
	if ( 'revealid_id' == $column ) {
		echo $id;
	}
}

add_shortcode( 'schedule', 'handle_schedule_shortcode' );
function handle_schedule_shortcode( $atts ) {
	$schedule_id = $atts['id'];

	// check if the repeater field has rows of data
	if ( have_rows( 'event', $schedule_id ) ) {
		$table_styles = get_field( 'table_styles', $schedule_id );
		$tr_styles = get_field( 'row_styles', $schedule_id );
		$td_styles = get_field( 'column_styles', $schedule_id );

		ob_start();

		echo "<table style=\"${table_styles}\">";
		// loop through the rows of data
		while ( have_rows( 'event', $schedule_id ) ) {
			echo "<tr style=\"${tr_styles}\">";

			the_row();

			echo "<td style=\"${td_styles}\">";
			the_sub_field( 'start_time' );
			echo ' - ';
			the_sub_field( 'end_time' );
			echo '</td>';

			echo "<td style=\"${td_styles}\">";
			the_sub_field( 'event_details' );
			echo '</td>';

			echo '</tr>';
		}

		echo '</table>';

		return ob_get_clean();
	} else {
		// no rows found
	}
}

// Remove some buttons from the HTML (text) editor
add_filter( 'quicktags_settings', 'remove_quicktags' );
function remove_quicktags( $qtInit ) {
	$qtInit['buttons'] = 'strong,em,link,img,ul,ol,li';
	return $qtInit;
}

// Add other buttons to the HTML (text) editor
add_action( 'admin_print_footer_scripts', 'add_quicktags' );
function add_quicktags() {
	if ( wp_script_is( 'quicktags' ) ) {
?>
	<script type="text/javascript">
	QTags.addButton( 'h2', 'h2', '<h2>', '</h2>', '', 'h2 heading tag', 1 );
	QTags.addButton( 'h3', 'h3', '<h3>', '</h3>', '', 'h3 heading tag', 2 );
	QTags.addButton( 'h4', 'h4', '<h4>', '</h4>', '', 'h4 heading tag', 3 );
	QTags.addButton( 'paragraph', 'p', '<p>', '</p>', 'p', 'Paragraph tag', 1 );
	</script>
<?php
	}
}

// Reduce height of Event Details editor
add_action( 'admin_head', 'colby_schedule_custom_wysiwyg' );
function colby_schedule_custom_wysiwyg() {
	echo '<style>
    .wp-editor-container textarea.wp-editor-area {
      height: 150px !important;
      resize: none;
    }
  </style>';
}
