<?php namespace BEA\ACF_Options_For_Polylang;

class Helpers {
	/**
	 * Get the original option id without language attributes
	 *
	 * @param $post_id
	 *
	 * @author Maxime CULEA
	 *
	 * @since  1.0.4
	 *
	 * @return string
	 */
	public static function original_option_id( $post_id ) {
		return str_replace( sprintf( '_%s', pll_current_language( 'locale' ) ), '', $post_id );
	}

	/**
	 * Check if the given post id is from an options page or not
	 *
	 * @param string $post_id
	 *
	 * @since  1.0.2
	 * @author Maxime CULEA
	 *
	 * @return bool
	 */
	public static function is_option_page( $post_id ) {
		$post_id = Helpers::original_option_id( $post_id );
		if ( false !== strpos( $post_id, 'option' ) ) {
			return true;
		}

		$options_pages = self::get_option_page_ids();
		return ! empty( $options_pages ) && in_array( $post_id, $options_pages );
	}

	/**
	 * Get all registered options pages as array [ post_id => page title ]
	 *
	 * @since  1.0.2
	 * @author Maxime CULEA
	 *
	 * @return array
	 */
	public static function get_option_page_ids() {
		return wp_list_pluck( acf_options_page()->get_pages(), 'post_id' );
	}
}
