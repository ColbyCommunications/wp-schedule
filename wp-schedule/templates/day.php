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
	<header class="col-12">
		<h3>
			<?php echo date_create( $date )->format( 'l, F j' ); ?>
		</h3>
	</header>
	<?php foreach ( $events as $event ) : ?>
	<?php include 'event.php'; ?>
	<?php endforeach; ?>
</section>
