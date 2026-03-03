<?php

namespace BEA\ACF_Options_For_Polylang;

class Helpers {
	/**
	 * Cached regex fragment for Polylang language values, keyed by attribute (e.g. 'locale' => (fr_FR|en_US)).
	 *
	 * @var array<string, string>
	 */
	private static $locales_regex_fragment_cache = [];

	/**
	 * Returns the Polylang language attribute used for option key suffix (e.g. 'locale', 'slug').
	 * Overridable via constant BEA_ACF_OPTIONS_FOR_POLYLANG_LANG_ATTRIBUTE or filter.
	 *
	 * @since 2.0.0
	 *
	 * @return string Polylang pll_current_language() / pll_languages_list() field (e.g. 'locale', 'slug').
	 */
	public static function get_lang_attribute(): string {
		$default = defined( 'BEA_ACF_OPTIONS_FOR_POLYLANG_LANG_ATTRIBUTE' )
			? BEA_ACF_OPTIONS_FOR_POLYLANG_LANG_ATTRIBUTE
			: 'locale';

		/**
		 * Filters the Polylang language attribute used for option key suffix.
		 *
		 * @since 2.0.0
		 * @param string $attribute Attribute passed to pll_current_language() / pll_languages_list() (e.g. 'locale', 'slug').
		 */
		$attribute = apply_filters( 'bea.aofp.lang_attribute', $default );

		return is_string( $attribute ) && '' !== $attribute ? $attribute : 'locale';
	}

	/**
	 * Get the original option id without language attributes
	 *
	 * @param $post_id
	 *
	 * @return string
	 * @since  1.0.4
	 * @see acf_get_valid_post_id() for object testing
	 *
	 * @author Maxime CULEA
	 */
	public static function original_option_id( $post_id ) {
		// Default value for $post_id
		$processed_post_id = is_object( $post_id ) ? 0 : $post_id;

		// If $post_id is an object, determine its type
		if ( is_object( $post_id ) ) {
			if ( isset( $post_id->post_type, $post_id->ID ) ) {
				$processed_post_id = $post_id->ID;
			} elseif ( isset( $post_id->roles, $post_id->ID ) ) {
				$processed_post_id = 'user_' . $post_id->ID;
			} elseif ( isset( $post_id->taxonomy, $post_id->term_id ) ) {
				$processed_post_id = 'term_' . $post_id->term_id;
			} elseif ( isset( $post_id->comment_ID ) ) {
				$processed_post_id = 'comment_' . $post_id->comment_ID;
			}
		}

		// Replace 'option' with 'options'
		$processed_post_id = ( 'option' === $processed_post_id ) ? 'options' : $processed_post_id;

		// Remove the language suffix from $processed_post_id (only string/array to avoid PHP 8.1+ null deprecation)
		if ( function_exists( 'pll_current_language' ) ) {
			if ( ! is_string( $processed_post_id ) && ! is_array( $processed_post_id ) ) {
				return 0;
			}
			return str_replace( sprintf( '_%s', \pll_current_language( self::get_lang_attribute() ) ), '', $processed_post_id );
		}

		return $processed_post_id;
	}


	/**
	 * Check if the given post id is from an options page or not
	 *
	 * @param string $post_id
	 *
	 * @return bool
	 * @author Maxime CULEA
	 *
	 * @since  1.0.2
	 */
	public static function is_option_page( $post_id ) {
		$post_id = self::original_option_id( $post_id );
		if ( false !== strpos( $post_id, 'option' ) ) {
			return true;
		}

		$options_pages = self::get_option_page_ids();
		if ( empty( $options_pages ) ) {
			return false;
		}

		// Return false if the option page ID is in the list of excluded ID's
		if ( in_array( $post_id, apply_filters( 'bea.aofp.excluded_post_ids', [] ), true ) ) {
			return false;
		}

		return in_array( $post_id, $options_pages, false );
	}

	/**
	 * Get all registered options pages as array [ menu_slug => post_id ]
	 *
	 * @return array
	 * @author Maxime CULEA
	 *
	 * @since  1.0.2
	 */
	public static function get_option_page_ids() {
		if ( ! function_exists( 'acf_options_page' ) ) {
			return [];
		}

		return wp_list_pluck( acf_options_page()->get_pages(), 'post_id' );
	}

	/**
	 * Define if a post id has already been localized (e.g. ends with a Polylang locale).
	 *
	 * @param mixed $post_id Post ID (string) to check.
	 *
	 * @since  1.1.7
	 * @author Maxime CULEA
	 *
	 * @return bool
	 */
	public static function already_localized( $post_id ) {
		if ( ! is_string( $post_id ) ) {
			return false;
		}

		$locales_regex_fragment = self::locales_regex_fragment();
		if ( ! $locales_regex_fragment ) {
			return false;
		}

		$language = [];
		preg_match( '/_(' . $locales_regex_fragment . ')$/', $post_id, $language );

		return ! empty( $language );
	}

	/**
	 * Returns a regex fragment matching all Polylang configured language values (e.g. (fr_FR|en_US) or (fr|en)).
	 * Result is cached per request and per lang attribute.
	 *
	 * @return string Regex fragment including parentheses, or empty string if none.
	 */
	public static function locales_regex_fragment(): string {
		$attribute = self::get_lang_attribute();
		if ( isset( self::$locales_regex_fragment_cache[ $attribute ] ) ) {
			return self::$locales_regex_fragment_cache[ $attribute ];
		}

		if ( ! function_exists( 'pll_languages_list' ) ) {
			self::$locales_regex_fragment_cache[ $attribute ] = '';

			return '';
		}

		$locales = pll_languages_list(
			[
				'hide_empty' => false,
				'fields'     => $attribute,
			]
		);
		if ( ! $locales || ! is_array( $locales ) ) {
			self::$locales_regex_fragment_cache[ $attribute ] = '';

			return '';
		}

		$fragment = '(' . implode(
			'|',
			array_map(
				function ( $lang ) {
					return preg_quote( $lang, '/' );
				},
				$locales
			)
		) . ')';
		self::$locales_regex_fragment_cache[ $attribute ] = $fragment;

		return $fragment;
	}
}
