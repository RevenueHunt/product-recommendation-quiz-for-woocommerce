# Code Patterns

## Core Principle - Keep It Simple

**ALWAYS prioritize:**

- **SIMPLICITY** - Choose the simplest solution that works
- **DRY** - Don't Repeat Yourself - reuse existing code, patterns, and components
- **RELEVANCE** - Only make changes that directly address the task
- **NO OVERCOMPLICATION** - Avoid unnecessary abstractions or complex patterns

## Project Overview

Product Recommendation Quiz for eCommerce - WordPress/WooCommerce plugin that connects stores to the RevenueHunt quiz platform.

### Tech Stack

- **CMS**: WordPress 3.0.1+ (tested up to 6.8.3)
- **E-commerce**: WooCommerce 3.5+ (tested up to 10.2.2)
- **Language**: PHP 5.6+ (vanilla, no frameworks)
- **JavaScript**: Remote embed.js from RevenueHunt CDN
- **CSS**: Single admin stylesheet
- **Build Tools**: None (direct file editing)

## WordPress Plugin Architecture

### Plugin Boilerplate Pattern

This plugin follows the WordPress Plugin Boilerplate structure:

```
plugin-root/
├── admin/                    # Admin-facing functionality
│   ├── class-...-admin.php  # Admin class
│   ├── css/                 # Admin stylesheets
│   └── img/                 # Admin images
├── includes/                 # Core plugin classes
│   ├── class-...-loader.php # Hooks loader
│   ├── class-...-i18n.php   # Internationalization
│   ├── class-....php        # Main plugin class
│   ├── class-...-activator.php   # Activation hooks
│   └── class-...-deactivator.php # Deactivation hooks
├── public/                   # Public-facing functionality
│   └── class-...-public.php # Public class
├── languages/               # Translation files (.pot, .po, .mo)
└── plugin-file.php          # Main entry point
```

### Class Naming Convention

All classes use the prefix `Product_Recommendation_Quiz_For_Ecommerce_`:

```php
class Product_Recommendation_Quiz_For_Ecommerce           // Main class
class Product_Recommendation_Quiz_For_Ecommerce_Loader    // Hooks loader
class Product_Recommendation_Quiz_For_Ecommerce_Admin     // Admin functionality
class Product_Recommendation_Quiz_For_Ecommerce_Public    // Public functionality
class Product_Recommendation_Quiz_For_Ecommerce_i18n      // Internationalization
class Product_Recommendation_Quiz_For_Ecommerce_Activator // Activation
class Product_Recommendation_Quiz_For_Ecommerce_Deactivator // Deactivation
```

### File Organization

| Directory | Purpose |
|-----------|---------|
| `/admin/` | Admin panel UI, admin-specific CSS, OAuth handling |
| `/includes/` | Core plugin classes, loader, i18n, activator/deactivator |
| `/public/` | Frontend functionality, public embed.js loading |
| `/assets/` | Screenshots for WordPress.org repository |
| `/languages/` | Translation files (.pot) |

## PHP Patterns

### Main Plugin File Structure

```php
<?php
/**
 * Plugin Name: Product Recommendation Quiz for eCommerce
 * Plugin URI:  https://revenuehunt.com/
 * Description: Plugin description here
 * Version:     X.X.X
 * Author:      RevenueHunt
 * License:     GPL-2.0+
 * Text Domain: product-recommendation-quiz-for-ecommerce
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('WPINC')) {
    die;
}

// Define constants
define('PRQ_VERSION', 'X.X.X');
define('PRQ_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PRQ_PLUGIN_URL', plugin_dir_url(__FILE__));

// Activation/deactivation hooks
register_activation_hook(__FILE__, 'activate_plugin');
register_deactivation_hook(__FILE__, 'deactivate_plugin');

// Run the plugin
function run_plugin() {
    $plugin = new Product_Recommendation_Quiz_For_Ecommerce();
    $plugin->run();
}
run_plugin();
```

### Loader Pattern

The loader class manages all hooks and filters:

```php
class Product_Recommendation_Quiz_For_Ecommerce_Loader {
    protected $actions;
    protected $filters;

    public function __construct() {
        $this->actions = array();
        $this->filters = array();
    }

    public function add_action($hook, $component, $callback, $priority = 10, $accepted_args = 1) {
        $this->actions = $this->add($this->actions, $hook, $component, $callback, $priority, $accepted_args);
    }

    public function add_filter($hook, $component, $callback, $priority = 10, $accepted_args = 1) {
        $this->filters = $this->add($this->filters, $hook, $component, $callback, $priority, $accepted_args);
    }

    public function run() {
        foreach ($this->filters as $hook) {
            add_filter($hook['hook'], array($hook['component'], $hook['callback']), $hook['priority'], $hook['accepted_args']);
        }
        foreach ($this->actions as $hook) {
            add_action($hook['hook'], array($hook['component'], $hook['callback']), $hook['priority'], $hook['accepted_args']);
        }
    }
}
```

### Environment Detection

