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

		// Initialize Polylang if available.
		if ( function_exists( 'PLL' ) ) {
			$polylang = \PLL();
			if ( method_exists( $polylang, 'init' ) ) {
				$polylang->init();
			}
		}
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

		// Should remove the locale suffix.
		$this->assertEquals( 'options', $result );
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
	 */
	public function test_original_option_id_with_post_object() {
		$post = $this->factory->post->create_and_get();

		$result = Helpers::original_option_id( $post );

		// Should return the post ID.
		$this->assertEquals( $post->ID, $result );
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
		// Add filter to exclude a specific post_id.
		add_filter(
			'bea.aofp.excluded_post_ids',
			function ( $excluded ) {
				$excluded[] = 'my_custom_option';
				return $excluded;
			}
		);

		$post_id = 'my_custom_option';

		$result = Helpers::is_option_page( $post_id );

		// Should return false for excluded post_id.
		$this->assertFalse( $result );

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

		// Should return true for localized post_id.
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
		$test_cases = [
			'options_en_US' => true,
			'options_de_DE' => true,
			'options_pt_BR' => true,
			'custom_page_es_ES' => true,
			'no_locale' => false,
			'123' => false,
		];

		foreach ( $test_cases as $post_id => $expected ) {
			$result = Helpers::already_localized( $post_id );
			$this->assertEquals( $expected, $result, "Failed for post_id: {$post_id}" );
		}
	}
}
