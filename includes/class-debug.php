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
	 * Get the Log file path.
	 *
	 * @return string The Log file path.
	 */
	private static function get_file_path() {
		return WP_CONTENT_DIR . '/' . self::$log_file;
	}

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
	 * Checks the filesystem status and logs error to debug log.
	 *
	 * @param WP_Filesystem_Base $wp_filesystem The filesystem object.
	 *
	 * @return boolean true on success and false on failure.
	 */
	private static function verify_filesystem( $wp_filesystem ) {
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
			return false;
		}
		return true;
	}

	/**
	 * Get the content of the log file truncated upto N number of lines.
	 *
	 * @param integer $limit Max no of lines to return. Defaults to a 1000 lines.
	 *
	 * @return string|WP_Error The File content truncate upto the number of lines set in the limit parameter.
	 */
	public static function read( $limit = 1000 ) {
		$wp_filesystem = self::init_filesystem();
		$file_path     = self::get_file_path();
		if ( ! self::verify_filesystem( $wp_filesystem ) || ! $wp_filesystem->exists( $file_path ) || ! $wp_filesystem->is_readable( $file_path ) ) {
			return new \WP_Error( 'not_readable', __( 'Error: Unable to read the log file.', 'AspireUpdate' ) );
		}

		$file_content = $wp_filesystem->get_contents_array( $file_path );
		$content      = '';
		$index        = 0;
		foreach ( $file_content as $file_content_lines ) {
			if ( ( $index < $limit ) ) {
				$content .= $file_content_lines . PHP_EOL;
				++$index;
			}
		}
		if ( '' === trim( $content ) ) {
			$content = esc_html__( '*****Log file is empty.*****', 'AspireUpdate' );
		} elseif ( $limit < count( $file_content ) ) {
			$content .= PHP_EOL . sprintf(
				/* translators: 1: The number of lines at which the content was truncated. */
				esc_html__( '*****Log truncated at %s lines.*****', 'AspireUpdate' ),
				$limit
			);
		}
		return $content;
	}

	/**
	 * Clear content of the log file.
	 *
	 * @return boolean true on success and false on failure.
	 */
	public static function clear() {
		$wp_filesystem = self::init_filesystem();
		if ( ! self::verify_filesystem( $wp_filesystem ) ) {
			return false;
		}
		$file_path = self::get_file_path();
		if ( $wp_filesystem->exists( $file_path ) && $wp_filesystem->is_writable( $file_path ) ) {
			$wp_filesystem->put_contents(
				$file_path,
				'',
				FS_CHMOD_FILE
			);
			return true;
		}
		return false;
	}

	/**
	 * Logs a message to the debug log file.
	 *
	 * @param mixed  $message The message to log.
	 * @param string $type   The log level ('string', 'request', 'response').
	 */
	public static function log( $message, $type = 'string' ) {
		$wp_filesystem = self::init_filesystem();
		if ( self::verify_filesystem( $wp_filesystem ) ) {
			$timestamp         = gmdate( 'Y-m-d H:i:s' );
			$formatted_message = sprintf(
				'[%s] [%s]: %s',
				$timestamp,
				strtoupper( $type ),
				self::format_message( $message )
			) . PHP_EOL;

			$file_path = self::get_file_path();

			$content = '';
			if ( $wp_filesystem->exists( $file_path ) ) {
				if ( $wp_filesystem->is_readable( $file_path ) ) {
					$content = $wp_filesystem->get_contents( $file_path );
				}
			}
			$wp_filesystem->put_contents(
				$file_path,
				$formatted_message . $content,
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
	 *
	 * @return void
	 */
	public static function log_string( $message ) {
		$admin_settings = Admin_Settings::get_instance();
		$debug_mode     = $admin_settings->get_setting( 'enable_debug', false );
		$debug_types    = $admin_settings->get_setting( 'enable_debug_type', [] );
		if ( $debug_mode && is_array( $debug_types ) && in_array( 'string', $debug_types, true ) ) {
			self::log( $message, 'string' );
		}
	}

	/**
	 * Log a warning message.
	 *
	 * @param mixed $message The message to log.
	 *
	 * @return void
	 */
	public static function log_request( $message ) {
		$admin_settings = Admin_Settings::get_instance();
		$debug_mode     = $admin_settings->get_setting( 'enable_debug', false );
		$debug_types    = $admin_settings->get_setting( 'enable_debug_type', [] );
		if ( $debug_mode && is_array( $debug_types ) && in_array( 'request', $debug_types, true ) ) {
			self::log( $message, 'request' );
		}
	}

	/**
	 * Log an error message.
	 *
	 * @param mixed $message The message to log.
	 *
	 * @return void
	 */
	public static function log_response( $message ) {
		$admin_settings = Admin_Settings::get_instance();
		$debug_mode     = $admin_settings->get_setting( 'enable_debug', false );
		$debug_types    = $admin_settings->get_setting( 'enable_debug_type', [] );
		if ( $debug_mode && is_array( $debug_types ) && in_array( 'response', $debug_types, true ) ) {
			self::log( $message, 'response' );
		}
	}
}
