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

new EventMeta();
new SchedulePost();
new EventPost();
new ScheduleShortcode();
