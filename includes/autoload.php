<?php
/**
 * The Autoloader.
 *
 * @package aspire-update
 */

spl_autoload_register( 'aspire_update_autoloader' );

/**
 * The Class Autoloader.
 *
 * @param string $class_name The name of the class to load.
 * @return void
 */
function aspire_update_autoloader( $class_name ) {
	if ( false !== strpos( $class_name, 'AspireUpdate\\' ) ) {
		$class_name = strtolower( str_replace( array( 'AspireUpdate\\', '_' ), array( '', '-' ), $class_name ) );
		$file       = __DIR__ . DIRECTORY_SEPARATOR . 'class-' . $class_name . '.php';

		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}
}
