<?php

trait AspirePress_RewriteRuleTrait
{
    protected $hostRewriteRules = [];

    protected $pathRewriteRules = [];

    protected $excludedPathRewriteRules = [];

    public function rewrite(string $url): string
    {
        $urlParts = parse_url($url);

        if ($this->canRewrite($url, $urlParts)) {
            $urlParts = $this->rewriteHost($urlParts);
            $urlParts = $this->rewritePath($urlParts);
            $url = AspirePress_Utils::buildUrl($urlParts);
        }

        return $url;
    }

    public function canRewrite($url, ?array $urlParts = null): bool
    {
        $parts = $urlParts ?? parse_url($url);
        $host = $parts['host'] ?? '';
        $path = $parts['path'] ?? '';
        if (empty($host) ||
            !isset($this->hostRewriteRules[$host]) ||
            in_array($path, $this->excludedPathRewriteRules)
        ) {
            return false;
        }

        AspirePress_Debug::logString('Can rewrite URL: ' . $url);
        AspirePress_Debug::logNonScalar($this->excludedPathRewriteRules);
        AspirePress_Debug::logNonScalar($path);
        return true;
    }

    public function setHostRewriteRule(string $originHost, string $targetHost): void
    {
        $this->hostRewriteRules[$originHost] = $targetHost;
    }

    public function setPathRewriteRule(string $originPath, string $targetPath): void
    {
        if (empty($originPath)) {
            return;
        }

        $this->pathRewriteRules[$originPath] = $targetPath;
    }

    public function setExcludedPathRewriteRule(string $path): void
    {
        if (empty($path)) {
            return;
        }

        if (isset($this->pathRewriteRules[$path])) {
            unset($this->pathRewriteRules[$path]);
        }

        $this->excludedPathRewriteRules[] = $path;
    }

    private function rewriteHost(array $parts): array
    {
        if (isset($this->hostRewriteRules[$parts['host']])) {
            $parts['host'] = $this->hostRewriteRules[$parts['host']];
        }

        return $parts;
    }

    private function rewritePath(array $parts): array
    {
        if (in_array($parts['path'], $this->excludedPathRewriteRules)) {
            return $parts;
        }

        if (isset($this->pathRewriteRules[$parts['path']])) {
            $parts['path'] = $this->pathRewriteRules[$parts['path']];
        }

        return $parts;
    }
}
