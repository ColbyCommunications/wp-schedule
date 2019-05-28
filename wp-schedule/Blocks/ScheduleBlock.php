<?php
/**
 * Creates a shortcode which renders a schedule.
 *
 * @package wp-schedule
 */

namespace ColbyComms\Schedules\Blocks;

use ColbyComms\SVG\SVG;
use ColbyComms\Schedules\Event\EventMeta;
use ColbyComms\Schedules\Schedule\Schedule;
use ColbyComms\Schedules\Utils\{WpQuery, WpFunctions as WP};

/**
 * Block [schedule].
 */
class ScheduleBlock {
	const SHORTCODE_TAG = 'schedule';

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
	 * Registers the shortcode callback.
	 */
	public function __construct() {
		if ( ! WP::shortcode_exists( self::SHORTCODE_TAG ) ) {
			WP::add_shortcode( self::SHORTCODE_TAG, [ __CLASS__, 'render_shortcode' ] );
		}

		WP::add_filter( 'query_vars', [ __CLASS__, 'add_url_query_vars' ] );
	}

	/**
	 * Renders the block.
	 *
	 * @param array $atts Arguments.
	 * @param array $days Associative array of posts sorted by day.
	 * @param array $tags The tags applicable to the page.
	 * @param array $active_tags The initially active tag.
	 * @param \WP_Term $term If we're on a schedule archive page, the WP_Term object.
	 * @return string HTML.
	 */
	public static function render( array $atts = [], array $days = [], array $tags = [], array $active_tags = [], \WP_Term $term = null ) : string {
		ob_start();

		if ( isset( $atts['tag-selector'] ) && 'true' === $atts['tag-selector'] ) : ?>
<div class="tag-selector">
	<button data-all-events-button class="btn highlight tag-selector-btn">
		Show all events
	</button>
	<form class="schedule__tag-form">
		<div class="schedule__list-description">Select by event category</div>
		<ul class="schedule__tag-list">
			<?php foreach ( $tags as $tag ) : ?>
			<li>
				<label>
					<input
						type="checkbox"
						name="event-tag"
						value="<?php echo WP::esc_attr( $tag->term_id ); ?>"
						<?php echo null === $term || in_array( $tag->name, $active_tags, true ) ? 'checked' : ''; ?>>
					<span><?php echo $tag->name; ?></span>
				</label>
			</li>
		<?php endforeach; ?>
		</ul>
	</form>
</div>
<?php endif; ?>
<aside class="schedule__print-email">
	<div class="schedule__print">
		<a href="javascript:window.print()"><?php SVG::show( 'print' ); ?></a>
	</div>
</aside>
<div class="schedule">
		<?php
		foreach ( $days as $date => $events ) :

			echo DayBlock::render( $atts, $date, $events, $term );

	endforeach;
		?>
</div>

		<?php
		return ob_get_clean();
	}

	/**
	 * Usort callback for putting tags in order by name.
	 *
	 * @param object $tag1 The first tag.
	 * @param object $tag2 The second tag.
	 * @return integer
	 */
	public static function sort_tags( $tag1, $tag2 ) : int {
		if ( $tag1->name === $tag2->name ) {
			return 0;
		}

		return $tag1->name < $tag2->name ? -1 : 1;
	}

	/**
	 * Set up variables to send to the template.
	 *
	 * @param \WP_Query $events_query A query for events posts.
	 * @param array $atts Block attributes.
	 * @param mixed $term The term if we're on a term archive page.
	 * @return string The shortcode output.
	 */
	public static function render_block( \WP_Query $events_query, array $atts = [], $term = null ) : string {
		$atts = WP::shortcode_atts( self::DEFAULT_ATTS, $atts );
		$posts = self::filter_posts_not_in_this_category( $events_query->posts );
		$days = self::sort_posts_by_day( $posts );
		$tags = self::get_all_post_tag_ids( $events_query->posts );
		usort( $tags, [ __CLASS__, 'sort_tags' ] );

		// Make an array from the active tags passed to the shortcode.
		$active_tags = array_map( 'trim', explode( ',', isset( $atts['active'] ) ? $atts['active'] : '' ) );

		// Sort by date.
		ksort( $days );

		return self::render( $atts, $days, $tags, $active_tags, $term );
	}

