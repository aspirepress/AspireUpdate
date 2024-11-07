<?php
/**
 * Class Branding_OutputAdminNoticeTest
 *
 * @package AspireUpdate
 */

/**
 * Tests for Branding::output_admin_notice()
 *
 * @covers \AspireUpdate\Branding::output_admin_notice
 */
class Branding_OutputAdminNoticeTest extends WP_UnitTestCase {
	/**
	 * Test that the expected admin notice is output.
	 *
	 * @dataProvider data_screen_specific_messages
	 *
	 * @param string $hook     The current screen's hook.
	 * @param string $expected The expected substring to find.
	 */
	public function test_should_output_admin_notice( $hook, $expected ) {
		if ( is_multisite() ) {
			$hook .= '-network';
		}
		set_current_screen( $hook );

		$branding = new AspireUpdate\Branding();
		$this->assertStringContainsString( $expected, get_echo( [ $branding, 'output_admin_notice' ] ) );
	}

	/**
	 * Test that no admin notice is output on adjacent screens.
	 *
	 * @dataProvider data_screen_specific_messages
	 *
	 * @group ms-required
	 *
	 * @param string $hook The current screen's hook.
	 */
	public function test_should_not_output_notice_on_single_site_screens_in_multisite( $hook ) {
		set_current_screen( $hook );

		$branding = new AspireUpdate\Branding();
		$this->assertSame( '', get_echo( [ $branding, 'output_admin_notice' ] ) );
	}

	/**
	 * Data provider.
	 *
	 * @return array[]
	 */
	public function data_screen_specific_messages() {
		return [
			'update-core'    => [
				'hook'     => 'update-core',
				'expected' => 'WordPress, plugin, theme and translation updates',
			],
			'plugins'        => [
				'hook'     => 'plugins',
				'expected' => 'plugin updates',
			],
			'plugin-install' => [
				'hook'     => 'plugin-install',
				'expected' => 'plugin updates',
			],
			'themes'         => [
				'hook'     => 'themes',
				'expected' => 'theme updates',
			],
			'theme-install'  => [
				'hook'     => 'theme-install',
				'expected' => 'theme updates',
			],
		];
	}

	/**
	 * Test that no admin notice is output on adjacent screens.
	 *
	 * @dataProvider data_adjacent_screens
	 *
	 * @param string $hook The current screen's hook.
	 */
	public function test_should_not_output_notice_on_adjacent_screens( $hook ) {
		if ( is_multisite() ) {
			$hook .= '-network';
		}
		set_current_screen( $hook );

		$branding = new AspireUpdate\Branding();
		$this->assertSame( '', get_echo( [ $branding, 'output_admin_notice' ] ) );
	}

	/**
	 * Data provider.
	 *
	 * @return array[]
	 */
	public function data_adjacent_screens() {
		return self::text_array_to_dataprovider(
			[
				'dashboard',
				'nav-menus',
				'plugin-editor',
			]
		);
	}

	/**
	 * Test that no admin notice is output when there is no screen.
	 */
	public function test_should_not_output_notice_when_there_is_no_screen() {
		global $current_screen;
		$current_screen_backup = $current_screen;
		unset( $current_screen );

		$branding       = new AspireUpdate\Branding();
		$actual         = get_echo( [ $branding, 'output_admin_notice' ] );
		$current_screen = $current_screen_backup;

		$this->assertSame( '', $actual );
	}

	/**
	 * Test that no admin notice is output when AP_REMOVE_UI is set to true.
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function test_should_not_output_notice_when_ap_remove_ui_is_true() {
		// Set to a screen that should display an admin notice.
		set_current_screen( is_multisite() ? 'plugins-network' : 'plugins' );

		// Prevent the notice from being displayed.
		define( 'AP_REMOVE_UI', true );

		$branding = new AspireUpdate\Branding();
		$this->assertSame( '', get_echo( [ $branding, 'output_admin_notice' ] ) );
	}
}
