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
	 */
	public function submitbox_before_major_actions() {
		$current_lang = pll_current_language( 'name' );
		if ( false !== $current_lang ) {
			/* translators: %s: current language name */
			$output = sprintf( __( 'You are changing the options for language %s', 'bea-acf-options-for-polylang' ), $current_lang );
		} else {
			$output = __( '<strong>Be careful</strong>, you are going to modify the untranslated options, they will probably be used as defaults if the translation is not completed.', 'bea-acf-options-for-polylang' );
		}

		echo '<p class="misc-pub-section">' . $output . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

}
