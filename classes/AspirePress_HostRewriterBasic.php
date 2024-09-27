<?php

class AspirePress_HostRewriterBasic implements AspirePress_HostRewriterInterface
{
    private array $rewriteUrls = [];

    public function __construct(array $rewriteUrls)
    {
        $this->rewriteUrls = $rewriteUrls;
    }

    public function rewrite($url): string
    {
        $urlParts = parse_url($url);
        AspirePress_Debug::logString('Rewriting host: ' . $urlParts['host']);

        if (isset($this->rewriteUrls[$urlParts['host']])) {
            $urlParts['host'] = $this->rewriteUrls[$urlParts['host']];
        }

        return AspirePress_Utils::buildUrl($urlParts);
    }
}
