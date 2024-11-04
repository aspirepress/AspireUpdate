<?php
/**
 * The Class for Debug Functions.
 *
 * @package aspire-update
 */

namespace AspireUpdate;

/**
 * The Class for Debug Functions.
 */
class Debug {

	/**
	 * Name of the debug log file.
	 *
	 * @var string
	 */
	private static $log_file = 'debug-aspire-update.log';

	/**
	 * Initializes the WordPress Filesystem.
	 *
	 * @return WP_Filesystem_Base|false The filesystem object or false on failure.
	 */
	private static function init_filesystem() {
		global $wp_filesystem;

		if ( ! $wp_filesystem ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			WP_Filesystem();
		}

		return $wp_filesystem;
	}

	/**
	 * Logs a message to the debug log file.
	 *
	 * @param mixed  $message The message to log.
	 * @param string $type   The log level ('string', 'request', 'response').
	 */
	public static function log( $message, $type = 'string' ) {
		$wp_filesystem = self::init_filesystem();

		if ( ! $wp_filesystem ) {
			if (
				defined( 'WP_DEBUG' ) &&
				( true === WP_DEBUG ) &&
				defined( 'WP_DEBUG_LOG' ) &&
				( true === WP_DEBUG_LOG )
			) {
				// phpcs:disable WordPress.PHP.DevelopmentFunctions
				/**
				 * Log error in file write fails only if debug is set to true.  This is a valid use case.
				 */
				error_log( 'AspireUpdate - Could not open or write to the file system. Check file system permissions to debug log directory.' );
				// phpcs:enable
			}
			return;
		}

		$timestamp         = gmdate( 'Y-m-d H:i:s' );
		$formatted_message = sprintf(
			"[%s] [%s]: %s\n" . PHP_EOL,
			$timestamp,
			strtoupper( $type ),
			self::format_message( $message )
		);

		$file_path = WP_CONTENT_DIR . '/' . self::$log_file;

		$content = '';
		if ( $wp_filesystem->exists( $file_path ) ) {
			$content = $wp_filesystem->get_contents( $file_path );
		}

		$wp_filesystem->put_contents(
			$file_path,
			$content . $formatted_message,
			FS_CHMOD_FILE
		);
	}

	/**
	 * Formats the message to be logged.
	 *
	 * @param mixed $message The message to format (string, array, object, etc.).
	 * @return string The formatted message.
	 */
	private static function format_message( $message ) {
		if ( is_array( $message ) || is_object( $message ) ) {
			// phpcs:disable WordPress.PHP.DevelopmentFunctions
			/**
			 * Priting an array or object to log file.  This is a valid use case.
			 */
			return print_r( $message, true );
			 // phpcs:enable
		}
		return (string) $message;
	}

	/**
	 * Log an info message.
	 *
	 * @param mixed $message The message to log.
	 */
	public static function log_string( $message ) {
		$admin_settings = Admin_Settings::get_instance();
		$debug_mode     = $admin_settings->get_setting( 'enable_debug', false );
		$debug_types    = $admin_settings->get_setting( 'enable_debug_type', array() );
		if ( $debug_mode && is_array( $debug_types ) && in_array( 'string', $debug_types, true ) ) {
			self::log( $message, 'string' );
		}
	}

	/**
	 * Log a warning message.
	 *
	 * @param mixed $message The message to log.
	 */
	public static function log_request( $message ) {
		$admin_settings = Admin_Settings::get_instance();
		$debug_mode     = $admin_settings->get_setting( 'enable_debug', false );
		$debug_types    = $admin_settings->get_setting( 'enable_debug_type', array() );
		if ( $debug_mode && is_array( $debug_types ) && in_array( 'request', $debug_types, true ) ) {
			self::log( $message, 'request' );
		}
	}

	/**
	 * Log an error message.
	 *
	 * @param mixed $message The message to log.
	 */
	public static function log_response( $message ) {
		$admin_settings = Admin_Settings::get_instance();
		$debug_mode     = $admin_settings->get_setting( 'enable_debug', false );
		$debug_types    = $admin_settings->get_setting( 'enable_debug_type', array() );
		if ( $debug_mode && is_array( $debug_types ) && in_array( 'response', $debug_types, true ) ) {
			self::log( $message, 'response' );
		}
	}
}
