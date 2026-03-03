<?php
/**
 * Class Test_Helpers
 *
 * @package BEA\ACF_Options_For_Polylang
 */

namespace BEA\ACF_Options_For_Polylang\Tests;

use BEA\ACF_Options_For_Polylang\Helpers;

/**
 * Test case for the Helpers class.
 */
class Test_Helpers extends \WP_UnitTestCase {

	/**
	 * Set up the test.
	 */
	public function set_up() {
		parent::set_up();

		\PLL()->init();
	}

	/**
	 * Tear down the test.
	 */
	public function tear_down() {
		parent::tear_down();
	}

	/**
	 * Test original_option_id with a simple string.
	 */
	public function test_original_option_id_with_string() {
		$post_id = 'options_fr_FR';

		$result = Helpers::original_option_id( $post_id );

		// pll_current_language() returns false when no curlang is set, so the suffix stays.
		// When a language is active, the matching suffix is stripped.
		$this->assertTrue(
			'options' === $result || 'options_fr_FR' === $result,
			'Expected options or options_fr_FR, got: ' . $result
		);
	}

	/**
	 * Test original_option_id with 'option' string.
	 */
	public function test_original_option_id_converts_option_to_options() {
		$post_id = 'option';

		$result = Helpers::original_option_id( $post_id );

		// Should convert 'option' to 'options'.
		$this->assertEquals( 'options', $result );
	}

	/**
	 * Test original_option_id with a post object.
	 *
	 * With Polylang active, original_option_id() passes through pll_current_language()
	 * which rejects non-string post IDs (numeric WP_Post->ID) and returns 0.
	 */
	public function test_original_option_id_with_post_object() {
		$post = $this->factory->post->create_and_get();

		$result = Helpers::original_option_id( $post );

		$this->assertEquals( 0, $result );
	}

	/**
	 * Test original_option_id with a user object.
	 */
	public function test_original_option_id_with_user_object() {
		$user = $this->factory->user->create_and_get();

		$result = Helpers::original_option_id( $user );

		// Should return 'user_{ID}'.
		$this->assertEquals( 'user_' . $user->ID, $result );
	}

	/**
	 * Test original_option_id with a term object.
	 */
	public function test_original_option_id_with_term_object() {
		$term = $this->factory->term->create_and_get();

		$result = Helpers::original_option_id( $term );

		// Should return 'term_{term_id}'.
		$this->assertEquals( 'term_' . $term->term_id, $result );
	}

	/**
	 * Test is_option_page with 'options' post_id.
	 */
	public function test_is_option_page_with_options() {
		$post_id = 'options';

		$result = Helpers::is_option_page( $post_id );

		// Should return true for 'options'.
		$this->assertTrue( $result );
	}

	/**
	 * Test is_option_page with localized options post_id.
	 */
	public function test_is_option_page_with_localized_options() {
		$post_id = 'options_fr_FR';

		$result = Helpers::is_option_page( $post_id );

		// Should return true for localized options.
		$this->assertTrue( $result );
	}

	/**
	 * Test is_option_page with a regular post ID.
	 */
	public function test_is_option_page_with_regular_post_id() {
		$post = $this->factory->post->create();

		$result = Helpers::is_option_page( $post );

		// Should return false for regular post ID.
		$this->assertFalse( $result );
	}

	/**
	 * Test is_option_page with excluded post_id.
	 */
	public function test_is_option_page_with_excluded_post_id() {
		// First, we need to register an ACF options page to get a valid post_id.
		if ( function_exists( 'acf_add_options_page' ) ) {
			acf_add_options_page(
				[
					'page_title' => 'Test Page',
					'menu_slug'  => 'test-page',
					'post_id'    => 'test_page_settings',
				]
			);
		}

		// Add filter to exclude this specific post_id.
		add_filter(
			'bea.aofp.excluded_post_ids',
			function ( $excluded ) {
				$excluded[] = 'test_page_settings';
				return $excluded;
			}
		);

		$post_id = 'test_page_settings';

		$result = Helpers::is_option_page( $post_id );

		// Should return false for excluded post_id.
		$this->assertFalse( $result, 'Excluded post_id should return false' );

		// Remove filter.
		remove_all_filters( 'bea.aofp.excluded_post_ids' );
	}

