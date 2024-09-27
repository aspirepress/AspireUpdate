<?php

/**
 * Plugin Name: AspirePress Updater
 * Description: Update plguins and themes for WordPress
 * Version: 1.0
 * Author: AspirePress
 */

if (!defined('ABSPATH')) {
    die;
}

spl_autoload_register('aspirePressAutoloader');

function aspirePressAutoloader($class)
{
    if (file_exists(__DIR__ . '/classes/' . $class . '.php')) {
        require_once __DIR__ . '/classes/' . $class . '.php';
    }

    return false;
}

if (! defined('AP_UPDATER_API_KEY')) {
    define('AP_UPDATER_API_KEY', false);
}

if ( defined('AP_UPDATER_DEBUG') && AP_UPDATER_DEBUG ) {
    AspirePress_Debug::enableDebug();
}

if (defined('AP_UPDATER_DEBUG_TYPES') && AP_UPDATER_DEBUG_TYPES) {
    foreach (AP_UPDATER_DEBUG_TYPES as $type) {
        AspirePress_Debug::registerDesiredType($type);
    }
}

if (defined('AP_UPDATER_DEBUG_TYPES_EXCLUDE') && AP_UPDATER_DEBUG_TYPES_EXCLUDE) {
    foreach (AP_UPDATER_DEBUG_TYPES_EXCLUDE as $type) {
        AspirePress_Debug::removeDesiredType($type);
    }
}

if (defined('AP_UPDATER_DEBUG_LOG_PATH') && AP_UPDATER_DEBUG_LOG_PATH) {
    AspirePress_Debug::setLogPath(AP_UPDATER_DEBUG_LOG_PATH);
}

$hostRewrites = [];
$pathRewrites = [];

if (defined('AP_UPDATER_HOST_REWRITES') && is_array(AP_UPDATER_HOST_REWRITES)) {
    $hostRewrites = AP_UPDATER_HOST_REWRITES;
}

if (defined ('AP_UPDATER_PATH_REWRITES') && is_array(AP_UPDATER_PATH_REWRITES)) {
    $pathRewrites = AP_UPDATER_PATH_REWRITES;
}


$aspirePressUpdater = new AspirePress_Updater(
    new AspirePress_RewriteUrls(
        new AspirePress_HostRewriterBasic($hostRewrites),
        new AspirePress_PathRewriterBasic($pathRewrites)
    ),
    new AspirePress_HeaderManager(WP_SITEURL, AP_UPDATER_API_KEY)
);

add_filter('pre_http_request', function (...$args) use ($aspirePressUpdater) {
    $arguments = $args[1] ?? [];
    $url = $args[2] ?? null;

    if (!$url) {
        return false;
    }

    return $aspirePressUpdater->callApi($url, $arguments);
}, 100, 3);

add_filter('http_response', function (...$args) use ($aspirePressUpdater) {
    $response = $args[0];
    $url = $args[2];

    if (empty($response) || empty($url)) {
        return $response;
    }
    $aspirePressUpdater->examineResponse($url, $response);
    return $response;
}, 10, 3);
