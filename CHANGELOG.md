# Changelog

## 2.0.0 - Unreleased

### 🚨 Breaking Changes
* **BREAKING**: Minimum PHP version raised from 5.6 to 7.4
* **BREAKING**: Minimum WordPress version raised from 4.7 to 6.0
* **BREAKING**: Updated plugin header with modern WordPress standards

### ✨ New Features
* Added configurable Polylang language attribute for option key suffix: constant `BEA_ACF_OPTIONS_FOR_POLYLANG_LANG_ATTRIBUTE`, helper `Helpers::get_lang_attribute()`, and filter `bea.aofp.lang_attribute` allow using `slug` (or other Polylang fields) instead of default `locale`
* Added comprehensive unit testing suite with PHPUnit
* Added wp-env configuration for Docker-based testing environment
* Added GitHub Actions workflow for automated testing (66 PHP/WP combinations)
* Added GitHub Actions workflow for code quality checks across all PHP versions
* Added TESTING.md documentation for running and writing tests

### 🔧 Improvements
* Updated all require-dev dependencies to latest versions:
  - PHPUnit: ^9.6 || ^10.0 || ^11.0 (multi-version support)
  - WordPress Coding Standards: ^3.1 (with WP 6.0+ support)
  - PHP_CodeSniffer: ^3.10
  - PHPUnit Polyfills: ^2.0 || ^3.0
  - GrumPHP: ^2.7
  - PHPLint: ^9.0
  - Mockery: ^1.6
* Enforced short array syntax [] in phpcs.xml configuration
* Fixed phpcs.xml text_domain property deprecation warning
* Updated .gitignore with all test-related files and artifacts
* Updated .distignore to exclude all development files from production zip
* Updated plugin header with all standard WordPress fields:
  - Requires at least, Requires PHP, Tested up to
  - License, License URI, Text Domain, Domain Path, Network
* Updated copyright year to 2025
* Removed custom PHP version check and compat.php; PHP requirement is now enforced by WordPress Requires PHP header (WP 5.2+)

### 📊 Testing & Quality
* 61 unit tests, 92 assertions, 0 skips covering all main classes:
  - 25 tests for Main class (filters, language switching, default values, untranslated context, switch/restore)
  - 19 tests for Helpers class (option IDs, localization detection, lang attribute, regex fragment)
  - 8 tests for Admin class (submitbox output, hooks, language display)
  - 6 tests for Requirements class (dependency checks, error display)
  - 3 tests for public API functions (switch/restore integration)
* Fixed Polylang initialization in test environment by defining `PLL_ADMIN` in bootstrap, ensuring all Polylang API functions are available
* Added `set_polylang_language()` test helper for proper runtime language switching via `PLL()->curlang` and `switch_to_locale()`
* Test coverage across PHP 7.4, 8.0, 8.1, 8.2, 8.3, 8.4
* Test coverage across WordPress 6.0, 6.1, 6.2, 6.3, 6.4, 6.5, 6.6, 6.7, 6.8, 6.9, latest
* Automated code quality checks on all PHP versions
* Code coverage reporting with Codecov integration
* All code complies with WordPress Coding Standards

### 🐛 Bug Fixes
* Fixed coding standards violations (use __DIR__ instead of dirname(__FILE__))
* Converted array() syntax to short [] syntax throughout codebase

### 📚 Documentation
* Documented language attribute override (constant and filter) in README.md
* Added comprehensive TESTING.md with setup and usage instructions
* Added test coverage documentation
* Added contribution guidelines for tests
* Updated README.md with testing quick start section

## 1.1.12 - 26 March 2025
* FIX: Resolved an issue where the plugin would sometimes deactivate randomly on multisite installations when visiting a site.

## 1.1.11 - 27 July 2023
* Feature: [#85](https://github.com/BeAPI/acf-options-for-polylang/pull/85) Add support for `composer/installer:2.0`
* Tested up on WP 6.2

## 1.1.10 - 1 Sept 2021
* FIX: WordPress.org version generation

## 1.1.9 - 1 Sept 2021
* FIX: ACF 5.6.0 version check
* FEATURE: Add new filter bea.aofp.excluded_post_ids to skip page ids

## 1.1.8 - 27 March 2021
* FIX [#27](https://github.com/BeAPI/acf-options-for-polylang/issues/27): Rest API returns now the right value
* FIX [#61](https://github.com/BeAPI/acf-options-for-polylang/issues/61): Ajax requests where not localized
* FIX [#64](https://github.com/BeAPI/acf-options-for-polylang/pull/64) : Compatibility with new versions of ACF

## 1.1.7 - 07 May 2019
* Feature: Add a context-sensitive help to the user on ACF options page (tired of updating the generic options ...)
* Feature: Improve object detection from ACF with get_field()
* Feature: Add translation POT and french translation
* FIX [#41](https://github.com/BeAPI/acf-options-for-polylang/issues/41): fix bug with all language failback and repeater
* Test: Test up on WP 5.2
* FEATURE [#31](https://github.com/BeAPI/acf-options-for-polylang/issues/31): Brand for wp.org

## 1.1.6 - 19 Mar 2019
* FIX [#32](https://github.com/BeAPI/acf-options-for-polylang/issues/32) & [#40](https://github.com/BeAPI/acf-options-for-polylang/issues/40) : fix `get_field()` if an object is provided (WP Term, WP Post, WP Comment)

## 1.1.5 - 11 Dec 2018
* FIX wrong constant

## 1.1.4 - 13 Nov 2018
* Refactor by adding the Helpers class
* FEATURE [#26](https://github.com/BeAPI/acf-options-for-polylang/issues/26) : allow to precise to show or hide default values for a specific option page
* FEATURE [#21](https://github.com/BeAPI/acf-options-for-polylang/pull/21) : handle custom option id

## 1.1.3 - 2 Aug 2018
* FEATURE [#23](https://github.com/BeAPI/acf-options-for-polylang/pull/23) : requirement to php5.6 whereas namespace are 5.3

## 1.1.2 - 31 Jul 2018
* FIX [#22](https://github.com/BeAPI/acf-options-for-polylang/pull/22) : error with repeater fields default values

## 1.1.1 - 9 Mai 2018
* FIX [#15](https://github.com/BeAPI/acf-options-for-polylang/issues/15) : way requirements are checked to trigger on front / admin

## 1.1.0 - Mar 2018
* True (complet) plugin.
* Add check for ACF 5.6.

## 1.0.2 - 23 Dec 2017
* Refactor and reformat.
* Handle all options page and custom post_id.
* Now load only if ACF & Polylang are activated.
* Load later at plugins loaded.

## 1.0.1 - 19 Sep 2016
* Plugin update.

## 1.0.0 - 8 Mar 2016
* Init plugin.
