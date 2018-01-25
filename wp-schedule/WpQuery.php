<?php
/**
 * WpQuery.php
 *
 * @package colbycomms/wp-collapsible
 */

namespace ColbyComms\Schedules;

/**
 * A testable wrapper for WP's WP_Query class.
 */
class WpQuery {
	/**
	 * Initaties the query.
	 *
	 * @param array|\WP_Query $args Arguments for the WP_Query constructor or a WP_Query instance.
	 */
	public function __construct( $args = [] ) {
		if ( is_object( $args ) && 'WP_Query' === get_class( $args ) ) {
			$this->query = $args;
			return;
		}

		$this->query = class_exists( '\WP_Query' )
			? new \WP_Query( $args )
			: (object) [
				'posts' => [],
			];
	}

	/**
	 * Gets values from WP_Query when requested from this class object.
	 *
	 * @param string $name The attribute name.
	 * @return mixed The attribute value.
	 */
	public function __get( string $name ) {
		if ( isset( $this->query->$name ) ) {
			return $this->query->$name;
		}

		return null;
	}

	/**
	 * Wraps WP_Query::have_posts.
	 *
	 * @return bool Whether the query has posts.
	 */
	public function have_posts() : bool {
		if ( method_exists( $this->query, 'have_posts' ) ) {
			return $this->query->have_posts();
		}

		return ! ! $this->query->posts;
	}
}
