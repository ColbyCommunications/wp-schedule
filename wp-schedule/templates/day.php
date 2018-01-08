<?php
/**
 * Template for a single day.
 *
 * @package colbycomms/wp-schedule
 */

if ( empty( $date ) || empty( $events ) ) {
	return;
}

?>

<section class="row day">
	<heading class="col-12">
		<h3>
			<?php echo date_create( $date )->format( get_option( 'date_format' ) ); ?>
		</h3>
	</heading>
	<?php foreach ( $events as $event ) : ?>
	<?php include 'event.php'; ?>
	<?php endforeach; ?>
</section>
