<?php
/*
  Plugin Name: BEA - Polylang's ACF Option page
  Plugin URI: http://www.beapi.fr
  Description: Add ACF options page support for Polylang
  Author: BeAPI
  Author URI: http://www.beapi.fr
  Version: 1.0.0
 */
class BEA_ACF_For_Polylang {

	function __construct() {
		// Set Polylang current lang
		add_filter( 'acf/settings/current_language', array( __CLASS__, 'get_current_site_lang' ) );

		// Load default Polylang's option page value
		add_filter( 'acf/load_value', array( __CLASS__, 'set_default_value' ), 10, 3 );
	}

	/**
	 * Get the current Polylang's locale or the wp's one
	 *
	 * @author Maxime CULEA
	 *
	 * @return bool|string
	 */
	public static function get_current_site_lang() {
		return function_exists( 'pll_current_language' ) ? pll_current_language( 'locale' ) : get_locale();
	}

	/**
	 * Load default value in front, if none found for an acf option
	 *
	 * @author Maxime CULEA
	 *
	 * @param $value
	 * @param $post_id
	 * @param $field
	 *
	 * @return mixed|string|void
	 */
	public static function set_default_value( $value, $post_id, $field ) {
		if ( is_admin() || false === strpos( $post_id, 'options' ) || ! function_exists( 'pll_current_language' ) || ! ( empty( $value ) && $value === false ) ) {
			return $value;
		}

		/**
		 * Take off filters for loading "default" Polylang saved value
		 * and for avoiding infinite looping on current filter
		 */
		remove_filter( 'acf/settings/current_language', array( __CLASS__, 'get_current_site_lang' ) );
		remove_filter( 'acf/load_value', array( __CLASS__, 'set_default_value' ) );

		$value = acf_get_metadata( 'options', $field['name'] );

		/**
		 * Re-add taken off filters
		 */
		add_filter( 'acf/settings/current_language', array( __CLASS__, 'get_current_site_lang' ) );
		add_filter( 'acf/load_value', array( __CLASS__, 'set_default_value' ), 10, 3 );

		return $value;
	}

}
new BEA_ACF_For_Polylang();
