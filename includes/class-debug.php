<?php
/**
 * The Class for Debug Functions.
 *
 * @package aspire-update
 */

namespace AspirePress;

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
			error_log( 'AspireUpdate - WP_Filesystem initialization failed.' );
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

		$content = $wp_filesystem->get_contents( $file_path );
		if ( $content === false ) {
			$wp_filesystem->put_contents(
				$file_path,
				$formatted_message,
				FS_CHMOD_FILE
			);
		} else {
			$wp_filesystem->put_contents(
				$file_path,
				$content . $formatted_message,
				FS_CHMOD_FILE
			);
		}
	}

	/**
	 * Formats the message to be logged.
	 *
	 * @param mixed $message The message to format (string, array, object, etc.).
	 * @return string The formatted message.
	 */
	private static function format_message( $message ) {
		if ( is_array( $message ) || is_object( $message ) ) {
			return print_r( $message, true );
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
