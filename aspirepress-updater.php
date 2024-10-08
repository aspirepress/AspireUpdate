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

if (defined('AP_UPDATER_DEBUG_LEVEL') && is_int(AP_UPDATER_DEBUG_LEVEL)) {
    AspirePress_Debug::setDebugLevel(AP_UPDATER_DEBUG_LEVEL);
}

$rewriteRuleDefs = [];

if (defined('AP_UPDATER_REWRITE_WPORG_API') && AP_UPDATER_REWRITE_WPORG_API && defined('AP_UPDATER_API_URL')) {
    $rewriteRuleDefs[] = new AspirePress_ApiWordpressOrgRewriteRule(AP_UPDATER_API_URL);
}

if (defined('AP_UPDATER_REWRITE_WPORG_DL') && AP_UPDATER_REWRITE_WPORG_DL && defined('AP_UPDATER_DL_URL')) {
    $rewriteRuleDefs[] = new AspirePress_DownloadsWordpressOrgRewriteRule(AP_UPDATER_DL_URL);
}

if (! defined('AP_UPDATER_DEBUG_SSL')) {
    define('AP_UPDATER_DEBUG_SSL', false);
}

if (! defined('AP_UPDATER_EXAMINE_RESPONSES')) {
    define('AP_UPDATER_EXAMINE_RESPONSES', false);
}

new AspirePress_AdminSettings();

$apiKey = get_option('ap_api_key');

if ($apiKey) {

    $aspirePressUpdater = new AspirePress_Updater(
        new AspirePress_RewriteUrls($rewriteRuleDefs),
        new AspirePress_HeaderManager(WP_SITEURL, $apiKey)
    );

    add_filter('pre_http_request', function (...$args) use ($aspirePressUpdater) {
        $arguments = $args[1] ?? [];
        $url = $args[2] ?? null;

        if (!$url) {
            return false;
        }

        if (AP_UPDATER_DEBUG_SSL) {
            $arguments['sslverify'] = false;
        }

        return $aspirePressUpdater->callApi($url, $arguments);
    }, 100, 3);

    if (AP_UPDATER_EXAMINE_RESPONSES) {
        add_filter('http_api_debug', function (...$args) use ($aspirePressUpdater) {
            $response = $args[0];
            $url = $args[4];

            if (empty($response) || empty($url)) {
                return $response;
            }
            $aspirePressUpdater->examineResponse($url, $response);
            return $response;
        }, 10, 5);
    }

}

function api_key_setting()
{
    $value = get_option('ap_api_key');
    echo '<input id="ap_api_key" name="ap_api_key" value="' . $value . '" type="password" />';
}

function ap_text()
{
    echo 'Enter your API key for AspirePress. <strong>Note: You cannot access AspirePress until you have entered an API key.</strong><br /><br /><a href="#">Get your API key here.</a>';
}

add_action('admin_init', function () {
    add_settings_section('ap_api_key_section', 'AspirePress API Key', 'ap_text', 'general');
    register_setting('general', 'ap_api_key');
    add_settings_field( 'ap_api_key', 'AspirePress API Key', 'api_key_setting', 'general', 'ap_api_key_section');
});

if (! $apiKey) {
    function ap_admin_notice_no_api_key() {
        $class = 'notice notice-warning';
        $message = 'You have not defined an API key for AspirePress. You cannot use AspirePress until you have defined an API key.';
        $link = '<a href="#">Get one now.</a>';

        printf( '<div class="%1$s"><p>%2$s %3$s</p></div>', esc_attr( $class ), esc_html( $message ), $link );
    }
    add_action( 'admin_notices', 'ap_admin_notice_no_api_key' );
}


