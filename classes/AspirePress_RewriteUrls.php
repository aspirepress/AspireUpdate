<?php

class AspirePress_RewriteUrls {


	private $rewrite_defs = array();

	public function __construct( array $rewrite_defs = array() ) {
		$this->rewrite_defs = $rewrite_defs;
	}

	public function rewrite( $url ) {
		AspirePress_Debug::logString( 'Attempting to rewrite URL: ' . $url, AspirePress_Debug::INFO );
		/** @var AspirePress_RewriteRuleInterface $rewrite_def */
		foreach ( $this->rewrite_defs as $rewrite_def ) {
			if ( $rewrite_def->canRewrite( $url ) ) {
				AspirePress_Debug::logString( 'Can rewrite URL: ' . $url );
				AspirePress_Debug::logString( 'Rewriting URL with ' . get_class( $rewrite_def ) );
				return $rewrite_def->rewrite( $url );
			}

			AspirePress_Debug::logString( 'Unable to rewrite URL with ' . get_class( $rewrite_def ) );
		}

		return $url;
	}
}
