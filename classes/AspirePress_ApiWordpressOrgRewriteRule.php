<?php

class AspirePress_ApiWordpressOrgRewriteRule implements AspirePress_RewriteRuleInterface
{
    use AspirePress_RewriteRuleTrait;

    public function __construct(string $apiDestination)
    {
        $this->setHostRewriteRule('api.wordpress.org', $apiDestination);
    }
}
