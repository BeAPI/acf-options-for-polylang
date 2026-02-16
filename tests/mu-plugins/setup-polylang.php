<?php
/**
 * Setup test environment for ACF Options for Polylang
 *
 * This mu-plugin:
 * - Creates French and English languages for Polylang
 * - Registers an ACF options page for testing (custom option key, not default 'options')
 *
 * @package BEA\ACF_Options_For_Polylang
 */

/**
 * Custom options page post_id (used so the plugin suffixes it per language, e.g. theme-general-settings_fr_FR).
 */
const BEA_AOFP_THEME_OPTIONS_POST_ID = 'theme-general-settings';

/*
add_filter( 'bea.aofp.lang_attribute', function( $attribute ) {
	return 'slug'; // or 'locale', 'name', etc.
} );
*/

/**
 * Setup Polylang languages.
 */
add_action(
	'init',
	function () {
		if ( ! function_exists( 'PLL' ) || ! function_exists( 'pll_languages_list' ) ) {
			return;
		}

		// Check if languages already exist.
		$existing_languages = pll_languages_list( [ 'fields' => 'slug' ] );

		// Get Polylang model.
		$polylang = PLL();

		// Create French language if it doesn't exist.
		if ( ! in_array( 'fr', $existing_languages, true ) ) {
			$polylang->model->add_language(
				[
					'name'       => 'Français',
					'slug'       => 'fr',
					'locale'     => 'fr_FR',
					'rtl'        => 0,
					'term_group' => 1,
					'flag'       => 'fr',
				]
			);
		}

		// Create English language if it doesn't exist.
		if ( ! in_array( 'en', $existing_languages, true ) ) {
			$polylang->model->add_language(
				[
					'name'       => 'English',
					'slug'       => 'en',
					'locale'     => 'en_US',
					'rtl'        => 0,
					'term_group' => 2,
					'flag'       => 'us',
				]
			);
		}

		// Set default language to French if not already set.
		$options = get_option( 'polylang' );
		if ( empty( $options['default_lang'] ) ) {
			$options['default_lang'] = 'fr';
			update_option( 'polylang', $options );
		}
	},
	1
);

/**
 * Register ACF options page and fields for testing.
 */
add_action(
	'acf/init',
	function () {
		// Check function exists.
		if ( ! function_exists( 'acf_add_options_page' ) ) {
			return;
		}

		// Register options page with custom post_id (not default 'options') so ACF Options for Polylang suffixes per language.
		acf_add_options_page(
			[
				'page_title' => __( 'Theme General Settings', 'bea-acf-options-for-polylang' ),
				'menu_title' => __( 'Theme Settings', 'bea-acf-options-for-polylang' ),
				'menu_slug'  => BEA_AOFP_THEME_OPTIONS_POST_ID,
				'post_id'    => BEA_AOFP_THEME_OPTIONS_POST_ID,
				'capability' => 'edit_posts',
				'redirect'   => false,
			]
		);

		// Check if we can register fields programmatically.
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		// Register ACF field group for the options page.
		acf_add_local_field_group(
			[
				'key'                   => 'group_theme_settings',
				'title'                 => 'Theme Settings',
				'fields'                => [
					[
						'key'          => 'field_site_title',
						'label'        => 'Site Title',
						'name'         => 'site_title',
						'type'         => 'text',
						'instructions' => 'Custom site title for testing',
						'required'     => 0,
					],
					[
						'key'          => 'field_site_description',
						'label'        => 'Site Description',
						'name'         => 'site_description',
						'type'         => 'textarea',
						'instructions' => 'Custom site description for testing',
						'required'     => 0,
						'rows'         => 4,
					],
					[
						'key'           => 'field_contact_email',
						'label'         => 'Contact Email',
						'name'          => 'contact_email',
						'type'          => 'email',
						'instructions'  => 'Contact email address',
						'required'      => 0,
						'default_value' => '',
					],
					[
						'key'           => 'field_enable_feature',
						'label'         => 'Enable Feature',
						'name'          => 'enable_feature',
						'type'          => 'true_false',
						'instructions'  => 'Enable or disable a feature',
						'required'      => 0,
						'default_value' => 0,
						'ui'            => 1,
					],
					[
						'key'        => 'field_links_repeater',
						'label'       => 'Links',
						'name'       => 'links',
						'type'       => 'repeater',
						'layout'     => 'table',
						'min'        => 0,
						'max'        => 5,
						'sub_fields' => [
							[
								'key'   => 'field_link_label',
								'label' => 'Label',
								'name'  => 'label',
								'type'  => 'text',
							],
							[
								'key'   => 'field_link_url',
								'label' => 'URL',
								'name'  => 'url',
								'type'  => 'url',
							],
							[
								'key'            => 'field_link_related_post',
								'label'          => 'Related post',
								'name'           => 'related_post',
								'type'           => 'relationship',
								'post_type'      => [ 'post', 'page' ],
								'return_format'  => 'object',
								'min'            => 0,
								'max'            => 1,
								'filters'        => [ 'search' ],
								'elements'       => [ 'featured_image' ],
							],
						],
					],
				],
				'location'              => [
					[
						[
							'param'    => 'options_page',
							'operator' => '==',
							'value'    => BEA_AOFP_THEME_OPTIONS_POST_ID,
						],
					],
				],
				'menu_order'            => 0,
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen'        => '',
			]
		);
	}
);

