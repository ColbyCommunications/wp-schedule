<?php
/**
 * Template for post archives.
 *
 * @package colbycomms/wp-theme-twentyeighteen
 */

use ColbyComms\Schedules\Schedules;
use ColbyComms\Schedules\Blocks\ScheduleBlock;
use ColbyComms\Schedules\Schedule\{Schedule, ScheduleMeta};

get_header();

$queried_object = get_term_by( 'slug', get_query_var( Schedule::CATEGORY_NAME ), Schedule::CATEGORY_NAME );

if ( ! function_exists( 'render_taxonomy_schedule_category' ) ) {
	/**
	 * Echoes the schedule block.
	 *
	 * @param \WP_Term $queried_object A term instance.
	 * @return void
	 */
	function render_taxonomy_schedule_category( \WP_Term $queried_object ) {
		$do_tag_selector = ScheduleMeta::get( ScheduleMeta::DO_TAG_LIST_KEY, $queried_object->term_id )
			? 'true'
			: 'false';
		$do_description = ScheduleMeta::get( ScheduleMeta::DO_HIDE_DESCRIPTION_KEY, $queried_object->term_id )
			? 'false'
			: 'true';

		/**
		 * TO-DO: Handle this in a way that doesn't specify the active term by name.
		 * 'Signature Events' is for Reunion 2018.
		 */
		if ( $queried_object->parent ) {
			$args = [
				'active' => 'Signature Events',
				'tag-selector' => $do_tag_selector,
				'show-description' => $do_description,
			];
		} else {
			$args = [
				'tag-selector' => $do_tag_selector,
				'show-description' => $do_description,
			];
		}

		echo ScheduleBlock::render_block(
			$GLOBALS['wp_query'],
			$args,
			$queried_object->parent ? $queried_object : null
		);
	}
}

/**
 * Filters content to go before the schedule.
 *
 * @param string Content.
 * @param \WP_Term This archive page's term.
 */
echo apply_filters( Schedules::BEFORE_SCHEDULE_ARCHIVE_FILTER, '', $queried_object );

?>
	<div class="wp-schedule">
		<div class="wp-schedule__content mb-4">
		<?php echo apply_filters( 'the_content', $queried_object->description ); ?>
		</div>
		<div class="wp-schedule__schedule">
		<?php render_taxonomy_schedule_category( $queried_object ); ?>
		</div>
	</div>
<?php

/**
 * Filters content to go after the schedule.
 *
 * @param string Content.
 * @param \WP_Term This archive page's term.
 */
echo apply_filters( Schedules::AFTER_SCHEDULE_ARCHIVE_FILTER, '', $queried_object );

get_footer();
