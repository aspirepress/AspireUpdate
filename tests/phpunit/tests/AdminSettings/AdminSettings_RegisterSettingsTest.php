<?php
/**
 * Class AdminSettings_RegisterSettingsTest
 *
 * @package AspireUpdate
 */

/**
 * Tests for Admin_Settings::register_settings()
 *
 * @covers \AspireUpdate\Admin_Settings::register_settings
 */
class AdminSettings_RegisterSettingsTest extends AdminSettings_UnitTestCase {
	/**
	 * A backup of $wp_registered_settings.
	 *
	 * @var array|null
	 */
	private static $wp_registered_settings_backup;

	/**
	 * Backs up registered settings before any tests run.
	 *
	 * @return void
	 */
	public static function set_up_before_class() {
		parent::set_up_before_class();

		global $wp_registered_settings;
		self::$wp_registered_settings_backup = $wp_registered_settings;
	}

	/**
	 * Resets registered settings after each test runs.
	 *
	 * @return void
	 */
	public function tear_down() {
		global $wp_registered_settings;

		if ( self::$wp_registered_settings_backup ) {
			$wp_registered_settings = self::$wp_registered_settings_backup;
		} else {
			unset( $wp_registered_settings );
		}

		parent::tear_down();
	}

	/**
	 * Test that the main setting is registered.
	 *
	 * @return void
	 */
	public function test_should_register_setting() {
		global $wp_registered_settings;

		$admin_settings = new AspireUpdate\Admin_Settings();
		$admin_settings->register_settings();

		$this->assertIsArray( $wp_registered_settings );
		$this->assertArrayHasKey( self::$option_name, $wp_registered_settings );
	}

	/**
	 * Test that the expected settings section is registered.
	 *
	 * @dataProvider data_settings_sections
	 *
	 * @param string $section_id The ID of the settings section.
	 */
	public function test_should_register_settings_section( $section_id ) {
		global $wp_settings_sections;

		$admin_settings = new AspireUpdate\Admin_Settings();
		$admin_settings->register_settings();

		$this->assertIsArray(
			$wp_settings_sections,
			'There are no settings sections.'
		);

		$this->assertArrayHasKey(
			self::$options_page,
			$wp_settings_sections,
			'There is no entry for the plugin in settings sections.'
		);

		$this->assertIsArray(
			$wp_settings_sections[ self::$options_page ],
			'There are no settings sections registered for the plugin.'
		);

		$this->assertArrayHasKey(
			$section_id,
			$wp_settings_sections[ self::$options_page ],
			"Section {$section_id} is not registered."
		);
	}

	/**
	 * Data provider.
	 *
	 * @return array[]
	 */
	public function data_settings_sections() {
		return self::text_array_to_dataprovider(
			[
				'aspireupdate_settings_section',
				'aspireupdate_debug_settings_section',
			]
		);
	}

	/**
	 * Test that the expected settings field is registered.
	 *
	 * @dataProvider data_settings_fields
	 *
	 * @param string $section_id The ID of the settings section.
	 * @param string $field_id   The ID of the settings field.
	 */
	public function test_should_register_settings_field( $section_id, $field_id ) {
		global $wp_settings_fields;

		$admin_settings = new AspireUpdate\Admin_Settings();
		$admin_settings->register_settings();

		$this->assertIsArray(
			$wp_settings_fields,
			'There are no settings sections.'
		);

		$this->assertArrayHasKey(
			self::$options_page,
			$wp_settings_fields,
			'There is no entry for the plugin in settings sections.'
		);

		$this->assertIsArray(
			$wp_settings_fields[ self::$options_page ],
			'There are no settings sections registered for the plugin.'
		);

		$this->assertArrayHasKey(
			$section_id,
			$wp_settings_fields[ self::$options_page ],
			"Section {$section_id} is not registered."
		);

		$this->assertIsArray(
			$wp_settings_fields[ self::$options_page ],
			"There are no fields registered for Section {$section_id}."
		);

		$this->assertArrayHasKey(
			$field_id,
			$wp_settings_fields[ self::$options_page ][ $section_id ],
			"Field {$field_id} is not registered for Section {$section_id}."
		);
	}

	/**
	 * Data provider.
	 *
	 * @return array[]
	 */
	public function data_settings_fields() {
		return [
			'aspireupdate_settings_section -> enable'   => [
				'section_id' => 'aspireupdate_settings_section',
				'field_id'   => 'enable',
			],
			'aspireupdate_settings_section -> api_host' => [
				'section_id' => 'aspireupdate_settings_section',
				'field_id'   => 'api_host',
			],
			'aspireupdate_settings_section -> api_key'  => [
				'section_id' => 'aspireupdate_settings_section',
				'field_id'   => 'api_key',
			],
			'aspireupdate_debug_settings_section -> enable_debug' => [
				'section_id' => 'aspireupdate_debug_settings_section',
				'field_id'   => 'enable_debug',
			],
			'aspireupdate_debug_settings_section -> enable_debug_type' => [
				'section_id' => 'aspireupdate_debug_settings_section',
				'field_id'   => 'enable_debug_type',
			],
			'aspireupdate_debug_settings_section -> disable_ssl_verification' => [
				'section_id' => 'aspireupdate_debug_settings_section',
				'field_id'   => 'disable_ssl_verification',
			],
		];
	}
}
