<?php
/**
 * Class PluginsScreens_ConstructTest
 *
 * @package AspireUpdate
 */

/**
 * Tests for Plugins_Screens::__construct()
 *
 * These tests cause constants to be defined.
 * They must run in separate processes and must not preserve global state.
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 *
 * @covers \AspireUpdate\Plugins_Screens::__construct
 */
class PluginsScreens_ConstructTest extends WP_UnitTestCase {
	/**
	 * Test that hooks are added when API rewriting is enabled.
	 *
	 * @dataProvider data_single_site_hooks_and_methods
	 *
	 * @string $hook   The hook's name.
	 * @string $method The method to hook.
	 */
	public function test_should_add_hooks( $hook, $method ) {
		define( 'AP_ENABLE', true );

		$plugins_screens = new AspireUpdate\Plugins_Screens();
		$this->assertIsInt( has_action( $hook, [ $plugins_screens, $method ] ) );
	}

	/**
	 * Test that hooks are not added when API rewriting is disabled.
	 *
	 * @dataProvider data_single_site_hooks_and_methods
	 *
	 * @string $hook   The hook's name.
	 * @string $method The method to hook.
	 */
	public function test_should_not_add_hooks( $hook, $method ) {
		define( 'AP_ENABLE', false );

		$plugins_screens = new AspireUpdate\Plugins_Screens();
		$this->assertFalse( has_action( $hook, [ $plugins_screens, $method ] ) );
	}

	/**
	 * Data provider.
	 *
	 * @return array[]
	 */
	public function data_single_site_hooks_and_methods() {
		return [
			'install_plugins_tabs -> remove_unused_filter_tabs' => [
				'hook'   => 'install_plugins_tabs',
				'method' => 'remove_unused_filter_tabs',
			],
		];
	}
}
