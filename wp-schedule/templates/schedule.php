<?php
/**
 * Schedule template.
 *
 * @package colbycomms/wp-schedule
 */

use ColbyComms\SVG\SVG;
use ColbyComms\Schedules\WpFunctions as WP;

if ( empty( $days || ! is_array( $days ) ) || ! is_array( $tags ) || ! isset( $active_tags ) ) {
	return;
}

$atts = $atts ?? [ 'show-description' => 'false' ];

if ( isset( $atts['tag-selector'] ) && 'true' === $atts['tag-selector'] ) : ?>
<form class="schedule__tag-form">
	<ul class="schedule__tag-list">
	<?php foreach ( $tags as $tag ) : ?>
		<li>
			<label>
				<input
					type="checkbox"
					name="event-tag"
					value="<?php echo WP::esc_attr( $tag->term_id ); ?>"
					<?php echo null === $term || in_array( $tag->name, $active_tags, true ) ? 'checked' : ''; ?>>
				<?php echo $tag->name; ?>
			</label>
		</li>
	<?php endforeach; ?>
	</ul>
</form>
<?php endif; ?>
<aside class="schedule__print-email">
	<div class="schedule__print">
		<a href="javascript:window.print()"><?php SVG::show( 'print' ); ?></a>
	</div>
	<div class="schedule__email">
		<button data-email-to type="submit"><?php SVG::show( 'email' ); ?></button>
	</div>
</aside>
<div class="schedule">
	<?php
	foreach ( $days as $date => $events ) :

		include 'day.php';

	endforeach;
	?>
</div>
