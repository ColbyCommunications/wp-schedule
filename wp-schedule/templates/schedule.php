<?php
/**
 * Schedule template.
 *
 * @package colbycomms/wp-schedule
 */

use ColbyComms\Schedules\WpFunctions as WP;

if ( empty( $days || ! is_array( $days ) ) || ! is_array( $tags ) || ! isset( $active_tags ) ) {
	return;
}

?>
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
<div class="schedule">
	<?php
	foreach ( $days as $date => $events ) :

		include 'day.php';

	endforeach;
	?>
</div>
