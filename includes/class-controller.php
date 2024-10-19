<?php
/**
 * The Class for managing the plugins Workflow.
 *
 * @package aspire-update
 */

namespace AspirePress;

/**
 * The Class for managing the plugins Workflow.
 */
class Controller {
	/**
	 * The Constructor.
	 */
	public function __construct() {
		Admin_Settings::get_instance();
		$this->api_rewrite();
	}

	/**
	 * Enable API Rewrites based on the Users settings.
	 *
	 * @return void
	 */
	private function api_rewrite() {
		$admin_settings = Admin_Settings::get_instance();
		$api_key        = $admin_settings->get_setting( 'api_key', '' );
		if ( $admin_settings->get_setting( 'enable', false ) && ( '' !== $api_key ) ) {
			$api_hosts = $admin_settings->get_setting( 'api_hosts', '' );
			if ( isset( $api_hosts ) && is_array( $api_hosts ) ) {
				foreach ( $api_hosts as $api_host ) {
					if (
						isset( $api_host ) &&
						( '' !== $api_host )
					) {
						$debug_mode  = $admin_settings->get_setting( 'debug_mode', false );
						$disable_ssl = $admin_settings->get_setting( 'disable_ssl_verification', false );
						if ( $debug_mode && $disable_ssl ) {
							new API_Rewrite( $api_host, true );
						} else {
							new API_Rewrite( $api_host, false );
						}
					}
				}
			}
		}
	}
}
