<?php
/**
 * WP Hunt Template.
 *
 * @since   0.0.1
 * @package WP_Hunt
 */

/**
 * WP Hunt Template.
 *
 * @since 0.0.1
 */
class WPH_Template {
	/**
	 * Parent plugin class.
	 *
	 * @since 0.0.1
	 *
	 * @var   WP_Hunt
	 */
	protected $plugin = null;

	/**
	 * Constructor.
	 *
	 * @since  0.0.1
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
	 * @since  0.0.1
	 */
	public function hooks() {

	}

	/**
	 * Get template.
	 *
	 * @param string $template_name Template name.
	 * @param array  $args          Arguments. (default: array).
	 * @param string $default_path  Default path. (default: '').
	 * @return string
	 */
	public function get_template( $template_name, $args = array(), $template_path = '' ) {
		if ( ! $template_path ) {
			$template_path = $this->plugin->path . '/templates/';
		}

		$filter_template = trailingslashit( $template_path ) . $template_name;

		if ( ! file_exists( $filter_template ) ) {
			_doing_it_wrong( __FUNCTION__, sprintf( __( '%s does not exist.', 'wp-hunt' ), '<code>' . $template . '</code>' ), '5.2.2' );
			return;
		}

		if ( ! empty( $args ) && is_array( $args ) ) {
			extract( $args ); // @codingStandardsIgnoreLine
		}

		// Look within passed path within the theme - this is priority.
		$template = locate_template(
			array(
				$filter_template,
				$template_name,
			)
		);

	}

	public function get_template_html( $template_name, $args = array(), $template_path = '' ) {
		ob_start();
		$this->get_template( $template_name, $args, $template_path );
		return ob_get_clean();
	}
}
