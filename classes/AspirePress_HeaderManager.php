<?php

class AspirePress_HeaderManager {

	private string $site_url;

	private string $api_key;
	public function __construct( string $site_url, string $api_key ) {
		$this->site_url = $site_url;
		$this->api_key  = $api_key;
	}

	public function addHeaders( array $headers ) {
		if ( $this->site_url && $this->api_key ) {
			$headers['Authorization'] = base64_encode( $this->site_url . ':' . $this->api_key );// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		}

		return $headers;
	}
}
