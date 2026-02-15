<?php

namespace BEA\ACF_Options_For_Polylang;

class Helpers {
	/**
	 * Cached regex fragment for Polylang locales (e.g. (fr_FR|en_US)).
	 *
	 * @var string|null
	 */
	private static $locales_regex_fragment_cache = null;

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

		// Remove the locale suffix from $processed_post_id (only string/array to avoid PHP 8.1+ null deprecation)
		if ( function_exists( 'pll_current_language' ) ) {
			if ( ! is_string( $processed_post_id ) && ! is_array( $processed_post_id ) ) {
				return 0;
			}
			return str_replace( sprintf( '_%s', \pll_current_language( 'locale' ) ), '', $processed_post_id );
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
	 * Returns a regex fragment matching all Polylang configured locales (e.g. (fr_FR|en_US)).
	 * Result is cached per request.
	 *
	 * @return string Regex fragment including parentheses, or empty string if none.
	 */
	public static function locales_regex_fragment(): string {
		if ( null !== self::$locales_regex_fragment_cache ) {
			return self::$locales_regex_fragment_cache;
		}

		if ( ! function_exists( 'pll_languages_list' ) ) {
			self::$locales_regex_fragment_cache = '';

			return '';
		}

		$locales = pll_languages_list(
			[
				'hide_empty' => false,
				'fields' => 'locale',
			]
		);
		if ( ! $locales || ! is_array( $locales ) ) {
			self::$locales_regex_fragment_cache = '';

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
		self::$locales_regex_fragment_cache = $fragment;

		return $fragment;
	}
}
