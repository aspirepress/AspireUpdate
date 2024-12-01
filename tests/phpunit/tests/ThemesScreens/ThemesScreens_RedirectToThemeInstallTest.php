<?php
/**
 * Class ThemesScreens_RedirectToThemeInstallTest
 *
 * @package AspireUpdate
 */

/**
 * Tests for Themes_Screens::redirect_to_theme_install()
 *
 * These tests cause constants to be defined and also occur after headers are sent.
 * They must run in separate processes and must not preserve global state.
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 *
 * @covers \AspireUpdate\Themes_Screens::redirect_to_theme_install
 */
class ThemesScreens_RedirectToThemeInstallTest extends WP_UnitTestCase {
	/**
	 * Test that a redirect is not performed when $_REQUEST['_wpnonce'] is not set.
	 */
	public function test_should_not_redirect_when_request_wpnonce_is_not_set() {
		unset( $_REQUEST['_wpnonce'] );
		$_GET['browse'] = 'favorites';
		define( 'AP_ENABLE', true );

		$redirect = new MockAction();
		add_filter( 'wp_redirect', [ $redirect, 'filter' ] );

		$themes_screens = new AspireUpdate\Themes_Screens();
		$themes_screens->redirect_to_theme_install();

		$this->assertSame( 0, $redirect->get_call_count() );
	}

	/**
	 * Test that a redirect is not performed when nonce verification fails.
	 */
	public function test_should_not_redirect_when_nonce_verification_fails() {
		$_REQUEST['_wpnonce'] = 'incorrect_value';
		$_GET['browse']       = 'favorites';
		define( 'AP_ENABLE', true );

		$redirect = new MockAction();
		add_filter( 'wp_redirect', [ $redirect, 'filter' ] );

		$themes_screens = new AspireUpdate\Themes_Screens();
		$themes_screens->redirect_to_theme_install();

		$this->assertSame( 0, $redirect->get_call_count() );
	}

	/**
	 * Test that a redirect is not performed when not viewing an unsupported filter.
	 */
	public function test_should_not_redirect_when_not_viewing_an_unsupported_filter() {
		$_REQUEST['_wpnonce'] = wp_create_nonce( 'query-themes' );
		$_GET['browse']       = 'some-filter';
		define( 'AP_ENABLE', true );

		$redirect = new MockAction();
		add_filter( 'wp_redirect', [ $redirect, 'filter' ] );

		$themes_screens = new AspireUpdate\Themes_Screens();
		$themes_screens->redirect_to_theme_install();

		$this->assertSame( 0, $redirect->get_call_count() );
	}

	/**
	 * Test that a redirect is not performed when AP_ENABLE is false.
	 */
	public function test_should_not_redirect_when_ap_enable_is_false() {
		$_REQUEST['_wpnonce'] = wp_create_nonce( 'query-themes' );
		$_GET['browse']       = 'favorites';
		define( 'AP_ENABLE', false );

		$redirect = new MockAction();
		add_filter( 'wp_redirect', [ $redirect, 'filter' ] );

		$themes_screens = new AspireUpdate\Themes_Screens();
		$themes_screens->redirect_to_theme_install();

		$this->assertSame( 0, $redirect->get_call_count() );
	}

	/**
	 * Test that a redirect is performed when viewing an unsupported filter.
	 *
	 * @dataProvider data_unsupported_filters
	 *
	 * @param string $filter The unsupported filter.
	 */
	public function test_should_redirect_when_viewing_an_unsupported_filter( $filter ) {
		$_REQUEST['_wpnonce'] = wp_create_nonce( 'query-themes' );
		$_GET['browse']       = $filter;
		define( 'AP_ENABLE', true );

		$redirect = new MockAction();
		add_filter( 'wp_redirect', [ $redirect, 'filter' ] );

		$themes_screens = new AspireUpdate\Themes_Screens();
		$themes_screens->redirect_to_theme_install();

		$this->assertSame( 1, $redirect->get_call_count() );
	}

	/**
	 * Data provider.
	 *
	 * @return array[]
	 */
	public function data_unsupported_filters() {
		return self::text_array_to_dataprovider(
			[
				'favorites',
			]
		);
	}
}
