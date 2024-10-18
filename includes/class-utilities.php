<?php
/**
 * The Class for Miscellaneous Helper Functions.
 *
 * @package aspire-update
 */

namespace AspirePress;

/**
 * The Class for Admin Settings Page and functions to access Settings Values.
 */
class Utilities {

	/**
	 * Get the top level domain name from the site URL.
	 *
	 * @return string the top level domain name.
	 */
	public static function get_top_level_domain() {
		$site_url     = get_site_url();
		$domain_name  = wp_parse_url( $site_url, PHP_URL_HOST );
		$domain_parts = explode( '.', $domain_name );
		return implode( '.', array_slice( $domain_parts, -2 ) );
	}
}