```php
// Check if running locally
if (preg_match('/\.local/i', PRQ_STORE_URL)) {
    // Development environment
    define('PRQ_API_URL', 'http://localhost:tunnel/');
    define('PRQ_ADMIN_URL', 'http://localhost:tunnel/admin');
} else {
    // Production environment
    define('PRQ_API_URL', 'https://api.revenuehunt.com');
    define('PRQ_ADMIN_URL', 'https://admin.revenuehunt.com');
}
```

### Enqueuing Scripts (Admin)

```php
public function enqueue_scripts() {
    // Remote JavaScript
    wp_enqueue_script(
        $this->plugin_name,
        PRQ_ADMIN_URL . '/embed.js',
        array(),
        $this->version,
        true
    );

    // Pass data to JavaScript
    wp_localize_script(
        $this->plugin_name,
        'prqData',
        array(
            'shopHashid' => get_option('rh_shop_hashid'),
            'apiKey' => get_option('rh_api_key'),
            'version' => PRQ_VERSION
        )
    );
}
```

## REST API Patterns

### Registering Endpoints

```php
add_action('rest_api_init', function() {
    // WooCommerce namespace
    register_rest_route('wc/v3', '/prq_set_token', array(
        'methods' => 'POST',
        'callback' => 'prq_set_token_callback',
        'permission_callback' => 'prq_verify_wc_auth'
    ));

    // Custom namespace
    register_rest_route('prq/v1', '/settoken', array(
        'methods' => 'POST',
        'callback' => 'prq_set_token_callback',
        'permission_callback' => '__return_true'
    ));
});
```

### REST Callback with Sanitization

```php
function prq_set_token_callback($request) {
    $shop_hashid = sanitize_text_field($request->get_param('shop_hashid'));
    $api_key = sanitize_text_field($request->get_param('api_key'));

    if (empty($shop_hashid) || empty($api_key)) {
        return new WP_Error('missing_params', 'Required parameters missing', array('status' => 400));
    }

    update_option('rh_shop_hashid', $shop_hashid);
    update_option('rh_api_key', $api_key);

    return new WP_REST_Response(array('success' => true), 200);
}
```

## Security Patterns

### Prevent Direct Access

Every PHP file should start with:

```php
<?php
// Prevent direct access
if (!defined('WPINC')) {
    die;
}
```

Or use an empty index.php in directories:

```php
<?php
// Silence is golden.
```

### Sanitizing Input

```php
// Text field
$clean_text = sanitize_text_field($_POST['field_name']);

// Email
$clean_email = sanitize_email($_POST['email']);

// URL
$clean_url = esc_url_raw($_POST['url']);

// Integer
$clean_int = absint($_POST['number']);
```

### Escaping Output

```php
// Text
echo esc_html($variable);

// HTML attributes
echo '<div class="' . esc_attr($class) . '">';

// URLs
echo '<a href="' . esc_url($url) . '">';

// JavaScript data
echo '<script>var data = ' . wp_json_encode($data) . ';</script>';
```

### Nonces for Admin Forms

```php
// In the form
wp_nonce_field('prq_action', 'prq_nonce');

// In the handler
if (!isset($_POST['prq_nonce']) ||
    !wp_verify_nonce($_POST['prq_nonce'], 'prq_action')) {
    wp_die('Security check failed');
}
```

## WooCommerce Integration

### HPOS Compatibility Declaration

```php
add_action('before_woocommerce_init', function() {
    if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
            'custom_order_tables',
            __FILE__,
            true
        );
    }
});
```

### Check WooCommerce Active

```php
if (class_exists('WooCommerce')) {
    // WooCommerce is active
}

// Or check for specific version
if (defined('WC_VERSION') && version_compare(WC_VERSION, '3.5', '>=')) {
    // WooCommerce 3.5+ features
}
```

## Internationalization

### Text Domain

All strings use the text domain `product-recommendation-quiz-for-ecommerce`:

```php
// Simple string
__('Hello', 'product-recommendation-quiz-for-ecommerce');

// Echo string
_e('Hello', 'product-recommendation-quiz-for-ecommerce');

// With placeholder
sprintf(__('Hello %s', 'product-recommendation-quiz-for-ecommerce'), $name);
```

### Loading Text Domain

```php
public function load_plugin_textdomain() {
    load_plugin_textdomain(
        'product-recommendation-quiz-for-ecommerce',
        false,
        dirname(plugin_basename(__FILE__)) . '/languages/'
    );
}
```

## Common Pitfalls to Avoid

1. **Direct file access** - Always check for WPINC/ABSPATH
2. **Unescaped output** - Always escape with esc_html(), esc_attr(), etc.
3. **Missing text domains** - All strings should use the plugin text domain
4. **Hardcoded URLs** - Use plugin_dir_url() and plugins_url()
5. **Global namespace pollution** - Prefix all functions and classes
6. **Missing capability checks** - Verify user permissions before admin actions
7. **console.log in production** - Remove debug statements before releasing
