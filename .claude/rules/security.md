# Security Patterns

Security guidelines specific to this WordPress/WooCommerce plugin.

## Authentication Flow

### OAuth with WooCommerce

The plugin uses WooCommerce REST API authentication:

```php
// Verify WooCommerce authentication
function prq_verify_wc_auth($request) {
    // Use WooCommerce's built-in authentication
    $authentication = new WC_REST_Authentication();
    $user_id = $authentication->authenticate(false);

    return !is_wp_error($user_id) && $user_id > 0;
}
```

### Token Storage

Sensitive data is stored in `wp_options`:

| Option | Purpose | Sensitivity |
|--------|---------|-------------|
| `rh_shop_hashid` | Store identifier | Medium |
| `rh_api_key` | API credentials | High |
| `rh_token` | Auth token | High |

**Best Practices:**

- Never expose API keys in frontend JavaScript
- Always sanitize before storing
- Delete on plugin uninstall

## Input Validation

### REST API Parameters

Always validate and sanitize REST API inputs:

```php
function prq_set_token_callback($request) {
    // Sanitize all inputs
    $shop_hashid = sanitize_text_field($request->get_param('shop_hashid'));
    $api_key = sanitize_text_field($request->get_param('api_key'));

    // Validate required fields
    if (empty($shop_hashid)) {
        return new WP_Error(
            'missing_shop_hashid',
            __('Shop hashid is required', 'product-recommendation-quiz-for-ecommerce'),
            array('status' => 400)
        );
    }

    // Validate format if applicable
    if (!preg_match('/^[a-zA-Z0-9]+$/', $shop_hashid)) {
        return new WP_Error(
            'invalid_shop_hashid',
            __('Invalid shop hashid format', 'product-recommendation-quiz-for-ecommerce'),
            array('status' => 400)
        );
    }

    // Process valid input
    update_option('rh_shop_hashid', $shop_hashid);

    return new WP_REST_Response(array('success' => true), 200);
}
```

### Sanitization Functions

| Function | Use For |
|----------|---------|
| `sanitize_text_field()` | Single-line text |
| `sanitize_textarea_field()` | Multi-line text |
| `sanitize_email()` | Email addresses |
| `sanitize_url()` | URLs |
| `absint()` | Positive integers |
| `sanitize_key()` | Keys, slugs |

## Output Escaping

### In Admin Pages

```php
// Text content
echo esc_html($shop_name);

// HTML attributes
echo '<input value="' . esc_attr($value) . '">';

// URLs
echo '<a href="' . esc_url($oauth_url) . '">Connect</a>';

// Translation with HTML
printf(
    wp_kses(
        __('Click <a href="%s">here</a> to connect', 'product-recommendation-quiz-for-ecommerce'),
        array('a' => array('href' => array()))
    ),
    esc_url($url)
);
```

### In JavaScript Localization

```php
wp_localize_script('prq-admin', 'prqData', array(
    'shopHashid' => esc_js(get_option('rh_shop_hashid')),
    'adminUrl' => esc_url(admin_url()),
    'nonce' => wp_create_nonce('prq_ajax_nonce')
));
```

## REST API Security

### Permission Callbacks

Never use `__return_true` for sensitive endpoints:

```php
// BAD - Anyone can access
register_rest_route('prq/v1', '/settings', array(
    'permission_callback' => '__return_true'  // Don't do this for sensitive data
));

// GOOD - Require admin capability
register_rest_route('prq/v1', '/settings', array(
    'permission_callback' => function() {
        return current_user_can('manage_options');
    }
));

// GOOD - Require WooCommerce authentication
register_rest_route('wc/v3', '/prq_set_token', array(
    'permission_callback' => 'prq_verify_wc_auth'
));
```

### Rate Limiting Considerations

For sensitive endpoints, consider rate limiting:

```php
function prq_rate_limit_check() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $transient_key = 'prq_rate_' . md5($ip);
    $attempts = get_transient($transient_key);

    if ($attempts >= 10) {
        return new WP_Error(
            'rate_limited',
            __('Too many requests. Please try again later.', 'product-recommendation-quiz-for-ecommerce'),
            array('status' => 429)
        );
    }

    set_transient($transient_key, ($attempts ? $attempts + 1 : 1), MINUTE_IN_SECONDS);
    return true;
}
```

## CSRF Protection

### Admin Forms

```php
// Generate nonce field
wp_nonce_field('prq_save_settings', 'prq_nonce');

// Verify nonce
if (!wp_verify_nonce($_POST['prq_nonce'], 'prq_save_settings')) {
    wp_die(__('Security check failed', 'product-recommendation-quiz-for-ecommerce'));
}
```

### AJAX Requests

```php
// In JavaScript
jQuery.ajax({
    url: ajaxurl,
    data: {
        action: 'prq_action',
        nonce: prqData.nonce,
        // other data
    }
});

// In PHP handler
check_ajax_referer('prq_ajax_nonce', 'nonce');
```

## Capability Checks

Always verify user capabilities before admin actions:

```php
// Before saving settings
if (!current_user_can('manage_options')) {
    wp_die(__('Unauthorized', 'product-recommendation-quiz-for-ecommerce'));
}

// Before accessing WooCommerce settings
if (!current_user_can('manage_woocommerce')) {
    wp_die(__('Unauthorized', 'product-recommendation-quiz-for-ecommerce'));
}
```

## Secure Uninstall

Clean up all data when plugin is deleted:

```php
// uninstall.php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

// Remove all options
delete_option('rh_shop_hashid');
delete_option('rh_api_key');
delete_option('rh_domain');
delete_option('rh_token');

// Clear any transients
global $wpdb;
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_prq_%'");
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_prq_%'");
```

## Security Checklist

Before any release:

- [ ] All user inputs are sanitized
- [ ] All outputs are escaped
- [ ] REST endpoints have proper permission callbacks
- [ ] Admin forms use nonces
- [ ] Capability checks before sensitive actions
- [ ] No secrets in frontend JavaScript
- [ ] Uninstall cleans up all data
- [ ] No debug output in production
- [ ] Direct file access is blocked

## Common Vulnerabilities to Avoid

| Vulnerability | Prevention |
|---------------|------------|
| XSS | Escape all output with esc_html(), esc_attr(), etc. |
| SQL Injection | Use $wpdb->prepare() for all queries |
| CSRF | Use wp_nonce_field() and wp_verify_nonce() |
| Privilege Escalation | Check current_user_can() before admin actions |
| Information Disclosure | Don't expose API keys, don't output errors in production |
| Remote Code Execution | Never use eval(), never include user input |
