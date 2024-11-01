<?php
/**
 * Class AdminSettings_GetSettingTest
 *
 * @package AspireUpdate
 */

/**
 * Tests for Admin_Settings::get_setting()
 * 
 * @covers \AspireUpdate\Admin_Settings::get_setting
 */
class AdminSettings_GetSettingTest extends WP_UnitTestCase {
	/**
	 * Shared instance of the class.
	 *
	 * @var \AspireUpdate\Admin_Settings
	 */
	private static $instance;

	/**
	 * Set up before any tests run.
	 */
	public static function set_up_before_class() {
		self::$instance = \AspireUpdate\Admin_Settings::get_instance();
	}

	/**
	 * Test that the default 'api_host' value is retrieved.
	 * 
	 * @covers \AspireUpdate\Admin_Settings::get_default_settings
	 */
	public function test_should_get_default_api_host() {
		$actual = self::$instance->get_setting( 'api_host' );

		$this->assertIsString( $actual, 'The API host value is not a string.' );
		$this->assertNotEmpty( $actual, 'The API host value is empty. ' . var_export( $actual, true ) );
	}
}
