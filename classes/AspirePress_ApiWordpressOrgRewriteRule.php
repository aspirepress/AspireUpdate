<?php

class AspirePress_ApiWordpressOrgRewriteRule implements AspirePress_RewriteRuleInterface {

	use AspirePress_RewriteRuleTrait;

	public function __construct( string $api_destination ) {
		$this->setHostRewriteRule( 'api.wordpress.org', $api_destination );
		$this->setExcludedPathRewriteRule( '/plugins/info/1.2/' );
	}
}
