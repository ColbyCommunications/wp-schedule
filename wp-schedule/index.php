<?php
/**
 * Initialize the main classes. This file autoloads.
 *
 * @package colby-wp-schedule
 */

namespace ColbyComms\Schedules;

use Carbon_Fields\Helper\Helper;
use ColbyComms\Schedules\WpFunctions as WP;

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

WP::add_action( 'after_setup_theme', [ 'Carbon_Fields\\Carbon_Fields', 'boot' ] );

new Plugin();
new Options();
new EventMeta();
