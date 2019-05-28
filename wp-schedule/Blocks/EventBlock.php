<?php
/**
 * Displays a single event from a post.
 *
 * @package colbycomms/wp-schedule
 */

namespace ColbyComms\Schedules\Blocks;

use ColbyComms\Schedules\Schedules;
use ColbyComms\Schedules\Utils\{WpFunctions as WP};
use ColbyComms\Schedules\Event\{Event, EventMeta};
use ColbyComms\Schedules\Schedule\Schedule;

/**
 * Block [event].
 */
class EventBlock {
	/**
	 * The tag for this block's shortcode.
	 */
	const SHORTCODE_TAG = 'event';

	/**
	 * Default shortcode attributes.
	 *
	 * @var array
	 */
	const DEFAULT_ATTS = [
		'name'                => null,
		'tags'                => null,
		'include-past-events' => 'false',
		'active' => '',
		'tag-selector' => 'true',
		'show-description' => 'false',
	];

	/**
	 * Class setup and hooks.
	 *
	 * @param \WP_Post $post The current post.
	 * @param \WP_Term $term The term archive currently displaying.
	 */
	public function __construct( \WP_Post $post = null, \WP_Term $term = null ) {
		$this->post = $post;
		$this->term = $term;

		WP::add_action( 'init', [ $this, 'add_shortcode' ] );
	}

	/**
	 * Maybe register the shortcode.
	 *
	 * @return void
	 */
	public function add_shortcode() : void {
		if ( WP::shortcode_exists( self::SHORTCODE_TAG ) ) {
			return;
		}

		/**
		 * Filters whether to add a shortcode for this block.
		 *
		 * @param boolean Default false.
		 */
		if ( ! WP::apply_filters( Schedules::DO_EVENT_SHORTCODE_FILTER, false ) ) {
			return;
		}

		WP::add_shortcode( self::SHORTCODE_TAG, [ $this, 'render_block' ] );
	}

	/**
	 * Get the post to display.
	 *
	 * @param array $atts Attributes passed in by shortcode or otherwise.
	 * @return \WP_Post|null The found post or null.
	 */
	public function get_post( array $atts = [] ) : ?\WP_Post {
		if ( ! class_exists( 'WP_Query' ) ) {
			return null;
		}

		$args = [ 'post_type' => 'event' ];

		if ( isset( $atts['title'] ) ) {
			$args['title'] = $atts['title'];
		} elseif ( isset( $atts['slug'] ) ) {
			$args['name'] = $atts['slug'];
		} elseif ( isset( $atts['id'] ) ) {
			$args['p'] = $atts['id'];
		} else {
			return null;
		}

		$query = new \WP_Query( $args );
		if ( $query->posts ) {
			return $query->posts[0];
		}
	}

	/**
	 * Get terms for the current post.
	 *
	 * @return array A list of terms.
	 */
	public function get_terms() : array {
		return WP::get_the_terms( WP::get_the_id(), Schedule::TAG_NAME ) ?: [];
	}

	/**
	 * Whether this post should have a map.
	 *
	 * @return boolean
	 */
	public function should_do_map() : bool {
		return EventMeta::get( EventMeta::DO_MAP_KEY )
			&& empty( get_query_var( 'print' ) );
	}

	/**
	 * Get the map meta for this post.
	 *
	 * @return array The map details.
	 */
	public function get_map() : ?array {
		return $this->do_map
			? EventMeta::get( EventMeta::MAP_KEY )
			: null;
	}

	/**
	 * Whether this post should have an initially hidden drawer.
	 *
	 * @return boolean
	 */
	public function should_do_expandable() : bool {
		return $this->do_map
			|| ( WP::get_the_content() && 'true' !== $this->atts['show-description'] );
	}

	/**
	 * Whether this post should always be visible.
	 *
	 * @return boolean
	 */
	public function should_always_be_visible() : bool {
		return null !== $this->term
			? WP::has_term( $this->term->term_id, Schedule::CATEGORY_NAME, $this->post )
			: false;
	}

	/**
	 * Whether this post should be hidden on pageload.
	 *
	 * @return boolean
	 */
	public function should_display_none() : bool {
		static $should_display_none;

		return ! empty( $this->term ) && ( 'true' === $this->atts['tag-selector'] && ! $this->always_visible );
	}

	/**
	 * Get a string of term slugs separated by spaces for use as CSS classes.
	 *
	 * @param array $terms Term objects.
	 * @return string The term slugs prepared for a CSS class.
	 */
	public static function term_slugs_to_class_list( array $terms = [] ) : string {
		return $terms
			? ' ' . implode(
				' ',
				array_map(
					function( $term ) {
						return $term->slug;
					},
					$terms
				)
			)
			: '';
	}

	/**
	 * Get the term ids.
	 *
	 * @param array $terms Term objects.
	 * @return array The term ids.
	 */
	public static function get_term_ids( array $terms = [] ) : array {
		return array_map(
			function( $term ) {
				return $term->term_id;
			},
			$terms
		);
	}

	/**
	 * Echo the term IDs separated by commas.
	 *
	 * @param array $terms Term objects.
	 * @return void
	 */
	public static function term_ids( array $terms = [] ) : void {
		echo $terms
			? implode(
				',',
				self::get_term_ids( $terms )
			)
			: '0';
	}

