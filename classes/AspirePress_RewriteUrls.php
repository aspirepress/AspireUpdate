<?php

class AspirePress_RewriteUrls
{

    private AspirePress_HostRewriterInterface $hostRewriter;

    private AspirePress_PathRewriterInterface $pathRewriter;
    public function __construct(AspirePress_HostRewriterInterface $hostRewriter, AspirePress_PathRewriterInterface $pathRewriter) {
        $this->hostRewriter = $hostRewriter;
        $this->pathRewriter = $pathRewriter;
    }

    public function rewrite($url)
    {
        $rewrittenUrl = $this->hostRewriter->rewrite($url);

        if ($url === $rewrittenUrl) {
            AspirePress_Debug::logString('The URL was not rewritten; not rewriting the paths...', 'INFO');
            return $url;
        }

        return $this->pathRewriter->rewrite($rewrittenUrl);
    }
}
