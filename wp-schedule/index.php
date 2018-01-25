<?php
/**
 * Initialize the main classes. This file autoloads.
 *
 * @package colby-wp-schedule
 */

namespace ColbyComms\Schedules;

use ColbyComms\Schedules\WpFunctions as WP;

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

require 'pp.php';

define( __NAMESPACE__ . '\\TEXT_DOMAIN', 'wp-schedule' );
define( __NAMESPACE__ . '\\VERSION', '1.0.0' );
define( __NAMESPACE__ . '\\PROD', false );

WP::add_action( 'after_setup_theme', [ 'Carbon_Fields\\Carbon_Fields', 'boot' ] );

new Plugin();
new Options();
new EventMeta();

add_action( 'pre_get_posts', function( $query ) {
	if ( ! $query->is_main_query() || ! $query->is_tax( 'schedule_category' ) ) {
		return;
	}

	$query->set( 'meta_query', [
		'relation'     => 'AND',
		'schedule_date' => [
			'key'     => '_colby_schedule__date',
			'value'   => date( 'Y-m-d' ),
			'compare' => '>',
		],
		'schedule_time' => [
			'key'     => '_colby_schedule__start_time',
			'compare' => 'EXISTS',
		],
	] );

	$query->set( 'orderby', [
		'schedule_date' => 'ASC',
		'schedule_time' => 'ASC',
	] );
} );
