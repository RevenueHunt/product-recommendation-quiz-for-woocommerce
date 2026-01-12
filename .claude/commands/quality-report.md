---
description: Code quality dashboard - comprehensive health metrics across the plugin
allowed-tools: ['Bash', 'Read', 'Glob', 'Grep', 'Task', 'Write']
argument-hint: '[options]'
---

# Quality Report

Generates comprehensive code quality report for the WordPress plugin.
**Outputs an HTML report and opens it automatically:**
- `.project/reports/quality-dashboard.html` - Quality metrics, file inventory, issues

## Usage

```
/quality-report [options]
```

**Options:**

- (no args) - Full dashboard with all metrics
- `--quick` - Skip slow analysis

## Action: $ARGUMENTS

## Output

HTML report is generated:

```
.project/reports/quality-dashboard.html   # Quality metrics
```

The report is automatically opened in the default browser when complete.

## Metrics Collected

### 1. File Inventory

```bash
# PHP files
find . -name "*.php" -not -path "./.git/*" | wc -l

# CSS files
find . -name "*.css" -not -path "./.git/*" | wc -l

# Lines of code
find . -name "*.php" -not -path "./.git/*" -exec wc -l {} + | tail -1
```

### 2. PHP Code Quality

```bash
# Missing text domains (i18n)
grep -rn "__(" --include="*.php" . | grep -v "product-recommendation-quiz-for-ecommerce" | wc -l

# Direct echo without escaping
grep -rn "echo \$" --include="*.php" . | grep -v "esc_" | wc -l

# Unescaped output
grep -rn "<?=" --include="*.php" . | wc -l
```

### 3. Security Checks

```bash
# SQL queries without prepare
grep -rn "wpdb->" --include="*.php" . | grep -v "prepare" | wc -l

# Missing nonces
grep -rn "\$_POST\|\$_GET\|\$_REQUEST" --include="*.php" . | wc -l

# Potential XSS (unescaped output)
grep -rn "echo.*\$_" --include="*.php" . | wc -l

# Direct file access without WPINC check
grep -rL "WPINC\|ABSPATH" --include="*.php" . 2>/dev/null | wc -l
```

### 4. Technical Debt Indicators

```bash
# TODO/FIXME/HACK comments
grep -rn "TODO\|FIXME\|HACK" --include="*.php" . | wc -l

# Large files (>300 LOC)
find . -name "*.php" -not -path "./.git/*" -exec wc -l {} \; | awk '$1 > 300 {print}'

# Console.log statements (in any JS)
grep -rn "console.log" --include="*.js" . | wc -l
```

### 5. WordPress Plugin Patterns

```bash
# Missing wp_enqueue for scripts/styles
grep -rn "<script" --include="*.php" . | grep -v "wp_enqueue" | wc -l

# Hardcoded URLs
grep -rn "http://" --include="*.php" . | grep -v "schema.org\|xmlns" | wc -l

# Missing permission callbacks
grep -rn "register_rest_route" --include="*.php" . | wc -l
```

## Procedure

### Phase 1: File Inventory

```bash
echo "=== FILE INVENTORY ==="
echo "PHP files: $(find . -name '*.php' -not -path './.git/*' | wc -l)"
echo "CSS files: $(find . -name '*.css' -not -path './.git/*' | wc -l)"
echo "JS files: $(find . -name '*.js' -not -path './.git/*' | wc -l)"
```

### Phase 2: Code Quality Scan

Run all quality checks and collect metrics.

### Phase 3: Security Audit

Run security-focused checks.

### Phase 4: Generate HTML Dashboard

Create `.project/reports/quality-dashboard.html` with all collected metrics.

### Phase 5: Open Report

```bash
mkdir -p .project/reports && open .project/reports/quality-dashboard.html
```

## HTML Report Template

```html
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quality Report - PRQ Plugin</title>
    <style>
      :root {
        --green: #4ade80;
        --yellow: #facc15;
        --red: #f87171;
        --blue: #60a5fa;
        --bg: #0f172a;
        --card-bg: #1e293b;
        --text: #f1f5f9;
        --text-muted: #94a3b8;
        --border: #334155;
      }
      * { box-sizing: border-box; margin: 0; padding: 0; }
      body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background: var(--bg);
        color: var(--text);
        line-height: 1.6;
        padding: 2rem;
      }
      .container { max-width: 1200px; margin: 0 auto; }
      h1 { font-size: 2rem; margin-bottom: 0.5rem; }
      .subtitle { color: var(--text-muted); margin-bottom: 2rem; }
      .grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
      }
      .card {
        background: var(--card-bg);
        border-radius: 8px;
        padding: 1.5rem;
        border: 1px solid var(--border);
      }
      .card-title { font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.5rem; }
      .card-value { font-size: 2rem; font-weight: 700; }
      .status-pass { color: var(--green); }
      .status-warn { color: var(--yellow); }
      .status-fail { color: var(--red); }
      .section {
        background: var(--card-bg);
        border-radius: 8px;
        padding: 1.5rem;
        border: 1px solid var(--border);
        margin-bottom: 1.5rem;
      }
      .section-title { font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem; }
      table { width: 100%; border-collapse: collapse; }
      th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid var(--border); }
      th { font-weight: 600; color: var(--text-muted); font-size: 0.875rem; }
      .file-list { font-family: monospace; font-size: 0.875rem; list-style: none; }
      .file-list li { padding: 0.5rem 0; border-bottom: 1px solid var(--border); }
    </style>
  </head>
  <body>
    <div class="container">
      <h1>📊 Quality Report</h1>
      <p class="subtitle">Generated: {{TIMESTAMP}} | Product Recommendation Quiz Plugin</p>

      <!-- File Inventory -->
      <div class="grid">
        <div class="card">
          <div class="card-title">PHP Files</div>
          <div class="card-value">{{PHP_COUNT}}</div>
        </div>
        <div class="card">
          <div class="card-title">CSS Files</div>
          <div class="card-value">{{CSS_COUNT}}</div>
        </div>
        <div class="card">
          <div class="card-title">Total LOC</div>
          <div class="card-value">{{TOTAL_LOC}}</div>
        </div>
      </div>

      <!-- Code Quality -->
      <div class="section">
        <h2 class="section-title">🔍 Code Quality</h2>
        <table>
          <thead>
            <tr><th>Check</th><th>Count</th><th>Status</th></tr>
          </thead>
          <tbody>
            {{QUALITY_ROWS}}
          </tbody>
        </table>
      </div>

      <!-- Security -->
      <div class="section">
        <h2 class="section-title">🔒 Security Checks</h2>
        <table>
          <thead>
            <tr><th>Check</th><th>Count</th><th>Priority</th></tr>
          </thead>
          <tbody>
            {{SECURITY_ROWS}}
          </tbody>
        </table>
      </div>

      <!-- Large Files -->
      <div class="section">
        <h2 class="section-title">📄 Large Files (>300 LOC)</h2>
        <ul class="file-list">
          {{LARGE_FILES_LIST}}
        </ul>
      </div>

      <!-- Technical Debt -->
      <div class="section">
        <h2 class="section-title">🔧 Technical Debt</h2>
        <div class="grid">
          <div class="card">
            <div class="card-title">TODO</div>
            <div class="card-value">{{TODO_COUNT}}</div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
```

## Integration

Called by:

- Manual invocation for status check
- Before releases
- Periodic health monitoring

## Notes

- No build tools in this project - checks are file-based
- Focus on WordPress plugin coding standards
- Security checks are critical for WordPress.org approval
- Report is saved to `.project/reports/quality-dashboard.html`
