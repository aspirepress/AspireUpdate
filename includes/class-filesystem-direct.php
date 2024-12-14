<?php
/**
 * The Class for WordPress Direct Filesystem with optimized read and write routines.
 *
 * @package aspire-update
 */

namespace AspireUpdate;

/**
 * The Class for WordPress Direct Filesystem with optimized read and write routines.
 */
class Filesystem_Direct extends \WP_Filesystem_Direct {

	/**
	 * Reads entire file into an array with options for limiting the number of lines and direction from the the lines are counted.
	 *
	 * @since 2.5.0
	 *
	 * @param string $file Path to the file.
	 * @param int    $number_of_lines The number of lines to read. Default is -1 (read all lines).
	 * @param bool   $count_bottom_to_up Count the lines from the bottom up. Default is false (count from top to bottom).
	 *
	 * @return array|false File contents in an array on success, false on failure.
	 */
	public function get_contents_array( $file, $number_of_lines = -1, $count_bottom_to_up = false ) {
		if ( ! $this->exists( $file ) ) {
			return false;
		}

		if ( -1 === $number_of_lines ) {
			return @file( $file );
		}

		// phpcs:disable WordPress.WP.AlternativeFunctions.file_system_operations_fopen
		/**
		 * Extending WP_Filesystem methods for efficiency.  This is a valid use case.
		 */
		$handle = @fopen( $file, 'r' );
		// phpcs:enable
		if ( ! $handle ) {
			return false;
		}

		$lines      = [];
		$line_count = 0;

		while ( ( $line = fgets( $handle ) ) !== false ) {
			$lines[] = rtrim( $line, "\r\n" );
			++$line_count;

			if ( $count_bottom_to_up ) {
				if ( $number_of_lines > 0 && $line_count > $number_of_lines ) {
					array_shift( $lines );
				}
			} elseif ( $number_of_lines > 0 && $line_count >= $number_of_lines ) {
					break;
			}
		}

		// phpcs:disable WordPress.WP.AlternativeFunctions.file_system_operations_fclose
		/**
		 * Extending WP_Filesystem methods for efficiency.  This is a valid use case.
		 */
		fclose( $handle );
		// phpcs:enable

		return $lines;
	}

	/**
	 * Write contents to a file with additional modes.
	 *
	 * @param string    $file The path to the file.
	 * @param string    $contents The content to write.
	 * @param int|false $mode     Optional. The file permissions as octal number, usually 0644.
	 *                            Default false.
	 * @param string    $write_mode The write mode:
	 *                     'w'  - Overwrite the file (default).
	 *                     'a'  - Append to the file.
	 *                     'x'  - Create a new file and write, fail if the file exists.
	 *                     'c'  - Open the file for writing, but do not truncate.
	 * @return bool True on success, false on failure.
	 */
	public function put_contents( $file, $contents, $mode = false, $write_mode = 'w' ) {
		$valid_write_modes = [ 'w', 'a', 'x', 'c' ];
		if ( ! in_array( $write_mode, $valid_write_modes, true ) ) {
			return false;
		}
		// phpcs:disable WordPress.WP.AlternativeFunctions.file_system_operations_fopen
		/**
		 * Extending WP_Filesystem methods for efficiency.  This is a valid use case.
		 */
		$handle = @fopen( $file, $write_mode );
		// phpcs:enable

		if ( ! $handle ) {
			return false;
		}

		mbstring_binary_safe_encoding();
		$data_length = strlen( $contents );
		// phpcs:disable WordPress.WP.AlternativeFunctions.file_system_operations_fwrite
		/**
		 * Extending WP_Filesystem methods for efficiency.  This is a valid use case.
		 */
		$bytes_written = fwrite( $handle, $contents );
		// phpcs:enable
		reset_mbstring_encoding();

		// phpcs:disable WordPress.WP.AlternativeFunctions.file_system_operations_fclose
		/**
		 * Extending WP_Filesystem methods for efficiency.  This is a valid use case.
		 */
		fclose( $handle );
		// phpcs:enable

		if ( $data_length !== $bytes_written ) {
			return false;
		}

		$this->chmod( $file, $mode );

		return true;
	}
}
