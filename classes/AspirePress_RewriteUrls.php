<?php

class AspirePress_RewriteUrls
{

    private $rewriteDefs = [];

    public function __construct(array $rewriteDefs = []) {
        $this->rewriteDefs = $rewriteDefs;
    }

    public function rewrite($url)
    {
        AspirePress_Debug::logString('Attempting to rewrite URL: ' . $url);
        /** @var AspirePress_RewriteRuleInterface $rewriteDef */
        foreach ($this->rewriteDefs as $rewriteDef) {
            if ($rewriteDef->canRewrite($url)) {
                AspirePress_Debug::logString('Rewriting URL with ' . get_class($rewriteDef));
                return $rewriteDef->rewrite($url);
            }

            AspirePress_Debug::logString('Unable to rewrite URL with ' . get_class($rewriteDef));
        }

        return $url;
    }
}
