# Changelog

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