	/**
	 * Filters out posts that have a category that isn't the parent and are not in the current category.
	 *
	 * @param array $posts Post objects.
	 * @return array Filtered posts.
	 */
	public static function filter_posts_not_in_this_category( array $posts = [] ) : array {
		static $parent_term;

		return array_filter(
			$posts,
			function( $post ) use ( &$parent_term ) {
				$terms = get_the_terms( $post, 'schedule_category' );

				if ( count( $terms ) === 1 && '0' === $terms[0]->parent ) {
					return true;
				}

				$parent_term = $parent_term ?? array_filter(
					$terms,
					function( $term ) {
						return in_array( $term->parent, [ 0, '0' ], true );
					}
				)[0]->term_id;

				$has_other_term = false;
				foreach ( $terms as $term ) {
					if ( ! in_array( $term->term_id, [ get_queried_object_id(), $parent_term ], true ) ) {
						$has_other_term = true;
					}

					if ( get_queried_object_id() === $term->term_id ) {
						return true;
					}
				}

				if ( $has_other_term ) {
					return false;
				}

				return true;
			}
		);
	}

	/**
	 * The [schedule] shortcode callback.
	 *
	 * @param array  $atts Block attributes.
	 * @param string $content Block content.
	 * @return string The shortcode output.
	 */
	public static function render_shortcode( $atts = [], $content = '' ) {
		$atts = WP::shortcode_atts( self::DEFAULT_ATTS, $atts );
		$events_query = self::get_events_query( $atts );

		if ( ! $events_query->have_posts() ) {
			return '';
		}

		return self::render_block( $events_query, $atts );
	}

	/**
	 * Sort an array of posts into an associative array sorted by date.
	 *
	 * @param array $posts A set of WP_Post objects.
	 * @return array The resulting associative array.
	 */
	public static function sort_posts_by_day( $posts = [] ) {
		return array_reduce(
			$posts,
			function( $output, $post ) {
				$day = EventMeta::get( EventMeta::DATE_KEY, $post->ID );
				$output[ $day ][] = $post;
				return $output;
			},
			[]
		);
	}

	/**
	 * Gets an array of unique event_tag term objects from a group of posts.
	 *
	 * @param array $posts WP_Post objects.
	 * @return array The tag ids.
	 */
	public static function get_all_post_tag_ids( $posts = [] ) {
		return array_reduce(
			$posts,
			function( $output, $post ) {
				static $term_ids_in_output;

				if ( empty( $term_ids_in_output ) ) {
					$term_ids_in_output = [];
				}

				$terms = WP::get_the_terms( $post->ID, 'event_tag' ) ?: [];

				if ( ! $terms && ! in_array( 0, $term_ids_in_output, true ) ) {
					$term_ids_in_output[] = 0;
					$output[] = (object) [
						'term_id' => 0,
						'name' => 'Uncategorized',
					];
				}

				foreach ( $terms as $term ) {
					// The term has not been captured yet.
					if ( ! in_array( $term->term_id, $term_ids_in_output, true ) ) {
						$term_ids_in_output[] = $term->term_id;
						$output[] = $term;
					}
				}

				return $output;
			},
			[]
		);
	}

	/**
	 * Add 'event-tag' to query vars.
	 *
	 * @param array $qvars Query variables.
	 * @return array Updated query variables.
	 */
	public static function add_url_query_vars( $qvars ) {
		$qvars[] = 'invitee-group';
		return $qvars;
	}

	/**
	 * Gets a query for posts with the event post_type.
	 *
	 * @param array $atts Block attributes.
	 * @return \WP_Query
	 */
	public static function get_events_query( array $atts = [] ) : \WP_Query {
		$query_params = [
			'post_type'      => 'event',
			'posts_per_page' => 99,
			'meta_query'     => [
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
			],
			'orderby' => [
				'schedule_date' => 'ASC',
				'schedule_time' => 'ASC',
			],
		];

		// URL params take precedent.
		if ( empty( $query_params['tax_query'] ) ) {
			$query_params = self::add_params_from_shortcode_atts( $query_params, $atts );
		}

		return new \WP_Query( $query_params );
	}

	/**
	 * Create query parameters from shortcode attributes.
	 *
	 * @param array $query_params Query parameters.
	 * @param array $atts Block attributes.
	 * @return array Parameters for the WP_Query.
	 */
	private static function add_params_from_shortcode_atts( $query_params, $atts ) {
		if ( isset( $atts['name'] ) || isset( $atts['tags'] ) ) {
			$query_params['tax_query'] = [];
		}

		if ( isset( $atts['name'] ) ) {
			$query_params['tax_query'][] = [
				'taxonomy' => 'schedule_category',
				'field'    => 'name',
				'terms'    => [ "{$atts['name']}" ],
			];
		}

		if ( isset( $atts['tags'] ) ) {
			$query_params['tax_query'][] = [
				'taxonomy' => 'event_tag',
				'field'    => 'name',
				'terms'    => explode( ',', $atts['tags'] ),
			];
		}

		// Show events that have passed.
		if ( false !== $atts['include-past-events'] ) {
			unset( $query_params['meta_query']['schedule_date']['value'] );
			$query_params['meta_query']['schedule_date']['compare'] = 'EXISTS';

		}
		return $query_params;
	}
}