	/**
	 * Render the map fields as data attributes.
	 *
	 * @param array $map Map fields.
	 * @return void
	 */
	public static function google_map_attributes( $map ) {
		echo array_reduce(
			array_keys( $map ),
			function( $output, $key ) use ( $map ) {
				if ( ! $key || empty( $map[ $key ] ) ) {
					return $output;
				}

				$value = WP::esc_attr( $map[ $key ] );
				return $output .= " data-$key=\"$value\"";
			},
			''
		);
	}

	/**
	 * Setup and render the block.
	 *
	 * @param array $atts Arguments from the shortcode or otherwise.
	 * @return string HTML.
	 */
	public function render_block( array $atts = [] ) : string {
		global $post;

		$this->atts = shortcode_atts( self::DEFAULT_ATTS, $atts );
		$this->post = $this->post ?? $this->get_post( $atts );

		if ( empty( $this->post ) ) {
			return '';
		}

		$post = $this->post; // @codingStandardsIgnoreLine WordPress.Variables.GlobalVariables.OverrideProhibited

		setup_postdata( $post );

		$this->terms = $this->get_terms();
		$this->do_map = $this->should_do_map();
		$this->map = $this->get_map();
		$this->do_expandable = $this->should_do_expandable();
		$this->always_visible = $this->should_always_be_visible();

		return $this->render();
	}

	/**
	 * Gets the term name to display above the event title (if one exists).
	 *
	 * @return string The term name or an empty string.
	 */
	public function get_term_name() {
		$terms = WP::get_the_terms( WP::get_the_id(), Schedule::CATEGORY_NAME );

		$terms = array_filter(
			$terms,
			function( $term ) {
				return ! in_array( $term->parent, [ 0, '0' ], true );
			}
		);

		if ( count( $terms ) ) {
			return $terms[0]->name;
		}

		return '';
	}

	/**
	 * Renders the HTML.
	 *
	 * @return string HTML.
	 */
	public function render() : string {
		$name = $this->get_term_name();

		ob_start(); ?>
		<div data-event
		class="col-12 event-container <?php echo self::term_slugs_to_class_list( $this->terms ); ?>"
		data-event-always-visible="<?php echo $this->always_visible ? 'true' : 'false'; ?>"
		data-event-tag-ids="<?php self::term_ids( $this->terms ); ?>"
		<?php if ( $this->should_display_none() ) : ?>
		style="display: none"
		<?php endif; ?>
		>
		<div class="collapsible event" data-collapsible>
			<<?php echo 'true' !== $this->atts['show-description'] ? 'button' : 'div'; ?> class="collapsible-heading event__heading"
				aria-pressed="false">
				<span class="event__time">
					<?php echo Event::get_formatted_event_time(); ?>
				</span>	
				<span class="event__info">
					<span class="event__details">
						<?php if ( $name ) : ?>
						<span class="event__always-visible-text">
							<?php echo $name; ?>
						</span>
						<?php endif; ?>
						<span class="event__title">
							<?php WP::the_title(); ?>
						</span>
						<span class="event__location">
							<?php echo EventMeta::get( EventMeta::LOCATION_KEY ); ?>
						</span>
						<?php if ( 'true' === $this->atts['show-description'] ) : ?>
						<span class="event__description">
							<?php the_content(); ?>
						</span>
							<?php echo Event::get_calendar_data(); ?>
						<?php endif; ?>
					</span>
				</span>
				<?php if ( 'true' !== $this->atts['show-description'] ) : ?>
				<span class="event__arrow-container">
					<svg width="1792" height="1792" viewBox="0 0 1792 1792" class="down-arrow-svg">
						<title>Show More</title>
						<path d="M1395 736q0 13-10 23l-466 466q-10 10-23 10t-23-10l-466-466q-10-10-10-23t10-23l50-50q10-10 23-10t23 10l393 393 393-393q10-10 23-10t23 10l50 50q10 10 10 23z" fill="currentColor"/>
					</svg>
				</span>
				<?php endif; ?>
			</<?php echo 'true' !== $this->atts['show-description'] ? 'button' : 'div'; ?>>
			<?php if ( 'true' !== $this->atts['show-description'] ) : ?>
			<div class="collapsible-panel" aria-hidden="true">
				<?php if ( ! $this->do_map ) : ?>
					<?php WP::the_content(); ?>
					<?php echo Event::get_calendar_data(); ?>
				<?php else : ?>
				<div class="row">
					<div class="col-12 col-md-6">
						<?php if ( trim( WP::get_the_content() ) ) : ?>
							<?php WP::the_content(); ?>
						<?php endif; ?>
						<?php echo Event::get_calendar_data(); ?>
					</div>
					<div class="col-12<?php echo trim( WP::get_the_content() ) ? ' col-md-6' : ''; ?>">
						<div style="height: 100%; min-height: 250px;"
							data-google-map<?php self::google_map_attributes( $this->map ); ?>>
						</div>
					</div>
				</div>
				<?php endif; ?>
			</div>
			<?php endif; ?>
		</div>
	</div>
		<?php
		return ob_get_clean();
	}
}
