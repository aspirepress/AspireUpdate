<?php

class AspirePress_Updater
{
    private AspirePress_RewriteUrls $rewriter;

    private AspirePress_HeaderManager $headerManager;

    public function __construct(AspirePress_RewriteUrls $rewriter, AspirePress_HeaderManager $headerManager)
    {
        $this->rewriter = $rewriter;
        $this->headerManager = $headerManager;
    }
    public function callApi($url, array $arguments = [])
    {
        AspirePress_Debug::logString($url, 'ORIGINAL URL');
        $rewrittenUrl = $this->rewriter->rewrite($url);


        if ($url === $rewrittenUrl) {
            return false;
        }

        AspirePress_Debug::logString($rewrittenUrl, 'REWRITTEN URL');

        AspirePress_Debug::logString('Adding Headers', 'INFO');
        $argument['headers'] = $this->headerManager->addHeaders($arguments['headers']);

        AspirePress_Debug::logString('Making Rewritten Request');
        AspirePress_Debug::logRequest($rewrittenUrl, $arguments);
        $http = _wp_http_get_object();
        return $http->request($rewrittenUrl, $arguments);
    }

    public function examineResponse($url, $response)
    {
        AspirePress_Debug::logResponse($url, $response);
    }
}
