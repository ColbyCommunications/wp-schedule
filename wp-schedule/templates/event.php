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

$do_always_showing = isset( $do_always_showing ) ? $do_always_showing : true;
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
		: '0';
}
endif;

if ( ! function_exists( 'google_map_attributes' ) ) :
/**
 * Render the map fields as data attributes.
 *
 * @param array $map Map fields.
 * @return void
 */
function google_map_attributes( $map ) {
	echo array_reduce(
		array_keys( $map ),
		function( $output, $key ) use ( $map ) {
			if ( ! $key || empty( $map[ $key ] ) ) {
				return $output;
			}

			$value = esc_attr( $map[ $key ] );
			return $output .= " data-$key=\"$value\"";
		},
		''
	);
}
endif;

if ( ! function_exists( 'time_string_replacements' ) ) :
	/**
	 * Reformat a time string for Colby style.
	 *
	 * @param string $string The input string.
	 * @return string The reformated string.
	 */
	function time_string_replacements( $string ) {
		return str_replace(
			[ '12:00 pm', '12:00 am', 'am', 'pm', ':00' ],
			[ 'noon', 'midnight', 'a.m.', 'p.m.', '' ],
			$string
		);
	}
endif;

if ( ! function_exists( 'event_time' ) ) :
	/**
	 * Outputs a formatted version of the event time.
	 *
	 * @param string $start_time A start time.
	 * @param string $end_time An end time.
	 * @return void
	 */
	function event_time( string $start_time = '', string $end_time = '' ) : void {
		$start_time = $start_time ?: carbon_get_the_post_meta( 'colby_schedule__start_time' );
		$end_time = $end_time ?: carbon_get_the_post_meta( 'colby_schedule__end_time' );

		if ( $start_time ) {
			$start_time = date_format( date_create( $start_time ), 'g:i a' );
		}

		if ( $end_time ) {
			$end_time = date_format( date_create( $end_time ), 'g:i a' );
		} else {
			echo ucfirst( time_string_replacements( $start_time ) );
			return;
		}

		$time = "<span>$start_time -</span> <span>$end_time</span>";
		if ( strpos( $time, 'am' ) !== false && strpos( $time, 'pm' ) !== false ) {
			echo time_string_replacements( $time );
			return;
		}

		$start_time = ucfirst(
			str_replace(
				[ 'a.m.', 'p.m.' ],
				'',
				time_string_replacements( $start_time )
			)
		);

		echo time_string_replacements( "$start_time - $end_time" );
	}
endif;

$do_map = carbon_get_the_post_meta( 'colby_schedule__do_map' );

if ( $do_map ) {
	$map = carbon_get_the_post_meta( 'colby_schedule__map' );
}

$always_visible = null !== $term ? has_term( $term->term_id, 'schedule_category', $event ) : false;

?>
<div data-event
	class="col-12<?php echo has_post_thumbnail() ? ' has-post-thumbnail' : ''; ?> event-container <?php term_classes( $terms ); ?>"
	data-event-always-visible="<?php echo $always_visible ? 'true' : 'false'; ?>"
	data-event-tag-ids="<?php term_ids( $terms ); ?>">
	<div class="collapsible event" data-collapsible>
		<button class="collapsible-heading event__heading"
			aria-pressed="false">
			<span class="event__time">
				<?php event_time(); ?>
			</span>	
			<span class="event__info"
				<?php echo has_post_thumbnail() ? 'style="background-image: url(\'' . get_the_post_thumbnail_url() . '\');"' : ''; ?>>
				<span class="event__details">
					<?php if ( $always_visible ) : ?>
					<span class="event__always-visible-text">
						<?php echo $term->name; ?>
					</span>
					<?php endif; ?>
					<span class="event__title">
						<?php the_title(); ?>
					</span>
					<span class="event__location">
						<?php echo carbon_get_the_post_meta( 'colby_schedule__location' ); ?>
					</span>
				</span>
				<span class="event__arrow-container">
					<svg width="1792" height="1792" viewBox="0 0 1792 1792" class="down-arrow-svg">
						<title>Show More</title>
						<path d="M1395 736q0 13-10 23l-466 466q-10 10-23 10t-23-10l-466-466q-10-10-10-23t10-23l50-50q10-10 23-10t23 10l393 393 393-393q10-10 23-10t23 10l50 50q10 10 10 23z" fill="currentColor"/>
					</svg>
				</span>
			</span>
		</button>
		<div class="collapsible-panel" aria-hidden="true">
			<?php if ( ! $do_map ) : ?>
			<?php the_content(); ?>
			<?php else : ?>
			<div class="row">
				<?php if ( trim( get_the_content() ) ) : ?>
				<div class="col-12 col-md-6">
					<?php the_content(); ?>
				</div>
				<?php endif; ?>
				<div class="col-12<?php echo trim( get_the_content() ) ? ' col-md-6' : ''; ?>">
					<div style="height: 100%; min-height: 250px;"
						data-google-map<?php google_map_attributes( $map ); ?>>
					</div>
				</div>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php

wp_reset_postdata();
