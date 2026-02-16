<?php

namespace BEA\ACF_Options_For_Polylang;

class Main {
	/**
	 * Use the trait
	 */
	use Singleton;

	/**
	 * Stack for untranslated context (allows nested switch/restore).
	 *
	 * @var array<int, true>
	 */
	private static $untranslated_context_stack = [];

	protected function init() {
		// Set the setting's lang
		add_filter( 'acf/validate_post_id', [ $this, 'set_options_id_lang' ], 10, 2 );

		// When in untranslated context, force load from unsuffixed post_id (covers repeater sub_fields, relationship, etc.).
		add_filter( 'acf/load_value', [ $this, 'maybe_load_untranslated_value' ], 5, 3 );

		// Set Polylang current lang
		add_filter( 'acf/settings/current_language', [ $this, 'set_current_site_lang' ] );

		// Get default Polylang's option page value
		add_filter( 'acf/load_value', [ $this, 'get_default_value' ], 10, 3 );

		// Help loading right field for repeaters
		add_filter( 'acf/load_reference', [ $this, 'get_default_reference' ], 10, 3 );
	}

	/**
	 * When in untranslated context, load value from post_id without locale suffix.
	 * Ensures repeater sub_fields (e.g. relationship) use default values.
	 *
	 * @param mixed  $value   The value (ignored when we override).
	 * @param string $post_id The post_id ACF is using (may be localized).
	 * @param array  $field   The ACF field.
	 * @return mixed
	 */
	public function maybe_load_untranslated_value( $value, $post_id, $field ) {
		if ( empty( self::$untranslated_context_stack ) ) {
			return $value;
		}
		if ( ! Helpers::already_localized( $post_id ) ) {
			return $value;
		}
		$original_post_id = Helpers::original_option_id( $post_id );
		if ( $original_post_id === $post_id ) {
			return $value;
		}
		// Only override for option page keys (base or sub_keys like repeater rows).
		$option_pages = Helpers::get_option_page_ids();
		$is_option_key = false;
		foreach ( $option_pages as $opt_id ) {
			if ( $original_post_id === $opt_id || strpos( $original_post_id, $opt_id . '_' ) === 0 ) {
				$is_option_key = true;
				break;
			}
		}
		if ( ! $is_option_key ) {
			return $value;
		}
		remove_filter( 'acf/load_value', [ $this, 'maybe_load_untranslated_value' ], 5 );
		remove_filter( 'acf/load_value', [ $this, 'get_default_value' ], 10 );
		$value = acf_get_value( $original_post_id, $field );
		add_filter( 'acf/load_value', [ $this, 'get_default_value' ], 10, 3 );
		add_filter( 'acf/load_value', [ $this, 'maybe_load_untranslated_value' ], 5, 3 );
		return $value;
	}

	/**
	 * Get the current Polylang language value (locale, slug, etc.) or the wp's locale.
	 *
	 * @return bool|string
	 * @author Maxime CULEA
	 *
	 */
	public function set_current_site_lang() {
		if ( ! defined( 'REST_API' ) && function_exists( 'pll_current_language' ) ) {
			return \pll_current_language( Helpers::get_lang_attribute() );
		}

		return \get_locale();
	}

	/**
	 * Load the correct reference for field key in have_rows loops
	 *
	 * @param $reference
	 * @param $field_name
	 * @param $post_id
	 *
	 * @return string
	 * @since  1.1.2
	 *
	 * @author Jukra, Maxime CULEA
	 *
	 */
	public function get_default_reference( $reference, $field_name, $post_id ) {
		if ( ! empty( $reference ) || ! $post_id ) {
			return $reference;
		}

		$locales_regex_fragment = Helpers::locales_regex_fragment();
		if ( ! $locales_regex_fragment ) {
			return $reference;
		}

		/**
		 * Dynamically get the options page ID by stripping Polylang locale suffix.
		 */
		$_post_id = preg_replace( '/_(' . $locales_regex_fragment . ')$/', '', $post_id );

		remove_filter( 'acf/load_reference', [ $this, 'get_default_reference' ] );
		$reference = acf_get_reference( $field_name, $_post_id );
		add_filter( 'acf/load_reference', [ $this, 'get_default_reference' ], 10, 3 );

		return $reference;
	}

