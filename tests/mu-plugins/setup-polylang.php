<?php
/**
 * Setup test environment for ACF Options for Polylang
 *
 * This mu-plugin:
 * - Creates French and English languages for Polylang
 * - Registers an ACF options page for testing
 *
 * @package BEA\ACF_Options_For_Polylang
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

		// Register options page.
		acf_add_options_page(
			[
				'page_title' => __( 'Theme General Settings', 'bea-acf-options-for-polylang' ),
				'menu_title' => __( 'Theme Settings', 'bea-acf-options-for-polylang' ),
				'menu_slug'  => 'theme-general-settings',
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
				],
				'location'              => [
					[
						[
							'param'    => 'options_page',
							'operator' => '==',
							'value'    => 'theme-general-settings',
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
 * Display option page values in the footer on the front (for testing).
 * Only runs when not in admin.
 */
add_action(
	'wp_footer',
	function () {
		if ( is_admin() || ! function_exists( 'get_field' ) ) {
			return;
		}

		$site_title       = get_field( 'site_title', 'option' );
		$site_description = get_field( 'site_description', 'option' );
		$contact_email    = get_field( 'contact_email', 'option' );
		$enable_feature   = get_field( 'enable_feature', 'option' );

		$site_title       = is_string( $site_title ) ? $site_title : '';
		$site_description = is_string( $site_description ) ? $site_description : '';
		$contact_email    = is_string( $contact_email ) ? $contact_email : '';
		$enable_feature   = (bool) $enable_feature;

		$option_label = __( 'Theme options (current language)', 'bea-acf-options-for-polylang' );
		printf(
			'<div class="theme-options-preview" style="margin:1em 0;padding:1em;border:1px solid #ccc;background:#f9f9f9;font-size:0.9em;">'
			. '<strong>%s</strong>'
			. '<dl style="margin:0.5em 0 0;display:grid;gap:0.25em;">'
			. '<dt style="font-weight:600;">Site Title</dt><dd>%s</dd>'
			. '<dt style="font-weight:600;">Site Description</dt><dd>%s</dd>'
			. '<dt style="font-weight:600;">Contact Email</dt><dd>%s</dd>'
			. '<dt style="font-weight:600;">Enable Feature</dt><dd>%s</dd>'
			. '</dl></div>',
			esc_html( $option_label ),
			esc_html( $site_title ),
			esc_html( $site_description ),
			esc_html( $contact_email ),
			$enable_feature ? esc_html__( 'Yes', 'bea-acf-options-for-polylang' ) : esc_html__( 'No', 'bea-acf-options-for-polylang' )
		);
	},
	10,
	0
);
