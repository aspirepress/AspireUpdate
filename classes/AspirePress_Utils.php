<?php

abstract class AspirePress_Utils
{
    public static function buildUrl($urlParts)
    {
        $scheme = isset($urlParts['scheme']) ? $urlParts['scheme'] . '://' : '';
        $host = isset($urlParts['host']) ? $urlParts['host'] : '';
        $port = isset($urlParts['port']) ? ':' . $urlParts['port'] : '';
        $user = isset($urlParts['user']) ? $urlParts['user'] : '';
        $pass = isset($urlParts['pass']) ? ':' . $urlParts['pass'] : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = isset($urlParts['path']) ? $urlParts['path'] : '';
        $query = isset($urlParts['query']) ? '?' . $urlParts['query'] : '';
        $fragment = isset($urlParts['fragment']) ? '#' . $urlParts['fragment'] : '';
        return "$scheme$user$pass$host$port$path$query$fragment";
    }
}
