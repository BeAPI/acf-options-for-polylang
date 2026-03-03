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

		\PLL()->init();

		$this->main = Main::get_instance();
	}

	/**
	 * Tear down the test.
	 */
	public function tear_down() {
		restore_previous_locale();
		parent::tear_down();
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
		$this->assertNotFalse( has_filter( 'acf/load_value', [ $this->main, 'maybe_load_untranslated_value' ] ) );
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
		$this->set_polylang_language( 'fr' );
		$this->assertEquals( 'fr_FR', $this->main->set_current_site_lang(), 'French locale should be returned when language is FR.' );

		$this->set_polylang_language( 'en' );
		$this->assertEquals( 'en_US', $this->main->set_current_site_lang(), 'English locale should be returned when language is EN.' );

		$this->set_polylang_language( 'fr' );
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
		$original_id = 'theme-general-settings';
		if ( ! Helpers::is_option_page( $original_id ) ) {
			$this->markTestSkipped( 'ACF options page theme-general-settings is not registered.' );
		}

		$this->set_polylang_language( 'fr' );
		$result_fr = $this->main->set_options_id_lang( $original_id, $original_id );
		$this->assertStringContainsString( '_fr_FR', $result_fr, 'Option ID should contain fr_FR when current language is FR.' );
		$this->assertEquals( 'theme-general-settings_fr_FR', $result_fr );

		$this->set_polylang_language( 'en' );
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

	/**
	 * Test maybe_load_untranslated_value returns value when not in untranslated context.
	 */
	public function test_maybe_load_untranslated_value_returns_value_when_not_in_untranslated_context() {
		$value   = 'original_value';
		$post_id = 'theme-general-settings_fr_FR';
		$field   = [
			'type' => 'text',
			'name' => 'site_title',
			'key'  => 'field_site_title',
		];

		$result = $this->main->maybe_load_untranslated_value( $value, $post_id, $field );

		$this->assertEquals( $value, $result );
	}

	/**
	 * Test maybe_load_untranslated_value returns value when post_id is not localized.
	 */
	public function test_maybe_load_untranslated_value_returns_value_when_post_id_not_localized() {
		Main::switch_to_untranslated();

		$value   = 'original_value';
		$post_id = 'theme-general-settings';
		$field   = [
			'type' => 'text',
			'name' => 'site_title',
			'key'  => 'field_site_title',
		];

		$result = $this->main->maybe_load_untranslated_value( $value, $post_id, $field );

		$this->assertEquals( $value, $result );

		Main::restore_current_lang();
	}

	/**
	 * Test maybe_load_untranslated_value returns value when post_id is not an option key.
	 */
	public function test_maybe_load_untranslated_value_returns_value_when_not_option_key() {
		Main::switch_to_untranslated();

		$value   = 'original_value';
		$post_id = 'my_custom_thing_fr_FR';
		$field   = [
			'type' => 'text',
			'name' => 'some_field',
			'key'  => 'field_some_field',
		];

		$result = $this->main->maybe_load_untranslated_value( $value, $post_id, $field );

		$this->assertEquals( $value, $result );

		Main::restore_current_lang();
	}

	/**
	 * Test maybe_load_untranslated_value loads unsuffixed value in untranslated context.
	 */
	public function test_maybe_load_untranslated_value_loads_unsuffixed_value_in_untranslated_context() {
		$this->set_polylang_language( 'fr' );

		// Store the value directly in the DB the way ACF does for options pages.
		update_option( 'theme-general-settings_site_title', 'Default Title' );
		update_option( '_theme-general-settings_site_title', 'field_site_title' );

		Main::switch_to_untranslated();

		$field = acf_get_field( 'field_site_title' );
		$result = $this->main->maybe_load_untranslated_value( '', 'theme-general-settings_fr_FR', $field );

		$this->assertEquals( 'Default Title', $result );

		Main::restore_current_lang();
	}

	/**
	 * Test switch_to_untranslated causes set_options_id_lang to return unsuffixed post_id,
	 * and restore_current_lang re-enables the suffix.
	 *
	 * Uses 'en' (non-default) because the default language ('fr') is intentionally
	 * not suffixed by set_options_id_lang ($cl === $dl => no suffix).
	 */
	public function test_switch_to_untranslated_and_restore() {
		$original_id = 'theme-general-settings';
		if ( ! Helpers::is_option_page( $original_id ) ) {
			$this->markTestSkipped( 'ACF options page theme-general-settings is not registered.' );
		}

		$this->set_polylang_language( 'en' );

		Main::switch_to_untranslated();
		$result_during = $this->main->set_options_id_lang( $original_id, $original_id );
		$this->assertEquals( $original_id, $result_during, 'In untranslated context, post_id should not be suffixed.' );

		Main::restore_current_lang();
		$result_after = $this->main->set_options_id_lang( $original_id, $original_id );
		$this->assertStringContainsString( '_en_US', $result_after, 'After restore, post_id should be suffixed again.' );
	}

	/**
	 * Test that nested switch_to_untranslated calls require matching restore_current_lang calls.
	 *
	 * Uses 'en' (non-default) because the default language ('fr') is intentionally
	 * not suffixed by set_options_id_lang ($cl === $dl => no suffix).
	 */
	public function test_switch_to_untranslated_nesting() {
		$original_id = 'theme-general-settings';
		if ( ! Helpers::is_option_page( $original_id ) ) {
			$this->markTestSkipped( 'ACF options page theme-general-settings is not registered.' );
		}

		$this->set_polylang_language( 'en' );

		Main::switch_to_untranslated();
		Main::switch_to_untranslated();

		// After one restore, context should still be untranslated.
		Main::restore_current_lang();
		$result = $this->main->set_options_id_lang( $original_id, $original_id );
		$this->assertEquals( $original_id, $result, 'After one restore with two switches, context should still be untranslated.' );

		// After second restore, suffix should be back.
		Main::restore_current_lang();
		$result = $this->main->set_options_id_lang( $original_id, $original_id );
		$this->assertStringContainsString( '_en_US', $result, 'After all restores, post_id should be suffixed again.' );
	}

	/**
	 * Test that restore_current_lang without prior switch is a safe no-op.
	 *
	 * Uses 'en' (non-default) because the default language ('fr') is intentionally
	 * not suffixed by set_options_id_lang ($cl === $dl => no suffix).
	 */
	public function test_restore_current_lang_without_switch_is_noop() {
		$original_id = 'theme-general-settings';
		if ( ! Helpers::is_option_page( $original_id ) ) {
			$this->markTestSkipped( 'ACF options page theme-general-settings is not registered.' );
		}

		$this->set_polylang_language( 'en' );

		Main::restore_current_lang();

		$result = $this->main->set_options_id_lang( $original_id, $original_id );
		$this->assertStringContainsString( '_en_US', $result, 'restore_current_lang without switch should not affect behavior.' );
	}

	/**
	 * Test get_default_value falls back to default when localized value is empty.
	 */
	public function test_get_default_value_falls_back_to_default_when_empty() {
		$this->set_polylang_language( 'fr' );

		// Write a default (unsuffixed) value.
		update_option( 'options_site_title', 'Default Title' );
		update_option( '_options_site_title', 'field_site_title' );

		// Ensure front context (not admin).
		set_current_screen( 'front' );

		$field = [
			'type' => 'text',
			'name' => 'site_title',
			'key'  => 'field_site_title',
		];

		$result = $this->main->get_default_value( '', 'options', $field );

		$this->assertEquals( 'Default Title', $result );
	}

	/**
	 * Test get_default_value with an array of empty strings triggers fallback.
	 */
	public function test_get_default_value_with_empty_array() {
		set_current_screen( 'front' );

		$field = [
			'type' => 'gallery',
			'name' => 'gallery',
			'key'  => 'field_gallery',
		];

		$result = $this->main->get_default_value( [ '', '' ], 'options', $field );

		// Array of empty strings is considered empty, so fallback is attempted.
		// Result depends on whether a default exists; it should not be the original array.
		$this->assertNotEquals( [ '', '' ], $result );
	}

	/**
	 * Test get_default_value with null value triggers fallback.
	 */
	public function test_get_default_value_with_null_value() {
		set_current_screen( 'front' );

		$field = [
			'type' => 'text',
			'name' => 'site_title',
			'key'  => 'field_site_title',
		];

		$result = $this->main->get_default_value( null, 'options', $field );

		// Null triggers the fallback path (is_null check).
		$this->assertTrue( is_null( $result ) || is_string( $result ), 'Null value should go through fallback path.' );
	}

	/**
	 * Test get_default_value with repeater type and empty string value triggers fallback.
	 *
	 * For repeater fields, the empty-string check in the elseif is skipped (since type === 'repeater'),
	 * so the fallback to default values is always attempted.
	 */
	public function test_get_default_value_with_repeater_type() {
		set_current_screen( 'front' );

		$field = [
			'type' => 'repeater',
			'name' => 'links',
			'key'  => 'field_links_repeater',
		];

		// For repeater type, '' falls through to the fallback path.
		// Since no default data exists, the fallback also returns a falsy value.
		$result = $this->main->get_default_value( '', 'options', $field );

		// Verify the method completes without error. The result is falsy because no default data is stored.
		$this->assertEmpty( $result, 'Repeater fallback without stored data should return empty/falsy.' );
	}

	/**
	 * Test get_default_value with bea.aofp.get_default_enable filter to force enable in admin.
	 */
	public function test_get_default_value_with_bea_aofp_get_default_enable_filter() {
		set_current_screen( 'edit-post' );

		add_filter( 'bea.aofp.get_default_enable', '__return_true' );

		// Store value AND field reference in the DB the way ACF does for options pages.
		update_option( 'options_site_title', 'Forced Default' );
		update_option( '_options_site_title', 'field_site_title' );

		$field = acf_get_field( 'field_site_title' );
		if ( ! $field ) {
			$field = [
				'type' => 'text',
				'name' => 'site_title',
				'key'  => 'field_site_title',
			];
		}

		// Flush ACF's internal value store so it reads fresh from DB.
		acf_get_store( 'values' )->reset();

		$result = $this->main->get_default_value( '', 'options', $field );

		$this->assertEquals( 'Forced Default', $result );

		remove_all_filters( 'bea.aofp.get_default_enable' );
		set_current_screen( 'front' );
	}
}
