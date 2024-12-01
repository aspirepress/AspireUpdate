<?php
/**
 * Class ThemesScreens_ConstructTest
 *
 * @package AspireUpdate
 */

/**
 * Tests for Themes_Screens::__construct()
 *
 * These tests cause constants to be defined.
 * They must run in separate processes and must not preserve global state.
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 *
 * @covers \AspireUpdate\Themes_Screens::__construct
 *
 */
class ThemesScreens_ConstructTest extends WP_UnitTestCase {
	/**
	 * Test that hooks are added when API rewriting is enabled.
	 *
	 * @dataProvider data_hooks_and_methods
	 *
	 * @string $hook   The hook's name.
	 * @string $method The method to hook.
	 */
	public function test_should_add_hooks( $hook, $method ) {
		define( 'AP_ENABLE', true );

		$themes_screens = new AspireUpdate\Themes_Screens();
		$this->assertIsInt( has_action( $hook, [ $themes_screens, $method ] ) );
	}

	/**
	 * Test that hooks are not added when API rewriting is disabled.
	 *
	 * @dataProvider data_hooks_and_methods
	 *
	 * @string $hook   The hook's name.
	 * @string $method The method to hook.
	 */
	public function test_should_not_add_hooks( $hook, $method ) {
		define( 'AP_ENABLE', false );

		$themes_screens = new AspireUpdate\Themes_Screens();
		$this->assertFalse( has_action( $hook, [ $themes_screens, $method ] ) );
	}

	/**
	 * Data provider.
	 *
	 * @return array[]
	 */
	public function data_hooks_and_methods() {
		return [
			'admin_enqueue_scripts -> admin_enqueue_scripts' => [
				'hook'   => 'admin_enqueue_scripts',
				'method' => 'admin_enqueue_scripts',
			],
			'load-theme-install.php -> redirect_to_theme_install' => [
				'hook'   => 'load-theme-install.php',
				'method' => 'redirect_to_theme_install',
			],
		];
	}
}
