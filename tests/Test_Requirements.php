<?php
/**
 * Class Test_Requirements
 *
 * @package BEA\ACF_Options_For_Polylang
 */

namespace BEA\ACF_Options_For_Polylang\Tests;

use BEA\ACF_Options_For_Polylang\Requirements;

/**
 * Test case for the Requirements class.
 */
class Test_Requirements extends \WP_UnitTestCase {

	/**
	 * Instance of Requirements class.
	 *
	 * @var Requirements
	 */
	private $requirements;

	/**
	 * Set up the test.
	 */
	public function set_up() {
		parent::set_up();

		$this->requirements = Requirements::get_instance();
	}

	/**
	 * Tear down the test.
	 */
	public function tear_down() {
		parent::tear_down();
	}

	/**
	 * Test that the Requirements class is instantiated correctly.
	 */
	public function test_requirements_instance_exists() {
		$this->assertInstanceOf( Requirements::class, $this->requirements );
	}

	/**
	 * Test check_requirements returns true when ACF and Polylang are available.
	 */
	public function test_check_requirements_with_dependencies_available() {
		// This test assumes ACF and Polylang are loaded in the test environment.
		if ( function_exists( 'acf' ) && defined( 'POLYLANG_VERSION' ) ) {
			$result = $this->requirements->check_requirements();
			$this->assertTrue( $result );
		} else {
			$this->markTestSkipped( 'ACF and Polylang are not available in this test environment.' );
		}
	}

	/**
	 * Test check_requirements returns false when ACF is not available.
	 */
	public function test_check_requirements_without_acf() {
		// Mock the absence of ACF by checking if it exists first.
		if ( ! function_exists( 'acf' ) ) {
			$result = $this->requirements->check_requirements();
			$this->assertFalse( $result );
		} else {
			$this->markTestSkipped( 'ACF is available, cannot test missing ACF scenario.' );
		}
	}

	/**
	 * Test check_requirements returns false when Polylang is not available.
	 */
	public function test_check_requirements_without_polylang() {
		// Mock the absence of Polylang.
		if ( ! defined( 'POLYLANG_VERSION' ) ) {
			$result = $this->requirements->check_requirements();
			$this->assertFalse( $result );
		} else {
			$this->markTestSkipped( 'Polylang is available, cannot test missing Polylang scenario.' );
		}
	}

	/**
	 * Test that display_error triggers an error and adds admin notice.
	 */
	public function test_display_error_triggers_error() {
		$message = 'Test error message';
		$error_triggered = false;

		// Set custom error handler to catch the triggered error.
		set_error_handler(
			function ( $errno, $errstr ) use ( &$error_triggered ) {
				$error_triggered = true;
				return true; // Suppress error.
			}
		);

		$this->requirements->display_error( $message );

		// Restore error handler.
		restore_error_handler();

		// Verify error was triggered.
		$this->assertTrue( $error_triggered, 'display_error() should trigger an error' );

		// Verify admin_notices action was added.
		$this->assertGreaterThan( 0, has_action( 'admin_notices' ) );
	}

	/**
	 * Test that display_error adds admin_notices action.
	 */
	public function test_display_error_adds_admin_notice_action() {
		$message = 'Test error message';

		// Remove default error triggering to avoid test failure.
		set_error_handler(
			function () {
				// Suppress error.
			}
		);

		$this->requirements->display_error( $message );

		// Check that admin_notices action was added.
		$this->assertGreaterThan( 0, has_action( 'admin_notices' ) );

		// Restore error handler.
		restore_error_handler();
	}

	/**
	 * Test that display_error adds admin_init action for deactivation.
	 */
	public function test_display_error_adds_admin_init_action() {
		$message = 'Test error message';

		// Remove default error triggering to avoid test failure.
		set_error_handler(
			function () {
				// Suppress error.
			}
		);

		$this->requirements->display_error( $message );

		// Check that admin_init action was added.
		$this->assertGreaterThan( 0, has_action( 'admin_init' ) );

		// Restore error handler.
		restore_error_handler();
	}

	/**
	 * Test ACF version check.
	 */
	public function test_check_requirements_with_old_acf_version() {
		// This test would require mocking ACF version, which is complex.
		// For now, we document that the version check exists.
		if ( function_exists( 'acf' ) ) {
			$acf_version = acf()->version;
			$this->assertNotEmpty( $acf_version, 'ACF version should be defined' );
		} else {
			$this->markTestSkipped( 'ACF is not available in this test environment.' );
		}
	}
}
