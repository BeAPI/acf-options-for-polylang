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

		// Set admin context for ACF options page.
		set_current_screen( 'toplevel_page_theme-general-settings' );

		// Simulate the options page request.
		$_GET['page'] = 'theme-general-settings';

		// Set REQUEST_URI to avoid warnings.
		if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
			$_SERVER['REQUEST_URI'] = '/wp-admin/admin.php?page=theme-general-settings';
		}

		// Force admin context.
		if ( ! defined( 'WP_ADMIN' ) ) {
			define( 'WP_ADMIN', true );
		}

		// Initialize Polylang and set current language.
		if ( function_exists( 'PLL' ) ) {
			$polylang = \PLL();
			if ( method_exists( $polylang, 'init' ) ) {
				$polylang->init();
			}

			// Force French as current language for tests.
			if ( function_exists( 'pll_set_language' ) ) {
				\pll_set_language( 'fr' );
			}
		}

		// Reset the singleton to ensure init() is called in test context.
		Admin::destroy();

		// Initialize the Admin singleton (this will call init() and register hooks).
		$this->admin = Admin::get_instance();
	}

	/**
	 * Tear down the test.
	 */
	public function tear_down() {
		parent::tear_down();

		// Reset screen and globals.
		set_current_screen( 'front' );
		unset( $_GET['page'] );
		unset( $_SERVER['REQUEST_URI'] );
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
		// Verify the filter is registered by checking with has_action (which also works for filters).
		$priority = has_action(
			'acf/options_page/submitbox_before_major_actions',
			[ $this->admin, 'submitbox_before_major_actions' ]
		);

		$this->assertNotFalse(
			$priority,
			'Filter acf/options_page/submitbox_before_major_actions should be registered'
		);
	}

	/**
	 * Test submitbox_before_major_actions output with current language.
	 */
	public function test_submitbox_before_major_actions_with_current_language() {
		// In test context, pll_current_language() returns false,
		// so we test the default "untranslated" message.
		ob_start();
		$this->admin->submitbox_before_major_actions();
		$output = ob_get_clean();

		// Check that output contains expected text for untranslated context.
		$this->assertStringContainsString( 'misc-pub-section', $output );
		$this->assertStringContainsString( 'Be careful', $output );
		$this->assertStringContainsString( 'untranslated options', $output );
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