	/**
	 * Load default value (all languages) in front if none found for an acf option
	 *
	 * @param $value
	 * @param $post_id
	 * @param $field
	 *
	 * @return mixed|string|void
	 * @author Maxime CULEA
	 *
	 */
	public function get_default_value( $value, $post_id, $field ) {
		$enable = acf_is_ajax() || ( ! is_admin() && Helpers::is_option_page( $post_id ) );

		/**
		 * Allow to override default logic for enabling default values.
		 *
		 * @since 2.0.0
		 * @param bool   $enable  Whether to enable default value fallback.
		 * @param string $post_id The post ID (options page ID).
		 * @param array  $field   The ACF field array.
		 */
		if ( ! apply_filters( 'bea.aofp.get_default_enable', $enable, $post_id, $field ) ) {
			return $value;
		}

		$original_post_id = Helpers::original_option_id( $post_id );

		/**
		 * Activate or deactivate the default value (all languages) for the given post id
		 *
		 * @param bool $show_default : whatever to show default for the given post id
		 * @param string $original_post_id : the original post id without lang attributes
		 *
		 * @since 1.0.4
		 */
		if ( ! apply_filters( 'bea.aofp.get_default', true, $original_post_id ) ) {
			return $value;
		}

		/**
		 * According to his type, check the value to be not an empty string.
		 * While false or 0 could be returned, so "empty" method could not be here useful.
		 *
		 * @see   https://github.com/atomicorange : Thx to atomicorange for the issue
		 *
		 * @since 1.0.1
		 */
		if ( ! is_null( $value ) ) {
			if ( is_array( $value ) ) {
				// Get from array all the not empty strings
				$is_empty = array_filter(
					$value,
					function ( $value_c ) {
						return '' !== $value_c;
					}
				);

				if ( ! empty( $is_empty ) ) { // Not an array of empty values
					return $value;
				}
			} elseif ( 'repeater' !== $field['type'] ) {
				if ( '' !== $value ) { // Not an empty string
					return $value;
				}
			}
		}

		/**
		 * Delete filters for loading "default" Polylang saved value
		 * and for avoiding infinite looping on current filter
		 */
		remove_filter( 'acf/settings/current_language', [ $this, 'set_current_site_lang' ] );
		remove_filter( 'acf/load_value', [ $this, 'get_default_value' ] );

		// Get the "All language" value
		$value = acf_get_value( $original_post_id, $field );

		/**
		 * Re-add deleted filters
		 */
		add_filter( 'acf/settings/current_language', [ $this, 'set_current_site_lang' ] );
		add_filter( 'acf/load_value', [ $this, 'get_default_value' ], 10, 3 );

		return $value;
	}

	/**
	 * Manage to change the post_id with the current lang to save option against
	 *
	 * @param string $future_post_id
	 * @param string $original_post_id
	 *
	 * @return string
	 * @author Maxime CULEA
	 *
	 * @since  1.0.2
	 */
	public function set_options_id_lang( $future_post_id, $original_post_id ) {
		// Only on custom post id option page
		if ( ! Helpers::is_option_page( $original_post_id ) ) {
			return $future_post_id;
		}

		// When in untranslated context, use post_id without locale suffix so get_field() loads default values.
		if ( ! empty( self::$untranslated_context_stack ) ) {
			return $original_post_id;
		}

		if ( Helpers::already_localized( $future_post_id ) ) {
			return $future_post_id;
		}

		// For the default ACF options key ('options'), skip: Polylang locale already applies.
		// For custom option keys (e.g. theme slug), append locale so each language has its own storage.
		if ( 'options' !== Helpers::original_option_id( $future_post_id ) ) {
			$dl = acf_get_setting( 'default_language' );
			$cl = acf_get_setting( 'current_language' );
			if ( $cl && $cl !== $dl ) {
				$future_post_id .= '_' . $cl;
			}
		}

		return $future_post_id;
	}

	/**
	 * Switch context to untranslated (default) option values.
	 * Subsequent get_field( ..., option_page_id ) will load values without locale suffix.
	 * Must be paired with restore_current_lang().
	 *
	 * @since 2.0.0
	 */
	public static function switch_to_untranslated(): void {
		self::$untranslated_context_stack[] = true;
	}

	/**
	 * Restore context after switch_to_untranslated().
	 * Option values will again be loaded for the current language.
	 *
	 * @since 2.0.0
	 */
	public static function restore_current_lang(): void {
		if ( ! empty( self::$untranslated_context_stack ) ) {
			array_pop( self::$untranslated_context_stack );
		}
	}
}
