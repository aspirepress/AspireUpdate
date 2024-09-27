<?php

class AspirePress_PathRewriterBasic implements AspirePress_PathRewriterInterface
{
    private array $rewriteUrls = [
        'example.com' => [
            '/path/1' => '/rewritten/1',
            'path/2', '/rewritten/2'
        ]
    ];

    public function __construct(array $rewritePaths)
    {
        $this->rewriteUrls = $rewritePaths;
    }

    public function rewrite($url): string
    {
        $urlParts = parse_url($url);

        if (! isset($this->rewriteUrls[$urlParts['host']])) {
            return $url;
        }

        $paths = $this->rewriteUrls[$urlParts['host']];

        if (! isset($paths[$urlParts['path']])) {
            return $url;
        }

        $urlParts['path'] = $paths[$urlParts['path']];
        return AspirePress_Utils::buildUrl($urlParts);
    }
}
