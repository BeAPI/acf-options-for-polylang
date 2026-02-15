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
