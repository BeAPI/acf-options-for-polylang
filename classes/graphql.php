<?php

namespace BEA\ACF_Options_For_Polylang;

/**
 * WPGraphQL integration for ACF Options Pages with Polylang language support.
 *
 * Dynamically registers all ACF Options Page field groups as root query fields
 * in WPGraphQL, each with a `language` argument. Sets PLL()->curlang before
 * resolving so that acf-options-for-polylang returns the correct translated values.
 *
 * @since 2.1.0
 */
class Graphql {

	use Singleton;

	protected function init(): void {
		add_action( 'graphql_register_types', [ $this, 'register_graphql_fields' ] );
	}

	/**
	 * Register all ACF Options Page field groups in WPGraphQL.
	 */
	public function register_graphql_fields(): void {
		if ( ! function_exists( 'acf_get_field_groups' ) || ! function_exists( 'acf_get_fields' ) || ! function_exists( 'PLL' ) ) {
			return;
		}

		// Use the enum type from wp-graphql-polylang if available, otherwise fall back to String
		$language_type = $this->get_language_arg_type();

		$field_groups = acf_get_field_groups();

			$options_pages = $this->get_options_pages_map();

		foreach ( $field_groups as $group ) {
			$post_id = $this->get_options_page_post_id( $group, $options_pages );

			if ( ! $post_id ) {
				continue;
			}

			$acf_fields = acf_get_fields( $group['key'] );

			if ( empty( $acf_fields ) ) {
				continue;
			}

			$field_name = ! empty( $group['graphql_field_name'] )
				? $group['graphql_field_name']
				: lcfirst( $this->to_pascal_case( $group['title'] ) );

			// Skip if the generated name is empty or starts with a digit (invalid GraphQL name)
			if ( empty( $field_name ) || preg_match( '/^\d/', $field_name ) ) {
				continue;
			}

			$type_name = 'AofpOptions' . ucfirst( $field_name );

			// Build GraphQL fields and map from ACF
			$graphql_fields = [];
			$field_map      = [];

			foreach ( $acf_fields as $field ) {
				$graphql_type = $this->acf_to_graphql_type( $field );

				if ( ! $graphql_type ) {
					continue;
				}

				$camel_name                    = $this->to_camel_case( $field['name'] );
				$graphql_fields[ $camel_name ] = [
					'type'        => $graphql_type,
					'description' => $field['label'],
				];
				$field_map[ $camel_name ]      = $field['name'];
			}

			if ( empty( $graphql_fields ) ) {
				continue;
			}

			register_graphql_object_type( $type_name, [
				'description' => sprintf( 'ACF Options Page fields: %s', $group['title'] ),
				'fields'      => $graphql_fields,
			] );

			register_graphql_field( 'RootQuery', $field_name, [
				'type'        => $type_name,
				'description' => sprintf( 'Get translated fields from ACF Options Page group: %s', $group['title'] ),
				'args'        => [
					'language' => [
						'type'        => $language_type,
						'description' => 'Polylang language code (e.g. ES, EN)',
					],
				],
				'resolve'     => function ( $_root, $args ) use ( $field_map, $post_id ) {
					$lang_switched = false;
					$previous_lang = null;

					if ( ! empty( $args['language'] ) && function_exists( 'PLL' ) ) {
						$lang     = strtolower( $args['language'] );
						$language = PLL()->model->get_language( $lang );

						if ( $language ) {
							$previous_lang = PLL()->curlang;
							PLL()->curlang = $language;
							$lang_switched = true;
						}
					}

					try {
						$result = [];
						foreach ( $field_map as $camel_name => $acf_name ) {
							$result[ $camel_name ] = get_field( $acf_name, $post_id ) ?? null;
						}

						return $result;
					} finally {
						if ( $lang_switched ) {
							PLL()->curlang = $previous_lang;
						}
					}
				},
			] );
		}
	}

	/**
	 * Build a map of ACF Options Page slugs to their post_id values.
	 *
	 * @return array<string, string> slug => post_id
	 */
	private function get_options_pages_map(): array {
		if ( ! function_exists( 'acf_get_options_pages' ) ) {
			return [];
		}

		$pages = acf_get_options_pages();
		$map   = [];

		if ( is_array( $pages ) ) {
			foreach ( $pages as $page ) {
				$slug            = $page['menu_slug'] ?? '';
				$map[ $slug ]    = $page['post_id'] ?? 'options';
			}
		}

		return $map;
	}

	/**
	 * Get the post_id for the options page a field group is assigned to.
	 *
	 * @param array              $group         ACF field group.
	 * @param array<string, string> $options_pages Map of slug => post_id.
	 *
	 * @return string|false The post_id or false if not an options page group.
	 */
	private function get_options_page_post_id( array $group, array $options_pages ) {
		if ( empty( $group['location'] ) ) {
			return false;
		}

		foreach ( $group['location'] as $rules ) {
			foreach ( $rules as $rule ) {
				if ( isset( $rule['param'] ) && $rule['param'] === 'options_page' && $rule['operator'] === '==' ) {
					$slug = $rule['value'] ?? '';

					return $options_pages[ $slug ] ?? 'options';
				}
			}
		}

		return false;
	}

	/**
	 * Convert snake_case to camelCase.
	 */
	private function to_camel_case( string $string ): string {
		return lcfirst( str_replace( '_', '', ucwords( $string, '_' ) ) );
	}

	/**
	 * Convert a string to PascalCase.
	 */
	private function to_pascal_case( string $string ): string {
		$cleaned = preg_replace( '/[^a-zA-Z0-9]+/', ' ', $string );

		return str_replace( ' ', '', ucwords( $cleaned ) );
	}

	/**
	 * Map ACF field config to GraphQL scalar types.
	 *
	 * Returns null for complex types (image, gallery, repeater, etc.)
	 * and multi-value fields (multi-select, checkbox) that cannot be
	 * serialized as scalars.
	 *
	 * @param array $field ACF field configuration.
	 *
	 * @return string|null GraphQL type or null if unsupported.
	 */
	private function acf_to_graphql_type( array $field ) {
		$type = $field['type'] ?? '';

		switch ( $type ) {
			case 'text':
			case 'textarea':
			case 'email':
			case 'url':
			case 'password':
			case 'wysiwyg':
			case 'oembed':
			case 'radio':
			case 'button_group':
			case 'color_picker':
			case 'date_picker':
			case 'date_time_picker':
			case 'time_picker':
				return 'String';
			case 'select':
				// Multi-select returns an array, which can't be serialized as String
				return empty( $field['multiple'] ) ? 'String' : null;
			case 'number':
			case 'range':
				return 'Float';
			case 'true_false':
				return 'Boolean';
			case 'checkbox':
				// Checkbox always returns an array
				return null;
			default:
				// Complex types (image, file, gallery, repeater, group,
				// relationship, post_object, taxonomy, user, google_map,
				// flexible_content, clone, link) are not supported.
				return null;
		}
	}

	/**
	 * Get the GraphQL type for the language argument.
	 *
	 * Uses String instead of LanguageCodeFilterEnum to avoid a hard dependency
	 * on wp-graphql-polylang. Accepts the same language codes (e.g. "ES", "EN").
	 */
	private function get_language_arg_type(): string {
		return 'String';
	}
}
