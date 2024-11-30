<?php
/**
 * The Class for overriding the default themes screens.
 *
 * @package aspire-update
 */

namespace AspireUpdate;

/**
 * The Class for overriding the default themes screens.
 */
class Themes_Screens {
	/**
	 * Hold a single instance of the class.
	 *
	 * @var object
	 */
	private static $instance = null;

	/**
	 * Hold an array of unsupported filters.
	 *
	 * @var array
	 */
	protected $unsupported_filters = [
		'favorites',
	];

	/**
	 * The Constructor.
	 */
	public function __construct() {
		$admin_settings = Admin_Settings::get_instance();
		if ( $admin_settings->get_setting( 'enable', false ) ) {
			add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
			add_action( 'load-theme-install.php', [ $this, 'redirect_to_theme_install' ] );
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
	 * Enqueue the styles.
	 *
	 * @param string $hook The page identifier.
	 * @return void
	 */
	public function admin_enqueue_scripts( $hook ) {
		if ( 'theme-install.php' !== $hook ) {
			return;
		}

		if ( ! empty( $this->unsupported_filters ) ) {
			wp_register_style(
				'aspire_update_themes_screens_css',
				false,
				[],
				AP_VERSION
			);

			wp_enqueue_style( 'aspire_update_themes_screens_css' );

			$css_selectors = [];
			foreach ( $this->unsupported_filters as $filter ) {
				$css_selectors[] = '.wp-filter .filter-links a[data-sort="' . $filter . '"]';
			}

			wp_add_inline_style(
				'aspire_update_themes_screens_css',
				implode( ', ', $css_selectors ) . '{ display: none; }'
			);
		}
	}

	/**
	 * Redirect unsupported filters to theme-install.php.
	 *
	 * @param string $hook The page identifier.
	 * @return void
	 */
	public function redirect_to_theme_install() {

		$nonce = isset( $_REQUEST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ) : false;
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'query-themes' ) ) {
			return;
		}

		$browse = isset( $_GET['browse'] ) ? sanitize_text_field( wp_unslash( $_GET['browse'] ) ) : '';

		if ( ! in_array( $browse, $this->unsupported_filters, true ) ) {
			return;
		}

		$admin_settings = Admin_Settings::get_instance();
		if ( $admin_settings->get_setting( 'enable', false ) ) {
			wp_safe_redirect( admin_url( 'theme-install.php' ) );
			! defined( 'AP_RUN_TESTS' ) && exit;
		}
	}
}
