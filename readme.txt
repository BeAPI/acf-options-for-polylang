=== ACF Options For Polylang ===
Contributors: momo360modena, BeAPI, maximeculea, NicolasKulka
Author URI: https://beapi.fr
Plugin URL: https://github.com/BeAPI/acf-options-for-polylang
Donate link: https://www.paypal.me/BeAPI
Requires at Least: 6.0
Tested up to: 6.9
Tags: acf, polylang, option, options, options page
Stable tag: 2.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Improves Polylang by adding per-language support for ACF options pages—each language can have its own option values.

== Description ==

Are you using Advanced Custom Fields for option pages and Polylang for your multilingual site?

Polylang does not natively support ACF Option Pages, so option values are shared across all languages. This plugin improves that: once activated, you can set different values per language. Values are stored per language in the database and the correct one is loaded according to the current Polylang language. If a value isn't set for a language, the "All languages" value is used by default.

**Note:** On activation, existing option values become temporarily unavailable (but remain in the database); you can recover them by deactivating the plugin. To edit options for a language, use the Polylang language switcher in the admin bar before opening the options page.

Requirements: WordPress 6.0+, PHP 7.4–8.4, [Advanced Custom Fields](https://www.advancedcustomfields.com/pro) 5.6.0+, [Polylang](https://polylang.pro/) (tested up to 3.7.7).

For full documentation (usage, filters, API, excluding pages, default fallback, loading untranslated values), see the [project README on GitHub](https://github.com/BeAPI/acf-options-for-polylang#readme).

== Installation ==

Activate and configure Polylang, then activate ACF Options For Polylang.

= WordPress =

* Install via the built-in plugin installer, then activate in the "Plugins" screen.
* Or place the `acf-options-for-polylang` folder in `wp-content/mu-plugins`.

= Composer =

* `composer require wpackagist-plugin/acf-options-for-polylang`

== Frequently Asked Questions ==

= Do I need to change my theme code to use this plugin? =

No. Use ACF's get_field() and the_field() as before with the options page ID (e.g. 'options'). The plugin handles language automatically.

= What happens to my existing option values when I activate the plugin? =

They become temporarily unavailable but remain in the database. You can recover them by deactivating the plugin. After activation, set or copy your values per language using the Polylang language switcher in the admin bar.

= Where is the full documentation (filters, API, excluding pages)? =

See the [README on GitHub](https://github.com/BeAPI/acf-options-for-polylang#readme).

== Screenshots ==

1. Use the Polylang language switcher in the admin bar to choose the language before editing ACF options.

== Upgrade Notice ==

= 2.0.0 =
Breaking: requires PHP 7.4+ and WordPress 6.0+. See Changelog for full details.

== Changelog ==

= 2.0.0 - 16 February 2025 =

**Breaking Changes**
* Minimum PHP version raised from 5.6 to 7.4
* Minimum WordPress version raised from 4.7 to 6.0

**New Features**
* Configurable Polylang language attribute for option key suffix: constant BEA_ACF_OPTIONS_FOR_POLYLANG_LANG_ATTRIBUTE, helper Helpers::get_lang_attribute(), and filter bea.aofp.lang_attribute allow using slug (or other Polylang fields) instead of default locale
* Added comprehensive unit testing suite with PHPUnit (41 test methods)
* Added wp-env configuration for Docker-based testing environment
* Added GitHub Actions CI/CD workflows for automated testing
* Test coverage across PHP 7.4-8.4 and WordPress 6.0-6.9
* Added TESTING.md documentation for developers

**Improvements**
* Updated all development dependencies to latest versions
* WordPress Coding Standards 3.1 (WP 6.0+ support)
* PHPUnit 9.6/10.0/11.0 multi-version support
* Enforced modern short array syntax []
* Updated plugin header with all standard WordPress fields
* Updated copyright to 2025
* Removed custom PHP version check and compat.php; PHP requirement is now enforced by WordPress Requires PHP header

**Documentation**
* Documented language attribute override (constant and filter) in README

**Code Quality**
* 100% compliance with WordPress Coding Standards
* Automated testing on 66 PHP/WordPress combinations
* Code coverage reporting with Codecov
* Automated code quality checks

= 1.1.12 – 26 March 2025 =
– FIX: Resolved an issue where the plugin would sometimes deactivate randomly on multisite installations when visiting a site.

= 1.1.11 – 27 July 2023
– Tested up on WP 6.2

= 1.1.10 – 1 Sept 2021
– FIX: WordPress.org version generation

= 1.1.9 – 1 Sept 2021
– FIX: ACF 5.6.0 version check
– FEATURE: Add new filter bea.aofp.excluded_post_ids to skip page ids


= 1.1.8 – 27 March 2021
– FIX : Rest API returns now the right value
– FIX : Ajax requests where not localized
– FIX : Compatibility with new versions of ACF

= 1.1.7 – 07 May 2019 =
– Feature: Add a context-sensitive help to the user on ACF options page (tired of updating the generic options …)
– Improve: object detection from ACF with get_field()
– Feature: Add translation POT and french translation
– FEATURE [#31](https://github.com/BeAPI/acf-options-for-polylang/issues/31): Brand for wp.org
– Test: Test up on WP 5.2
– FIX [#41](https://github.com/BeAPI/acf-options-for-polylang/issues/41): fix bug with all language failback and repeater

= 1.1.6 – 19 Mar 2019 =
– FIX [#32](https://github.com/BeAPI/acf-options-for-polylang/issues/32) & [#40](https://github.com/BeAPI/acf-options-for-polylang/issues/40) : fix `get_field()` if an object is provided (WP Term, WP Post, WP Comment)

= 1.1.5 – 11 Dec 2018 =
– FIX wrong constant

= 1.1.4 – 13 Nov 2018 =
– Refactor by adding the Helpers class
– FEATURE [#26](https://github.com/BeAPI/acf-options-for-polylang/issues/26) : allow to precise to show or hide default values for a specific option page
– FEATURE [#21](https://github.com/BeAPI/acf-options-for-polylang/pull/21) : handle custom option id

= 1.1.3 – 2 Aug 2018 =
– FEATURE [#23](https://github.com/BeAPI/acf-options-for-polylang/pull/23) : requirement to php5.6 whereas namespace are 5.3

= 1.1.2 – 31 Jul 2018 =
– FIX [#22](https://github.com/BeAPI/acf-options-for-polylang/pull/22) : error with repeater fields default values

= 1.1.1 – 9 Mai 2018 =
– FIX [#15](https://github.com/BeAPI/acf-options-for-polylang/issues/15) : way requirements are checked to trigger on front / admin

= 1.1.0 – Mar 2018 =
– True (complet) plugin.
– Add check for ACF 5.6.

= 1.0.2 – 23 Dec 2017 =
– Refactor and reformat.
– Handle all options page and custom post_id.
– Now load only if ACF & Polylang are activated.
– Load later at plugins loaded.

= 1.0.1 – 19 Sep 2016 =
– Plugin update.

= 1.0.0 – 8 Mar 2016 =
– Init plugin.
