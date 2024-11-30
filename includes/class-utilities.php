<?php
/**
 * The Class for Miscellaneous Helper Functions.
 *
 * @package aspire-update
 */

namespace AspireUpdate;

/**
 * The Class for Admin Settings Page and functions to access Settings Values.
 */
class Utilities {
	/**
	 * Get the domain name from the site URL.
	 *
	 * @return string The domain name.
	 */
	public static function get_site_domain() {
		$site_url = network_site_url();
		return wp_parse_url( $site_url, PHP_URL_HOST );
	}

	/**
	 * Return the content of the File after processing.
	 *
	 * @param string $file File name.
	 * @param array  $args Data to pass to the file.
	 */
	public static function include_file( $file, $args = [] ) {
		$file_path = __DIR__ . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $file;
		if ( ( '' !== $file ) && file_exists( $file_path ) ) {
			//phpcs:disable
			// Usage of extract() is necessary in this content to simulate templating functionality.
			extract( $args );
			//phpcs:enable
			include $file_path;
		}
	}
}
