<?php
/**
 * Creates a shortcode rendering a category/tag picker for schedule events.
 *
 * @package colbycomms/colby-wp-schedule
 */

namespace ColbyComms\Schedules;

/**
 * Shortcode [schedule-picker].
 */
class SchedulePickerShortcode {
	/**
	 * Default attributes for the shortcode.
	 *
	 * @var array
	 */
	public static $defaults = [
		'include-tags' => false,
		'text' => 'Select',
	];

	/**
	 * Registers the shortcode.
	 */
	public function __construct() {
		if ( ! shortcode_exists( 'schedule-picker' ) ) {
			add_shortcode( 'schedule-picker', [ __CLASS__, 'schedule_picker' ] );
		}
	}

	/**
	 * Runs a WP_Term_Query from shortcode attributes.
	 *
	 * @param array $atts The shortcode atts.
	 * @return array The found event tags.
	 */
	public static function get_event_tag_terms( $atts ) {
		$term_query_args = [
			'taxonomy' => 'event_tag',
			'hide_empty' => false,
		];

		if ( ! empty( $atts['include-tags'] ) ) {
			$term_query_args['name'] = array_map( 'trim', explode( ',', $atts['include-tags'] ) );
		}

		$term_query = new \Wp_Term_Query( $term_query_args );

		return $term_query->terms;
	}

	/**
	 * Builds an associative array of term_slug => term_name from a list of WP_Terms.
	 *
	 * @param array $terms The WP_Terms array.
	 * @return array The built array.
	 */
	public static function make_term_array( $terms = [] ) {
		return array_reduce(
			$terms,
			function( $output, $term ) {
				$output[ $term->slug ] = $term->name;
				return $output;
			},
			[]
		);
	}

	/**
	 * The shortcode hook.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string The HTML output.
	 */
	public static function schedule_picker( $atts = [] ) {

		$atts = shortcode_atts( self::$defaults, $atts );
		$terms = self::get_event_tag_terms( $atts );

		return self::render(
			[
				'terms' => self::make_term_array( $terms ),
				'text' => $atts['text'],
			]
		);
	}

	/**
	 * Builds the shortcode HTML.
	 *
	 * @param array $args Arguments used in the HTML.
	 * @return string Rendered HTML.
	 */
	public static function render( $args = [] ) {

		ob_start();
		?>
<form>
	<select name="event-tag" onChange="this.form.submit()">
		<option>-- <?php echo $args['text']; ?> --</option>
		<?php foreach ( $args['terms'] as $slug => $name ) : ?>
		<option value="<?php echo $slug; ?>"><?php echo $name; ?></option>
		<?php endforeach; ?>
	</select>
</form>
		<?php

		return ob_get_clean();
	}
}
