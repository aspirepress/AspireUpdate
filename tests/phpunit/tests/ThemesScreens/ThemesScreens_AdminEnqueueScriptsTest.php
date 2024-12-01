<?php
/**
 * Class ThemesScreens_AdminEnqueueScriptsTest
 *
 * @package AspireUpdate
 */

/**
 * Tests for Themes_Screens::admin_enqueue_scripts()
 *
 * @covers \AspireUpdate\Themes_Screens::admin_enqueue_scripts
 */
class ThemesScreens_AdminEnqueueScriptsTest extends WP_UnitTestCase {
	/**
	 * Dequeue the stylesheet after each test runs.
	 *
	 * @return void
	 */
	public function tear_down() {
		wp_dequeue_style( 'aspire_update_themes_screens_css' );
	}

	/**
	 * Test that the stylesheet is enqueued on certain screens.
	 */
	public function test_should_enqueue_style_on_theme_install() {
		$themes_screens = new AspireUpdate\Themes_Screens();
		$themes_screens->admin_enqueue_scripts( 'theme-install.php' );
		$this->assertTrue( wp_style_is( 'aspire_update_themes_screens_css' ) );
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

		$themes_screens = new AspireUpdate\Themes_Screens();
		$themes_screens->admin_enqueue_scripts( $hook );
		$this->assertFalse( wp_style_is( 'aspire_update_themes_screens_css' ) );
	}

	/**
	 * Data provider.
	 *
	 * @return array[]
	 */
	public function data_adjacent_screens() {
		return self::text_array_to_dataprovider(
			[
				'themes',
				'nav-menus',
				'theme-editor',
			]
		);
	}

	/**
	 * Test that the stylesheet is not enqueued when there is no screen.
	 */
	public function test_should_not_enqueue_style_when_there_is_no_screen() {
		$themes_screens = new AspireUpdate\Themes_Screens();
		$themes_screens->admin_enqueue_scripts( '' );
		$this->assertFalse( wp_style_is( 'aspire_update_themes_screens_css' ) );
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

		$hook           = is_multisite() ? 'theme-install-network' : 'theme-install';
		$themes_screens = new AspireUpdate\Themes_Screens();
		$themes_screens->admin_enqueue_scripts( $hook );
		$this->assertFalse( wp_style_is( 'aspire_update_themes_screens_css' ) );
	}
}
