---
description: High-value roadmap overview - low-hanging fruit, prioritized by category
allowed-tools: ['Read', 'Bash']
---

# Roadmap Overview

Analyze `.project/planning/ROADMAP.md` and provide a visually scannable, prioritized overview. Use emoji icons and status indicators to help users quickly distinguish between task types and priorities.

## Procedure

### Step 1: Read Roadmap

Read the full roadmap file:

```
.project/planning/ROADMAP.md
```

### Step 2: Extract and Categorize Tasks

Parse tasks from these sections (in priority order):

1. **DOING** - Currently in progress
2. **CURRENT SPRINT** - Committed for this sprint
3. **BACKLOG** - Planned but not scheduled

Skip: VISION, NOT PRIORITY, DONE sections.

### Step 3: Identify Strategic Priorities

**Critical Priority** (show first):

- Items blocking other work
- Items with `critical` label
- WordPress.org review blockers
- Security fixes

**High Priority**:

- Items with `bug` or `high` label
- WooCommerce compatibility issues
- Items reducing support burden

**Medium Priority**:

- Technical debt with clear ROI
- Performance improvements
- Quality improvements

### Step 4: Identify Recommended Path

Look for dependency chains that unlock high business value:

- What blockers prevent the highest-value item?
- What sequence of tasks maximizes value delivery?

### Step 5: Output Visual Report

Use tables with emoji icons for visual scanning. Include:

1. Current focus (what's in progress)
2. High-value features grouped by priority
3. Recommended path (strategic sequence)

## Output Format

**MANDATORY OUTPUT STRUCTURE**:

```
# Roadmap Overview

### 🔵 In Progress (Doing)

| Task | Owner | Started |
|------|-------|---------|
| 📦 **Task name** - brief desc | @owner | YYYY-MM-DD |

---

## HIGH VALUE FEATURES

#### 🔴 Critical Priority

| Feature | Business Value | Status |
|---------|----------------|--------|
| 🔄 **Task name** | Value description | ⏸️ Blocked |

#### 🟠 High Priority

| Feature | Business Value | Status |
|---------|----------------|--------|
| 📦 **Feature 1** | Value description | 📋 Sprint |

#### 🟠 High Priority - Bugs

| Bug | Impact | Status |
|-----|--------|--------|
| 🐛 **Bug description** | Impact description | 📋 Sprint |

#### 🟡 Technical Debt

| Item | Impact | Status |
|------|--------|--------|
| ⚡ **Tech debt 1** | Impact description | 📥 Backlog |

---

## 🎯 NEXT STEPS

┌─────────────────────────────────────────────────────────┐
│  STRATEGIC GOAL - highest business value unlock         │
│                                                         │
│  1. 📦 First step                                       │
│  2. 📦 Second step                                      │
│  3. 🔄 Final delivery                                   │
└─────────────────────────────────────────────────────────┘

**Quick Wins:** 🐛 Bug fix │ 📦 Small feature │ ⚡ Performance
```

### Task Type Icons

| Icon | Use When |
|------|----------|
| 📦 | Features, new functionality |
| 🔌 | Integrations, API endpoints |
| 🔄 | WooCommerce compatibility, upgrades |
| 🐛 | Bugs, fixes |
| 🔒 | Security |
| 📝 | Documentation, README updates |
| ⚡ | Performance, optimization |
| 🌐 | i18n, translations |

### Status Icons

| Icon | Meaning |
|------|---------|
| ⏸️ | Blocked by dependency |
| 📋 | In Current Sprint |
| 📥 | In Backlog |
| 🔵 | In Progress |
| ✅ | Complete |

### Priority Colors

| Color | When to Use |
|-------|-------------|
| 🔴 Critical | Blockers, security, WordPress.org review |
| 🟠 High | Bugs, compatibility, high-value features |
| 🟡 Medium | Tech debt, quality improvements |
| 🟢 Low | Nice-to-haves, minor improvements |

## Notes

- Tables provide better visual scanning than tree structures
- Emoji icons help distinguish task types at a glance
- Status icons show where items are in the pipeline
- Recommended path gives strategic direction
- Keep descriptions concise but meaningful
- ALL tasks must be in tables, never bullet lists

## Final Reminder

**TABLES ARE MANDATORY FOR ALL TASK LISTS.** If you find yourself typing `-` or `*` followed by a task description, STOP and convert it to a table row instead.
