# PRD: Plugin Code Refactoring v1

## Overview

**Document Version:** 1.1
**Created:** 2026-01-13
**Status:** Draft - Awaiting Approval

### Summary

Refactor the Product Recommendation Quiz for eCommerce plugin to address technical debt, improve security, enhance maintainability, and align with WordPress coding standards. The plugin is functional but has accumulated code smells, duplicate logic, and inconsistent patterns that should be resolved.

### Current State

- **Version:** 2.2.14
- **Total Lines:** ~1,225 lines across 13 PHP files
- **Architecture:** WordPress Plugin Boilerplate pattern
- **Issues Found:** 15 (3 High, 6 Medium, 6 Low)

---

## Problem Statement

### Critical Issues

1. **Unsafe `$_REQUEST` Access** - Direct superglobal access in REST callback poses security risk
2. **Duplicate Cleanup Logic** - Same code exists in two places (main plugin file and Deactivator class)
3. **Hard `die()` Calls** - 7 ungraceful `die()` calls break admin UI for recoverable errors

### Medium Issues

4. **Inconsistent Option Deletion** - Uses `update_option(false)` instead of `delete_option()`
5. **Direct `$GLOBALS` Access** - Non-testable cache manipulation
6. **Weak REST Authentication** - Secondary endpoint only validates parameter presence
7. **No Rate Limiting** - REST endpoints vulnerable to brute force
8. **Mixed Security Patterns** - Two endpoints use different authentication methods

### Low Issues

9. **Hardcoded Environment Detection** - Only `.local` domains recognized as dev
10. **Missing Capability Checks** - Admin functions lack user permission verification
11. **Unused Activator Class** - Empty activate() method serves no purpose
12. **No PHPDoc Comments** - Methods lack documentation
13. **Magic Strings** - Option keys repeated throughout codebase
14. **Mixed Indentation** - Inconsistent tabs/spaces
15. **Missing WPINC Checks** - Admin and Public class files lack direct access prevention

---

## Goals

### Primary Goals

1. **Eliminate security vulnerabilities** - Fix all HIGH severity issues
2. **Consolidate duplicate code** - Single source of truth for all logic
3. **Improve error handling** - Graceful degradation instead of fatal errors
4. **Standardize patterns** - Consistent authentication, validation, and cleanup

### Secondary Goals

5. **Add defensive checks** - Capability verification on admin functions
6. **Improve testability** - Replace globals with WordPress functions
7. **Add documentation** - PHPDoc on all public methods
8. **Define constants** - Extract magic strings to named constants

### Non-Goals

- Adding new features
- Changing the plugin architecture
- Modifying the external API integration
- UI/UX changes

---

## Success Criteria

| Criterion | Measurement | Target |
|-----------|-------------|--------|
| Security issues | HIGH severity count | 0 |
| Duplicate code | Lines of duplicated logic | 0 |
| Fatal errors | `die()` calls in non-fatal scenarios | 0 |
| Test coverage | Unit tests for core functions | Basic coverage |
| Documentation | PHPDoc on public methods | 100% |
| Standards | WordPress coding standards violations | 0 |

---

## Implementation Plan

### Phase 1: Security Fixes (HIGH Priority)

#### 1.1 Fix Unsafe `$_REQUEST` Access

**File:** `product-recommendation-quiz-for-ecommerce.php`
**Line:** 119

**Current:**
```php
function prq_set_token($data) {
    $post = $_REQUEST;
    // ...
}
```

**Proposed:**
```php
function prq_set_token($request) {
    $shop_hashid = sanitize_text_field($request->get_param('shop_hashid'));
    $api_key = sanitize_text_field($request->get_param('api_key'));
    // ...
}
```

**Rationale:** Use the WP_REST_Request parameter already passed to callback. This is the WordPress-standard way to access REST API parameters.

#### 1.2 Unify REST Endpoint Authentication

**Files:** `product-recommendation-quiz-for-ecommerce.php`
**Lines:** 87-91 (WC endpoint), 153-167 (secondary endpoint)

**Current:** Two endpoints with different authentication:
- WC endpoint uses `check_woocommerce_api_permission()` (secure)
- Secondary endpoint only checks parameter presence (weak)

**Proposed:** Both endpoints should use consistent authentication or the secondary endpoint should be documented as intentionally open with additional validation.

**Options:**
1. Remove secondary endpoint (simplest)
2. Add nonce/signature validation to secondary endpoint
3. Document why secondary endpoint has different security model

