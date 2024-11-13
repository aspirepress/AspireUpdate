<?php
/**
 * Translations Updater
 *
 * @package Fragen\Translations_Updater
 * @author  Andy Fragen
 * @license MIT
 * @link    https://github.com/afragen/translations-updater
 */

namespace Fragen\Translations_Updater;

/*
 * Exit if called directly.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class API
 *
 * @package Fragen\Translations_Updater
 */
trait API {
	/**
	 * Variable for setting update transient hours.
	 *
	 * @var integer
	 */
	protected static $hours = 12;

	/**
	 * Variable to hold all repository remote info.
	 *
	 * @var array
	 */
	protected $response = [];

	/**
	 * Return repo data for API calls.
	 *
	 * @return array
	 */
	final protected function return_repo_type() {
		$arr           = [];
		$arr['type']   = $this->repo->type;
		$arr['branch'] = $this->repo->branch;

		switch ( $this->repo->git ) {
			case 'github':
				$arr['git']           = 'github';
				$arr['base_uri']      = 'https://api.github.com';
				$arr['base_download'] = 'https://github.com';
				break;
			case 'bitbucket':
				$arr['git']           = 'bitbucket';
				$arr['base_uri']      = 'https://bitbucket.org/api';
				$arr['base_download'] = 'https://bitbucket.org';
				break;
			case 'gitlab':
				$arr['git']           = 'gitlab';
				$arr['base_uri']      = 'https://gitlab.com/api/v4';
				$arr['base_download'] = 'https://gitlab.com';
				break;
			case 'gitea':
				$arr['git'] = 'gitea';
				// TODO: make sure this works.
				$arr['base_uri']      = $this->repo->languages . '/api/v1';
				$arr['base_download'] = $this->repo->languages;
				break;
		}

		return $arr;
	}

	/**
	 * Call the API and return a json decoded body.
	 *
	 * @param string $url Repository URL.
	 *
	 * @return boolean|\stdClass
	 */
	final protected function api( $url ) {
		$response = wp_remote_get( $this->get_api_url( $url ) );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		return json_decode( wp_remote_retrieve_body( $response ) );
	}

	/**
	 * Return API url.
	 *
	 * @access protected
	 *
	 * @param string $endpoint API endpoint.
	 *
	 * @return string $endpoint
	 */
	final protected function get_api_url( $endpoint ) {
		$type = $this->return_repo_type();

		switch ( $type['git'] ) {
			case 'bitbucket':
			case 'gitea':
				break;
			case 'github':
			case 'gitlab':
				$endpoint = add_query_arg( 'ref', $type['branch'], $endpoint );
				break;
			default:
		}

		return $type['base_uri'] . $endpoint;
	}

	/**
	 * Validate wp_remote_get response.
	 *
	 * @param \stdClass $response API response.
	 *
	 * @return bool true if invalid
	 */
	final protected function validate_response( $response ) {
		return empty( $response ) || isset( $response->message );
	}

	/**
	 * Returns repo cached data.
	 *
	 * @param string|bool $repo Repo name or false.
	 *
	 * @return array|bool false for expired cache
	 */
	final protected function get_repo_cache( $repo = false ) {
		if ( ! $repo ) {
			$repo = isset( $this->type->slug ) ? $this->type->slug : 'tu';
		}
		$cache_key = 'tu-' . md5( $repo );
		$cache     = get_site_option( $cache_key );

		if ( empty( $cache['timeout'] ) || time() > $cache['timeout'] ) {
			return [];
		}

		return $cache;
	}

	/**
	 * Sets repo data for cache in site option.
	 *
	 * @param string      $id       Data Identifier.
	 * @param mixed       $response Data to be stored.
	 * @param string|bool $repo     Repo name or false.
	 *
	 * @return bool
	 */
	final protected function set_repo_cache( $id, $response, $repo = false ) {
		if ( ! $repo ) {
			$repo = isset( $this->type->slug ) ? $this->type->slug : 'tu';
		}
		$cache_key = 'tu-' . md5( $repo );
		$timeout   = '+' . self::$hours . ' hours';

		$this->response['timeout'] = strtotime( $timeout );
		$this->response[ $id ]     = $response;

		update_site_option( $cache_key, $this->response );

		return true;
	}
}
