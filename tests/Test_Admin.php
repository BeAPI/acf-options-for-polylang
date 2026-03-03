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

		\PLL()->init();
		$this->set_polylang_language( 'fr' );

		// Reset the singleton to ensure init() is called in test context.
		Admin::destroy();

		// Initialize the Admin singleton (this will call init() and register hooks).
		$this->admin = Admin::get_instance();
	}

	/**
	 * Tear down the test.
	 */
	public function tear_down() {
		restore_previous_locale();
		parent::tear_down();

		// Reset screen and globals.
		set_current_screen( 'front' );
		unset( $_GET['page'] );
		unset( $_SERVER['REQUEST_URI'] );
	}

	/**
	 * Switch Polylang current language and WordPress locale for testing.
	 *
	 * @param string $slug Language slug (e.g. 'fr', 'en').
	 */
	private function set_polylang_language( string $slug ): void {
		$languages = \PLL()->model->get_languages_list();
		foreach ( $languages as $lang ) {
			if ( $slug === $lang->slug ) {
				\PLL()->curlang = $lang;
				switch_to_locale( $lang->locale );
				return;
			}
		}
		$this->fail( "Polylang language '{$slug}' not found." );
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
	 * Test submitbox_before_major_actions output with current language set.
	 */
	public function test_submitbox_before_major_actions_with_current_language() {
		$this->set_polylang_language( 'fr' );

		$page = [
			'post_id'   => 'theme-general-settings',
			'menu_slug' => 'theme-general-settings',
		];

		ob_start();
		$this->admin->submitbox_before_major_actions( $page );
		$output = ob_get_clean();

		$this->assertStringContainsString( 'misc-pub-section', $output );
		$this->assertStringContainsString( 'You are changing the options for language', $output );
	}

	/**
	 * Test submitbox_before_major_actions output without current language.
	 */
	public function test_submitbox_before_major_actions_without_current_language() {
		// Remove current language to trigger the "untranslated" message.
		\PLL()->curlang = null;

		$page = [
			'post_id'   => 'theme-general-settings',
			'menu_slug' => 'theme-general-settings',
		];

		ob_start();
		$this->admin->submitbox_before_major_actions( $page );
		$output = ob_get_clean();

		$this->assertStringContainsString( 'misc-pub-section', $output );
		$this->assertStringContainsString( 'Be careful', $output );
		$this->assertStringContainsString( 'untranslated options', $output );
	}

	/**
	 * Test submitbox_before_major_actions output contains HTML tags.
	 */
	public function test_submitbox_before_major_actions_contains_html() {
		$page = [
			'post_id'   => 'theme-general-settings',
			'menu_slug' => 'theme-general-settings',
		];

		ob_start();
		$this->admin->submitbox_before_major_actions( $page );
		$output = ob_get_clean();

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

	/**
	 * Test submitbox_before_major_actions produces no output for invalid page data.
	 */
	public function test_submitbox_before_major_actions_with_invalid_page() {
		$invalid_pages = [
			null,
			[],
			[ 'menu_slug' => 'test' ],
			[ 'post_id' => 123 ],
		];

		foreach ( $invalid_pages as $page ) {
			ob_start();
			$this->admin->submitbox_before_major_actions( $page );
			$output = ob_get_clean();

			$this->assertEmpty( $output, 'No output should be produced for invalid page data: ' . wp_json_encode( $page ) );
		}
	}

	/**
	 * Test submitbox_before_major_actions displays the language name when a language is active.
	 */
	public function test_submitbox_before_major_actions_with_active_language_name() {
		$this->set_polylang_language( 'fr' );

		$page = [
			'post_id'   => 'theme-general-settings',
			'menu_slug' => 'theme-general-settings',
		];

		ob_start();
		$this->admin->submitbox_before_major_actions( $page );
		$output = ob_get_clean();

		$this->assertStringContainsString( 'Fran', $output, 'Output should contain the French language name.' );
		$this->assertStringNotContainsString( 'untranslated', $output, 'Output should NOT contain the untranslated message.' );
	}
}
