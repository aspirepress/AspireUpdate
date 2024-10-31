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
	protected $unsupported_filters = array(
		'favorites',
	);

	/**
	 * The Constructor.
	 */
	public function __construct() {
		$admin_settings = Admin_Settings::get_instance();
		if ( $admin_settings->get_setting( 'enable', false ) ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
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
				array(),
				AP_VERSION
			);

			wp_enqueue_style( 'aspire_update_themes_screens_css' );

			$css_selectors = array();
			foreach ( $this->unsupported_filters as $filter ) {
				$css_selectors[] = '.wp-filter .filter-links a[data-sort="' . $filter . '"]';
			}

			wp_add_inline_style(
				'aspire_update_themes_screens_css',
				implode( ', ', $css_selectors ) . '{ display: none; }'
			);
		}
	}
}
