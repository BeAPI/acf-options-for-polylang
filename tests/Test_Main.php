<?php
/**
 * Class Test_Main
 *
 * @package BEA\ACF_Options_For_Polylang
 */

namespace BEA\ACF_Options_For_Polylang\Tests;

use BEA\ACF_Options_For_Polylang\Main;
use BEA\ACF_Options_For_Polylang\Helpers;

/**
 * Test case for the Main class.
 */
class Test_Main extends \WP_UnitTestCase {

	/**
	 * Instance of Main class.
	 *
	 * @var Main
	 */
	private $main;

	/**
	 * Set up the test.
	 */
	public function set_up() {
		parent::set_up();

		// Initialize Polylang.
		if ( function_exists( 'PLL' ) ) {
			$polylang = \PLL();
			if ( method_exists( $polylang, 'init' ) ) {
				$polylang->init();
			}
		}

		$this->main = Main::get_instance();
	}

	/**
	 * Tear down the test.
	 */
	public function tear_down() {
		parent::tear_down();
	}

	/**
	 * Test that the Main class is instantiated correctly.
	 */
	public function test_main_instance_exists() {
		$this->assertInstanceOf( Main::class, $this->main );
	}

	/**
	 * Test that the filters are registered.
	 */
	public function test_filters_are_registered() {
		$this->assertNotFalse( has_filter( 'acf/validate_post_id', [ $this->main, 'set_options_id_lang' ] ) );
		$this->assertNotFalse( has_filter( 'acf/settings/current_language', [ $this->main, 'set_current_site_lang' ] ) );
		$this->assertNotFalse( has_filter( 'acf/load_value', [ $this->main, 'get_default_value' ] ) );
		$this->assertNotFalse( has_filter( 'acf/load_reference', [ $this->main, 'get_default_reference' ] ) );
	}

	/**
	 * Test set_current_site_lang method.
	 */
	public function test_set_current_site_lang() {
		// Test when not in REST API context.
		$result = $this->main->set_current_site_lang();

		// Should return a locale string.
		$this->assertIsString( $result );
	}

	/**
	 * Test that set_current_site_lang returns different locale when language changes at runtime.
	 *
	 * Non-regression: Ensures that a switch from French to English (or vice versa) yields
	 * the correct locale so ACF options can store/retrieve different values per language.
	 */
	public function test_set_current_site_lang_changes_with_runtime_language_switch() {
		if ( ! function_exists( 'pll_set_language' ) || ! function_exists( 'pll_current_language' ) ) {
			$this->markTestSkipped( 'Polylang language switch is not available.' );
		}

		\pll_set_language( 'fr' );
		$this->assertEquals( 'fr_FR', $this->main->set_current_site_lang(), 'French locale should be returned when language is FR.' );

		\pll_set_language( 'en' );
		$this->assertEquals( 'en_US', $this->main->set_current_site_lang(), 'English locale should be returned when language is EN.' );

		\pll_set_language( 'fr' );
		$this->assertEquals( 'fr_FR', $this->main->set_current_site_lang(), 'Switching back to FR should return fr_FR again.' );
	}

	/**
	 * Test that set_options_id_lang returns different option IDs per current language at runtime.
	 *
	 * Non-regression: Ensures that the same logical option page (e.g. theme-general-settings)
	 * resolves to different storage keys (e.g. theme-general-settings_fr_FR vs theme-general-settings_en_US)
	 * so that values can differ between languages.
	 */
	public function test_set_options_id_lang_returns_different_id_per_runtime_language() {
		if ( ! function_exists( 'pll_set_language' ) ) {
			$this->markTestSkipped( 'Polylang language switch is not available.' );
		}

		// Use the custom option page from mu-plugin (theme-general-settings); "options" is not suffixed by this plugin.
		$original_id = 'theme-general-settings';
		if ( ! Helpers::is_option_page( $original_id ) ) {
			$this->markTestSkipped( 'ACF options page theme-general-settings is not registered.' );
		}

		\pll_set_language( 'fr' );
		$result_fr = $this->main->set_options_id_lang( $original_id, $original_id );
		$this->assertStringContainsString( '_fr_FR', $result_fr, 'Option ID should contain fr_FR when current language is FR.' );
		$this->assertEquals( 'theme-general-settings_fr_FR', $result_fr );

		\pll_set_language( 'en' );
		$result_en = $this->main->set_options_id_lang( $original_id, $original_id );
		$this->assertStringContainsString( '_en_US', $result_en, 'Option ID should contain en_US when current language is EN.' );
		$this->assertEquals( 'theme-general-settings_en_US', $result_en );

		$this->assertNotEquals( $result_fr, $result_en, 'Option IDs must differ between FR and EN so values can differ per language.' );
	}

