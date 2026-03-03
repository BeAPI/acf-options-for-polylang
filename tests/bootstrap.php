<?php
/**
 * PHPUnit bootstrap file for ACF Options for Polylang
 *
 * @package BEA\ACF_Options_For_Polylang
 */

// Force Polylang to select PLL_Admin as its context class so that init_context()
// is called even when no languages exist in DB yet (the mu-plugin creates them at `init`).
// Without this, PLL() / pll_current_language() / pll_languages_list() are never defined
// because api.php is only loaded inside init_context().
if ( ! defined( 'PLL_ADMIN' ) ) {
	define( 'PLL_ADMIN', true );
}

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

// Forward custom PHPUnit Polyfills configuration to PHPUnit bootstrap file.
$_phpunit_polyfills_path = getenv( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH' );
if ( false !== $_phpunit_polyfills_path ) {
	define( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH', $_phpunit_polyfills_path );
}

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	echo "Could not find $_tests_dir/includes/functions.php. Please ensure WordPress test suite is installed." . PHP_EOL; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	exit( 1 );
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
	// Load ACF plugin (try Pro first from GitHub zip, then other locations).
	if ( file_exists( WP_PLUGIN_DIR . '/main/acf.php' ) ) {
		// ACF Pro from GitHub archive (advanced-custom-fields-pro-main extracts to "main").
		require_once WP_PLUGIN_DIR . '/main/acf.php';
	}

	// Load Polylang plugin.
	if ( file_exists( WP_PLUGIN_DIR . '/polylang/polylang.php' ) ) {
		require_once WP_PLUGIN_DIR . '/polylang/polylang.php';
	}

	// Load our plugin.
	require dirname( __DIR__ ) . '/bea-acf-options-for-polylang.php';
}

tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';
