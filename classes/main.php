<?php

namespace BEA\ACF_Options_For_Polylang;

class Main {
	/**
	 * Use the trait
	 */
	use Singleton;

	protected function init() {
		// Set the setting's lang
		add_filter( 'acf/validate_post_id', [ $this, 'set_options_id_lang' ], 10, 2 );

		// Set Polylang current lang
		add_filter( 'acf/settings/current_language', [ $this, 'set_current_site_lang' ] );

		// Get default Polylang's option page value
		add_filter( 'acf/load_value', [ $this, 'get_default_value' ], 10, 3 );

		// Help loading right field for repeaters
		add_filter( 'acf/load_reference', [ $this, 'get_default_reference' ], 10, 3 );
	}

	/**
	 * Get the current Polylang's locale or the wp's one
	 *
	 * @return bool|string
	 * @author Maxime CULEA
	 *
	 */
	public function set_current_site_lang() {
		return ! defined( 'REST_API' ) ? pll_current_language( 'locale' ) : \get_locale();
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
		if ( ! empty( $reference ) ) {
			return $reference;
		}

		/**
		 * Dynamically get the options page ID
		 * @see : https://regex101.com/r/58uhKg/2/
		 */
		$_post_id = preg_replace( '/(_[a-z]{2}_[A-Z]{2})/', '', $post_id );

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
		if ( ! acf_is_ajax() && ( is_admin() || ! Helpers::is_option_page( $post_id ) ) ) {
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

		if ( Helpers::already_localized( $future_post_id ) ) {
			return $future_post_id;
		}

		// If no custom acf key, no need while already impacted by Polylang locale
		if ( 'options' !== Helpers::original_option_id( $future_post_id ) ) {
			$dl = acf_get_setting( 'default_language' );
			$cl = acf_get_setting( 'current_language' );
			if ( $cl && $cl !== $dl ) {
				$future_post_id .= '_' . $cl;
			}
		}

		return $future_post_id;
	}
}
