# WordPress Plugin Development Patterns

Common patterns and best practices for WordPress plugin development.

## Plugin Boilerplate Structure

### Main Plugin File

```php
<?php
/**
 * Plugin Name: Plugin Name
 * Plugin URI:  https://example.com/
 * Description: Plugin description.
 * Version:     1.0.0
 * Author:      Author Name
 * License:     GPL-2.0+
 * Text Domain: plugin-text-domain
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('WPINC')) {
    die;
}

// Define constants
define('PLUGIN_VERSION', '1.0.0');
define('PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PLUGIN_URL', plugin_dir_url(__FILE__));

// Activation/deactivation hooks
register_activation_hook(__FILE__, 'activate_plugin');
register_deactivation_hook(__FILE__, 'deactivate_plugin');

function activate_plugin() {
    require_once PLUGIN_DIR . 'includes/class-plugin-activator.php';
    Plugin_Activator::activate();
}

function deactivate_plugin() {
    require_once PLUGIN_DIR . 'includes/class-plugin-deactivator.php';
    Plugin_Deactivator::deactivate();
}

// Include main class and run
require PLUGIN_DIR . 'includes/class-plugin.php';

function run_plugin() {
    $plugin = new Plugin_Main_Class();
    $plugin->run();
}
run_plugin();
```

### Loader Class Pattern

```php
<?php
class Plugin_Loader {
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

    private function add($hooks, $hook, $component, $callback, $priority, $accepted_args) {
        $hooks[] = array(
            'hook'          => $hook,
            'component'     => $component,
            'callback'      => $callback,
            'priority'      => $priority,
            'accepted_args' => $accepted_args
        );
        return $hooks;
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

## Enqueuing Assets

### Admin Scripts and Styles

```php
public function enqueue_styles() {
    wp_enqueue_style(
        $this->plugin_name,
        plugin_dir_url(__FILE__) . 'css/plugin-admin.css',
        array(),
        $this->version,
        'all'
    );
}

public function enqueue_scripts() {
    wp_enqueue_script(
        $this->plugin_name,
        plugin_dir_url(__FILE__) . 'js/plugin-admin.js',
        array('jquery'),
        $this->version,
        true // Load in footer
    );

    // Pass data to JavaScript
    wp_localize_script(
        $this->plugin_name,
        'pluginData',
        array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('plugin_nonce'),
            'strings' => array(
                'error' => __('An error occurred', 'plugin-text-domain'),
                'success' => __('Success!', 'plugin-text-domain')
            )
        )
    );
}
```

## REST API Patterns

### Registering Endpoints

```php
add_action('rest_api_init', function() {
    register_rest_route('plugin/v1', '/endpoint', array(
        'methods' => WP_REST_Server::READABLE, // GET
        'callback' => 'handle_get_request',
        'permission_callback' => function() {
            return current_user_can('manage_options');
        }
    ));

    register_rest_route('plugin/v1', '/endpoint', array(
        'methods' => WP_REST_Server::CREATABLE, // POST
        'callback' => 'handle_post_request',
        'permission_callback' => function() {
            return current_user_can('manage_options');
        },
        'args' => array(
            'param' => array(
                'required' => true,
                'sanitize_callback' => 'sanitize_text_field',
                'validate_callback' => function($param) {
                    return !empty($param);
                }
            )
        )
    ));
});
```

### REST Callback

```php
function handle_post_request($request) {
    $param = $request->get_param('param');

    // Do something
    $result = process_data($param);

    if (is_wp_error($result)) {
        return new WP_Error(
            'processing_failed',
            __('Processing failed', 'plugin-text-domain'),
            array('status' => 500)
        );
    }

    return new WP_REST_Response(array(
        'success' => true,
        'data' => $result
    ), 200);
}
```

## Security Patterns

### Sanitizing Input

```php
// Text field
$clean_text = sanitize_text_field($_POST['field_name']);

// Email
$clean_email = sanitize_email($_POST['email']);

// URL
$clean_url = esc_url_raw($_POST['url']);

// HTML (limited tags)
$clean_html = wp_kses_post($_POST['content']);

// Integer
$clean_int = absint($_POST['number']);

// Array of text
$clean_array = array_map('sanitize_text_field', $_POST['items']);
```

### Escaping Output

```php
// Text
echo esc_html($variable);

// HTML attributes
echo '<div class="' . esc_attr($class) . '">';

// URLs
echo '<a href="' . esc_url($url) . '">';

// JavaScript
echo '<script>var data = ' . wp_json_encode($data) . ';</script>';

// HTML with limited tags
echo wp_kses_post($content);
```

### Nonces

```php
// In the form
wp_nonce_field('plugin_action', 'plugin_nonce');

// In the handler
if (!isset($_POST['plugin_nonce']) ||
    !wp_verify_nonce($_POST['plugin_nonce'], 'plugin_action')) {
    wp_die(__('Security check failed', 'plugin-text-domain'));
}
```

## Internationalization

### Translatable Strings

```php
// Simple string
__('Hello', 'plugin-text-domain');

// Echo string
_e('Hello', 'plugin-text-domain');

// With placeholder
sprintf(__('Hello %s', 'plugin-text-domain'), $name);

// Plural
sprintf(
    _n(
        '%d item',
        '%d items',
        $count,
        'plugin-text-domain'
    ),
    $count
);
```

### Loading Text Domain

```php
public function load_plugin_textdomain() {
    load_plugin_textdomain(
        'plugin-text-domain',
        false,
        dirname(plugin_basename(__FILE__)) . '/languages/'
    );
}
```

## WooCommerce Integration

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

### HPOS Compatibility

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

## Admin Pages

### Add Menu Page

```php
public function add_plugin_admin_menu() {
    add_menu_page(
        __('Plugin Settings', 'plugin-text-domain'), // Page title
        __('Plugin', 'plugin-text-domain'),          // Menu title
        'manage_options',                             // Capability
        'plugin-settings',                            // Menu slug
        array($this, 'display_plugin_settings_page'), // Callback
        'dashicons-admin-generic',                    // Icon
        65                                            // Position
    );
}

public function display_plugin_settings_page() {
    include_once 'partials/plugin-admin-display.php';
}
```

## Debugging

### Conditional Debug Output

```php
if (defined('WP_DEBUG') && WP_DEBUG) {
    error_log('PLUGIN DEBUG: ' . print_r($data, true));
}
```

### Query Monitor Integration

```php
// Log to Query Monitor (if installed)
do_action('qm/debug', $variable);
do_action('qm/info', 'Info message');
do_action('qm/warning', 'Warning message');
```
