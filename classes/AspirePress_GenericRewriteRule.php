<?php

class AspirePress_GenericRewriteRule implements AspirePress_RewriteRuleInterface
{
    use AspirePress_RewriteRuleTrait;

    public function __construct(
        string $originHost,
        string $destinationHost,
        array $pathRewriteRules = [],
        array $urlRewriteExclusions = []
    ) {
        $this->setHostRewriteRule($originHost, $destinationHost);

        foreach ($pathRewriteRules as $origin => $dest) {
            $this->setPathRewriteRule($origin, $dest);
        }

        foreach ($urlRewriteExclusions as $exclusion) {
            $this->setExcludedPathRewriteRule($exclusion);
        }
    }
}
