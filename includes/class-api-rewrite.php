<?php
/**
 * The Class for Rewriting the Default API.
 *
 * @package aspire-update
 */

namespace AspireUpdate;

/**
 * The Class for Rewriting the Default API.
 */
class API_Rewrite {
	/**
	 * The default API Host.
	 *
	 * @var string
	 */
	private $default_host = 'api.wordpress.org';

	/**
	 * The redirected API Host.
	 *
	 * @var string
	 */
	private $redirected_host;

	/**
	 * Disable SSL.
	 *
	 * @var boolean
	 */
	private $disable_ssl;

	/**
	 * The Constructor.
	 *
	 * @param string  $redirected_host The host to redirect to.
	 * @param boolean $disable_ssl Disable SSL.
	 */
	public function __construct( $redirected_host, $disable_ssl ) {
		if ( 'debug' === $redirected_host ) {
			$this->redirected_host = $this->default_host;
		} else {
			$this->redirected_host = strtolower( $redirected_host );
		}
		$this->disable_ssl = $disable_ssl;
		add_filter( 'pre_http_request', array( $this, 'pre_http_request' ), 10, 3 );
	}

	/**
	 * Rewrite the API End points.
	 *
	 * @param mixed  $response The response for the request.
	 * @param array  $parsed_args The arguments for the request.
	 * @param string $url The URL for the request.
	 *
	 * @return mixed The response or false.
	 */
	public function pre_http_request( $response, $parsed_args, $url ) {
		if (
			isset( $this->default_host ) &&
			( '' !== $this->default_host ) &&
			isset( $this->redirected_host ) &&
			( '' !== $this->redirected_host )
		) {
			if ( false !== strpos( $url, $this->default_host ) ) {
				Debug::log_string( 'Default API Found: ' . $url );
				Debug::log_request( $parsed_args );
				if ( $this->default_host !== $this->redirected_host ) {
					if ( $this->disable_ssl ) {
						Debug::log_string( 'SSL Verification Disabled' );
						$parsed_args['sslverify'] = false;
					}

					$updated_url = str_replace( $this->default_host, $this->redirected_host, $url );
					Debug::log_string( 'API Rerouted to: ' . $updated_url );

					/**
					 * Temporarily Unhook Filter to prevent recursion.
					 */
					remove_filter( 'pre_http_request', array( $this, 'pre_http_request' ) );
					$response = wp_remote_request( $updated_url, $parsed_args );
					add_filter( 'pre_http_request', array( $this, 'pre_http_request' ), 10, 3 );

					Debug::log_response( $response );

					return $response;
				}
			}
		}
		return $response;
	}
}
