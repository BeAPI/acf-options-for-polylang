# Changelog

## 2.0.0 - In Progress (Development)

### 🚨 Breaking Changes
* **BREAKING**: Minimum PHP version raised from 5.6 to 7.4
* **BREAKING**: Minimum WordPress version raised from 4.7 to 6.0
* **BREAKING**: Updated plugin header with modern WordPress standards

### ✨ New Features
* Add comprehensive unit testing suite with PHPUnit
* Add wp-env configuration for Docker-based testing environment
* Add GitHub Actions workflow for automated testing (66 PHP/WP combinations)
* Add GitHub Actions workflow for code quality checks across all PHP versions
* Add TESTING.md documentation for running and writing tests
* Add traditional test setup script (bin/install-wp-tests.sh)
* Add 41 unit test methods covering all main classes:
  - 15 tests for Main class
  - 13 tests for Helpers class
  - 7 tests for Requirements class
  - 6 tests for Admin class

### 🔧 Improvements
* Update all require-dev dependencies to latest versions:
  - PHPUnit: ^9.6 || ^10.0 || ^11.0 (multi-version support)
  - WordPress Coding Standards: ^3.1 (with WP 6.0+ support)
  - PHP_CodeSniffer: ^3.10
  - PHPUnit Polyfills: ^2.0 || ^3.0
  - GrumPHP: ^2.7
  - PHPLint: ^9.0
  - Mockery: ^1.6
* Enforce short array syntax [] in phpcs.xml configuration
* Fix phpcs.xml text_domain property deprecation warning
* Update .gitignore with all test-related files and artifacts
* Update .distignore to exclude all development files from production zip
* Update plugin header with all standard WordPress fields:
  - Requires at least, Requires PHP, Tested up to
  - License, License URI, Text Domain, Domain Path, Network
* Update copyright year to 2025

### 📊 Testing & Quality
* Test coverage across PHP 7.4, 8.0, 8.1, 8.2, 8.3, 8.4
* Test coverage across WordPress 6.0, 6.1, 6.2, 6.3, 6.4, 6.5, 6.6, 6.7, 6.8, 6.9, latest
* Automated code quality checks on all PHP versions
* Code coverage reporting with Codecov integration
* All code complies with WordPress Coding Standards

### 🐛 Bug Fixes
* Fix coding standards violations (use __DIR__ instead of dirname(__FILE__))
* Convert array() syntax to short [] syntax throughout codebase

### 📚 Documentation
* Add comprehensive TESTING.md with setup and usage instructions
* Add test coverage documentation
* Add contribution guidelines for tests
* Update README.md with testing quick start section

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
