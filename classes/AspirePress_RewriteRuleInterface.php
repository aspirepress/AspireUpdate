<?php

interface AspirePress_RewriteRuleInterface {

	public function rewrite( string $url ): string;

	public function canRewrite( $url ): bool;

	public function setHostRewriteRule( string $origin_host, string $target_host ): void;

	public function setPathRewriteRule( string $origin_path, string $target_path ): void;

	public function setExcludedPathRewriteRule( string $path ): void;
}
