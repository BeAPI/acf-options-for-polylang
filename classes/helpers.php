<?php

namespace BEA\ACF_Options_For_Polylang;

class Helpers {
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
		// $post_id may be an object
		if ( is_object( $post_id ) ) {
			if ( isset( $post_id->post_type, $post_id->ID ) ) { // post
				$post_id = $post_id->ID;
			} elseif ( isset( $post_id->roles, $post_id->ID ) ) { // user
				$post_id = 'user_' . $post_id->ID;
			} elseif ( isset( $post_id->taxonomy, $post_id->term_id ) ) { // term
				$post_id = 'term_' . $post_id->term_id;
			} elseif ( isset( $post_id->comment_ID ) ) { // comment
				$post_id = 'comment_' . $post_id->comment_ID;
			} else { // default
				$post_id = 0;
			}
		}

		// allow for option == options
		if ( 'option' === $post_id ) {
			$post_id = 'options';
		}

		return str_replace( sprintf( '_%s', pll_current_language( 'locale' ) ), '', $post_id );
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
	 * Define if a post id has already been localized fr_FR
	 *
	 * @param $post_id
	 *
	 * @since  1.1.7
	 * @author Maxime CULEA
	 *
	 * @return bool
	 */
	public static function already_localized( $post_id ) {
		preg_match( '/[a-z]{2}_[A-Z]{2}/', $post_id, $language );

		return ! empty( $language );
	}
}