#### 1.3 Replace Hard `die()` Calls with Graceful Error Handling

**File:** `admin/class-product-recommendation-quiz-for-ecommerce-admin.php`

There are 7 `die()` calls that break admin UI for recoverable errors:

| Line | Location | Context |
|------|----------|---------|
| 292 | `api_check_json()` | CURL not installed |
| 334 | `check_wpml()` | WPML compatibility warning |
| 346 | `prquiz_options()` | WooCommerce not installed |
| 358 | `prquiz_options()` | HTTPS/SSL missing |
| 364 | `prquiz_options()` | Running on localhost |
| 369 | `prquiz_options()` | Plain permalinks active |
| 394 | `prquiz_options()` | WP REST API JSON error |

**Current Pattern (all 7 locations):**
```php
// After displaying error message
$this->some_error_display();
die();
```

**Proposed Pattern:**

For `api_check_json()` (line 292):
```php
if ( !function_exists( 'curl_init' ) ) {
    return array(0, wp_json_encode(array('error' => 'curl_missing')));
}
```

For `prquiz_options()` (lines 346, 358, 364, 369, 394):
```php
// Replace die() with return after each error display method
$this->woocommerce_missing();
return; // Instead of die()
```

For `check_wpml()` (line 334):
```php
if ($shouldCallWpmlActive) {
    $this->wpml_active();
    return; // Instead of die()
}
```

**Rationale:** Never kill page execution for recoverable errors. The error display methods already render appropriate admin notices - they just need to return early instead of terminating execution. This allows WordPress to complete its normal shutdown process and prevents broken admin pages.

---

### Phase 2: Code Consolidation (MEDIUM Priority)

#### 2.1 Consolidate Cleanup Logic

**Problem:** Option cleanup exists in two places:
- `product-recommendation-quiz-for-ecommerce.php:140-148` (prq_deactivate_plugin)
- `includes/class-...-deactivator.php:32-40` (Deactivator::deactivate)

**Proposed Solution:**

Create a single cleanup method and call it from both locations:

```php
// In Deactivator class
class Product_Recommendation_Quiz_For_Ecommerce_Deactivator {

    /**
     * Clean up all plugin data.
     *
     * @param bool $delete_options Whether to delete options (true for uninstall, false for deactivate)
     */
    public static function cleanup($delete_options = false) {
        // Clear cache
        wp_cache_delete('rh_shop_hashid', 'options');
        wp_cache_delete('rh_api_key', 'options');
        wp_cache_delete('rh_domain', 'options');
        wp_cache_delete('rh_token', 'options');

        if ($delete_options) {
            delete_option('rh_shop_hashid');
            delete_option('rh_api_key');
            delete_option('rh_domain');
            delete_option('rh_token');
        }
    }

    public static function deactivate() {
        self::cleanup(true);
    }
}
```

Then in main plugin file:
```php
function prq_deactivate_plugin() {
    Product_Recommendation_Quiz_For_Ecommerce_Deactivator::cleanup(true);
}
```

#### 2.2 Fix Option Deletion Pattern

**Current:**
```php
update_option('rh_shop_hashid', false, false);
```

**Proposed:**
```php
delete_option('rh_shop_hashid');
```

**Rationale:** `update_option(key, false)` creates an option with value `false`, it doesn't delete it.

#### 2.3 Replace `$GLOBALS` with WordPress Functions

**Current:**
```php
$GLOBALS['wp_object_cache']->delete('rh_shop_hashid', 'options');
```

**Proposed:**
```php
wp_cache_delete('rh_shop_hashid', 'options');
```

**Rationale:** Use WordPress abstraction layer for testability and compatibility.

---

### Phase 3: Defensive Programming (MEDIUM Priority)

#### 3.1 Add Capability Checks to Admin Functions

**File:** `admin/class-product-recommendation-quiz-for-ecommerce-admin.php`

Add at the start of `prquiz_options()`:
```php
public function prquiz_options() {
    if (!current_user_can('manage_options')) {
        wp_die(
            esc_html__('You do not have sufficient permissions to access this page.', 'product-recommendation-quiz-for-ecommerce'),
            403
        );
    }
    // ... rest of function
}
```

#### 3.2 Add Input Validation to REST Callbacks

**Add format validation:**
```php
// Validate shop_hashid format (alphanumeric only)
if (!preg_match('/^[a-zA-Z0-9]+$/', $shop_hashid)) {
    return new WP_Error(
        'invalid_shop_hashid',
        __('Invalid shop hashid format', 'product-recommendation-quiz-for-ecommerce'),
        array('status' => 400)
    );
}
```

