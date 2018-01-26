<?php
/**
 * Initialize the main classes. This file autoloads.
 *
 * @package colby-wp-schedule
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

require 'pp.php';

new ColbyComms\Schedules\Schedules();
