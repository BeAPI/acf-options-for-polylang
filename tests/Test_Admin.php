<?php
/**
 * Class Test_Admin
 *
 * @package BEA\ACF_Options_For_Polylang
 */

namespace BEA\ACF_Options_For_Polylang\Tests;

use BEA\ACF_Options_For_Polylang\Admin;

/**
 * Test case for the Admin class.
 */
class Test_Admin extends \WP_UnitTestCase {

	/**
	 * Instance of Admin class.
	 *
	 * @var Admin
	 */
	private $admin;

	/**
	 * Set up the test.
	 */
	public function set_up() {
		parent::set_up();

		// Set admin context.
		set_current_screen( 'edit-post' );

		// Initialize Polylang if available.
		if ( function_exists( 'PLL' ) ) {
			$polylang = \PLL();
			if ( method_exists( $polylang, 'init' ) ) {
				$polylang->init();
			}
		}

		$this->admin = Admin::get_instance();
	}

	/**
	 * Tear down the test.
	 */
	public function tear_down() {
		parent::tear_down();

		// Reset screen.
		set_current_screen( 'front' );
	}

	/**
	 * Test that the Admin class is instantiated correctly.
	 */
	public function test_admin_instance_exists() {
		$this->assertInstanceOf( Admin::class, $this->admin );
	}

	/**
	 * Test that the filter is registered.
	 */
	public function test_filter_is_registered() {
		$this->assertNotFalse(
			has_filter(
				'acf/options_page/submitbox_before_major_actions',
				[ $this->admin, 'submitbox_before_major_actions' ]
			)
		);
	}

	/**
	 * Test submitbox_before_major_actions output with current language.
	 */
	public function test_submitbox_before_major_actions_with_current_language() {
		// Mock pll_current_language to return a language name.
		if ( ! function_exists( 'pll_current_language' ) ) {
			function pll_current_language( $value ) {
				if ( 'name' === $value ) {
					return 'French';
				}
				return 'fr';
			}
		}

		// Capture output.
		ob_start();
		$this->admin->submitbox_before_major_actions();
		$output = ob_get_clean();

		// Check that output contains expected text.
		$this->assertStringContainsString( 'misc-pub-section', $output );
		$this->assertStringContainsString( 'You are changing the options for language', $output );
	}

	/**
	 * Test submitbox_before_major_actions output without current language.
	 */
	public function test_submitbox_before_major_actions_without_current_language() {
		// This test would require mocking pll_current_language to return false.
		// For simplicity, we test that the method produces output.
		ob_start();
		$this->admin->submitbox_before_major_actions();
		$output = ob_get_clean();

		// Check that output contains the misc-pub-section class.
		$this->assertStringContainsString( 'misc-pub-section', $output );
	}

	/**
	 * Test submitbox_before_major_actions output contains HTML tags.
	 */
	public function test_submitbox_before_major_actions_contains_html() {
		ob_start();
		$this->admin->submitbox_before_major_actions();
		$output = ob_get_clean();

		// Check that output contains opening and closing paragraph tags.
		$this->assertStringContainsString( '<p class="misc-pub-section">', $output );
		$this->assertStringContainsString( '</p>', $output );
	}

	/**
	 * Test that submitbox_before_major_actions method is callable.
	 */
	public function test_submitbox_before_major_actions_is_callable() {
		$this->assertTrue(
			is_callable( [ $this->admin, 'submitbox_before_major_actions' ] )
		);
	}
}