#### 3.3 Add Rate Limiting (Optional)

```php
function prq_check_rate_limit() {
    // Note: For sites behind proxies/CDNs, may need to check HTTP_X_FORWARDED_FOR
    $ip = sanitize_text_field($_SERVER['REMOTE_ADDR']);
    $transient_key = 'prq_rate_' . md5($ip);
    $attempts = (int) get_transient($transient_key);

    if ($attempts >= 10) {
        return new WP_Error(
            'rate_limited',
            __('Too many requests. Try again later.', 'product-recommendation-quiz-for-ecommerce'),
            array('status' => 429)
        );
    }

    set_transient($transient_key, $attempts + 1, MINUTE_IN_SECONDS);
    return true;
}
```

---

### Phase 4: Code Quality (LOW Priority)

#### 4.1 Define Option Constants

**File:** `includes/class-product-recommendation-quiz-for-ecommerce.php`

```php
class Product_Recommendation_Quiz_For_Ecommerce {

    // Option keys
    const OPTION_SHOP_HASHID = 'rh_shop_hashid';
    const OPTION_API_KEY = 'rh_api_key';
    const OPTION_DOMAIN = 'rh_domain';
    const OPTION_TOKEN = 'rh_token';

    // ...
}
```

Then use throughout: `Product_Recommendation_Quiz_For_Ecommerce::OPTION_SHOP_HASHID`

#### 4.2 Improve Environment Detection

**Current:**
```php
if (preg_match('/\.local/i', PRQ_STORE_URL)) {
    // Local development
}
```

**Proposed:**
```php
private function is_development_environment() {
    // Check WordPress environment type first (WP 5.5+)
    if (function_exists('wp_get_environment_type')) {
        $env = wp_get_environment_type();
        if (in_array($env, array('local', 'development'), true)) {
            return true;
        }
    }

    // Fallback to domain detection
    $dev_patterns = array('/\.local$/i', '/\.test$/i', '/localhost/i', '/\.dev$/i');
    foreach ($dev_patterns as $pattern) {
        if (preg_match($pattern, PRQ_STORE_URL)) {
            return true;
        }
    }

    return false;
}
```

#### 4.3 Refactor Long Methods

**Target:** `prquiz_options()` (71 lines)

**Split into:**
- `check_prerequisites()` - WooCommerce, HTTPS, REST API checks
- `render_first_visit()` - Initial OAuth flow
- `render_admin_panel()` - Main admin iframe

#### 4.4 Add PHPDoc Comments

**Example:**
```php
/**
 * Generate OAuth URL for WooCommerce authentication.
 *
 * Builds the complete OAuth authorization URL with all required
 * parameters for the WooCommerce REST API authentication flow.
 *
 * @since 2.2.14
 * @return string The complete OAuth URL.
 */
public function prquiz_get_oauth_url() {
    // ...
}
```

#### 4.5 Standardize Indentation

All PHP files should use tabs (not spaces) per WordPress standards.

#### 4.6 Add WPINC Checks to Class Files

**Files:**
- `admin/class-product-recommendation-quiz-for-ecommerce-admin.php`
- `public/class-product-recommendation-quiz-for-ecommerce-public.php`

**Current:** Files start directly with `<?php` and docblock.

**Proposed:** Add WPINC check at the top of each file:
```php
<?php
// Prevent direct access
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * The admin-specific functionality of the plugin.
 * ...
```

**Rationale:** WordPress Plugin Boilerplate pattern requires all PHP files to prevent direct access. This is a security best practice that prevents files from being accessed directly via URL.

#### 4.7 Handle Unused Activator

**Options:**
1. Add comment explaining it's a placeholder for future use
2. Use it to set default options on activation
3. Remove if truly unnecessary

**Recommended:** Add initialization logic:
```php
public static function activate() {
    // Set default options if not exist
    if (false === get_option('rh_shop_hashid')) {
        add_option('rh_shop_hashid', '', '', false);  // false for autoload
    }
}
```

---

## File Changes Summary

