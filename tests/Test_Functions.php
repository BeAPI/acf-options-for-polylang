<?php
/**
 * Class Test_Functions
 *
 * @package BEA\ACF_Options_For_Polylang
 */

namespace BEA\ACF_Options_For_Polylang\Tests;

use BEA\ACF_Options_For_Polylang\Main;
use BEA\ACF_Options_For_Polylang\Helpers;

/**
 * Test case for the public helper functions in functions.php.
 */
class Test_Functions extends \WP_UnitTestCase {

	/**
	 * Set up the test.
	 */
	public function set_up() {
		parent::set_up();

		\PLL()->init();

		Main::get_instance();
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
	 * Test that bea_aofp_switch_to_untranslated function exists.
	 */
	public function test_bea_aofp_switch_to_untranslated_function_exists() {
		$this->assertTrue( function_exists( 'bea_aofp_switch_to_untranslated' ) );
	}

	/**
	 * Test that bea_aofp_restore_current_lang function exists.
	 */
	public function test_bea_aofp_restore_current_lang_function_exists() {
		$this->assertTrue( function_exists( 'bea_aofp_restore_current_lang' ) );
	}

	/**
	 * Test the full switch/restore workflow via the public API functions.
	 *
	 * Writes a default value on the unsuffixed post_id, then verifies that
	 * bea_aofp_switch_to_untranslated() causes set_options_id_lang() to return
	 * the unsuffixed post_id, and bea_aofp_restore_current_lang() restores
	 * the suffixed behavior.
	 */
	public function test_switch_and_restore_via_public_api() {
		$original_id = 'theme-general-settings';
		if ( ! Helpers::is_option_page( $original_id ) ) {
			$this->markTestSkipped( 'ACF options page theme-general-settings is not registered.' );
		}

		$this->set_polylang_language( 'fr' );
		$main = Main::get_instance();

		// Before switch: post_id should be suffixed.
		$result_before = $main->set_options_id_lang( $original_id, $original_id );
		$this->assertStringContainsString( '_fr_FR', $result_before );

		// Switch to untranslated via public function.
		bea_aofp_switch_to_untranslated();

		$result_during = $main->set_options_id_lang( $original_id, $original_id );
		$this->assertEquals( $original_id, $result_during, 'After switch, post_id should not be suffixed.' );

		// Restore via public function.
		bea_aofp_restore_current_lang();

		$result_after = $main->set_options_id_lang( $original_id, $original_id );
		$this->assertStringContainsString( '_fr_FR', $result_after, 'After restore, post_id should be suffixed again.' );
	}
}
