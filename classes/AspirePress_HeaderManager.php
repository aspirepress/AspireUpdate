<?php

class AspirePress_HeaderManager
{
    private string $siteUrl;

    private string $apiKey;
    public function __construct(string $siteUrl, string $apiKey)
    {
        $this->siteUrl = $siteUrl;
        $this->apiKey = $apiKey;
    }

    public function addHeaders(array $headers)
    {
        if ($this->siteUrl && $this->apiKey) {
            $headers['Authorization'] = base64_encode($this->siteUrl . ':' . $this->apiKey);
        }

        return $headers;
    }
}
