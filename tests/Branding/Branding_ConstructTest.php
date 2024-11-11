<?php
/**
 * Class Branding_ConstructTest
 *
 * @package AspireUpdate
 */

/**
 * Tests for Branding::__construct()
 *
 * @covers \AspireUpdate\Branding::__construct
 */
class Branding_ConstructTest extends WP_UnitTestCase {
	/**
	 * Test that hooks are added when API rewriting is enabled in single site.
	 *
	 * @dataProvider data_single_site_hooks_and_methods
	 *
	 * @group ms-excluded
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 *
	 * @string $hook   The hook's name.
	 * @string $method The method to hook.
	 */
	public function test_should_add_hooks_in_single_site( $hook, $method ) {
		define( 'AP_ENABLE', true );

		$branding = new AspireUpdate\Branding();
		$this->assertIsInt( has_action( $hook, [ $branding, $method ] ) );
	}

	/**
	 * Test that hooks are not added when API rewriting is disabled in single-site.
	 *
	 * @dataProvider data_single_site_hooks_and_methods
	 *
	 * @group ms-excluded
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 *
	 * @string $hook   The hook's name.
	 * @string $method The method to hook.
	 */
	public function test_should_not_add_hooks_in_single_site( $hook, $method ) {
		define( 'AP_ENABLE', false );

		$branding = new AspireUpdate\Branding();
		$this->assertFalse( has_action( $hook, [ $branding, $method ] ) );
	}

	/**
	 * Data provider.
	 *
	 * @return array[]
	 */
	public function data_single_site_hooks_and_methods() {
		return [
			'admin_notices -> output_admin_notice' => [
				'hook'   => 'admin_notices',
				'method' => 'output_admin_notice',
			],
			'admin_enqueue_scripts -> admin_enqueue_scripts' => [
				'hook'   => 'admin_enqueue_scripts',
				'method' => 'admin_enqueue_scripts',
			],
		];
	}

	/**
	 * Test that hooks are added when API rewriting is enabled in multisite.
	 *
	 * @dataProvider data_multisite_hooks_and_methods
	 *
	 * @group ms-required
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 *
	 * @string $hook   The hook's name.
	 * @string $method The method to hook.
	 */
	public function test_should_add_hooks_in_multisite( $hook, $method ) {
		define( 'AP_ENABLE', true );

		$branding = new AspireUpdate\Branding();
		$this->assertIsInt( has_action( $hook, [ $branding, $method ] ) );
	}

	/**
	 * Test that hooks are not added when API rewriting is disabled in multisite.
	 *
	 * @dataProvider data_multisite_hooks_and_methods
	 *
	 * @group ms-required
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 *
	 * @string $hook   The hook's name.
	 * @string $method The method to hook.
	 */
	public function test_should_not_add_hooks_in_multisite( $hook, $method ) {
		define( 'AP_ENABLE', false );

		$branding = new AspireUpdate\Branding();
		$this->assertFalse( has_action( $hook, [ $branding, $method ] ) );
	}

	/**
	 * Data provider.
	 *
	 * @return array[]
	 */
	public function data_multisite_hooks_and_methods() {
		return [
			'network_admin_notices -> output_admin_notice' => [
				'hook'   => 'network_admin_notices',
				'method' => 'output_admin_notice',
			],
			'admin_enqueue_scripts -> admin_enqueue_scripts' => [
				'hook'   => 'admin_enqueue_scripts',
				'method' => 'admin_enqueue_scripts',
			],
		];
	}
}
