<?php
/**
 * Class Utilities_GetSiteDomainTest
 *
 * @package AspireUpdate
 */

/**
 * Tests for Utilities::get_site_domain()
 *
 * @covers \AspireUpdate\Utilities::get_site_domain
 */
class Utilities_GetSiteDomainTest extends WP_UnitTestCase {
	/**
	 * The user ID of an administrator.
	 *
	 * @var int
	 */
	private static $admin_id;

	/**
	 * Creates an administrator user before any tests run.
	 *
	 * @return void
	 */
	public static function set_up_before_class() {
		parent::set_up_before_class();

		self::$admin_id = self::factory()->user->create(
			[
				'role' => 'administrator',
			]
		);

		wp_set_current_user( self::$admin_id );
		grant_super_admin( self::$admin_id );
	}

	/**
	 * Test that the site's domain is retrieved in single-site.
	 *
	 * @dataProvider data_single_site_domains
	 *
	 * @group ms-excluded
	 *
	 * @param string $site_url The site's URL.
	 * @param string $expected The site's domain.
	 */
	public function test_should_get_site_domain_in_single_site( $site_url, $expected ) {
		update_option( 'siteurl', $site_url );
		$this->assertSame( $expected, AspireUpdate\Utilities::get_site_domain() );
	}

	/**
	 * Data provider.
	 *
	 * @return array[]
	 */
	public function data_single_site_domains() {
		return [
			'HTTP, a single-part TLD and no WWW'  => [
				'site_url' => 'http://my-site.org',
				'expected' => 'my-site.org',
			],
			'HTTP, a two-part TLD and no WWW'     => [
				'site_url' => 'http://my-site.org.uk',
				'expected' => 'my-site.org.uk',
			],
			'HTTP, a single-part TLD and WWW'     => [
				'site_url' => 'http://www.my-site.org',
				'expected' => 'www.my-site.org',
			],
			'HTTP, a two-part TLD and WWW'        => [
				'site_url' => 'http://www.my-site.org.uk',
				'expected' => 'www.my-site.org.uk',
			],
			'HTTPS, a single-part TLD and no WWW' => [
				'site_url' => 'https://my-site.org',
				'expected' => 'my-site.org',
			],
			'HTTPS, a two-part TLD and no WWW'    => [
				'site_url' => 'https://my-site.org.uk',
				'expected' => 'my-site.org.uk',
			],
			'HTTPS, a single-part TLD and WWW'    => [
				'site_url' => 'https://www.my-site.org',
				'expected' => 'www.my-site.org',
			],
			'HTTPS, a two-part TLD and WWW'       => [
				'site_url' => 'https://www.my-site.org.uk',
				'expected' => 'www.my-site.org.uk',
			],
		];
	}

	/**
	 * Test that the main site's domain is retrieved when on a sub-site in multisite.
	 *
	 * @dataProvider data_multisite_domains
	 *
	 * @group ms-required
	 *
	 * @param string $main_site_domain The main site's domain.
	 */
	public function test_should_get_main_site_domain_in_multisite_subsite( $main_site_domain ) {
		add_filter(
			'get_network',
			static function ( $network ) use ( $main_site_domain ) {
				$network->domain = $main_site_domain;
				return $network;
			}
		);

		$new_blog_id = self::factory()->blog->create(
			[
				'domain'  => 'second-site.' . str_replace( 'www.', '', $main_site_domain ),
				'user_id' => self::$admin_id,
			]
		);

		switch_to_blog( $new_blog_id );
		$actual = AspireUpdate\Utilities::get_site_domain();
		restore_current_blog();

		$this->assertSame( $main_site_domain, $actual );
	}

	/**
	 * Data provider.
	 *
	 * @return array[]
	 */
	public function data_multisite_domains() {
		return [
			'a single-part TLD and no WWW' => [
				'main_site_domain' => 'main-site.org',
			],
			'a two-part TLD and no WWW'    => [
				'main_site_domain' => 'main-site.org.uk',
			],
			'a single-part TLD and WWW'    => [
				'main_site_domain' => 'www.main-site.org',
			],
			'a two-part TLD and WWW'       => [
				'main_site_domain' => 'www.main-site.org.uk',
			],
		];
	}
}
