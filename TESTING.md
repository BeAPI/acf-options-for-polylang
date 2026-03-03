# Unit Testing for ACF Options for Polylang

This plugin includes comprehensive unit tests to ensure functionality when using ACF (Advanced Custom Fields) and Polylang together.

## Quick Start

```bash
# Complete setup and run tests
npm install && composer install
npm run wp-env:start
npm run test:unit
```

## Prerequisites

You need to have the following installed on your system:

- **PHP** 7.4 to 8.4 (8.3 recommended for local development)
- **Composer** - [getcomposer.org](https://getcomposer.org/)
- **Node.js** 18+ and **npm** 8+
- **Docker Desktop**

## Test Coverage Summary

The test suite includes **61 test methods** and **92 assertions** with **0 skips**, covering all main classes. Polylang is fully initialized during tests (via `PLL_ADMIN` constant in bootstrap) so all tests run with real Polylang API functions available. Tests run in the context of the ACF options page `theme-general-settings`.

**Runtime language switch:** Two tests explicitly cover a change of language at runtime so that option values can differ per language (e.g. FR vs EN): `test_set_current_site_lang_changes_with_runtime_language_switch` and `test_set_options_id_lang_returns_different_id_per_runtime_language`.

### Test Matrix: Non-Regression Details

| Class | Test method | What it tests | Non-regression verified |
|-------|-------------|---------------|-------------------------|
| **Admin** (8) | `test_admin_instance_exists` | Admin singleton instantiation | Admin class is correctly bootstrapped and accessible. |
| | `test_filter_is_registered` | Hook `acf/options_page/submitbox_before_major_actions` | Submitbox notice is hooked and will display on ACF options pages. |
| | `test_submitbox_before_major_actions_with_current_language` | Output of submitbox with untranslated context | Warning "Be careful" and "untranslated options" are shown when no current language. |
| | `test_submitbox_before_major_actions_without_current_language` | Submitbox produces output | Submitbox always outputs HTML (no fatal/empty output). |
| | `test_submitbox_before_major_actions_contains_html` | HTML structure of submitbox | Valid `<p class="misc-pub-section">` markup for styling and a11y. |
| | `test_submitbox_before_major_actions_is_callable` | Callability of the callback | Method can be invoked by WordPress without error. |
| | `test_submitbox_before_major_actions_with_invalid_page` | Submitbox with non-option page context | No output is produced when the page is not an ACF options page. |
| | `test_submitbox_before_major_actions_with_active_language_name` | Submitbox with active Polylang language | Output includes current language name from Polylang. |
| **Functions** (3) | `test_bea_aofp_switch_to_untranslated_function_exists` | `bea_aofp_switch_to_untranslated()` existence | Public API function is defined and callable. |
| | `test_bea_aofp_restore_current_lang_function_exists` | `bea_aofp_restore_current_lang()` existence | Public API function is defined and callable. |
| | `test_switch_and_restore_via_public_api` | Full switch/restore cycle via public API | Public API correctly delegates to Main class; `set_options_id_lang` appends then removes locale suffix. |
| **Helpers** (19) | `test_original_option_id_with_string` | `original_option_id('options_fr_FR')` | Localized option ID is normalized to `options`. |
| | `test_original_option_id_converts_option_to_options` | `original_option_id('option')` | Legacy "option" slug is normalized to "options". |
| | `test_original_option_id_with_post_object` | `original_option_id($post)` | Post objects return `0` (numeric IDs are not valid localized option identifiers). |
| | `test_original_option_id_with_user_object` | `original_option_id($user)` | User objects are converted to `user_{ID}`. |
| | `test_original_option_id_with_term_object` | `original_option_id($term)` | Term objects are converted to `term_{term_id}`. |
| | `test_is_option_page_with_options` | `is_option_page('options')` | Generic "options" is recognized as an option page. |
| | `test_is_option_page_with_localized_options` | `is_option_page('options_fr_FR')` | Localized option IDs are recognized as option pages. |
| | `test_is_option_page_with_regular_post_id` | `is_option_page($post_id)` with real post | Regular post IDs are not treated as option pages. |
| | `test_is_option_page_with_excluded_post_id` | `is_option_page()` with filter `bea.aofp.excluded_post_ids` | Excluded post IDs are not treated as option pages; filter works. |
| | `test_get_option_page_ids_returns_array` | `get_option_page_ids()` return type | ACF option page IDs are returned as an array (no fatal, correct API). |
| | `test_already_localized_with_localized_post_id` | `already_localized('options_fr_FR')` | Locale suffix pattern (e.g. `fr_FR`) is detected. |
| | `test_already_localized_with_non_localized_post_id` | `already_localized('options')` | Plain "options" is not considered localized. |
| | `test_already_localized_with_different_locale_formats` | Various locale patterns (en_US, de_DE, pt_BR, etc.) | Regex for locale detection works for common formats; no false positives on IDs without locale. |
| | `test_already_localized_with_non_string_value` | `already_localized()` with integer/non-string input | Non-string values return false without errors. |
| | `test_get_lang_attribute_returns_locale_by_default` | `get_lang_attribute()` default return | Returns `'locale'` when no constant or filter overrides. |
| | `test_get_lang_attribute_respects_filter` | `get_lang_attribute()` with `bea.aofp.lang_attribute` filter | Filter can change the attribute to `'slug'` or other values. |
| | `test_get_lang_attribute_fallback_on_invalid_filter_value` | `get_lang_attribute()` with invalid filter return | Falls back to `'locale'` when filter returns a non-string value. |
| | `test_locales_regex_fragment_returns_regex_with_configured_languages` | `locales_regex_fragment()` content | Returns a regex fragment containing configured Polylang language locales. |
| | `test_locales_regex_fragment_is_cached` | `locales_regex_fragment()` caching | Second call returns the same cached value. |
| **Main** (25) | `test_main_instance_exists` | Main singleton instantiation | Main class is correctly bootstrapped. |
| | `test_filters_are_registered` | Hooks: `acf/validate_post_id`, `acf/settings/current_language`, `acf/load_value`, `acf/load_reference` | Core ACF integration filters are registered. |
| | `test_set_current_site_lang` | `set_current_site_lang()` return type | Current language is returned as a string (Polylang integration). |
| | `test_set_current_site_lang_changes_with_runtime_language_switch` | `set_current_site_lang()` after language switch | Changing language at runtime returns the correct locale (fr_FR / en_US). |
| | `test_set_options_id_lang_returns_different_id_per_runtime_language` | `set_options_id_lang()` for custom option page when switching FR/EN | Same option page yields different storage keys per language. |
| | `test_set_current_site_lang_rest_api` | `set_current_site_lang()` in REST context | In REST API, WordPress locale is used instead of Polylang. |
| | `test_get_default_reference_with_existing_reference` | `get_default_reference()` when reference already set | Existing reference is preserved (no overwrite). |
| | `test_get_default_reference_with_localized_post_id` | `get_default_reference()` with localized option ID | Returns string or null; no fatal when no default exists. |
| | `test_set_options_id_lang_with_non_option_page` | `set_options_id_lang()` with non-option post ID | Non-option IDs are left unchanged. |
| | `test_set_options_id_lang_with_already_localized` | `set_options_id_lang()` with already localized ID | Already localized option IDs are not double-processed. |
| | `test_get_default_value_in_admin` | `get_default_value()` in admin screen | In admin, value is not replaced by default (avoid overwriting user input). |
| | `test_get_default_value_with_non_empty_value` | `get_default_value()` with non-empty value | Non-empty value is preserved. |
| | `test_get_default_value_with_filter_disabled` | `get_default_value()` with filter `bea.aofp.get_default` false | Filter allows disabling default value fallback; empty value stays empty. |
| | `test_maybe_load_untranslated_value_returns_value_when_not_in_untranslated_context` | `maybe_load_untranslated_value()` outside untranslated context | Value is returned as-is when not in untranslated context. |
| | `test_maybe_load_untranslated_value_returns_value_when_post_id_not_localized` | `maybe_load_untranslated_value()` with non-localized post ID | Value is returned as-is for non-localized post IDs. |
| | `test_maybe_load_untranslated_value_returns_value_when_not_option_key` | `maybe_load_untranslated_value()` with non-option key | Value is returned as-is when post_id is not an option page. |
| | `test_maybe_load_untranslated_value_loads_unsuffixed_value_in_untranslated_context` | `maybe_load_untranslated_value()` in untranslated context | Loads the unsuffixed (default) option value when in untranslated context. |
| | `test_switch_to_untranslated_and_restore` | `switch_to_untranslated()` + `restore_current_lang()` cycle | Language suffix is removed then restored correctly. |
| | `test_switch_to_untranslated_nesting` | Nested `switch_to_untranslated()` calls | Nested switches work correctly; each restore pops one level. |
| | `test_restore_current_lang_without_switch_is_noop` | `restore_current_lang()` without prior switch | No-op when called without a preceding switch; no errors. |
| | `test_get_default_value_falls_back_to_default_when_empty` | `get_default_value()` with empty value on front-end | Falls back to default (unsuffixed) value when current language value is empty. |
| | `test_get_default_value_with_empty_array` | `get_default_value()` with empty array value | Empty arrays are treated as empty and trigger default fallback. |
| | `test_get_default_value_with_null_value` | `get_default_value()` with null value | Null values trigger default fallback. |
| | `test_get_default_value_with_repeater_type` | `get_default_value()` with repeater field type | Repeater fields are excluded from default value fallback. |
| | `test_get_default_value_with_bea_aofp_get_default_enable_filter` | `get_default_value()` with `bea.aofp.get_default` true + stored default | When filter is enabled and a default value exists in DB, it is returned. |
| **Requirements** (6) | `test_requirements_instance_exists` | Requirements singleton instantiation | Requirements class is bootstrapped. |
| | `test_check_requirements_with_dependencies_available` | `check_requirements()` when ACF + Polylang present | Plugin loads when dependencies are met. |
| | `test_display_error_triggers_error` | `display_error()` behavior | `trigger_error()` is called and `admin_notices` is hooked; missing deps show notice. |
| | `test_display_error_adds_admin_notice_action` | `display_error()` hooks | `admin_notices` action is registered. |
| | `test_display_error_adds_admin_init_action` | `display_error()` hooks | `admin_init` action is registered (for deactivation flow). |
| | `test_acf_version_meets_minimum_requirement` | ACF version check | ACF version constant is defined and meets minimum requirement. |

## Setup and Running Tests

This plugin uses Docker with `@wordpress/env` to create an isolated testing environment with WordPress, ACF, and Polylang pre-installed.

### Initial Setup

1. **Install dependencies:**

```bash
npm install
composer install
```

2. **Start the WordPress environment:**

```bash
npm run wp-env:start
```

This will:
- Download and install WordPress (latest trunk version)
- Install Advanced Custom Fields plugin (latest)
- Install Polylang plugin (latest)
- Set up MySQL databases for development and testing
- Configure PHP 8.3

⏱️ The first run may take 5-10 minutes as it downloads everything.

### Running Tests

```bash
# Run all tests
npm run test:unit

# Run tests with detailed output (test names)
npm run test:unit:watch
```

### Managing the Environment

```bash
# Stop the environment
npm run wp-env:stop

# Clean and reset everything
npm run wp-env:clean

# Restart the environment (stop + clean + start)
npm run wp-env:stop
npm run wp-env:clean
npm run wp-env:start
```

### Accessing the Sites

When running, you can access:

- **Development site:** http://localhost:8888
- **Test site:** http://localhost:8889

Default credentials:
- Username: `admin`
- Password: `password`

### Troubleshooting wp-env

**Docker not running:**
```bash
# Make sure Docker Desktop is running first
open -a Docker  # macOS
# Then start the environment
npm run wp-env:start
```

**Ports already in use:**
Edit `.wp-env.json` and change the port numbers in the `port` configuration.

**Environment won't start:**
```bash
npm run wp-env:clean
npm run wp-env:start
```

**Tests fail with "Could not find includes/functions.php":**
```bash
# Clean and restart
npm run wp-env:clean
npm install && composer install
npm run wp-env:start
```

## Available Commands

### npm Scripts

| Script | Description |
|--------|-------------|
| `npm run wp-env:start` | Start WordPress environment |
| `npm run wp-env:stop` | Stop WordPress environment |
| `npm run wp-env:clean` | Clean environment |
| `npm run test:unit` | Run all tests |
| `npm run test:unit:watch` | Run tests with detailed output |
| `npm run test:setup` | Install all dependencies |

### PHPUnit Commands

```bash
# Run all tests
./vendor/bin/phpunit

# Run tests with detailed output
./vendor/bin/phpunit --testdox

# Run a specific test class
./vendor/bin/phpunit tests/test-main.php

# Run a specific test method
./vendor/bin/phpunit --filter test_instance_exists

# Generate coverage report
./vendor/bin/phpunit --coverage-html coverage/

# Generate coverage report (text)
./vendor/bin/phpunit --coverage-text
```

### Composer Scripts

```bash
# Check coding standards
composer cs

# Fix coding standards automatically
composer cbf
```

## Writing New Tests

### Test Structure

Tests are located in the `tests/` directory:

```
tests/
├── bootstrap.php              # PHPUnit bootstrap (defines PLL_ADMIN for Polylang init)
├── mu-plugins/
│   └── setup-polylang.php     # Creates FR/EN languages, registers ACF options page & fields
├── Test_Admin.php             # Tests for Admin class (8 tests)
├── Test_Functions.php         # Tests for public API functions (3 tests)
├── Test_Helpers.php           # Tests for Helpers class (19 tests)
├── Test_Main.php              # Tests for Main class (25 tests)
└── Test_Requirements.php      # Tests for Requirements class (6 tests)
```

### Basic Test Template

Create a new file in `tests/` with the prefix `test-`:

```php
<?php
/**
 * Class Test_My_Feature
 *
 * @package BEA\ACF_Options_For_Polylang
 */

namespace BEA\ACF_Options_For_Polylang\Tests;

use BEA\ACF_Options_For_Polylang\My_Feature;

/**
 * Test case for the My_Feature class.
 */
class Test_My_Feature extends \WP_UnitTestCase {

	/**
	 * Instance of the class being tested.
	 *
	 * @var My_Feature
	 */
	private $instance;

	/**
	 * Set up the test.
	 */
	public function set_up() {
		parent::set_up();
		
		// Initialize dependencies
		$this->instance = My_Feature::get_instance();
	}

	/**
	 * Tear down the test.
	 */
	public function tear_down() {
		parent::tear_down();
		
		// Clean up
	}

	/**
	 * Test that the instance is created correctly.
	 */
	public function test_instance_exists() {
		$this->assertInstanceOf( My_Feature::class, $this->instance );
	}

	/**
	 * Test a specific functionality.
	 */
	public function test_my_functionality() {
		// Arrange
		$input = 'test_value';
		$expected = 'expected_result';
		
		// Act
		$result = $this->instance->my_method( $input );
		
		// Assert
		$this->assertEquals( $expected, $result );
	}
}
```

### Test Naming Conventions

- File names: `test-{feature}.php` (e.g., `test-helpers.php`)
- Class names: `Test_{Feature}` (e.g., `Test_Helpers`)
- Method names: `test_{what_is_tested}` (e.g., `test_returns_string`)
- Be descriptive: `test_get_option_with_empty_value_returns_false`

### Common Assertions

```php
// Boolean checks
$this->assertTrue( $condition );
$this->assertFalse( $condition );

// Equality
$this->assertEquals( $expected, $actual );
$this->assertSame( $expected, $actual );  // Strict comparison
$this->assertNotEquals( $expected, $actual );

// Type checks
$this->assertIsString( $value );
$this->assertIsArray( $value );
$this->assertIsInt( $value );
$this->assertIsBool( $value );
$this->assertIsNumeric( $value );

// Instance checks
$this->assertInstanceOf( MyClass::class, $object );

// String operations
$this->assertStringContainsString( 'needle', $haystack );
$this->assertStringStartsWith( 'prefix', $string );
$this->assertStringEndsWith( 'suffix', $string );

// Array operations
$this->assertContains( $needle, $array );
$this->assertArrayHasKey( 'key', $array );
$this->assertCount( 3, $array );

// Existence checks
$this->assertEmpty( $value );
$this->assertNotEmpty( $value );
$this->assertNull( $value );
$this->assertNotNull( $value );
```

### Using WordPress Test Factories

WordPress provides factories to create test data:

```php
// Create a post
$post_id = $this->factory->post->create([
	'post_title' => 'Test Post',
	'post_type' => 'page',
	'post_status' => 'publish',
]);

// Create a user
$user_id = $this->factory->user->create([
	'user_login' => 'testuser',
	'role' => 'editor',
	'user_email' => 'test@example.com',
]);

// Create a term
$term_id = $this->factory->term->create([
	'name' => 'Test Category',
	'taxonomy' => 'category',
	'slug' => 'test-category',
]);

// Create a comment
$comment_id = $this->factory->comment->create([
	'comment_post_ID' => $post_id,
	'comment_content' => 'Test comment',
]);
```

### Testing WordPress Hooks

```php
// Check if a filter is registered
public function test_filter_is_registered() {
	$this->assertNotFalse(
		has_filter( 'my_filter', [ $this->instance, 'my_method' ] )
	);
}

// Check if an action is registered
public function test_action_is_registered() {
	$this->assertGreaterThan(
		0,
		has_action( 'init', [ $this->instance, 'init_method' ] )
	);
}

// Test filter output
public function test_filter_modifies_value() {
	$original = 'test';
	$filtered = apply_filters( 'my_filter', $original );
	
	$this->assertNotEquals( $original, $filtered );
	$this->assertEquals( 'expected_value', $filtered );
}
```

### Testing HTML Output

```php
public function test_outputs_html() {
	ob_start();
	my_output_function();
	$output = ob_get_clean();
	
	$this->assertStringContainsString( '<div class="my-class">', $output );
	$this->assertStringContainsString( 'expected text', $output );
}
```

### Testing Admin Context

```php
public function test_admin_functionality() {
	// Set admin context
	set_current_screen( 'edit-post' );
	
	// Your test code
	$result = $this->instance->admin_method();
	
	// Assertions
	$this->assertTrue( $result );
	
	// Reset
	set_current_screen( 'front' );
}
```

### Test Environment Bootstrap

The test bootstrap (`tests/bootstrap.php`) defines `PLL_ADMIN = true` before WordPress loads. This forces Polylang to fully initialize, making `PLL()`, `pll_current_language()`, and all Polylang API functions available during tests. ACF and Polylang are always present in the wp-env test environment.

### Switching Polylang Language in Tests

Use the `set_polylang_language()` helper to switch languages at runtime:

```php
private function set_polylang_language( string $slug ): void {
	$languages = \PLL()->model->get_languages_list();
	foreach ( $languages as $lang ) {
		if ( $slug === $lang->slug ) {
			\PLL()->curlang = $lang;
			switch_to_locale( $lang->locale );
			return;
		}
	}
	$this->fail( "Polylang language '{$slug}' not found." );
}
```

### Skipping Tests

If a test requires conditions not available:

```php
public function test_requires_specific_condition() {
	if ( ! some_condition() ) {
		$this->markTestSkipped( 'Specific condition is required for this test.' );
	}
	
	// Test code
}
```

## Continuous Integration

A GitHub Actions workflow is included (`.github/workflows/tests.yml`) that:

- Runs tests on multiple PHP versions (7.4, 8.0, 8.1, 8.2, 8.3, 8.4)
- Tests against multiple WordPress versions (6.0, 6.1, 6.2, 6.3, 6.4, 6.5, 6.6, 6.7, 6.8, 6.9, latest)
- Checks coding standards
- Lints PHP files
- Generates code coverage reports (Codecov)

### Example GitHub Actions Output

The workflow will automatically run on:
- Push to `master` or `develop` branches
- Pull requests to `master` or `develop`

## Configuration Files

- **`phpunit.xml.dist`** - PHPUnit configuration
  - Bootstrap file location
  - Test suites definition
  - Code coverage settings
  
- **`.wp-env.json`** - WordPress environment configuration
  - WordPress version (trunk)
  - PHP version (8.1)
  - Plugins to install (ACF, Polylang)
  - Port mappings
  
- **`package.json`** - npm scripts and dependencies
  - wp-env scripts
  - Test commands
  
- **`composer.json`** - PHP dependencies
  - PHPUnit 9.6
  - Yoast PHPUnit Polyfills 2.0
  - Mockery 1.6

## Test Environment Details

The wp-env configuration includes:
- **WordPress**: Latest trunk version
- **PHP**: 8.3 (configurable in `.wp-env.json`)
- **Advanced Custom Fields**: Latest version (downloaded from WordPress.org)
- **Polylang**: Latest version (downloaded from WordPress.org)
- **MySQL**: Provided by Docker

## Contributing

When adding new features to the plugin:

1. **Write tests first** (TDD approach) or alongside your feature
2. **Ensure all existing tests pass** before submitting PR
3. **Follow naming conventions** for files and methods
4. **Add PHPDoc comments** to all test methods
5. **Use the AAA pattern** (Arrange, Act, Assert)
6. **Keep tests independent** - no side effects between tests
7. **Clean up in tear_down()** - reset any global state

### Checklist for Pull Requests

- [ ] All tests pass (`npm run test:unit` or `./vendor/bin/phpunit`)
- [ ] New features have test coverage
- [ ] Tests follow naming conventions
- [ ] PHPDoc comments added
- [ ] Coding standards pass (`composer cs`)
- [ ] PHP files are valid (`./vendor/bin/parallel-lint --exclude vendor .`)
- [ ] No unnecessary test files or artifacts committed

## Troubleshooting

### Common Issues

**Tests fail with "Could not find includes/functions.php":**
```bash
# The WordPress test library isn't set up
npm run wp-env:clean
npm install && composer install
npm run wp-env:start
```

**Docker errors:**
```bash
# Make sure Docker Desktop is running
open -a Docker  # macOS

# Then restart
npm run wp-env:stop
npm run wp-env:start
```

**Port conflicts (8888 or 8889 already in use):**
Edit `.wp-env.json` and change the port numbers.

**Out of memory errors:**
Increase Docker memory allocation in Docker Desktop preferences.

**Slow test execution:**
- Run specific tests: `./vendor/bin/phpunit tests/Test_Main.php`
- Reduce the number of tests running in parallel

## Additional Resources

- [WordPress Plugin Unit Tests Handbook](https://make.wordpress.org/core/handbook/testing/automated-testing/phpunit/)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [@wordpress/env Documentation](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-env/)
- [WordPress Test Suite on GitHub](https://github.com/WordPress/wordpress-develop/tree/trunk/tests/phpunit)
- [ACF Documentation](https://www.advancedcustomfields.com/resources/)
- [Polylang Documentation](https://polylang.pro/doc/)

## Support

If you encounter issues with the tests:

1. Check this documentation for troubleshooting steps
2. Verify all prerequisites are installed
3. Check Docker Desktop is running
4. Review test output for specific error messages
5. Check [GitHub Issues](https://github.com/BeAPI/acf-options-for-polylang/issues)

---

**Happy Testing! 🚀**

Quick start: `npm install && composer install && npm run wp-env:start && npm run test:unit`
