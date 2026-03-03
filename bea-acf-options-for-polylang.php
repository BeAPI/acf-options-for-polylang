<?php
/*
Plugin Name: ACF Options for Polylang
Plugin URI: https://github.com/BeAPI/acf-options-for-polylang
Description: Improves Polylang by adding per-language support for ACF options pages—each language can have its own option values.
Version: 2.0.0
Requires at least: 6.0
Requires PHP: 7.4
Tested up to: 6.9
Author: Be API
Author URI: https://beapi.fr
Contributors: Maxime Culea, Amaury BALMER
License: GPL-3.0+
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: bea-acf-options-for-polylang
Domain Path: /languages
Network: false
----

Copyright 2018-2025 Be API Technical team (human@beapi.fr)

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
define( 'BEA_ACF_OPTIONS_FOR_POLYLANG_VERSION', '2.0.0' );

/**
 * Polylang language attribute used for option key suffix (e.g. 'locale' → fr_FR, 'slug' → fr).
 * Define before loading the plugin (e.g. in wp-config.php) to override; otherwise use filter.
 *
 * @since 2.0.0
 */
if ( ! defined( 'BEA_ACF_OPTIONS_FOR_POLYLANG_LANG_ATTRIBUTE' ) ) {
	define( 'BEA_ACF_OPTIONS_FOR_POLYLANG_LANG_ATTRIBUTE', 'locale' );
}

// Plugin URL and PATH
define( 'BEA_ACF_OPTIONS_FOR_POLYLANG_URL', plugin_dir_url( __FILE__ ) );
define( 'BEA_ACF_OPTIONS_FOR_POLYLANG_DIR', plugin_dir_path( __FILE__ ) );
define( 'BEA_ACF_OPTIONS_MAIN_FILE_DIR', __FILE__ );
define( 'BEA_ACF_OPTIONS_FOR_POLYLANG_PLUGIN_DIRNAME', basename( rtrim( __DIR__, '/' ) ) );

/** Autoload all the things \o/ */
require_once BEA_ACF_OPTIONS_FOR_POLYLANG_DIR . 'autoload.php';

/** Helper functions (public API) */
require_once BEA_ACF_OPTIONS_FOR_POLYLANG_DIR . 'functions.php';

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
