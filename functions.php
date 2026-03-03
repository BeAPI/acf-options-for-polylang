<?php
/**
 * Helper functions for ACF Options for Polylang.
 *
 * @package BEA\ACF_Options_For_Polylang
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Switch context to untranslated (default) option values.
 * Subsequent get_field( ..., option_page_id ) will load values without locale suffix.
 * Must be paired with bea_aofp_restore_current_lang().
 *
 * @since 2.0.0
 */
function bea_aofp_switch_to_untranslated() {
	\BEA\ACF_Options_For_Polylang\Main::switch_to_untranslated();
}

/**
 * Restore context after bea_aofp_switch_to_untranslated().
 * Option values will again be loaded for the current language.
 *
 * @since 2.0.0
 */
function bea_aofp_restore_current_lang() {
	\BEA\ACF_Options_For_Polylang\Main::restore_current_lang();
}
