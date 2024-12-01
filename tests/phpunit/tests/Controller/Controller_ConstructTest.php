<?php
/**
 * Class Controller_ConstructTest
 *
 * @package AspireUpdate
 */

/**
 * Tests for Controller::__construct()
 *
 * @covers \AspireUpdate\Controller::__construct
 */
class Controller_ConstructTest extends WP_UnitTestCase {
	/**
	 * Test that hooks are added.
	 *
	 * @dataProvider data_hooks_and_methods
	 *
	 * @string $hook   The hook's name.
	 * @string $method The method to hook.
	 */
	public function test_should_add_hooks( $hook, $method ) {
		$controller = new AspireUpdate\Controller();
		$this->assertIsInt( has_action( $hook, [ $controller, $method ] ) );
	}

	/**
	 * Data provider.
	 *
	 * @return array[]
	 */
	public function data_hooks_and_methods() {
		return [
			'wp_ajax_aspireupdate_clear_log -> clear_log' => [
				'hook'   => 'wp_ajax_aspireupdate_clear_log',
				'method' => 'clear_log',
			],
			'wp_ajax_aspireupdate_read_log -> read_log'   => [
				'hook'   => 'wp_ajax_aspireupdate_read_log',
				'method' => 'read_log',
			],
		];
	}
}
