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
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_styles' ) );
		add_shortcode( 'wp-hunt-posts', array( $this, 'posts' ) );
		add_shortcode( 'wp-hunt-goals', array( $this, 'goals' ) );
	}

	/**
	 * Register the stylesheets for the public area.
	 *
	 * @since    0.0.0
	 */
	public function enqueue_public_styles() {
		wp_enqueue_style( $this->plugin->__get( 'name' ), $this->plugin->__get( 'url' ) . 'assets/css/wp-hunt-public.min.css', array(), $this->plugin->__get( 'version' ), 'all' );
	}


	public function update_api( $atts, $code ) {
		$a = shortcode_atts(
			array(
				'num' => 1,
			),
			$atts
		);

		$api = '';
		//storing api casue of api rate_limit, updates every 24hr
		if ( empty( get_option( "wp_hunt_{$code}" ) ) || ( ! empty( get_option( "wp_hunt_{$code}" ) ) && ! empty( get_option( "wp_hunt_{$code}_createdat" ) ) && date( 'Y-m-d H:i:s' ) > date( 'Y-m-d H:i:s', strtotime( '+24 hours', strtotime( get_option( "wp_hunt_{$code}_createdat" ) ) ) ) ) ) {

			switch ( $code ) {
				case 'posts':
					$api = $this->plugin->api->posts( $a['num'] );
					break;

				case 'goals':
					$api = $this->plugin->api->goals( $a['num'] );
					break;
			}

			if ( isset( $api['errors'] ) ) {
				return $api['errors'][0]['error_description'] . ' Reset in ' . gmdate( 'H:i:s', $api['errors'][0]['details']['reset_in'] );
			}

			update_option( "wp_hunt_{$code}", wp_json_encode( $api ) );
			update_option( "wp_hunt_{$code}_createdat", date( 'Y-m-d H:i:s' ) );
		} else {
			$api = json_decode( get_option( "wp_hunt_{$code}" ), true );
		}
		return $api;
	}

	public function posts( $atts ) {
		$api = $this->update_api( $atts, 'posts' );

		if ( empty( $api ) ) {
			return 'Not Working ...';
		}

		if ( ! is_array( $api ) ) {
			return $api;
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
				$table .= '<br />Rating: ' . $node['reviewsRating'];
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
		return $table;
	}

	public function goals( $atts ) {
		$api = $this->update_api( $atts, 'goals' );

		if ( empty( $api ) ) {
			return 'Not Working ...';
		}

		if ( ! is_array( $api ) ) {
			return $api;
		}

		$table  = '<div class="wp-hunt">';
		$table .= '<ul>';
		foreach ( $api['data']['goals']['edges'] as $nodes ) {
			foreach ( $nodes as $node ) {
				$table .= '<li><input type="checkbox"';
				$table .= empty( $node['completedAt'] ) ? '' : 'checked';
				$table .= '><h3>' . date( 'Y-m-d', strtotime( $node['createdAt'] ) ) . '  ' . $node['title'] . $node['user']['name'] . '</h3></li>';
			}
		}

		$table .= '</ul>';
		$table .= '</div>';
		return $table;
	}
}
