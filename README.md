<a href="https://beapi.fr">![Be API Github Banner](.wordpress-org/banner-github.png)</a>

# BEA - ACF Options For Polylang

[![CodeFactor](https://www.codefactor.io/repository/github/beapi/acf-options-for-polylang/badge)](https://www.codefactor.io/repository/github/beapi/acf-options-for-polylang)

Are you using Advanced Custom Fields for option pages and Polylang for your multilingual site?

Polylang does not natively support ACF Option Pages, so option values are shared across all languages.

This plugin improves that: Once activated, you’ll be able to set different values for each language. If a value isn’t set for a specific language, the “All languages” value will be used by default.

# How does it work?

This plugin saves separate values for each language in the database. Polylang’s language settings are then used to fetch the appropriate value from the database. <b>Note: When you activate the plugin, your existing option values will be temporarily unavailable but remain in the database. You can recover them by deactivating the plugin.</b>

To set or update your option pages in a specific language, simply use the Polylang language flags in the WordPress admin bar (the integrated language switcher) to select your desired language before editing the options.

# Requirements

- [WordPress](https://wordpress.org/) 6.0+ / Tested from 6.0 to latest
- PHP 7.4 to 8.4 / Tested from 7.4 to 8.4
- [Advanced Custom Fields](https://www.advancedcustomfields.com/pro) 5.6.0+
- [Polylang](https://polylang.pro/) (tested up to 3.7.7)

# Installation

First activate and configure Polylang in you site.
Then activate ACF Options For Polylang to handle ACF Options in setted Polylang's languages.

## WordPress

- Download and install using the built-in WordPress plugin installer.
- Site activate in the "Plugins" area of the admin.
- Optionally drop the entire `acf-options-for-polylang` directory into mu-plugins.
- Nothing more, this plugin is ready to use !

## [Composer](http://composer.rarst.net/)

- `composer require wpackagist-plugin/acf-options-for-polylang`
- Nothing more, this plugin is ready to use !

# What ?

## Contributing

Please refer to the [contributing guidelines](.github/CONTRIBUTING.md) to increase the chance of your pull request to be merged and/or receive the best support for your issue.

### Testing

This plugin includes comprehensive unit tests. See [TESTING.md](TESTING.md) for detailed instructions on running tests.

Quick start:
```bash
npm install && composer install
npm run wp-env:start
npm run test:unit
```

See [TESTING.md](TESTING.md) for detailed testing documentation.

### Issues & features request / proposal

If you identify any errors or have an idea for improving the plugin, feel free to open an [issue](../../issues/new). Please provide as much info as needed in order to help us resolving / approve your request.

## For developers

## Using fields

Nothing change, we have made all the hooks for you, so no need to prefix your fields with a lang or something else.
Only use ACF's helpers to get and show the fields as you did before with [get_field()](https://www.advancedcustomfields.com/resources/get_field/) or the_field() :

`get_field( 'footer_disclaimer', 'options' );`

## Excluding Option Pages from Localization

If you want to prevent this plugin from applying its functionality to a specific ACF options page, you can exclude it by adding its `post_id` to the exclusion filter.

Add the following code to your theme or custom plugin (replace `'custom_options_page_post_id'` with your actual options page post ID):

```php
add_filter( 'bea.aofp.excluded_post_ids', function( $ids ) {
    $ids[] = 'custom_options_page_post_id'; // Exclude this options page from localization
    return $ids;
}, 10, 1 );
```

This will ensure that the specified options page is not affected by language-specific behavior and will always load/save its values without localization.

## Language attribute for option key suffix

By default, the plugin uses Polylang’s **locale** (e.g. `fr_FR`, `en_US`) as the suffix for option keys. You can switch to **slug** (e.g. `fr`, `en`) or another Polylang language field.

**Option 1 – Constant** (e.g. in `wp-config.php`, before the plugin loads):

```php
define( 'BEA_ACF_OPTIONS_FOR_POLYLANG_LANG_ATTRIBUTE', 'slug' );
```

**Option 2 – Filter** (in theme or plugin):

```php
add_filter( 'bea.aofp.lang_attribute', function( $attribute ) {
    return 'slug'; // or 'locale', 'name', etc.
} );
```

The value must be a valid Polylang attribute for `pll_current_language()` / `pll_languages_list()` (e.g. `locale`, `slug`, `name`).

## Default Values (“All languages” Fallback)

By default, this plugin will use the Polylang “All languages” value when there is no value set for the current language. If you prefer not to use this fallback behavior, you can easily disable it using a filter.

### Disable fallback for all ACF Options pages

Add this code to your theme or plugin:
```php
add_filter( 'bea.aofp.get_default', '__return_false' );
```

### Disable fallback for a specific ACF Options page

You can target a single ACF Options page using the `$post_id` parameter:
```php
add_filter( 'bea.aofp.get_default', function( $show_default, $post_id ) {
	if ( 'my_custom_acf_option_post_id' === $post_id ) {
		// Disable default fallback for this specific ACF Options page
		return false;
	}

	return $show_default;
}, 10, 2 );
```

> **Note**: If you change this value dynamically or rely on the default/all languages fallback, you may sometimes need to clear ACF's internal cache so that your changes are properly recognized.  
To do this, use:
```php
// Clear ACF store
$store = acf_get_store('values');
$store->reset();
```
— this will force ACF to reload the values from the database on the next request.

## Loading untranslated (default) option values

When you need to read the **default / untranslated** values (the ones stored without a language suffix, used as fallback when no translation exists), use the context switch API. This applies to all fields, including repeater sub-fields and relationship fields.

### Switch / restore context

- **`bea_aofp_switch_to_untranslated()`** — subsequent `get_field( ..., option_page_id )` and `have_rows( ..., option_page_id )` will load values from the unsuffixed key (default values).
- **`bea_aofp_restore_current_lang()`** — restores the previous context; option values are again loaded for the current language.

Calls can be nested: each `restore_current_lang()` undoes the last `switch_to_untranslated()`.

### Example

```php
// Current language values
$title = get_field( 'site_title', 'theme-general-settings' );

// Temporarily load default (untranslated) values
bea_aofp_switch_to_untranslated();
$default_title = get_field( 'site_title', 'theme-general-settings' );
if ( have_rows( 'links', 'theme-general-settings' ) ) {
	while ( have_rows( 'links', 'theme-general-settings' ) ) {
		the_row();
		$default_related_post = get_sub_field( 'related_post' ); // Also uses default context
	}
}
bea_aofp_restore_current_lang();

// Back to current language
$title_again = get_field( 'site_title', 'theme-general-settings' );
```

# Who ?

Created by [Be API](https://beapi.fr), the French WordPress leader agency since 2009. Based in Paris, we are more than 30 people and always [hiring](https://beapi.workable.com) some fun and talented guys. So we will be pleased to work with you.

This plugin is only maintained, which means we do not guarantee some free support. Consider reporting an [issue](#issues--features-request--proposal) and be patient.

If you really like what we do or want to thank us for our quick work, feel free to [donate](https://www.paypal.me/BeAPI) as much as you want / can, even 1€ is a great gift for buying cofee :)

## License

BEA - ACF Options for Polylang is licensed under the [GPLv2 or later](LICENSE.md).
