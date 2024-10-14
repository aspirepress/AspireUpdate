<?php

class AspirePress_Debug {

	const ERROR   = 1;
	const WARNING = 2;
	const INFO    = 3;
	const DEBUG   = 4;
	const NONE    = 5;

	private static $status_translate = array(
		self::ERROR   => 'ERROR',
		self::WARNING => 'WARNING',
		self::INFO    => 'INFO',
		self::DEBUG   => 'DEBUG',
	);

	private const DESIRED_REQUEST_KEYS = array(
		'body',
		'method',
		'headers',
	);

	private const DESIRED_RESPONSE_KEYS = array(
		'body',
		'headers',
		'status',
	);

	private static $desired_types = array(
		'request',
		'response',
		'string',
	);

	private static $enabled = false;

	private static $log_path = ABSPATH;

	private static $debug_level = self::NONE;

	public static function logRequest( string $url, array $arguments, array $desired_keys = self::DESIRED_REQUEST_KEYS ) {
		if ( ! self::$enabled || ! in_array( 'request', self::$desired_types, true ) ) {
			return;
		}

		$logged_data = array(
			'url'       => $url,
			'arguments' => self::filterKeys( $arguments, $desired_keys ),
		);

		self::logData( 'REQUEST', $logged_data );
	}

	public static function logResponse( string $url, $response, array $desired_keys = self::DESIRED_RESPONSE_KEYS ) {
		if ( ! self::$enabled || ! in_array( 'response', self::$desired_types, true ) ) {
			return;
		}

		$logged_data = array(
			'url' => $url,
		);

		if ( $response instanceof WP_Error ) {
			$logged_data['wp_response'] = $response->get_error_message();
		}

		if ( is_array( $response ) ) {
			$logged_data['wp_response'] = self::filterResponse( $response['http_response'] );
		}

		self::logData( 'RESPONSE', $logged_data );
	}

	public static function logString( string $message, string $type = self::DEBUG ) {
		if ( ! self::$enabled || ! in_array( 'string', self::$desired_types, true ) || self::$debug_level > $type ) {
			return;
		}

		self::logData( self::$status_translate[ $type ], $message );
	}

	public static function logNonScalar( $message, string $type = self::DEBUG ) {
		if ( ! self::$enabled || ! in_array( 'string', self::$desired_types, true ) || self::$debug_level > $type ) {
			return;
		}

		self::logData( self::$status_translate[ $type ], $message );
	}

	private static function logData( string $type, $data ) {
		if ( ! self::$enabled ) {
			return;
		}

		$message = self::parseData( $data );

		$log_message = sprintf( '[%s] [%s] %s', $type, date( 'Y-m-d H:i:s' ), $message );

		$log_message .= str_repeat( '=', 20 );

		file_put_contents( self::$log_path . '/aspirepress-debug.log', $log_message . PHP_EOL, FILE_APPEND );
	}

	private static function filterKeys( array $data, array $desired_keys ) {
		return array_filter(
			$data,
			function ( $key ) use ( $desired_keys ) {
				return in_array( $key, $desired_keys, true );
			},
			ARRAY_FILTER_USE_KEY
		);
	}

	private static function filterResponse( WP_HTTP_Response $response ) {
		$return_response = array();
		if ( in_array( 'headers', self::DESIRED_RESPONSE_KEYS, true ) ) {
			$headers = $response->get_headers();
			if ( is_object( $headers ) && $headers instanceof Requests_Utility_CaseInsensitiveDictionary ) {
				$headers = $headers->getAll();
			}

			$return_response['headers'] = $headers;
		}

		if ( in_array( 'body', self::DESIRED_RESPONSE_KEYS, true ) ) {
			$return_response['body'] = $response->get_data();
		}

		if ( in_array( 'status', self::DESIRED_RESPONSE_KEYS, true ) ) {
			$return_response['status'] = $response->get_status();
		}

		return $return_response;
	}

	public static function setlog_path( string $path ) {
		if ( is_writable( $path ) ) {
			self::$log_path = $path;
			return true;
		}

		/**
		 * A wrong debug log path will lock the user out of WordPress admin providing him no way to fix the issue.
		 */
		// throw new \InvalidArgumentException('Unable to write debug log!');
	}

	public static function enableDebug() {
		return self::$enabled = true;
	}

	public static function disableDebug() {

		return self::$enabled = false;
	}

	public static function registerDesiredType( string $type ) {
		if ( ! in_array( $type, self::$desired_types, true ) ) {
			self::$desired_types[] = $type;
		}
	}

	public static function removeDesiredType( string $type ) {
		if ( ( $key = array_search( $type, self::$desired_types ) ) !== false ) {
			unset( self::$desired_types[ $key ] );
		}
	}

	private static function parseData( $data, $level = 1 ) {
		if ( is_scalar( $data ) ) {
			if ( 1 === $level ) {
				return PHP_EOL . $data . PHP_EOL;
			}
			return $data;
		}

		if ( is_object( $data ) ) {
			return print_r( $data, true );
		}

		if ( is_array( $data ) ) {
			$response = PHP_EOL;
			foreach ( $data as $key => $value ) {
				$response .= str_repeat( ' ', $level * 4 ) . '[' . $key . '] => ' . self::parseData( $value, $level + 1 ) . PHP_EOL;
			}
		}

		return $response;
	}

	public static function setdebug_level( int $level = self::DEBUG ) {
		if ( $level > 4 ) {
			self::$debug_level = self::ERROR;
			return;
		}

		self::$debug_level = $level;
	}
}
