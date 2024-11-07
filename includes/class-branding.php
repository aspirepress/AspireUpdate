<?php
/**
 * The Class for adding branding to the dashboard.
 *
 * @package aspire-update
 */

namespace AspireUpdate;

/**
 * The Class for adding branding to the dashboard.
 */
class Branding {
	/**
	 * Hold a single instance of the class.
	 *
	 * @var object
	 */
	private static $instance = null;

	/**
	 * The Constructor.
	 */
	public function __construct() {
		$admin_settings = Admin_Settings::get_instance();
		if ( $admin_settings->get_setting( 'enable', false ) ) {
			add_action( 'admin_notices', [ $this, 'output_admin_notice' ] );
			add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
		}
	}

	/**
	 * Initialize Class.
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @param string $hook The page identifier.
	 * @return void
	 */
	public function admin_enqueue_scripts( $hook ) {
		if ( defined( 'AP_REMOVE_UI' ) && AP_REMOVE_UI ) {
			return;
		}

		$allowed_screens = [
			'update-core.php',
			'plugins.php',
			'plugin-install.php',
			'themes.php',
			'theme-install.php',
		];

		if ( in_array( $hook, $allowed_screens, true ) ) {
			wp_enqueue_style( 'aspire_update_settings_css', plugin_dir_url( __DIR__ ) . 'assets/css/aspire-update.css', [], AP_VERSION );
		}
	}

	/**
	 * Output admin notice.
	 *
	 * @return void
	 */
	public function output_admin_notice() {
		if ( defined( 'AP_REMOVE_UI' ) && AP_REMOVE_UI ) {
			return;
		}

		$current_screen = get_current_screen();
		if ( ! $current_screen instanceof \WP_Screen ) {
			return;
		}

		$message = '';
		switch ( $current_screen->base ) {
			case 'plugins':
			case 'plugin-install':
				$message = sprintf(
					/* translators: 1: The name of the plugin, 2: The documentation URL. */
					__( 'Your plugin updates are now powered by <strong>%1$s</strong>. <a href="%2$s">Learn more</a>', 'AspireUpdate' ),
					'AspireUpdate',
					__( 'https://docs.aspirepress.org/aspireupdate/', 'AspireUpdate' )
				);
				break;
			case 'themes':
			case 'theme-install':
				$message = sprintf(
					/* translators: 1: The name of the plugin, 2: The documentation URL. */
					__( 'Your theme updates are now powered by <strong>%1$s</strong>. <a href="%2$s">Learn more</a>', 'AspireUpdate' ),
					'AspireUpdate',
					__( 'https://docs.aspirepress.org/aspireupdate/', 'AspireUpdate' )
				);
				break;
			case 'update-core':
				$message = sprintf(
					/* translators: 1: The name of the plugin, 2: The documentation URL. */
					__( 'Your WordPress, plugin, theme and translation updates are now powered by <strong>%1$s</strong>. <a href="%2$s">Learn more</a>', 'AspireUpdate' ),
					'AspireUpdate',
					__( 'https://docs.aspirepress.org/aspireupdate/', 'AspireUpdate' )
				);
				break;
		}

		if ( '' === $message ) {
			return;
		}

		echo wp_kses_post( '<div class="notice aspireupdate-notice notice-info"><p>' . $message . '</p></div>' );
	}
}
