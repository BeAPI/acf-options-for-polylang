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

The test suite includes **41 test methods** covering all main classes:

### Main Class (15 tests)
- Singleton instance verification
- Filter registration (acf/validate_post_id, acf/settings/current_language, acf/load_value, acf/load_reference)
- Language settings (REST API vs frontend)
- Default reference loading
- Options ID localization
- Default value retrieval with various conditions
- Filter to disable default values

### Helpers Class (13 tests)
- Original option ID extraction
- Localized option ID handling (fr_FR, en_US, de_DE, pt_BR patterns)
- Option page detection
- Excluded option pages
- Post/User/Term object handling
- Option page IDs retrieval
- Already localized detection

### Requirements Class (7 tests)
- Singleton instance verification
- Dependency checks (ACF and Polylang)
- ACF version validation (>= 5.6.0)
- Error display functionality
- Admin notices
- Plugin deactivation on missing dependencies

### Admin Class (6 tests)
- Singleton instance verification
- Filter registration (acf/options_page/submitbox_before_major_actions)
- Language indicator in submitbox
- HTML output validation
- Method callability checks

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
├── bootstrap.php           # PHPUnit bootstrap
├── test-main.php          # Tests for Main class
├── test-helpers.php       # Tests for Helpers class
├── test-requirements.php  # Tests for Requirements class
└── test-admin.php         # Tests for Admin class
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

### Skipping Tests

If a test requires conditions not available:

```php
public function test_requires_acf() {
	if ( ! function_exists( 'acf' ) ) {
		$this->markTestSkipped( 'ACF is required for this test.' );
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

**Out of memory errors:**
Increase Docker memory allocation in Docker Desktop preferences.

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
