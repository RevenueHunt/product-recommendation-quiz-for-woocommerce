# Development Guidelines

## Local Development

This plugin runs in Local by Flywheel:

```
/Users/libertas/Local Sites/productrecommendationquiz/app/public/wp-content/plugins/product-recommendation-quiz-for-ecommerce/
```

Access the local site at the URL configured in Local by Flywheel.

## Code Style

### PHP

1. **WordPress Coding Standards**: Follow [WordPress PHP Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/)
2. **Indentation**: Tabs (not spaces)
3. **Braces**: Opening brace on same line
4. **Naming**: snake_case for functions, StudlyCaps for classes

```php
// Good - Class naming
class Product_Recommendation_Quiz_For_Ecommerce_Admin {
    // ...
}

// Good - Function naming
function prq_get_shop_hashid() {
    $shop_hashid = get_option('rh_shop_hashid');
    return $shop_hashid;
}

// Bad
function prqGetShopHashid() {
    $shopHashid = get_option('rh_shop_hashid');
}
```

### JavaScript

Since JavaScript is loaded remotely from RevenueHunt CDN, local JS changes are minimal. When needed:

1. **Indentation**: 2 spaces
2. **Semicolons**: Always use them
3. **ES6**: Use modern syntax when browser support allows

### CSS

1. **Indentation**: 2 spaces
2. **Selectors**: Prefix with plugin name to avoid conflicts
3. **Properties**: One per line

```css
/* Good */
.prq-admin-container {
  padding: 20px;
  background-color: #fff;
}

.prq-admin-container .prq-button {
  margin-top: 10px;
}
```

## File Naming

- **PHP files**: kebab-case with class prefix (e.g., `class-product-recommendation-quiz-for-ecommerce-admin.php`)
- **CSS files**: kebab-case (e.g., `product-recommendation-quiz-for-ecommerce-admin.css`)
- **Translation files**: Text domain based (e.g., `product-recommendation-quiz-for-ecommerce.pot`)

## Git Workflow

**DO NOT** perform any git write operations:

- No `git add`
- No `git stash`
- No `git commit`
- No `git push`

The user will handle all git write operations.

**Read-only operations are allowed:**

- `git status`
- `git branch`
- `git diff`
- `git log`

### Branch Strategy

- `master` - Production branch (released to WordPress.org)
- `develop` - Development branch
- Feature branches as needed

## Deployment

### WordPress.org SVN

The plugin is distributed via WordPress.org plugin repository:

1. Update version in main plugin file header
2. Update version in `README.txt` (Stable tag)
3. Update `changelog.txt`
4. Commit to SVN trunk
5. Create SVN tag for release

## Planning Guidelines

**MANDATORY**: When creating any plan, ALWAYS explicitly state:
"I will find the simplest solution that keeps the code DRY, ensures changes are relevant and necessary, and avoids unnecessary complexity."

## Implementation Workflow

After completing any non-trivial implementation:

### 1. Test Locally

- Activate plugin in Local by Flywheel
- Test with WooCommerce enabled and disabled
- Test OAuth flow if authentication is involved
- Check browser console for JavaScript errors
- Verify REST API endpoints work

### 2. Run Code Review

Run `/review-staged` to get an independent AI review of your changes.

This launches reviewers that check for:
- Security issues (XSS, SQL injection)
- WordPress plugin standards
- PHP errors and warnings
- WooCommerce compatibility

### 3. Fix Issues Found

Address any Critical or High severity issues before proceeding.

### 4. Report to User

After review passes, provide a summary:
- What was implemented
- What the review found (if anything)
- Ready for user to commit

**DO NOT commit yourself** - the user handles all git write operations.

## Common Tasks

### Updating Plugin Version

1. Update version in main plugin file header comment
2. Update `PRQ_VERSION` constant
3. Update "Stable tag" in `README.txt`
4. Add entry to `changelog.txt`

### Adding Admin Functionality

1. Edit `admin/class-product-recommendation-quiz-for-ecommerce-admin.php`
2. Register hooks in the main plugin class
3. Add any needed styles to `admin/css/`

### Adding Public Functionality

1. Edit `public/class-product-recommendation-quiz-for-ecommerce-public.php`
2. Register hooks in the main plugin class

### Adding REST Endpoints

1. Add endpoint registration in main plugin file
2. Create callback function with proper sanitization
3. Set appropriate permission_callback

### Modifying Options

Plugin options stored in `wp_options`:

```php
// Get option
$value = get_option('rh_shop_hashid');

// Update option
update_option('rh_shop_hashid', $new_value);

// Delete option
delete_option('rh_shop_hashid');
```

## Debug Logging

Use specific prefixes for debugging:

```php
// PHP (remove before releasing)
if (defined('WP_DEBUG') && WP_DEBUG) {
    error_log('PRQ DEBUG: ' . print_r($data, true));
}
```

## Testing Checklist

Before any release:

- [ ] Plugin activates without errors
- [ ] Plugin deactivates cleanly
- [ ] OAuth flow completes successfully
- [ ] Quiz embed loads on frontend
- [ ] Admin panel loads without errors
- [ ] Works with latest WordPress version
- [ ] Works with latest WooCommerce version
- [ ] No PHP notices/warnings
- [ ] No JavaScript console errors

## Roadmap Maintenance

The roadmap lives at `.project/planning/ROADMAP.md`. Follow these rules when updating it.

### Commit References (MANDATORY)

Every completed item MUST have commit references for traceability:

```markdown
- [x] **Feature name** @assignee
      Description (commit abc1234).
```

If commit is unknown, search git history before marking done:
```bash
git log --oneline --all --grep="keyword" | head -10
```

### Moving Completed Items

Items marked `[x]` must be moved to the appropriate `DONE WEEK YYYY-MM-DD` section.

### Bug Placement

**Bugs are ALWAYS priority.** Any item with `BUG:`, `Fix:`, `fix:`, or `bug` label:
- Goes directly to **Current Sprint**
- Never to Backlog or Not Priority
- Gets worked on before features
