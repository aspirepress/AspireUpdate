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
	 * The Constructor.
	 */
	public function __construct() {
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
}
