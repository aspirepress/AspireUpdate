<?php

trait AspirePress_RewriteRuleTrait {

	protected $host_rewrite_rules = array();

	protected $path_rewrite_rules = array();

	protected $excludedpath_rewrite_rules = array();

	public function rewrite( string $url ): string {
		$url_parts = parse_url( $url );

		if ( $this->canRewrite( $url, $url_parts ) ) {
			$url_parts = $this->rewriteHost( $url_parts );
			$url_parts = $this->rewritePath( $url_parts );
			$url      = AspirePress_Utils::buildUrl( $url_parts );
			AspirePress_Debug::logString( 'Rewrote URL: ' . $url, AspirePress_Debug::INFO );
		}

		return $url;
	}

	public function canRewrite( $url, ?array $url_parts = null ): bool {
		$parts = $url_parts ?? wp_parse_url( $url );
		$host  = $parts['host'] ?? '';
		$path  = $parts['path'] ?? '';
		if ( empty( $host ) ||
			! isset( $this->host_rewrite_rules[ $host ] ) ||
			in_array( $path, $this->excludedpath_rewrite_rules )
		) {
			return false;
		}

		return true;
	}

	public function setHostRewriteRule( string $origin_host, string $target_host ): void {
		$this->host_rewrite_rules[ $origin_host ] = $target_host;
	}

	public function setPathRewriteRule( string $origin_path, string $target_path ): void {
		if ( empty( $origin_path ) ) {
			return;
		}

		$this->path_rewrite_rules[ $origin_path ] = $target_path;
	}

	public function setExcludedPathRewriteRule( string $path ): void {
		if ( empty( $path ) ) {
			return;
		}

		if ( isset( $this->path_rewrite_rules[ $path ] ) ) {
			unset( $this->path_rewrite_rules[ $path ] );
		}

		$this->excludedpath_rewrite_rules[] = $path;
	}

	private function rewriteHost( array $parts ): array {
		if ( isset( $this->host_rewrite_rules[ $parts['host'] ] ) ) {
			$parts['host'] = $this->host_rewrite_rules[ $parts['host'] ];
		}

		return $parts;
	}

	private function rewritePath( array $parts ): array {
		if ( in_array( $parts['path'], $this->excludedpath_rewrite_rules, true ) ) {
			return $parts;
		}

		if ( isset( $this->path_rewrite_rules[ $parts['path'] ] ) ) {
			$parts['path'] = $this->path_rewrite_rules[ $parts['path'] ];
		}

		return $parts;
	}
}
