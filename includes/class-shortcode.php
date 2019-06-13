<?php
/**
 * WP Hunt Shortcode.
 *
 * @since   0.0.0
 * @package WP_Hunt
 */

/**
 * WP Hunt Shortcode.
 *
 * @since 0.0.0
 */
class WPH_Shortcode {
	/**
	 * Parent plugin class.
	 *
	 * @since 0.0.0
	 *
	 * @var   WP_Hunt
	 */
	protected $plugin = null;

	/**
	 * Constructor.
	 *
	 * @since  0.0.0
	 *
	 * @param  WP_Hunt $plugin Main plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since  0.0.0
	 */
	public function hooks() {
		add_shortcode( 'wp-hunt-posts', array( $this, 'posts' ) );
	}

	public function posts( $atts ) {
		$a   = shortcode_atts(
			array(
				'num' => 1,
			),
			$atts
		);

		$api = '';
		//storing api casue of api rate_limit, updates every 24hr
		if ( empty( get_option( 'wp_hunt_posts' ) ) || ( ! empty( get_option( 'wp_hunt_posts_createdat' ) ) && date( 'Y-m-d H:i:s' ) > date( 'Y-m-d H:i:s', strtotime( '+24 hours', strtotime( get_option( 'wp_hunt_posts_createdat' ) ) ) ) ) ) {
			$api = $this->plugin->api->posts( $a['num'] );
			if ( isset( $api['errors'] ) ) {
				return $api['errors'][0]['error_description'] . ' Reset in ' . gmdate( 'H:i:s', $api['errors'][0]['details']['reset_in'] );
			}
			update_option( 'wp_hunt_posts', wp_json_encode( $api ) );
			update_option( 'wp_hunt_posts_createdat', date( 'Y-m-d H:i:s' ) );
		} else {
			$api = json_decode( get_option( 'wp_hunt_posts' ), true );
		}

		if ( empty( $api ) ) {
			return 'Not Working ...';
		}

		$table  = '<div class="wp-hunt">';
		$table .= '<ul>';
		foreach ( $api['data']['posts']['edges'] as $nodes ) {
			foreach ( $nodes as $node ) {
				$table .= '<li><a class="inner" href="' . $node['url'] . '">';
				if ( 'image' === $node['thumbnail']['type'] ) {
					$table .= '<img alt="image" class="img-responsive" src="' . $node['thumbnail']['url'] . '" />';
				} else {
					$table .= '<video><source src="' . $node['thumbnail']['videoUrl'] . ' "></video>';
				}
				$table .= '</a>';
				$table .= '<h3><a class="inner" href="' . $node['url'] . '">' . $node['name'] . '</a></h3>';
				$table .= '<p>' . $node['tagline'];
				$table .= '<br />Launched: ' . date( 'Y-m-d', strtotime( $node['createdAt'] ) );
				$table .= '<br />Rating: ' . $node['reviewsRating'] ;
				$table .= '<br />Votes: ' . $node['votesCount'];
				$table .= '<br />' . $node['description'] . '</p>';
				$table .= '<br /><table cellspacing="0"><tr>';
				foreach ( $node['makers'] as $maker ) {
					$table .= '<td><a href="' . $maker['url'] . '" ><img alt="' . $maker['username'] . '" src="' . $maker['profileImage'] . '" /><p>@' . $maker['username'] . '</p></a></td>';
				}
				$table .= '</tr></table>';
				$table .= '</li>';
			}
		}

		$table .= '</ul>';
		$table .= '</div>';
		$table .= '</div>';
		return $table;
	}
}
