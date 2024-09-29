<?php

class AspirePress_Updater
{
    private $rewriter;

    private $headerManager;

    public function __construct(AspirePress_RewriteUrls $rewriter, AspirePress_HeaderManager $headerManager)
    {
        $this->rewriter = $rewriter;
        $this->headerManager = $headerManager;
    }
    public function callApi($url, array $arguments = [])
    {
        AspirePress_Debug::logString('[ORIGINAL URL] '. $url);
        $rewrittenUrl = $this->rewriter->rewrite($url);


        if ($url === $rewrittenUrl) {
            return false;
        }

        AspirePress_Debug::logString('[REWRITTEN URL] ' . $rewrittenUrl);

        AspirePress_Debug::logString('Adding Headers');
        $arguments['headers'] = $this->headerManager->addHeaders($arguments['headers']);

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
