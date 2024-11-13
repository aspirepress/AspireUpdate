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
	 * Test that the default 'api_host' value is retrieved.
	 *
	 * @covers \AspireUpdate\Admin_Settings::get_default_settings
	 */
	public function test_should_get_default_api_host() {
		$admin_settings = new \AspireUpdate\Admin_Settings();
		$actual         = $admin_settings->get_setting( 'api_host' );

		$this->assertIsString( $actual, 'The API host value is not a string.' );
		$this->assertNotEmpty( $actual, 'The API host value is empty. ' );
	}
}
