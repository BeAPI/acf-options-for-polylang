<a href="https://beapi.fr">![Be API Github Banner](.wordpress-org/banner-github.png)</a>

# BEA - ACF Options For Polylang

[![CodeFactor](https://www.codefactor.io/repository/github/beapi/acf-options-for-polylang/badge)](https://www.codefactor.io/repository/github/beapi/acf-options-for-polylang)

You are using Advanced Custom Fields for creating option pages and you have Polylang installed for awsome multilingual site ?

Sadly, Polylang is not handling ACF's Option Pages. Which means values will be the same for all languages you have set.

We are here to save your life ! Once this plugin is activated, you will be able to set a different value for each language, and if none is set, the "All languages" value will be used as default one.

# How ?

This plugin will store a value for each language into database. Then Polylang's languages are used to get the values from the DB. <b>That means at activation, all existing data will not be anymore available, but still in database. You will retrieve it ad plugin deactivation.</b>

Then to set and contribute your option page, simply use the Polylang's language admin flags ui.

# Requirements

- Require [WordPress](https://wordpress.org/) 4.7+ / Tested up to 5.2
- Require PHP 5.6
- [Advanced Custom Fields](https://www.advancedcustomfields.com/pro) 5.6.0+
- [Polylang](https://polylang.pro/)

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

## Features

- Almost simple fields (text, textarea, links, etc)
- Repeater fields (with simple fields)

## More features to come

As you can see, some [issues](../../issues?q=is%3Aissue+is%3Aopen+label%3Aquestion) are feature requests :

- Migration of data for using plugin : at activation, all data will not be anymore available, but still in database.
- Migration of data for not using plugin anymore

## Next Roadmap

- Fixing [#41] : repeater issue when need to get all languages one.

## Contributing

Please refer to the [contributing guidelines](.github/CONTRIBUTING.md) to increase the chance of your pull request to be merged and/or receive the best support for your issue.

### Issues & features request / proposal

If you identify any errors or have an idea for improving the plugin, feel free to open an [issue](../../issues/new). Please provide as much info as needed in order to help us resolving / approve your request.

## For developers

## Using fields

Nothing change, we have made all the hooks for you, so no need to prefix your fields with a lang or something else.
Only use ACF's helpers to get and show the fields as you did before with [get_field()](https://www.advancedcustomfields.com/resources/get_field/) or the_field() :

`get_field( 'footer_disclaimer', 'options' );`

## Excluding options pages

If you don't want this functionality on a certain options page, add the `post_id` to the list of excluded options pages.

```
add_filter( 'bea.aofp.excluded_post_ids', function($ids) {
	$ids[] = 'custom_options_page_post_id';
	return $ids;
}, 10, 1 );
```

## Default values (with "All languages")

The plugin is designed to get the Polylang "All languages" value if the current lang one is empty. But if you are not interested about this behaviour, you can programmatically deactivate it using the following filter by setting to false.

### For all ACF Options pages

```
<?php add_filter( 'bea.aofp.get_default', '__return_false' );
```

### For just one ACF Options page

```
<?php add_filter( 'bea.aofp.get_default', function( $show_default, $post_id ) {
	if ( 'my_custom_acf_option_post_id' === $post_id ) {
		// Custom condition for the wanted post id ACF Option page
		return false;
	}

	return $show_default;
}, 10, 2 );
```

# Who ?

Created by [Be API](https://beapi.fr), the French WordPress leader agency since 2009. Based in Paris, we are more than 30 people and always [hiring](https://beapi.workable.com) some fun and talented guys. So we will be pleased to work with you.

This plugin is only maintained, which means we do not guarantee some free support. Consider reporting an [issue](#issues--features-request--proposal) and be patient.

If you really like what we do or want to thank us for our quick work, feel free to [donate](https://www.paypal.me/BeAPI) as much as you want / can, even 1€ is a great gift for buying cofee :)

## License

BEA - ACF Options for Polylang is licensed under the [GPLv2 or later](LICENSE.md).