	/**
	 * Test set_current_site_lang in REST API context.
	 */
	public function test_set_current_site_lang_rest_api() {
		// Mock REST API context.
		if ( ! defined( 'REST_API' ) ) {
			define( 'REST_API', true );
		}

		$result = $this->main->set_current_site_lang();

		// Should return the WordPress locale.
		$this->assertEquals( get_locale(), $result );
	}

	/**
	 * Test get_default_reference method.
	 */
	public function test_get_default_reference_with_existing_reference() {
		$reference   = 'field_123456';
		$field_name  = 'test_field';
		$post_id     = 'options';

		$result = $this->main->get_default_reference( $reference, $field_name, $post_id );

		// Should return the existing reference.
		$this->assertEquals( $reference, $result );
	}

	/**
	 * Test get_default_reference method with localized post_id.
	 */
	public function test_get_default_reference_with_localized_post_id() {
		$reference   = '';
		$field_name  = 'test_field';
		$post_id     = 'options_fr_FR';

		$result = $this->main->get_default_reference( $reference, $field_name, $post_id );

		// Result can be a string or null if no default reference exists.
		$this->assertTrue(
			is_string( $result ) || is_null( $result ),
			'Result should be a string or null'
		);
	}

	/**
	 * Test set_options_id_lang with a non-option page.
	 */
	public function test_set_options_id_lang_with_non_option_page() {
		$future_post_id   = '123';
		$original_post_id = '123';

		$result = $this->main->set_options_id_lang( $future_post_id, $original_post_id );

		// Should return the same post ID.
		$this->assertEquals( $future_post_id, $result );
	}

	/**
	 * Test set_options_id_lang with already localized post_id.
	 */
	public function test_set_options_id_lang_with_already_localized() {
		$future_post_id   = 'options_fr_FR';
		$original_post_id = 'options_fr_FR';

		$result = $this->main->set_options_id_lang( $future_post_id, $original_post_id );

		// Should return the same post ID.
		$this->assertEquals( $future_post_id, $result );
	}

	/**
	 * Test get_default_value in admin context.
	 */
	public function test_get_default_value_in_admin() {
		// Set admin context.
		set_current_screen( 'edit-post' );

		$value   = 'test_value';
		$post_id = 'options';
		$field   = [
			'type' => 'text',
			'name' => 'test_field',
		];

		$result = $this->main->get_default_value( $value, $post_id, $field );

		// Should return the same value in admin.
		$this->assertEquals( $value, $result );

		// Reset screen.
		set_current_screen( 'front' );
	}

	/**
	 * Test get_default_value with non-empty value.
	 */
	public function test_get_default_value_with_non_empty_value() {
		$value   = 'test_value';
		$post_id = 'options';
		$field   = [
			'type' => 'text',
			'name' => 'test_field',
		];

		// Make sure we're not in admin or AJAX context.
		$result = $this->main->get_default_value( $value, $post_id, $field );

		// Should return the non-empty value.
		$this->assertEquals( $value, $result );
	}

	/**
	 * Test get_default_value with filter to disable default.
	 */
	public function test_get_default_value_with_filter_disabled() {
		// Add filter to disable default value.
		add_filter(
			'bea.aofp.get_default',
			function ( $show_default, $original_post_id ) {
				return false;
			},
			10,
			2
		);

		$value   = '';
		$post_id = 'options';
		$field   = [
			'type' => 'text',
			'name' => 'test_field',
		];

		$result = $this->main->get_default_value( $value, $post_id, $field );

		// Should return the empty value without trying to get default.
		$this->assertEquals( $value, $result );

		// Remove filter.
		remove_all_filters( 'bea.aofp.get_default' );
	}
}
