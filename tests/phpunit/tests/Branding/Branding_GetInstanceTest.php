<?php
/**
 * Class Branding_GetInstanceTest
 *
 * @package AspireUpdate
 */

/**
 * Tests for Branding::get_instance()
 *
 * @covers \AspireUpdate\Branding::get_instance
 */
class Branding_GetInstanceTest extends WP_UnitTestCase {
	/**
	 * Test that the same instance is retrieved.
	 */
	public function test_should_get_the_same_instance() {
		$this->assertSame(
			AspireUpdate\Branding::get_instance(),
			AspireUpdate\Branding::get_instance()
		);
	}
}
