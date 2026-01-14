# Task Routing

Route user requests to the appropriate skill/command based on intent.

## Intent Mapping

| Intent | Keywords | Action |
|--------|----------|--------|
| Bug Fix | "bug", "fix", "broken", "issue", "defect" | Follow `development.md` -> Implementation Workflow |
| Authentication | "oauth", "connect", "token", "auth" | Check REST API and OAuth flow |
| Admin Panel | "admin", "settings", "dashboard" | Edit admin class |
| Frontend | "embed", "quiz", "frontend", "public" | Edit public class |
| WooCommerce | "woo", "woocommerce", "shop", "store" | Check WC integration |
| REST API | "api", "endpoint", "rest" | Check main plugin file and security.md |
| Deployment | "deploy", "release", "version" | `/deploy` |
| Quality Report | "quality", "code review", "issues" | `/quality-report` |
| Staged Review | "review", "check my changes" | `/review-staged` |
| Roadmap | "roadmap", "priorities", "what's next" | `/roadmap` |

## File Location by Task

| Task Type | Primary Files |
|-----------|---------------|
| Plugin activation | `includes/class-...-activator.php` |
| Plugin deactivation | `includes/class-...-deactivator.php` |
| Hook registration | `includes/class-...-loader.php` |
| Main plugin logic | `includes/class-....php` |
| Admin functionality | `admin/class-...-admin.php` |
| Admin styles | `admin/css/...admin.css` |
| Public functionality | `public/class-...-public.php` |
| REST API | `product-recommendation-quiz-for-ecommerce.php` |
| Internationalization | `includes/class-...-i18n.php` |
| Translations | `languages/*.pot` |
| Uninstall cleanup | `uninstall.php` |
| Documentation | `README.txt`, `README.md` |
| Changelog | `changelog.txt` |

## Subagent Strategy by Task Type

| Task Type | Strategy |
|-----------|----------|
| Code search | Explore agent |
| Deep analysis | Sonnet agent |
| Implementation | Haiku agents in parallel |
| Security review | Sonnet agent |
| Simple edits | Direct edit (no agent needed) |

## WordPress Plugin-Specific Routing

| Plugin Task | Approach |
|-------------|----------|
| Add admin menu | Edit admin class `add_plugin_admin_menu()` |
| Add settings page | Edit admin class, add options page |
| Add REST endpoint | Edit main plugin file, register_rest_route() |
| Modify activation | Edit activator class |
| Modify deactivation | Edit deactivator class |
| Add translation string | Use text domain, regenerate .pot file |
| Update version | Edit plugin header, README.txt, changelog.txt |

## WooCommerce-Specific Routing

| WooCommerce Task | Approach |
|------------------|----------|
| HPOS compatibility | Check before_woocommerce_init hook |
| WC API auth | Check WC_REST_Authentication usage |
| Shop data | Use WC functions like wc_get_page_id() |
| Product integration | Use WC_Product class |

## Common Patterns

### Adding a New REST Endpoint

1. Add registration in main plugin file `rest_api_init` hook
2. Create callback function with sanitization
3. Set permission_callback (check security.md)
4. Add any needed option storage

### Modifying Admin Panel

1. Edit `admin/class-...-admin.php`
2. Add hooks via loader if needed
3. Add styles to `admin/css/` if needed

### Adding Public Feature

1. Edit `public/class-...-public.php`
2. Register hooks via loader
3. Test with theme compatibility

### Security Fix

1. Identify vulnerability type
2. Check security.md for pattern
3. Apply fix with proper sanitization/escaping
4. Test thoroughly
