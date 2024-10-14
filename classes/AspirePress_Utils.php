<?php

abstract class AspirePress_Utils {

	public static function buildUrl( $urlParts ) {
		$scheme   = isset( $urlParts['scheme'] ) ? $urlParts['scheme'] . '://' : '';
		$host     = isset( $urlParts['host'] ) ? $urlParts['host'] : '';
		$port     = isset( $urlParts['port'] ) ? ':' . $urlParts['port'] : '';
		$user     = isset( $urlParts['user'] ) ? $urlParts['user'] : '';
		$pass     = isset( $urlParts['pass'] ) ? ':' . $urlParts['pass'] : '';
		$pass     = ( $user || $pass ) ? "$pass@" : '';
		$path     = isset( $urlParts['path'] ) ? $urlParts['path'] : '';
		$query    = isset( $urlParts['query'] ) ? '?' . $urlParts['query'] : '';
		$fragment = isset( $urlParts['fragment'] ) ? '#' . $urlParts['fragment'] : '';
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
