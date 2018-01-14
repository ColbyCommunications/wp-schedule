<?php
/**
 * Initialize the main classes. This file autoloads.
 *
 * @package colby-wp-schedule
 */

namespace ColbyComms\Schedules;

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

require 'pp.php';

add_action( 'after_setup_theme', [ 'Carbon_Fields\\Carbon_Fields', 'boot' ] );

new Plugin();
new Options();
new EventMeta();

add_action(
	'init', function() {
		/**
		 * Filters whether to run. Useful for theme contexts where users opt in to this feature.
		 *
		 * @var bool True to run.
		 */
		if ( apply_filters( 'colby_wp_schedule_run', true ) ) {
			new EventPost();
			new ScheduleShortcode();
			new SchedulePickerShortcode();
		}
	}, 8
);


add_action(
	'wp_enqueue_scripts', function() {
		$key = carbon_get_theme_option( 'wp_schedule_google_maps_api_key' );
		wp_enqueue_script(
			'google-maps',
			"https://maps.googleapis.com/maps/api/js?key=$key",
			[],
			false,
			true
		);
	}, 1
);


add_action(
	'save_post',
	function( $post_id, $post ) {
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( 'event' !== $post->post_type ) {
			return;
		}

		$terms = get_the_terms( $post_id, 'invitee_group' );

		if ( ! empty( $terms ) ) {
			return;
		}

		$all_term = get_term_by( 'slug', 'all', 'invitee_group' );

		wp_set_post_terms( $post_id, $all_term->term_id, 'invitee_group' );
	},
	10,
	2
);

add_action(
	'pre_get_posts', function( $query ) {
		if ( ! $query->is_main_query() ) {
			return;
		}

		$slug = get_query_var( 'schedule_category' );

		if ( ! $slug ) {
			return;
		}

		$query->set( 'post_type', 'event' );
		$query->set( 'posts_per_page', -1 );

		$term = get_term_by( 'slug', $slug, 'schedule_category' );
		$parents = get_ancestors( $term->term_id, 'schedule_category', 'taxonomy' ) ?: [];
		$term_parents_query = array_map(
			function( $term_id ) {
				$the_term = get_term_by( 'id', $term_id, 'schedule_category' );

				return [
					'taxonomy' => 'schedule_category',
					'field' => 'slug',
					'terms' => $the_term->slug,
				];
			},
			$parents
		);

		$query->set(
			'tax_query',
			[
				'relation' => 'OR',
				$term_parents_query,
			]
		);
	}
);
