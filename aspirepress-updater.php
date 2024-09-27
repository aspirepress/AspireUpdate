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

$rewriteRuleDefs = [];

if (defined('AP_UPDATER_REWRITE_WPORG_API') && defined('AP_UPDATER_API_URL')) {
    $rewriteRuleDefs[] = new AspirePress_ApiWordpressOrgRewriteRule(AP_UPDATER_API_URL);
}

if (defined('AP_UPDATER_REWRITE_WPORG_DL') && defined('AP_UPDATER_DL_URL')) {
    $rewriteRuleDefs[] = new AspirePress_DownloadsWordpressOrgRewriteRule(AP_UPDATER_DL_URL);
}

$aspirePressUpdater = new AspirePress_Updater(
    new AspirePress_RewriteUrls($rewriteRuleDefs),
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
