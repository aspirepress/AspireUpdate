<?php
/**
 * Class Utilities_IncludeFileTest
 *
 * @package AspireUpdate
 */

/**
 * Tests for Utilities::include_file()
 *
 * @covers \AspireUpdate\Utilities::include_file
 */
class Utilities_IncludeFileTest extends WP_UnitTestCase {
	/**
	 * Test that a file is not included when an empty path has been provided.
	 */
	public function test_should_not_include_file_when_path_is_empty() {
		$output = get_echo( [ 'AspireUpdate\Utilities', 'include_file' ], [ '' ] );
		$this->assertEmpty( $output );
	}

	/**
	 * Test that a file is not included when it does not exist.
	 */
	public function test_should_not_include_file_when_file_does_not_exist() {
		$output = get_echo( [ 'AspireUpdate\Utilities', 'include_file' ], [ 'non-existent-file.php' ] );
		$this->assertEmpty( $output );
	}

	/**
	 * Test that a file is included.
	 */
	public function test_should_include_file() {
		$output = get_echo( [ 'AspireUpdate\Utilities', 'include_file' ], [ 'page-admin-settings.php' ] );
		$this->assertNotEmpty( $output );
	}
}
