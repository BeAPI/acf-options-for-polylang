<?php

namespace BEA\ACF_Options_For_Polylang;

class Admin {
	/**
	 * Use the trait
	 */
	use Singleton;

	/**
	 * Register hooks
	 */
	protected function init() {
		add_filter(
			'acf/options_page/submitbox_before_major_actions',
			[
				$this,
				'submitbox_before_major_actions',
			],
			10
		);
	}

	/**
	 * Displays context-sensitive help to the user
	 *
	 * @param array $page ACF options page data (e.g. post_id, menu_slug).
	 */
	public function submitbox_before_major_actions( $page ) {
		// If Polylang isn't active for this page, don't display the message
		if ( ! is_array( $page ) || ! isset( $page['post_id'] ) || ! Helpers::is_option_page( $page['post_id'] ) ) {
			return;
		}

		$current_lang = function_exists( 'pll_current_language' ) ? \pll_current_language( 'name' ) : false;
		if ( false !== $current_lang ) {
			/* translators: %s: current language name */
			$output = sprintf( __( 'You are changing the options for language %s', 'bea-acf-options-for-polylang' ), $current_lang );
		} else {
			$output = __( '<strong>Be careful</strong>, you are going to modify the untranslated options, they will probably be used as defaults if the translation is not completed.', 'bea-acf-options-for-polylang' );
		}

		$switcher_hint = __( 'Use the language switcher in the admin bar to change the language.', 'bea-acf-options-for-polylang' );
		$output       = $output . ' <br /><br />' . $switcher_hint;

		echo '<p class="misc-pub-section">' . $output . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
