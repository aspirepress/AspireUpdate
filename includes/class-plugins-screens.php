<?php
/**
 * The Class for overriding the default plugins screens.
 *
 * @package aspire-update
 */

namespace AspireUpdate;

/**
 * The Class for overriding the default plugins screens.
 */
class Plugins_Screens {
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
		'featured',
		'favorites',
	);

	/**
	 * The Constructor.
	 */
	public function __construct() {
		$admin_settings = Admin_Settings::get_instance();
		if ( $admin_settings->get_setting( 'enable', false ) ) {
			add_filter( 'install_plugins_tabs', array( $this, 'remove_unused_filter_tabs' ) );
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
	 * Remove unused filter tabs from the Add New Plugin screen.
	 *
	 * @param array $tabs An array of tab labels, keyed on each tab's slug.
	 * @return array An array of tabs.
	 */
	public function remove_unused_filter_tabs( $tabs ) {
		foreach ( $this->unsupported_filters as $filter ) {
			unset( $tabs[ $filter ] );
		}
		return $tabs;
	}
}
