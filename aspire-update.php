<?php
/**
 * AspireUpdate - Update plugins and themes for WordPress.
 *
 * @package     aspire-update
 * @author      AspireUpdate
 * @copyright   AspireUpdate
 * @license     GPLv2
 *
 * Plugin Name:       AspireUpdate
 * Plugin URI:        https://aspirepress.org/
 * Description:       Update plugins and themes for WordPress.
 * Version:           0.5
 * Author:            AspirePress
 * Author URI:        https://docs.aspirepress.org/aspireupdate/
 * Requires at least: 5.3
 * Requires PHP:      7.4
 * Tested up to:      6.7
 * License:           GPLv2
 * License URI:       https://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
 * Text Domain:       AspireUpdate
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! defined( 'AP_VERSION' ) ) {
	define( 'AP_VERSION', '0.5' );
}


add_action( 'plugins_loaded', 'define_constant' );
function define_constant() {
	if ( ! defined( 'AP_PATH' ) ) {
		define( 'AP_PATH', dirname( plugin_basename( __FILE__ ) ) );
	}
}

require_once __DIR__ . '/includes/autoload.php';

add_action( 'plugins_loaded', 'aspire_update' );
function aspire_update() {
	if ( ! defined( 'AP_RUN_TESTS' ) ) {
		new AspireUpdate\Controller();
	}
}