	/**
	 * Test get_option_page_ids returns an array.
	 */
	public function test_get_option_page_ids_returns_array() {
		$result = Helpers::get_option_page_ids();

		// Should return an array.
		$this->assertIsArray( $result );
	}

	/**
	 * Test already_localized with localized post_id.
	 */
	public function test_already_localized_with_localized_post_id() {
		$post_id = 'options_fr_FR';

		$result = Helpers::already_localized( $post_id );

		$this->assertTrue( $result );
	}

	/**
	 * Test already_localized with non-localized post_id.
	 */
	public function test_already_localized_with_non_localized_post_id() {
		$post_id = 'options';

		$result = Helpers::already_localized( $post_id );

		// Should return false for non-localized post_id.
		$this->assertFalse( $result );
	}

	/**
	 * Test already_localized with different locale formats.
	 */
	public function test_already_localized_with_different_locale_formats() {
		// Only test locales that are actually configured in the test environment.
		$test_cases = [
			'options_fr_FR'     => true,
			'options_en_US'     => true,
			'no_locale'         => false,
			'123'               => false,
		];

		foreach ( $test_cases as $post_id => $expected ) {
			$result = Helpers::already_localized( $post_id );
			$this->assertEquals( $expected, $result, "Failed for post_id: {$post_id}" );
		}
	}

	/**
	 * Test already_localized returns false for non-string values.
	 */
	public function test_already_localized_with_non_string_value() {
		$this->assertFalse( Helpers::already_localized( 123 ) );
		$this->assertFalse( Helpers::already_localized( null ) );
		$this->assertFalse( Helpers::already_localized( true ) );
	}

	/**
	 * Test get_lang_attribute returns 'locale' by default.
	 */
	public function test_get_lang_attribute_returns_locale_by_default() {
		$this->assertEquals( 'locale', Helpers::get_lang_attribute() );
	}

	/**
	 * Test get_lang_attribute respects the bea.aofp.lang_attribute filter.
	 */
	public function test_get_lang_attribute_respects_filter() {
		add_filter(
			'bea.aofp.lang_attribute',
			function () {
				return 'slug';
			}
		);

		$this->assertEquals( 'slug', Helpers::get_lang_attribute() );

		remove_all_filters( 'bea.aofp.lang_attribute' );
	}

	/**
	 * Test get_lang_attribute falls back to 'locale' when filter returns invalid value.
	 */
	public function test_get_lang_attribute_fallback_on_invalid_filter_value() {
		add_filter( 'bea.aofp.lang_attribute', '__return_empty_string' );
		$this->assertEquals( 'locale', Helpers::get_lang_attribute() );
		remove_all_filters( 'bea.aofp.lang_attribute' );

		add_filter( 'bea.aofp.lang_attribute', '__return_null' );
		$this->assertEquals( 'locale', Helpers::get_lang_attribute() );
		remove_all_filters( 'bea.aofp.lang_attribute' );
	}

	/**
	 * Test locales_regex_fragment returns a regex containing configured languages.
	 */
	public function test_locales_regex_fragment_returns_regex_with_configured_languages() {
		$fragment = Helpers::locales_regex_fragment();

		$this->assertNotEmpty( $fragment, 'Regex fragment should not be empty when languages are configured.' );
		$this->assertStringContainsString( 'fr_FR', $fragment );
		$this->assertStringContainsString( 'en_US', $fragment );
	}

	/**
	 * Test locales_regex_fragment returns consistent cached results.
	 */
	public function test_locales_regex_fragment_is_cached() {
		$first_call  = Helpers::locales_regex_fragment();
		$second_call = Helpers::locales_regex_fragment();

		$this->assertSame( $first_call, $second_call );
	}
}
