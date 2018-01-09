<?php
/**
 * Schedule template.
 *
 * @package colbycomms/wp-schedule
 */

if ( empty( $days || ! is_array( $days ) ) || ! is_array( $tags ) ) {
	return;
}
?>
<form class="schedule__tag-form">
	<label class="schedule__all-events-checkbox">
		<input
			type="checkbox"
			name="all-event-types"
			checked>
		All events
	</label>
	<ul class="schedule__tag-list">
	<?php foreach ( $tags as $tag ) : ?>
		<li>
			<label>
				<input
					type="checkbox"
					name="event-tag"
					value="<?php echo esc_attr( $tag->term_id ); ?>">
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
