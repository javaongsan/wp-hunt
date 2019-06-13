<?php
/**
 * WP Hunt Ajax.
 *
 * @since   0.0.0
 * @package WP_Hunt
 */

/**
 * WP Hunt Ajax.
 *
 * @since 0.0.0
 */
class WPH_Ajax {
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
		add_action( 'wp_ajax_wp_hunt_api_authenticate', array( $this, 'wp_hunt_api_authenticate' ) );
	}

	public function wp_hunt_api_authenticate() {
		if ( isset( $_POST['data'] ) && wp_verify_nonce( $_POST['wp_hunt_nonce'], 'wp-hunt-nonce' ) ) {
			$results = $this->plugin->api->authenticate( $_POST['data'] );
			if ( $results ) {
				echo $results;
			} else {
				echo 'fail';
			}
		}
		die();
	}
}
