<?php
/**
 * WP Hunt Admin.
 *
 * @since   0.0.0
 * @package WP_Hunt
 */

/**
 * WP Hunt Admin.
 *
 * @since 0.0.0
 */
class WPH_Admin {
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
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    0.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin->__get( 'name' ), $this->plugin->__get( 'url' ) . 'assets/css/wp-hunt.min.css', array(), $this->plugin->__get( 'version' ), 'all' );
		wp_enqueue_style( 'fontawesome', $this->plugin->__get( 'url' ) . 'assets/css/all.min.css', array(), '5.8.1', 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    0.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin->__get( 'name' ), $this->plugin->__get( 'url' ) . 'assets/js/wp-hunt.min.js', array( 'jquery' ), $this->plugin->__get( 'version' ), true );
		wp_localize_script( $this->plugin->__get( 'name' ),
			'wp_hunt_vars',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'wp-hunt-nonce' ),
				'error_message' => __( 'Sorry, there was a problem processing your request.', 'wp-hunt' ),
				'success_message' => __( 'Settings updated!', 'wp-hunt' ),
			)
		);
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    0.0.0
	 */
	public function add_menu() {
		add_menu_page( 'WP Hunt', 'Product Hunt', 'manage_options', 'wp-hunt', array( $this, 'page_display' ) );
		add_submenu_page( 'wp-hunt', 'WP Hunt API', 'Product Hunt', 'manage_options', 'wp-hunt', array( $this, 'page_display' ) );
	}

	/**
	 * Initializing settings.
	 *
	 * @since    0.0.0
	 */
	public function init() {
		add_option( 'wp_hunt_clientid' );
		add_option( 'wp_hunt_secretkey' );
		add_option( 'wp_hunt_api_token' );
	}

	/**
	 * Current Page.
	 *
	 * @since    0.0.0
	 */
	public function page_display() {
		?>
		<div class="wrap">
			<div id="wp-hunt">
				<h1><?php esc_html_e( 'WP Hunt', 'wp-hunt' ); ?></h1>
				<H2><?php esc_attr_e( 'Product Hunt API Setup', 'wp-hunt' ); ?></H2>
				<div class="divTable">
					<div class="divTableRow">
						<div class="divTableCell-first">
							<label><?php esc_html_e( 'API Client ID', 'wp-hunt' ); ?></label>
						</div>
						<div class="divTableCell-content">
							<input type="text" name="wp-hunt-client-id" id="wp-hunt-client-id" value="<?php echo get_option( 'wp_hunt_clientid' ) ?>" placeholder="YOUR_API_KEY_HERE" />
						</div>
						<div class="divTableCell-placeholder">The id of your application
						</div>
					</div>
					<div class="divTableRow">
						<div class="divTableCell-first">
							<label><?php esc_html_e( 'API Client Secret Key', 'wp-hunt' ); ?></label>
						</div>
						<div class="divTableCell-content">
							<input type="text" name="wp-hunt-client-secret" id="wp-hunt-client-secret" value="<?php echo get_option( 'wp_hunt_secretkey' ) ?>" placeholder="YOUR_API_SECRET_HERE" />
						</div>
						<div class="divTableCell-placeholder">The secret of your application
						</div>
					</div>
					<!-- <div class="divTableRow">
						<div class="divTableCell-first">
							<label><?php esc_html_e( 'API Redirect Uri', 'wp-hunt' ); ?></label>
						</div>
						<div class="divTableCell-content">
							<input type="text" name="wp-hunt-redirect-uri" id="wp-hunt-redirect-uri" value="<?php echo !empty( get_option( 'wp_hunt_redirect_uri' ) )  ? esc_attr( get_option( 'wp_hunt_redirect_uri' ) ) : get_site_url()  . '/wp_hunt/callback'; ?>" placeholder="YOUR_API_REDIRECT_URI_HERE" />
						</div>
						<div class="divTableCell-placeholder">Where shall we redirect the client afterwards to?
						</div>
					</div> -->
					<?php if ( ! empty( get_option( 'wp_hunt_api_token' ) ) ): ?>
						<div id="wp-hunt-api-token-div" class="divTableRow">
							<div class="divTableCell-first">
								<label><?php esc_html_e( 'API Token', 'wp-hunt' ); ?></label>
							</div>
							<div class="divTableCell-content">
								<input type="text" name="wp-hunt-api-token" id="wp-hunt-api-token" value="<?php echo get_option( 'wp_hunt_api_token' ) ?>" readonly />
							</div>
							<div class="divTableCell-placeholder">
							</div>
						</div>
					<?php endif ?>
					<div class="divTableRow">
						<div class="divTableCell-content">
							<button type='submit' name='wp-hunt-api-authenticate' id='wp-hunt-api-authenticate' class='button btn' data-api-type="<?php echo empty( get_option( 'wp_hunt_api_token' ) ) ? 1 : 0; ?>" ><?php esc_html_e( 'Authenticate', 'wp-hunt' ); ?> <i class='fa fa-key fa-lg'></i></button>
						</div>
					</div>
				</div>
				<div id='wp-hunt-api-results'></div>
			</div>
		</div>
		<div>
			<button type='submit' name='wp-hunt-api-posts' id='wp-hunt-api-posts' class='button btn'><?php esc_html_e( 'POSTS', 'wp-hunt' ); ?> <i class='fa fa-key fa-lg'></i></button>
		</div>
	<?php
	}
}
