<?php
/**
 * Template for a single event.
 *
 * @package colbycomms/wp-schedule
 */

use Carbon_Fields\Helper\Helper;
use ColbyComms\Schedules\{TemplateUtils as Templates, WpFunctions as WP};

global $post;

if ( empty( $event ) ) :
	return;
endif;

$do_always_showing = isset( $do_always_showing ) ? $do_always_showing : true;
$post = $event; // @codingStandardsIgnoreLine WordPress.Variables.GlobalVariables.OverrideProhibited

WP::setup_postdata( $post );

$terms = WP::get_the_terms( WP::get_the_id(), 'event_tag' ) ?: [];

$do_map = Helper::get_the_post_meta( 'colby_schedule__do_map' ) && ! isset( $_GET['print'] );

if ( $do_map ) {
	$map = Helper::get_the_post_meta( 'colby_schedule__map' );
}

$do_expandable = $do_map || ( WP::get_the_content() && 'true' !== $atts['show-description'] );

$always_visible = null !== $term ? WP::has_term( $term->term_id, 'schedule_category', $event ) : false;

?>
	<div data-event
		class="col-12 event-container <?php Templates::term_classes( $terms ); ?>"
		data-event-always-visible="<?php echo $always_visible ? 'true' : 'false'; ?>"
		data-event-tag-ids="<?php Templates::term_ids( $terms ); ?>">
		<div class="collapsible event" data-collapsible>
			<<?php echo $do_expandable ? 'button' : 'div'; ?> class="collapsible-heading event__heading"
				aria-pressed="false">
				<span class="event__time">
					<?php Templates::event_time(); ?>
				</span>	
				<span class="event__info"
					<span class="event__details">
						<?php if ( $always_visible ) : ?>
						<span class="event__always-visible-text">
							<?php echo $term->name; ?>
						</span>
						<?php endif; ?>
						<span class="event__title">
							<?php WP::the_title(); ?>
						</span>
						<span class="event__location">
							<?php echo Helper::get_the_post_meta( 'colby_schedule__location' ); ?>
						</span>
						<?php if ( 'true' === $atts['show-description'] ) : ?>
						<span class="event__description">
							<?php the_content(); ?>
						</span>
						<?php endif; ?>
					</span>
					<?php if ( $do_expandable ) : ?>
					<span class="event__arrow-container">
						<svg width="1792" height="1792" viewBox="0 0 1792 1792" class="down-arrow-svg">
							<title>Show More</title>
							<path d="M1395 736q0 13-10 23l-466 466q-10 10-23 10t-23-10l-466-466q-10-10-10-23t10-23l50-50q10-10 23-10t23 10l393 393 393-393q10-10 23-10t23 10l50 50q10 10 10 23z" fill="currentColor"/>
						</svg>
					</span>
					<?php endif; ?>
				</span>
			</<?php echo $do_expandable ? 'button' : 'div'; ?>>
			<?php if ( $do_expandable ) : ?>
			<div class="collapsible-panel" aria-hidden="true">
				<?php if ( ! $do_map ) : ?>
				<?php WP::the_content(); ?>
				<?php else : ?>
				<div class="row">
					<?php if ( trim( WP::get_the_content() ) ) : ?>
					<div class="col-12 col-md-6">
							<?php WP::the_content(); ?>
					</div>
					<?php endif; ?>
					<div class="col-12<?php echo trim( WP::get_the_content() ) ? ' col-md-6' : ''; ?>">
						<div style="height: 100%; min-height: 250px;"
							data-google-map<?php Templates::google_map_attributes( $map ); ?>>
						</div>
					</div>
				</div>
				<?php endif; ?>
			</div>
			<?php endif; ?>
		</div>
	</div>

<?php

WP::wp_reset_postdata();
