<?php

abstract class AspirePress_Utils {

	public static function buildUrl( $url_parts ) {
		$scheme   = isset( $url_parts['scheme'] ) ? $url_parts['scheme'] . '://' : '';
		$host     = isset( $url_parts['host'] ) ? $url_parts['host'] : '';
		$port     = isset( $url_parts['port'] ) ? ':' . $url_parts['port'] : '';
		$user     = isset( $url_parts['user'] ) ? $url_parts['user'] : '';
		$pass     = isset( $url_parts['pass'] ) ? ':' . $url_parts['pass'] : '';
		$pass     = ( $user || $pass ) ? "$pass@" : '';
		$path     = isset( $url_parts['path'] ) ? $url_parts['path'] : '';
		$query    = isset( $url_parts['query'] ) ? '?' . $url_parts['query'] : '';
		$fragment = isset( $url_parts['fragment'] ) ? '#' . $url_parts['fragment'] : '';
		return "$scheme$user$pass$host$port$path$query$fragment";
	}

	/**
	 * Get the top level domain name from the site URL.
	 *
	 * @return string the top level domain name.
	 */
	public static function get_top_domain() {
		$site_url     = get_site_url();
		$domain_name  = parse_url( $site_url, PHP_URL_HOST );
		$domain_parts = explode( '.', $domain_name );
		return implode( '.', array_slice( $domain_parts, -2 ) );
	}

}
