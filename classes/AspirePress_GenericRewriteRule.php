<?php

class AspirePress_GenericRewriteRule implements AspirePress_RewriteRuleInterface {

	use AspirePress_RewriteRuleTrait;

	public function __construct(
		string $origin_host,
		string $destination_host,
		array $path_rewrite_rules = array(),
		array $url_rewrite_exclusions = array()
	) {
		$this->setHostRewriteRule( $origin_host, $destination_host );

		foreach ( $path_rewrite_rules as $origin => $dest ) {
			$this->setPathRewriteRule( $origin, $dest );
		}

		foreach ( $url_rewrite_exclusions as $exclusion ) {
			$this->setExcludedPathRewriteRule( $exclusion );
		}
	}
}
