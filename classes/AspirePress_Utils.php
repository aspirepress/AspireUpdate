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
}
