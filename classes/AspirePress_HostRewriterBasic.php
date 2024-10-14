<?php

class AspirePress_HostRewriterBasic implements AspirePress_HostRewriterInterface {

	private array $rewrite_urls = array();

	public function __construct( array $rewrite_urls ) {
		$this->rewrite_urls = $rewrite_urls;
	}

	public function rewrite( $url ): string {
		$url_parts = wp_parse_url( $url );
		AspirePress_Debug::logString( 'Rewriting host: ' . $url_parts['host'] );

		if ( isset( $this->rewrite_urls[ $url_parts['host'] ] ) ) {
			$url_parts['host'] = $this->rewrite_urls[ $url_parts['host'] ];
		}

		return AspirePress_Utils::buildUrl( $url_parts );
	}
}
