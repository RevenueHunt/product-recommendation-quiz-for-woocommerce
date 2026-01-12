# Product Recommendation Quiz for eCommerce

WordPress/WooCommerce plugin that connects stores to the RevenueHunt quiz platform.

## Quick Reference

```bash
# No build tools - direct file editing
# Test locally via Local by Flywheel
```

## Core Principles

1. **Keep it simple** - Choose the simplest solution that works
2. **DRY** - Reuse existing code, patterns, and components
3. **WordPress Plugin standards** - Follow WordPress plugin coding conventions
4. **Thin wrapper** - Plugin delegates UI/logic to remote RevenueHunt platform
5. **Security first** - Validate all inputs, escape all outputs

## Agentic Tenets

1. **Plan before acting** - Complex tasks need structured plans with success criteria
2. **Parallelize when independent** - Launch concurrent agents for unrelated tasks
3. **Verify with critique loops** - Producer -> Critic -> Refine until quality met
4. **Anticipate failure** - Build retry, fallback, and escalation into workflows
5. **Least privilege** - Grant minimum tool permissions; deny by default
6. **Commands invoke skills** - Commands should be thin wrappers that invoke skills
7. **Match existing patterns** - Check existing files for format patterns before creating new templates

## Project Rules

Detailed instructions are in `.claude/rules/`:

- `code-patterns.md` - WordPress plugin architecture, PHP patterns, class structure
- `development.md` - Code style, git workflow, deployment
- `security.md` - Authentication, REST API security, data handling
- `orchestration.md` - Sub-agent strategy, patterns, composable workflows
- `task-routing.md` - Intent -> skill/command mapping
- `reliability.md` - Exception handling, guardrails, recovery patterns

## Tech Stack

| Layer | Technology | Notes |
|-------|------------|-------|
| **CMS** | WordPress 3.0.1+ | Tested up to 6.8.3 |
| **E-commerce** | WooCommerce 3.5+ | Tested up to 10.2.2 |
| **Language** | PHP 5.6+ | Vanilla PHP, no frameworks |
| **JavaScript** | Remote embed.js | Loaded from RevenueHunt CDN |
| **CSS** | Minimal admin CSS | Single stylesheet |
| **Build Tools** | None | Direct file editing |
| **Deployment** | WordPress.org SVN | Plugin repository |

## Architecture

This plugin follows the **WordPress Plugin Boilerplate** pattern:

```
product-recommendation-quiz-for-ecommerce/
├── admin/                    # Admin-facing functionality
│   ├── class-...-admin.php  # Admin class
│   ├── css/                 # Admin stylesheets
│   └── img/                 # Admin images
├── includes/                 # Core plugin classes
│   ├── class-...-loader.php # Hooks loader
│   ├── class-...-i18n.php   # Internationalization
│   ├── class-....php        # Main plugin class
│   ├── class-...-activator.php
│   └── class-...-deactivator.php
├── public/                   # Public-facing functionality
│   └── class-...-public.php # Public class
├── languages/               # Translation files
├── assets/                  # Screenshots for WordPress.org
├── .claude/                 # Claude Code configuration
├── .project/                # Planning and roadmap
├── product-recommendation-quiz-for-ecommerce.php  # Main entry point
├── uninstall.php           # Cleanup on uninstall
├── README.txt              # WordPress.org readme
└── changelog.txt           # Version history
```

## Key Files

| File | Purpose |
|------|---------|
| `product-recommendation-quiz-for-ecommerce.php` | Main entry point, REST API registration |
| `includes/class-...-loader.php` | WordPress hooks registration |
| `includes/class-....php` | Main plugin class, environment detection |
| `admin/class-...-admin.php` | Admin UI, OAuth handling, embed.js loading |
| `public/class-...-public.php` | Frontend embed.js loading |
| `uninstall.php` | Cleanup when plugin is deleted |

## Plugin Options (wp_options)

| Option | Purpose |
|--------|---------|
| `rh_shop_hashid` | Store identifier |
| `rh_api_key` | API credentials |
| `rh_domain` | Store domain |
| `rh_token` | Authentication token |

## REST API Endpoints

| Endpoint | Method | Purpose |
|----------|--------|---------|
| `wc/v3/prq_set_token` | POST | WooCommerce auth flow |
| `prq/v1/settoken` | POST | Secondary token endpoint |

## Environment Detection

```php
// Development (localhost)
if (preg_match('/\.local/i', PRQ_STORE_URL)) {
    // Uses localhost tunnel
}

// Production
// API: https://api.revenuehunt.com
// Admin: https://admin.revenuehunt.com
```

## Documentation

| Location | Contents |
|----------|----------|
| `README.txt` | WordPress.org plugin description |
| `README.md` | Same content in Markdown |
| `changelog.txt` | Version history |
| `.project/planning/` | Roadmap, active planning |
| `.project/archive/` | Completed work, historical docs |

## Project Planning

Task management files are in `.project/planning/`:

| File | Purpose | When to Use |
|------|---------|-------------|
| `ROADMAP.md` | Unified Kanban board | All task management |

### Workflow

1. **Planning** -> Cards flow: Vision -> Backlog -> Current Sprint -> Doing
2. **Active Work** -> Doing section uses todo.txt syntax for tracking
3. **Execution** -> Track progress, move to Done when shipped

## After Implementation

After completing any non-trivial implementation:

1. Test in Local by Flywheel environment
2. Test with different WooCommerce versions
3. Run `/review-staged` for independent AI code review
4. Fix any Critical/High issues found
5. Report summary to user (ready for them to commit)

See `development.md` for full details.
