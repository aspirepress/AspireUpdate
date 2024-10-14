<?php

class AspirePress_DownloadsWordpressOrgRewriteRule implements AspirePress_RewriteRuleInterface {

	use AspirePress_RewriteRuleTrait;

	public function __construct( string $api_destination ) {
		$this->setHostRewriteRule( 'downloads.wordpress.org', $api_destination );
	}
}
