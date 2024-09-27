<?php

class AspirePress_DownloadsWordpressOrgRewriteRule implements AspirePress_RewriteRuleInterface
{
    use AspirePress_RewriteRuleTrait;

    public function __construct(string $apiDestination)
    {
        $this->setHostRewriteRule('downloads.wordpress.org', $apiDestination);
    }
}
