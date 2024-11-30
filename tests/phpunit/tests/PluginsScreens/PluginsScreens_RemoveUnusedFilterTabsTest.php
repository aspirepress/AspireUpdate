<?php
/**
 * Class PluginsScreens_RemoveUnusedFilterTabsTest
 *
 * @package AspireUpdate
 */

/**
 * Tests for Plugins_Screens::remove_unused_filter_tabs()
 *
 * @covers \AspireUpdate\Plugins_Screens::remove_unused_filter_tabs
 */
class PluginsScreens_RemoveUnusedFilterTabsTest extends WP_UnitTestCase {
	/**
	 * Test that unused filter tabs are removed.
	 */
	public function test_should_remove_unused_filter_tabs() {
		$plugins_screens = new AspireUpdate\Plugins_Screens();
		$reflected       = new ReflectionProperty(
			$plugins_screens,
			'unsupported_filters'
		);
		$reflected->setAccessible( true );
		$unsupported = $reflected->getValue( $plugins_screens );
		$reflected->setAccessible( false );

		$supported = [
			'tab1' => true,
			'tab2' => true,
			'tab3' => true,
		];

		$tabs = $supported;
		foreach ( $unsupported as $tab ) {
			$tabs[ $tab ] = true;
		}

		$this->assertSame(
			$supported,
			$plugins_screens->remove_unused_filter_tabs( $tabs )
		);
	}
}
