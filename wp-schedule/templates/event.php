<?php
/**
 * Template for a single event.
 *
 * @package colbycomms/wp-schedule
 */

global $post;

if ( empty( $event ) ) {
	return;
}

$post = $event; // @codingStandardsIgnoreLine WordPress.Variables.GlobalVariables.OverrideProhibited

setup_postdata( $post );

?>
<div data-event class="col-12 col-md-6 col-lg-4"<?php // @codingStandardsIgnoreLine
	$terms = implode(
		',',
		array_map(
			function( $term ) {
				return $term->term_id;
			},
			get_the_terms( get_the_id(), 'event_tag' ) ?: []
		)
	);

	if ( $terms ) :
		echo " data-event-tag-ids=\"$terms\"";
	endif;

	// @codingStandardsIgnoreLine ?>>
	<div class="card">
		<div class="card-header primary p-2">
			<h4 class="mb-0"><?php the_title(); ?></h4>
		</div>
		<div class="card-body px-0 small-3">
			<?php the_content(); ?>
		</div>
	</div>
</div>

<?php

wp_reset_postdata();
