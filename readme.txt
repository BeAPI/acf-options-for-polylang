=== ACF Options For Polylang ===
Contributors: momo360modena, BeAPI, maximeculea, NicolasKulka
Author URI: https://beapi.fr
Plugin URL: https://github.com/BeAPI/acf-options-for-polylang
Requires at Least: 6.0
Tested up to: 6.9
Tags: acf, polylang, option, options, options page, advanced custom fields
Stable tag: 2.0.0
Requires PHP: 7.4

Add ACF options page support for Polylang.

== Description ==

Are you using Advanced Custom Fields to create option pages and have Polylang installed for your awesome multilingual website?

Unfortunately, Polylang does not natively support ACF Option Pages. This results in the same values being used across all your site's languages.

This plugin solves that problem! Once activated, you'll be able to set different values for each language. If a value isn't set for a specific language, the "All languages" value will be used by default.

= How does it work? =

This plugin saves separate values for each language in the database. Polylang's language settings are then used to fetch the appropriate value from the database.

**Note:** When you activate the plugin, your existing option values will be temporarily unavailable but remain in the database. You can recover them by deactivating the plugin.

To set or update your option pages in a specific language, simply use the Polylang language flags in the WordPress admin bar (the integrated language switcher) to select your desired language before editing the options.

= Requirements =

