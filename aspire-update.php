<?php
/**
 * Aspire Updater - Update plguins and themes for WordPress.
 *
 * @package     aspire-update
 * @author      AspirePress
 * @copyright   AspirePress
 * @license     GPL-3.0-or-later
 *
 * Plugin Name:       AspirePress Updater
 * Plugin URI:        https://aspirepress.org/
 * Description:       Update plguins and themes for WordPress.
 * Version:           0.5
 * Author:            AspirePress
 * Author URI:        https://docs.aspirepress.org/aspireupdate/
 * Requires at least: 4.0
 * Requires PHP:      8.0.0
 * Tested up to:      6.7
 * License:           GPL-3.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       aspirepress
 * Domain Path:       /resources/languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! defined( 'AP_VERSION' ) ) {
	define( 'AP_VERSION', '0.5' );
}

require_once __DIR__ . '/includes/autoload.php';

new AspirePress\Controller();
