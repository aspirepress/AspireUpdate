<?php

class AspirePress_Updater {

	private $rewriter;

	private $header_manager;

	public function __construct( AspirePress_RewriteUrls $rewriter, AspirePress_header_manager $header_manager ) {
		$this->rewriter       = $rewriter;
		$this->header_manager = $header_manager;
	}
	public function callApi( $url, array $arguments = array() ) {
		AspirePress_Debug::logString( '[ORIGINAL URL] ' . $url );
		$rewritten_url = $this->rewriter->rewrite( $url );

		if ( $url === $rewritten_url ) {
			return false;
		}

		AspirePress_Debug::logString( '[REWRITTEN URL] ' . $rewritten_url );

		AspirePress_Debug::logString( 'Adding Headers' );
		$arguments['headers'] = $this->header_manager->addHeaders( $arguments['headers'] );

		AspirePress_Debug::logString( 'Making Rewritten Request' );
		AspirePress_Debug::logRequest( $rewritten_url, $arguments );
		$http = _wp_http_get_object();
		return $http->request( $rewritten_url, $arguments );
	}

	public function examineResponse( $url, $response ) {
		AspirePress_Debug::logResponse( $url, $response );
	}
}
