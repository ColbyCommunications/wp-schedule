<?php
/**
 * DayBlock.php
 *
 * @package colbycomms/wp-schedule
 */

namespace ColbyComms\Schedules\Blocks;

/**
 * Renders a day block.
 */
class DayBlock {
	/**
	 * Renders a day block.
	 *
	 * @param array $atts Attributes.
	 * @param string $date A date string.
	 * @param array $events An array of WP_Post objects.
	 * @param \WP_Term $term The current wp_term object.
	 * @return string HTML.
	 */
	public static function render( array $atts = [], string $date = '', array $events = [], \WP_Term $term = null ) : string {
		ob_start();
		?>

<section class="row day"
		<?php if ( 'true' === $atts['tag-selector'] ) : ?>
	style="display: none;"
	<?php endif; ?>
>
	<header class="col-12">
		<h3>
			<?php echo date_create( $date )->format( 'l, F j' ); ?>
		</h3>
	</header>
		<?php
		foreach ( $events as $event ) :
			$event_block = new EventBlock( $event, $term );
			echo $event_block->render_block( $atts );
	endforeach;
		?>
</section>
		<?php

		return ob_get_clean();
	}
}
