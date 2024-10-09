<?php

/**
 * Plugin Name: AspirePress Updater
 * Description: Update plguins and themes for WordPress
 * Version: 1.0
 * Author: AspirePress
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

spl_autoload_register( 'aspirePressAutoloader' );

function aspirePressAutoloader( $class ) {
	if ( file_exists( __DIR__ . '/classes/' . $class . '.php' ) ) {
		require_once __DIR__ . '/classes/' . $class . '.php';
	}

	return false;
}



/**
 * Enable or Disable features as per user settings.
 */
add_action(
	'init',
	function () {
		$aspirepress_admin_settings = new AspirePress_AdminSettings();

		if ( $aspirepress_admin_settings->get_setting( 'enable', false ) ) {
			if ( $aspirepress_admin_settings->get_setting( 'enable_debug', false ) ) {
				AspirePress_Debug::enableDebug();

				$debug_types = $aspirepress_admin_settings->get_setting( 'enable_debug_type', false );
				if ( is_array( $debug_types ) ) {
					foreach ( $debug_types as $debug_type ) {
						AspirePress_Debug::registerDesiredType( $debug_type );
					}
				}

				$exclude_debug_types = $aspirepress_admin_settings->get_setting( 'exclude_debug_type', false );
				if ( is_array( $exclude_debug_types ) ) {
					foreach ( $exclude_debug_types as $debug_type ) {
						AspirePress_Debug::removeDesiredType( $debug_type );
					}
				}

				$debug_log_path = $aspirepress_admin_settings->get_setting( 'debug_log_path', '' );
				if ( '' !== $debug_log_path ) {
					AspirePress_Debug::setLogPath( $debug_log_path );
				}
			}

			$rewrite_rule_defs = array();

			$api_url = $aspirepress_admin_settings->get_setting( 'api_url', '' );
			if ( $aspirepress_admin_settings->get_setting( 'rewrite_wporg_api', false ) && ( '' !== $api_url ) ) {
				$rewrite_rule_defs[] = new AspirePress_ApiWordpressOrgRewriteRule( $api_url );
			}
			$api_download_url = $aspirepress_admin_settings->get_setting( 'api_download_url', '' );
			if ( $aspirepress_admin_settings->get_setting( 'rewrite_wporg_dl', false ) && ( '' !== $api_download_url ) ) {
				$rewrite_rule_defs[] = new AspirePress_DownloadsWordpressOrgRewriteRule( $api_download_url );
			}

			$api_key = $aspirepress_admin_settings->get_setting( 'api_key', '' );
			if ( '' !== $api_key ) {

				$aspirepress_updater = new AspirePress_Updater(
					new AspirePress_RewriteUrls( $rewrite_rule_defs ),
					new AspirePress_HeaderManager( get_site_url(), $api_key )
				);

				if ( $aspirepress_admin_settings->get_setting( 'disable_ssl_verification', false ) && $aspirepress_admin_settings->get_setting( 'enable_debug', false ) ) {
					add_filter(
						'pre_http_request',
						function ( ...$args ) use ( $aspirepress_updater ) {
							$arguments = $args[1] ?? array();
							$url       = $args[2] ?? null;

							if ( ! $url ) {
								return false;
							}

							$arguments['sslverify'] = false;

							return $aspirepress_updater->callApi( $url, $arguments );
						},
						100,
						3
					);
				}

				if ( $aspirepress_admin_settings->get_setting( 'examine_responses', false ) && $aspirepress_admin_settings->get_setting( 'enable_debug', false ) ) {
					add_filter(
						'http_api_debug',
						function ( ...$args ) use ( $aspirepress_updater ) {
							$response = $args[0];
							$url      = $args[4];

							if ( empty( $response ) || empty( $url ) ) {
								return $response;
							}
							$aspirepress_updater->examineResponse( $url, $response );
							return $response;
						},
						10,
						5
					);
				}
			}
		}
	}
);

if ( defined( 'AP_UPDATER_DEBUG_LEVEL' ) && is_int( AP_UPDATER_DEBUG_LEVEL ) ) {
	AspirePress_Debug::setDebugLevel( AP_UPDATER_DEBUG_LEVEL );
}
