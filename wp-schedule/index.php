<?php
/**
 * Initialize the main classes. This file autoloads.
 *
 * @package colby-wp-schedule
 */

namespace ColbyComms\Schedules;

use ColbyComms\Schedules\Utils\WpFunctions as WP;

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

require 'pp.php';

define( __NAMESPACE__ . '\\TEXT_DOMAIN', 'wp-schedule' );
define( __NAMESPACE__ . '\\VERSION', '1.0.0' );
define( __NAMESPACE__ . '\\PROD', false );

WP::add_action( 'after_setup_theme', [ 'Carbon_Fields\\Carbon_Fields', 'boot' ] );

new Options();
new Plugin();
new Posts\EventMeta();
new Posts\TermMeta();
