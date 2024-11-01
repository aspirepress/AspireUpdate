<?php
/**
 * The Class for managing the plugins Workflow.
 *
 * @package aspire-update
 */

namespace AspireUpdate;

/**
 * The Class for managing the plugins Workflow.
 */
class Controller {
	/**
	 * The Constructor.
	 */
	public function __construct() {
		Admin_Settings::get_instance();
		Themes_Screens::get_instance();

		$this->api_rewrite();
	}

	/**
	 * Enable API Rewrites based on the Users settings.
	 *
	 * @return void
	 */
	private function api_rewrite() {
		$admin_settings = Admin_Settings::get_instance();

		if ( $admin_settings->get_setting( 'enable', false ) ) {
			$api_host = $admin_settings->get_setting( 'api_host', '' );
		} else {
			$api_host = 'debug';
		}

		if ( isset( $api_host ) && ( '' !== $api_host ) ) {
			$enable_debug = $admin_settings->get_setting( 'enable_debug', false );
			$disable_ssl  = $admin_settings->get_setting( 'disable_ssl_verification', false );
			if ( $enable_debug && $disable_ssl ) {
				new API_Rewrite( $api_host, true );
			} else {
				new API_Rewrite( $api_host, false );
			}
		}
	}
}
