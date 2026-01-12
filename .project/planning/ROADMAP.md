# Product Roadmap

A Kanban-style backlog of features and initiatives organized by status.

> **Flow**: Vision -> Not Priority -> Backlog -> Current Sprint -> Doing -> Done

---

## Format Reference

**Backlog cards:**

```markdown
- [ ] **Card Title** `label` @assignee
      Description or context.
```

**Active tasks (Doing section):**

```markdown
- [ ] (A) Task description +Project @context started:YYYY-MM-DD @assignee
      Details and context...
```

| Element    | Description              | Example                         |
|------------|--------------------------|--------------------------------|
| `(A)`      | Priority A-D (A=highest) | `(A)`, `(B)`                   |
| `+Project` | Project/feature tag      | `+OAuth`                       |
| `@context` | Area tag                 | `@admin`, `@api`, `@dev`       |
| `@assignee`| Team member              | `@angel`, `@team`              |
| `started:` | Start date               | `started:2026-01-12`           |
| `due:`     | Due date                 | `due:2026-01-20`               |
| `blocked:` | Blocker                  | `blocked:wc-approval`          |

---

### VISION

<!-- Long-term ideas. Things that need to be done eventually but not soon. -->

- [ ] **Multi-quiz support**
      Allow stores to embed multiple quizzes on different pages.

- [ ] **Quiz analytics dashboard**
      In-plugin analytics showing quiz performance.

- [ ] **Advanced product filtering**
      More sophisticated product recommendation algorithms.

### NOT PRIORITY

<!-- Nice-to-haves. If not implemented, nothing bad happens. -->

- [ ] **Admin UI redesign**
      Modernize the plugin admin interface.

- [ ] **Shortcode builder**
      Visual builder for quiz shortcodes.

### BACKLOG

<!-- Needs implementation. Timing to be discussed. -->

- [ ] **WooCommerce Blocks compatibility**
      Ensure compatibility with WooCommerce Blocks.

- [ ] **Performance optimization**
      Reduce embed.js load time impact.

- [ ] **Improved error handling**
      Better error messages for authentication failures.

- [ ] **WordPress.org compliance review**
      Ensure all code meets WordPress.org guidelines.

### CURRENT SPRINT

<!-- Tasks committed for the current sprint cycle. BUGS are always priority! -->

#### Bugs (Priority)

<!-- Any item with `bug` label goes here first -->

#### Features & Tasks

- [ ] **Update tested up to versions**
      Test with latest WordPress and WooCommerce.

### DOING

<!-- Actively being worked on right now. Use todo.txt syntax with priorities and dates. -->

**Sprint Goal**: Initial setup and documentation
**Sprint Dates**: 2026-01-12 to TBD

- [ ] (A) Set up Claude Code configuration +Setup @dev started:2026-01-12
      Configure .claude folder, rules, and commands for the WordPress plugin.

### DONE WEEK 2026-01-06

<!-- Completed in week starting Monday 2026-01-06 -->

---

## How to Use

### Bug Policy

**BUGS ARE ALWAYS PRIORITY.** Any item with `BUG:`, `Fix:`, `fix:`, or `bug` label goes directly to **Current Sprint**, never to Backlog or Not Priority.

### Adding New Items

1. Add cards to **Vision** or **Backlog** when ideas come up
2. Include labels if relevant (e.g., `bug`, `woocommerce`, `security`)
3. Add brief description and key context
4. **If it's a bug** -> Add directly to Current Sprint with `bug` label

### Promoting Cards

1. **Vision -> Backlog**: When it becomes a real priority
2. **Not Priority -> Backlog**: When circumstances change
3. **Backlog -> Current Sprint**: During sprint planning
4. **Current Sprint -> Doing**: When work starts, add `started:` date and priority
5. **Doing -> Done Week XX**: When shipped

### Starting Work

1. Pick task from **Current Sprint** (highest priority first)
2. Move to **Doing**, add `(A)` priority, `+Project`, `@context`, `started:YYYY-MM-DD`
3. If task needs PRD: Ask Claude to create one (uses `prd.md` skill)

### Completing Work

1. Mark task `- [x]`
2. Move to **Done WEEK YYYY-MM-DD** (use Monday's date)
3. Add commit reference in parentheses
4. Archive old weeks periodically

---

## Archive

<details>
<summary>Older releases (click to expand)</summary>

<!-- Move old Done Week sections here to keep the board clean -->

</details>