/**
 * Build the options preview block HTML for a given option context (post_id).
 *
 * @param string $post_id   ACF option page post_id (e.g. localized or unsuffixed).
 * @param string $title    Block title.
 * @return string HTML for the block.
 */
function bea_aofp_build_options_preview_block( $post_id, $title ) {
	$site_title       = get_field( 'site_title', $post_id );
	$site_description = get_field( 'site_description', $post_id );
	$contact_email    = get_field( 'contact_email', $post_id );
	$enable_feature   = get_field( 'enable_feature', $post_id );

	$site_title       = is_string( $site_title ) ? $site_title : '';
	$site_description = is_string( $site_description ) ? $site_description : '';
	$contact_email    = is_string( $contact_email ) ? $contact_email : '';
	$enable_feature   = (bool) $enable_feature;

	$links_html = '';
	if ( have_rows( 'links', $post_id ) ) :
		$links_html = '<ul style="margin:0;padding-left:1.2em;">';
		while ( have_rows( 'links', $post_id ) ) :
			the_row();
			$label        = get_sub_field( 'label' );
			$url          = get_sub_field( 'url' );
			$related_post = get_sub_field( 'related_post' );
			$label        = is_string( $label ) ? $label : '';
			$url          = is_string( $url ) ? $url : '';
			$related_html = '';
			if ( ! empty( $related_post ) ) {
				$post_obj = is_array( $related_post ) ? reset( $related_post ) : $related_post;
				if ( $post_obj instanceof \WP_Post ) {
					$related_html = sprintf(
						' <span style="color:#666;">(%s: <a href="%s">%s</a>)</span>',
						esc_html__( 'Related', 'bea-acf-options-for-polylang' ),
						esc_url( get_permalink( $post_obj ) ),
						esc_html( get_the_title( $post_obj ) )
					);
				}
			}
			if ( $label || $url || $related_html ) {
				$links_html .= '<li>';
				$links_html .= $url
					? sprintf( '<a href="%s">%s</a>', esc_url( $url ), esc_html( $label ?: $url ) )
					: esc_html( $label );
				$links_html .= $related_html;
				$links_html .= '</li>';
			}
		endwhile;
		$links_html .= '</ul>';
		$links_html  = '<dt style="font-weight:600;">Links</dt><dd>' . $links_html . '</dd>';
	else :
		$links_html = '<dt style="font-weight:600;">Links</dt><dd>' . esc_html__( 'None', 'bea-acf-options-for-polylang' ) . '</dd>';
	endif;

	return sprintf(
		'<div class="theme-options-preview" style="margin:1em 0;padding:1em;border:1px solid #ccc;background:#f9f9f9;font-size:0.9em;">'
		. '<strong>%s</strong>'
		. '<dl style="margin:0.5em 0 0;display:grid;gap:0.25em;">'
		. '<dt style="font-weight:600;">Site Title</dt><dd>%s</dd>'
		. '<dt style="font-weight:600;">Site Description</dt><dd>%s</dd>'
		. '<dt style="font-weight:600;">Contact Email</dt><dd>%s</dd>'
		. '<dt style="font-weight:600;">Enable Feature</dt><dd>%s</dd>'
		. '%s'
		. '</dl></div>',
		esc_html( $title ),
		esc_html( $site_title ),
		esc_html( $site_description ),
		esc_html( $contact_email ),
		$enable_feature ? esc_html__( 'Yes', 'bea-acf-options-for-polylang' ) : esc_html__( 'No', 'bea-acf-options-for-polylang' ),
		$links_html // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	);
}

/**
 * Display option page values in the footer on the front (for testing).
 * Block 1: localized values (current language).
 * Block 2: default / untranslated values (post_id without locale suffix), same as fallback when bea.aofp.get_default is used.
 * Only runs when not in admin.
 */
add_action(
	'wp_footer',
	function () {
		if ( is_admin() || ! function_exists( 'get_field' ) ) {
			return;
		}

		// Block 1: localized options (current language).
		echo bea_aofp_build_options_preview_block( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			BEA_AOFP_THEME_OPTIONS_POST_ID,
			__( 'Theme options (current language)', 'bea-acf-options-for-polylang' )
		);

		// Block 2: default / untranslated options via plugin context switch (no locale suffix).
		if ( function_exists( 'bea_aofp_switch_to_untranslated' ) && function_exists( 'bea_aofp_restore_current_lang' ) ) {
			bea_aofp_switch_to_untranslated();
			echo bea_aofp_build_options_preview_block( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				BEA_AOFP_THEME_OPTIONS_POST_ID,
				__( 'Theme options (default / untranslated)', 'bea-acf-options-for-polylang' )
			);
			bea_aofp_restore_current_lang();
		}

		// Block 3: localized options (current language) with default fallback disabled.
		add_filter( 'bea.aofp.get_default', '__return_false' );

		// Clear ACF store
		$store = acf_get_store( 'values' );
		$store->reset();

		echo bea_aofp_build_options_preview_block( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			BEA_AOFP_THEME_OPTIONS_POST_ID,
			__( 'Theme options (current language)', 'bea-acf-options-for-polylang' )
		);
	},
	10,
	0
);