| File | Changes | Priority |
|------|---------|----------|
| `product-recommendation-quiz-for-ecommerce.php` | Fix $_REQUEST, consolidate cleanup, fix option deletion | HIGH |
| `admin/class-product-recommendation-quiz-for-ecommerce-admin.php` | Replace die(), add capability check, refactor long method | HIGH/MEDIUM |
| `includes/class-product-recommendation-quiz-for-ecommerce-deactivator.php` | Consolidate cleanup logic | MEDIUM |
| `includes/class-product-recommendation-quiz-for-ecommerce.php` | Add constants, improve env detection | LOW |
| `includes/class-product-recommendation-quiz-for-ecommerce-activator.php` | Add initialization or document | LOW |
| All files | Add PHPDoc, fix indentation | LOW |

---

## Risks and Mitigations

| Risk | Impact | Likelihood | Mitigation |
|------|--------|------------|------------|
| Breaking existing installations | HIGH | LOW | Test with existing options, ensure backward compatibility |
| OAuth flow disruption | HIGH | LOW | Don't change OAuth URL generation logic |
| REST API regression | MEDIUM | LOW | Test both endpoints with real WC auth |
| Cache issues | LOW | MEDIUM | Test cleanup on multiple environments |

---

## Testing Plan

### Manual Testing

1. **Fresh Installation**
   - Activate plugin
   - Complete OAuth flow
   - Verify quiz loads on frontend

2. **Existing Installation**
   - Upgrade plugin
   - Verify existing credentials preserved
   - Verify quiz continues working

3. **Deactivation/Uninstall**
   - Deactivate and verify options cleared
   - Reactivate and verify OAuth required again

4. **Error Scenarios**
   - Test without WooCommerce installed
   - Test without HTTPS
   - Test without curl extension
   - Test with plain permalinks

### Automated Testing (Future)

- Unit tests for cleanup functions
- Unit tests for environment detection
- Integration tests for REST endpoints

---

## Rollback Plan

All changes are backward compatible. If issues arise:

1. Revert to previous version via WordPress rollback
2. No database migrations required
3. Options remain compatible

---

## Appendix A: Current Issue Severity Matrix

| ID | Issue | Severity | Effort | Phase |
|----|-------|----------|--------|-------|
| 1 | Unsafe $_REQUEST access | HIGH | Small | 1 |
| 2 | Duplicate cleanup code | MEDIUM | Small | 2 |
| 3 | Hard die() calls | HIGH | Small | 1 |
| 4 | Option false deletion | MEDIUM | Small | 2 |
| 5 | Direct GLOBALS access | MEDIUM | Small | 2 |
| 6 | Weak REST auth | MEDIUM | Medium | 1 |
| 7 | No rate limiting | LOW | Medium | 3 |
| 8 | Mixed security patterns | MEDIUM | Medium | 1 |
| 9 | Hardcoded env detection | LOW | Small | 4 |
| 10 | Missing capability checks | LOW | Small | 3 |
| 11 | Unused activator | LOW | Tiny | 4 |
| 12 | No PHPDoc comments | LOW | Medium | 4 |
| 13 | Magic strings | LOW | Small | 4 |
| 14 | Mixed indentation | LOW | Tiny | 4 |
| 15 | Missing WPINC checks | LOW | Tiny | 4 |

---

## Appendix B: Files Affected by Refactoring

```
product-recommendation-quiz-for-ecommerce/
├── product-recommendation-quiz-for-ecommerce.php  [HIGH - Phase 1, 2]
├── admin/
│   └── class-product-recommendation-quiz-for-ecommerce-admin.php  [HIGH - Phase 1, 3, 4]
├── includes/
│   ├── class-product-recommendation-quiz-for-ecommerce.php  [LOW - Phase 4]
│   ├── class-product-recommendation-quiz-for-ecommerce-deactivator.php  [MEDIUM - Phase 2]
│   ├── class-product-recommendation-quiz-for-ecommerce-activator.php  [LOW - Phase 4]
│   ├── class-product-recommendation-quiz-for-ecommerce-loader.php  [LOW - Phase 4]
│   └── class-product-recommendation-quiz-for-ecommerce-i18n.php  [LOW - Phase 4]
└── public/
    └── class-product-recommendation-quiz-for-ecommerce-public.php  [LOW - Phase 4]
```

---

## Approval

- [ ] Technical Review Complete
- [ ] Security Review Complete
- [ ] Implementation Approved

**Reviewers:**
- [ ] @libertas

---

## Changelog

| Version | Date | Changes |
|---------|------|---------|
| 1.0 | 2026-01-13 | Initial PRD created |
| 1.1 | 2026-01-13 | Expanded Phase 1.3 to cover all 7 die() calls; Added issue #15 (missing WPINC checks) |
