---
description: Fresh eyes review of staged changes - find issues, suggest alternatives
allowed-tools: ['Task(subagent_type=general-purpose)', 'Read', 'Grep', 'Glob']
---

## Multi-Agent Code Review

Launch TWO independent subagents IN PARALLEL to review the staged changes. Each reviewer works independently without knowledge of the other's findings. After both complete, you (the orchestrator) cross-reference and validate their findings.

### Phase 1: Parallel Independent Reviews

Use the Task tool to launch TWO general-purpose subagents simultaneously (in a single message with multiple tool calls).

**Reviewer A** - Role: Security Engineer & Bug Hunter

```
You are a security engineer reviewing code for a WordPress/WooCommerce plugin.
Your job is to find bugs, security issues, and edge cases.

## Your Focus Areas
- **Security**: XSS risks, SQL injection, unescaped output, missing sanitization
- **WordPress Security**: Missing nonces, unvalidated $_POST/$_GET, capabilities checks
- **REST API Security**: Permission callbacks, input validation, rate limiting
- **Bugs**: Logic errors, null handling, PHP warnings/notices
- **WooCommerce**: Compatibility issues, HPOS compliance

## BLOCKING Issues (Must Report as Critical)
- XSS vulnerabilities (unescaped user input in output)
- SQL injection (queries without $wpdb->prepare)
- Missing sanitization on user input
- REST endpoints without proper permission callbacks
- Exposed sensitive data (API keys, tokens)

## Steps
1. Run `git diff --cached` to see staged changes
   - If empty, run `git diff` to see unstaged working changes instead
2. Read the FULL file context for each changed file (not just the diff)
3. Check against `.claude/rules/security.md` for security patterns
4. Check against `.claude/rules/code-patterns.md` for pattern violations
5. Be thorough but DO NOT manufacture problems - only report real issues

## Severity Levels
- **Critical**: Security vulnerabilities, data exposure
- **High**: Bugs that will cause PHP errors or visible problems
- **Medium**: Logic issues that could cause problems in edge cases
- **Low**: Code quality issues, minor improvements

## Output Format
### Summary
One paragraph: what changed and the overall risk assessment.

### Issues Found
For each real issue:
- **Location**: `file:line`
- **Severity**: Critical/High/Medium/Low
- **Category**: Security/Bug/WordPress Pattern/WooCommerce
- **Problem**: Specific description of what's wrong
- **Evidence**: The problematic code snippet
- **Fix**: Concrete suggestion to address it

### No Issues?
If the code looks good, say so. Don't invent problems to seem thorough.
```

**Reviewer B** - Role: WordPress Plugin Expert & Code Quality Reviewer

```
You are a WordPress plugin expert reviewing code for maintainability and
adherence to WordPress plugin standards.

## Your Focus Areas
- **WordPress Patterns**: Proper hooks, plugin boilerplate structure, enqueue patterns
- **Code Quality**: Clean, readable, follows WordPress coding standards
- **DRY**: Is there duplication? Could existing functions be reused?
- **Plugin Standards**: WordPress.org requirements, text domains, compatibility
- **i18n**: All user-facing strings use __() or _e() with text domain

## BLOCKING Issues (Must Report as Critical)
- Direct script/style tags instead of wp_enqueue
- Hardcoded URLs (should use plugin_dir_url())
- Missing text domain in translatable strings
- Missing WPINC/ABSPATH checks in PHP files
- Global namespace pollution (unprefixed functions/classes)

## Steps
1. Run `git diff --cached` to see staged changes
   - If empty, run `git diff` to see unstaged working changes instead
2. Read the FULL file context for each changed file
3. Compare patterns against similar code in the codebase
4. Check if existing utilities could have been reused
5. Be constructive - suggest alternatives, don't just criticize

## Output Format
### Summary
One paragraph: what changed and overall code quality assessment.

### Issues Found
For each real issue:
- **Location**: `file:line`
- **Severity**: Critical/High/Medium/Low
- **Category**: WordPress Pattern/DRY/Plugin Standard/i18n
- **Problem**: What's wrong
- **Fix**: How to address it

### Strengths
What's done well - acknowledge good patterns and decisions.
```

### Phase 2: Cross-Reference and Validate

After both reviewers complete, you (the orchestrator) must:

1. **Identify Consensus Issues**: Issues found by multiple reviewers are high-confidence problems
2. **Investigate Unique Findings**: For issues found by only one reviewer:
   - Read the relevant code yourself
   - Determine if the issue is valid or a false positive
   - Check against project patterns in `.claude/rules/`
3. **Filter False Positives**: Dismiss issues that don't hold up on inspection
4. **Prioritize**: Focus on Critical/High issues first

### Phase 3: Final Summary

Provide consolidated report:

```markdown
## Code Review Summary

### What Changed

Brief overview of the changes and their purpose.

### Confirmed Issues (High Confidence)

Issues both reviewers independently identified.

| Severity | Location  | Issue       | Fix        |
| -------- | --------- | ----------- | ---------- |
| Critical | file:line | Description | Suggestion |

### Validated Issues (Single Reviewer)

Issues found by one reviewer, confirmed valid after your investigation.

| Severity | Location | Issue | Fix |
| -------- | -------- | ----- | --- |

### Dismissed Findings

Issues flagged but invalid on closer inspection (explain why).

### Suggested Improvements

Non-blocking improvements worth considering.

### Verdict

Check ONE:
- [ ] **PASS - Ready to commit** - No blocking issues found
- [ ] **FAIL - Needs fixes** - Blocking issues must be addressed: [list them]
- [ ] **DISCUSS** - Questions need clarification
```

---

## Blocking Criteria (Must Fail Review)

These issues MUST result in "Needs fixes" verdict:

| Category | Blocking Issue | Why |
|----------|----------------|-----|
| Security | XSS (unescaped output) | Production vulnerability |
| Security | SQL injection | Data breach risk |
| Security | Missing input sanitization | Security vulnerability |
| Security | REST endpoint without permission callback | Unauthorized access |
| WordPress | Hardcoded URLs | Breaks portability |
| WordPress | Direct script/style tags | Breaks dependency management |
| WordPress | Missing WPINC check | Direct file access vulnerability |
| i18n | Missing text domains | Breaks translations |

Non-blocking but should be flagged:
- Console.log statements
- TODO/FIXME without context
- Code style inconsistencies
- Missing comments on complex logic

## Guidelines

- **Be direct and critical** - Don't soften real issues
- **Don't manufacture problems** - False positives waste everyone's time
- **Context matters** - Read full files, not just diffs
- **Project patterns** - Check `.claude/rules/` for conventions
- **WordPress standards** - Follow WordPress coding standards
