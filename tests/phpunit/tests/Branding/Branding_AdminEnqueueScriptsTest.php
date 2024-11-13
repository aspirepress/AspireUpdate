<?php
/**
 * Class Branding_AdminEnqueueScriptsTest
 *
 * @package AspireUpdate
 */

/**
 * Tests for Branding::admin_enqueue_scripts()
 *
 * @covers \AspireUpdate\Branding::admin_enqueue_scripts
 */
class Branding_AdminEnqueueScriptsTest extends WP_UnitTestCase {
	/**
	 * Dequeue the stylesheet after each test runs.
	 *
	 * @return void
	 */
	public function tear_down() {
		wp_dequeue_style( 'aspire_update_settings_css' );
	}

	/**
	 * Test that the stylesheet is enqueued on certain screens.
	 *
	 * @dataProvider data_hooks
	 *
	 * @param string $hook The current screen's hook.
	 */
	public function test_should_enqueue_style_on_certain_screens( $hook ) {
		$branding = new AspireUpdate\Branding();
		$branding->admin_enqueue_scripts( $hook );
		$this->assertTrue( wp_style_is( 'aspire_update_settings_css' ) );
	}

	/**
	 * Data provider.
	 *
	 * @return array[]
	 */
	public function data_hooks() {
		return self::text_array_to_dataprovider(
			[
				'update-core',
				'plugins',
				'plugin-install',
				'themes',
				'theme-install',
			]
		);
	}

	/**
	 * Test that the stylesheet is not enqueued on adjacent screens.
	 *
	 * @dataProvider data_adjacent_screens
	 *
	 * @param string $hook The current screen's hook.
	 */
	public function test_should_not_enqueue_style_on_adjacent_screens( $hook ) {
		if ( is_multisite() ) {
			$hook .= '-network';
		}

		$branding = new AspireUpdate\Branding();
		$branding->admin_enqueue_scripts( $hook );
		$this->assertFalse( wp_style_is( 'aspire_update_settings_css' ) );
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
	 * Test that the stylesheet is not enqueued when there is no screen.
	 */
	public function test_should_not_enqueue_style_when_there_is_no_screen() {
		$branding = new AspireUpdate\Branding();
		$branding->admin_enqueue_scripts( '' );
		$this->assertFalse( wp_style_is( 'aspire_update_settings_css' ) );
	}

	/**
	 * Test that the stylesheet is not enqueued when AP_REMOVE_UI is set to true.
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function test_should_not_enqueue_style_when_ap_remove_ui_is_true() {
		// Prevent the notice from being displayed.
		define( 'AP_REMOVE_UI', true );

		$hook     = is_multisite() ? 'plugins-network' : 'plugins';
		$branding = new AspireUpdate\Branding();
		$branding->admin_enqueue_scripts( $hook );
		$this->assertFalse( wp_style_is( 'aspire_update_settings_css' ) );
	}
}
