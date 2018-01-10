<?php
// @codingStandardsIgnoreFile
// phpcs:disable Squiz.Commenting.FunctionComment.Missing,WordPress.PHP.DevelopmentFunctions.error_log_print_r
if ( ! function_exists( 'pp' ) ) {
	function pp( $data, $die = false ) {
		echo '<pre>';
		print_r( $data );
		echo '</pre>';
		if ( $die ) {
			wp_die();
		}
	}
}