* [WordPress](https://wordpress.org/) 6.0+ / Tested from 6.0 to latest
* PHP 7.4 to 8.4 / Tested from 7.4 to 8.4
* [Advanced Custom Fields](https://www.advancedcustomfields.com/pro) 5.6.0+
* [Polylang](https://polylang.pro/) (tested up to 3.7.7)

= Using fields =

Nothing changes, we have made all the hooks for you, so no need to prefix your fields with a lang or something else. Only use ACF's helpers to get and show the fields as you did before with get_field() or the_field():

`get_field( 'footer_disclaimer', 'options' );`

= Excluding Option Pages from Localization =

If you want to prevent this plugin from applying its functionality to a specific ACF options page, you can exclude it by adding its post_id to the exclusion filter.

Add the following code to your theme or custom plugin (replace 'custom_options_page_post_id' with your actual options page post ID):

[code]
add_filter( 'bea.aofp.excluded_post_ids', function( $ids ) {
    $ids[] = 'custom_options_page_post_id'; // Exclude this options page from localization
    return $ids;
}, 10, 1 );
[/code]

This will ensure that the specified options page is not affected by language-specific behavior and will always load/save its values without localization.

= Default Values ("All languages" Fallback) =

By default, this plugin will use the Polylang "All languages" value when there is no value set for the current language. If you prefer not to use this fallback behavior, you can easily disable it using a filter.

* Disable fallback for all ACF Options pages: add `add_filter( 'bea.aofp.get_default', '__return_false' );`
* Disable fallback for a specific ACF Options page: use the filter with the $post_id parameter to target a single options page.

= Loading untranslated (default) option values =

When you need to read the default / untranslated values (the ones stored without a language suffix, used as fallback when no translation exists), use the context switch API. This applies to all fields, including repeater sub-fields and relationship fields.

* **bea_aofp_switch_to_untranslated()** — subsequent get_field( ..., option_page_id ) and have_rows( ..., option_page_id ) will load values from the unsuffixed key (default values).
* **bea_aofp_restore_current_lang()** — restores the previous context; option values are again loaded for the current language.

Calls can be nested: each restore_current_lang() undoes the last switch_to_untranslated().

= Who? =

Created by [Be API](https://beapi.fr), the French WordPress leader agency since 2009. Based in Paris, we are more than 30 people and always [hiring](https://beapi.workable.com) some fun and talented guys. So we will be pleased to work with you.

This plugin is only maintained, which means we do not guarantee some free support. Consider reporting an [issue](https://github.com/BeAPI/acf-options-for-polylang/issues) and be patient.

If you really like what we do or want to thank us for our quick work, feel free to [donate](https://www.paypal.me/BeAPI) as much as you want / can, even 1€ is a great gift for buying coffee :)

BEA - ACF Options for Polylang is licensed under the GPLv2 or later.

== Installation ==

First activate and configure Polylang on your site. Then activate ACF Options For Polylang to handle ACF Options in Polylang's configured languages.

= WordPress =

* Download and install using the built-in WordPress plugin installer.
* Site activate in the "Plugins" area of the admin.
* Optionally drop the entire `acf-options-for-polylang` directory into mu-plugins.
* Nothing more, this plugin is ready to use!

= Composer =

* `composer require wpackagist-plugin/acf-options-for-polylang`
* Nothing more, this plugin is ready to use!

== Changelog ==

= 2.0.0 - In Development =

**⚠️ Breaking Changes**
* Minimum PHP version raised from 5.6 to 7.4
* Minimum WordPress version raised from 4.7 to 6.0

**New Features**
* Add comprehensive unit testing suite with PHPUnit (41 test methods)
* Add wp-env configuration for Docker-based testing environment
* Add GitHub Actions CI/CD workflows for automated testing
* Test coverage across PHP 7.4-8.4 and WordPress 6.0-6.9
* Add TESTING.md documentation for developers

**Improvements**
* Update all development dependencies to latest versions
* WordPress Coding Standards 3.1 (WP 6.0+ support)
* PHPUnit 9.6/10.0/11.0 multi-version support
* Enforce modern short array syntax []
* Update plugin header with all standard WordPress fields
* Update copyright to 2025

**Code Quality**
* 100% compliance with WordPress Coding Standards
* Automated testing on 66 PHP/WordPress combinations
* Code coverage reporting with Codecov
* Automated code quality checks

= 1.1.12 - 26 March 2025 =
- FIX: Resolved an issue where the plugin would sometimes deactivate randomly on multisite installations when visiting a site.

= 1.1.11 - 27 July 2023
- Tested up on WP 6.2

= 1.1.10 - 1 Sept 2021
- FIX: WordPress.org version generation

= 1.1.9 - 1 Sept 2021
- FIX: ACF 5.6.0 version check
- FEATURE: Add new filter bea.aofp.excluded_post_ids to skip page ids


= 1.1.8 - 27 March 2021
- FIX : Rest API returns now the right value
- FIX : Ajax requests where not localized
- FIX : Compatibility with new versions of ACF

= 1.1.7 - 07 May 2019 =
- Feature: Add a context-sensitive help to the user on ACF options page (tired of updating the generic options ...)
- Improve: object detection from ACF with get_field()
- Feature: Add translation POT and french translation
- FEATURE [#31](https://github.com/BeAPI/acf-options-for-polylang/issues/31): Brand for wp.org
- Test: Test up on WP 5.2
- FIX [#41](https://github.com/BeAPI/acf-options-for-polylang/issues/41): fix bug with all language failback and repeater

= 1.1.6 - 19 Mar 2019 =
- FIX [#32](https://github.com/BeAPI/acf-options-for-polylang/issues/32) & [#40](https://github.com/BeAPI/acf-options-for-polylang/issues/40) : fix `get_field()` if an object is provided (WP Term, WP Post, WP Comment)

= 1.1.5 - 11 Dec 2018 =
- FIX wrong constant

= 1.1.4 - 13 Nov 2018 =
- Refactor by adding the Helpers class
- FEATURE [#26](https://github.com/BeAPI/acf-options-for-polylang/issues/26) : allow to precise to show or hide default values for a specific option page
- FEATURE [#21](https://github.com/BeAPI/acf-options-for-polylang/pull/21) : handle custom option id

= 1.1.3 - 2 Aug 2018 =
- FEATURE [#23](https://github.com/BeAPI/acf-options-for-polylang/pull/23) : requirement to php5.6 whereas namespace are 5.3

= 1.1.2 - 31 Jul 2018 =
- FIX [#22](https://github.com/BeAPI/acf-options-for-polylang/pull/22) : error with repeater fields default values

= 1.1.1 - 9 Mai 2018 =
- FIX [#15](https://github.com/BeAPI/acf-options-for-polylang/issues/15) : way requirements are checked to trigger on front / admin

= 1.1.0 - Mar 2018 =
- True (complet) plugin.
- Add check for ACF 5.6.

= 1.0.2 - 23 Dec 2017 =
- Refactor and reformat.
- Handle all options page and custom post_id.
- Now load only if ACF & Polylang are activated.
- Load later at plugins loaded.

= 1.0.1 - 19 Sep 2016 =
- Plugin update.

= 1.0.0 - 8 Mar 2016 =
- Init plugin.
