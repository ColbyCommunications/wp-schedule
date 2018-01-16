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

$do_map = Helper::get_the_post_meta( 'colby_schedule__do_map' );

if ( $do_map ) {
	$map = Helper::get_the_post_meta( 'colby_schedule__map' );
}

$always_visible = null !== $term ? WP::has_term( $term->term_id, 'schedule_category', $event ) : false;

?>
<div data-event
	class="col-12<?php echo WP::has_post_thumbnail() ? ' has-post-thumbnail' : ''; ?> event-container <?php Templates::term_classes( $terms ); ?>"
	data-event-always-visible="<?php echo $always_visible ? 'true' : 'false'; ?>"
	data-event-tag-ids="<?php Templates::term_ids( $terms ); ?>">
	<div class="collapsible event" data-collapsible>
		<button class="collapsible-heading event__heading"
			aria-pressed="false">
			<span class="event__time">
				<?php Templates::event_time(); ?>
			</span>	
			<span class="event__info"
				<?php echo WP::has_post_thumbnail() ? 'style="background-image: url(\'' . WP::get_the_post_thumbnail_url() . '\');"' : ''; ?>>
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
	</div>
</div>

<?php

WP::wp_reset_postdata();
