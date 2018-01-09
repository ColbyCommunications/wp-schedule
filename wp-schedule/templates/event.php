<?php
/**
 * Template for a single event.
 *
 * @package colbycomms/wp-schedule
 */

global $post;

if ( empty( $event ) ) :
	return;
endif;

$post = $event; // @codingStandardsIgnoreLine WordPress.Variables.GlobalVariables.OverrideProhibited

setup_postdata( $post );

$terms = get_the_terms( get_the_id(), 'event_tag' ) ?: [];

if ( ! function_exists( 'term_classes' ) ) :
	/**
	 * Get a string of term slugs separated by spaces for use as CSS classes.
	 *
	 * @param array $terms Term objects.
	 */
	function term_classes( array $terms = [] ) {
		echo $terms
			? ' ' . implode(
				' ',
				array_map(
					function( $term ) {
						return $term->slug;
					},
					$terms
				)
			)
			: '';
	}
endif;

if ( ! function_exists( 'term_ids' ) ) :
/**
 * Get the term IDs separated by commas.
 *
 * @param array $terms Term objects.
 */
function term_ids( array $terms = [] ) {
	echo $terms
		? implode(
			',',
			array_map(
				function( $term ) {
					return $term->term_id;
				},
				$terms
			)
		)
		: '';
}
endif;

?>
<div data-event class="col-12 <?php term_classes( $terms ); ?>" data-event-tag-ids="<?php term_ids( $terms ); ?>">
	<div class="collapsible event" data-collapsible>
		<div class="event__event-visible">
			<?php if ( has_post_thumbnail() ) : // @codingStandardsIgnoreLine ?>
			<div class="event__thumbnail-container">
				<?php the_post_thumbnail( 'large' ); ?>
			</div>
			<?php endif; ?>
			<button class="collapsible-heading event__heading" aria-pressed="false">
				<span class="event__info">
					<span class="event__title">
						<?php the_title(); ?>

					</span>
					<span class="event__details">
						<span class="event__time">
							<?php
								$time = get_post_meta( get_the_id(), '_colby_schedule__start_time', true );
								$formatted_time = str_replace( [ 'am', 'pm' ], [ 'a.m.', 'p.m.' ], date_format( date_create( $time ), 'g:i a' ) );
								echo $formatted_time;
							?>
						</span>
						<span class="event__location">
							<?php
								echo get_post_meta( get_the_id(), '_colby_schedule__location', true );
							?>
						</span>
					</span>
				</span>
				<span class="event__arrow-container">
					<svg width="1792" height="1792" viewBox="0 0 1792 1792" class="down-arrow-svg">
						<title>Show More</title>
						<path d="M1395 736q0 13-10 23l-466 466q-10 10-23 10t-23-10l-466-466q-10-10-10-23t10-23l50-50q10-10 23-10t23 10l393 393 393-393q10-10 23-10t23 10l50 50q10 10 10 23z" fill="currentColor"/>
					</svg>
				</span>
			</button>
		</div>
		<div class="collapsible-panel" aria-hidden="true">
			<?php the_content(); ?>
		</div>
	</div>
</div>

<?php

wp_reset_postdata();
