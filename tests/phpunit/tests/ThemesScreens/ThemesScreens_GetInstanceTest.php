<?php
/**
 * Class ThemesScreens_GetInstanceTest
 *
 * @package AspireUpdate
 */

/**
 * Tests for Themes_Screens::get_instance()
 *
 * These tests rely on the method not being called during earlier test runs.
 * They must run in separate processes and must not preserve global state.
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 *
 * @covers \AspireUpdate\Themes_Screens::get_instance
 */
class ThemesScreens_GetInstanceTest extends WP_UnitTestCase {
	/**
	 * Test that the same instance is retrieved.
	 */
	public function test_should_get_the_same_instance() {
		$this->assertSame(
			AspireUpdate\Themes_Screens::get_instance(),
			AspireUpdate\Themes_Screens::get_instance()
		);
	}
}
