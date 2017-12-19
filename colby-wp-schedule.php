<?php
/**
 * Plugin Name: Colby Schedules
 *
 * Description: Schedule table creator for Colby College.
 * Version: 0.0.1
 * Author: Iavor Dekov
 * Author Email: ivdekov@gmail.com
 * Text Domain: colby-schedule
 *
 * @package colby-wp-schedule
 */

include 'vendor/autoload.php';

new Colby\Schedules\EventMeta();
new Colby\Schedules\SchedulePost();
