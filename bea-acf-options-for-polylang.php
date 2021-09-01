<?php
/*
Plugin Name: ACF Options for Polylang
Version: 1.1.10
Plugin URI: https://github.com/BeAPI/acf-options-for-polylang
Description: Add ACF options page support for Polylang.
Author: Be API
Author URI: https://beapi.fr
Contributors: Maxime Culea, Amaury BALMER
----

Copyright 2018-2019 Be API Technical team (human@beapi.fr)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// Plugin constants
define( 'BEA_ACF_OPTIONS_FOR_POLYLANG_VERSION', '1.1.10' );
define( 'BEA_ACF_OPTIONS_FOR_POLYLANG_MIN_PHP_VERSION', '5.6' );

// Plugin URL and PATH
define( 'BEA_ACF_OPTIONS_FOR_POLYLANG_URL', plugin_dir_url( __FILE__ ) );
define( 'BEA_ACF_OPTIONS_FOR_POLYLANG_DIR', plugin_dir_path( __FILE__ ) );
define( 'BEA_ACF_OPTIONS_MAIN_FILE_DIR', __FILE__ );
define( 'BEA_ACF_OPTIONS_FOR_POLYLANG_PLUGIN_DIRNAME', basename( rtrim( dirname( __FILE__ ), '/' ) ) );

// Check PHP min version
if ( version_compare( PHP_VERSION, BEA_ACF_OPTIONS_FOR_POLYLANG_MIN_PHP_VERSION, '<' ) ) {
	require_once BEA_ACF_OPTIONS_FOR_POLYLANG_DIR . 'compat.php';
	// possibly display a notice, trigger error
	add_action( 'admin_init', array( 'BEA\PB\Compatibility', 'admin_init' ) );

	// stop execution of this file
	return;
}

/** Autoload all the things \o/ */
require_once BEA_ACF_OPTIONS_FOR_POLYLANG_DIR . 'autoload.php';

add_action( 'init', 'bea_acf_options_for_polylang_load', 100 );
function bea_acf_options_for_polylang_load() {
	load_plugin_textdomain(
		'bea-acf-options-for-polylang',
		false,
		BEA_ACF_OPTIONS_FOR_POLYLANG_PLUGIN_DIRNAME . '/languages'
	);

	$requirements = \BEA\ACF_Options_For_Polylang\Requirements::get_instance();
	if ( ! $requirements->check_requirements() ) {
		return;
	}

	\BEA\ACF_Options_For_Polylang\Main::get_instance();

	if ( is_admin() ) {
		\BEA\ACF_Options_For_Polylang\Admin::get_instance();
	}
}
