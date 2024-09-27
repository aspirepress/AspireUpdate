<?php

interface AspirePress_RewriteRuleInterface
{
    public function rewrite(string $url): string;

    public function canRewrite($url): bool;

    public function setHostRewriteRule(string $originHost, string $targetHost): void;

    public function setPathRewriteRule(string $originPath, string $targetPath): void;

    public function setExcludedPathRewriteRule(string $path): void;
}
