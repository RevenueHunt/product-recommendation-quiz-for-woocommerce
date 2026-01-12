# PRD Creation Skill

Create Product Requirements Documents for new features and initiatives.

## When to Use

- Before implementing significant features
- When planning changes that affect multiple files
- For features that need stakeholder alignment

## PRD Template

Create a new file at `.project/planning/prd-{feature-name}.md`:

```markdown
# PRD: {Feature Name}

**Status**: Draft | In Review | Approved | In Progress | Complete
**Author**: {name}
**Created**: {date}
**Last Updated**: {date}

## Problem Statement

What problem are we solving? Who is affected? What's the current pain point?

## Goals

1. Primary goal
2. Secondary goal
3. Success metric

## Non-Goals

What we explicitly will NOT do in this iteration:
- Non-goal 1
- Non-goal 2

## Proposed Solution

### Overview

High-level description of the approach.

### Implementation Details

#### Files to Modify/Create

| File | Changes |
|------|---------|
| `includes/class-....php` | Main plugin modifications |
| `admin/class-...-admin.php` | Admin UI changes |
| `public/class-...-public.php` | Frontend changes |

#### WordPress/WooCommerce Integration

- Hooks needed
- REST endpoints to add/modify
- Options to store
- WooCommerce compatibility considerations

### Design Considerations

- WooCommerce version compatibility
- WordPress version compatibility
- Backward compatibility
- Performance impact

## Alternatives Considered

| Approach | Pros | Cons | Why Not |
|----------|------|------|---------|
| Alternative 1 | Pro | Con | Reason |

## Risks & Mitigations

| Risk | Impact | Likelihood | Mitigation |
|------|--------|------------|------------|
| Risk 1 | High/Med/Low | High/Med/Low | How to mitigate |

## Timeline

| Phase | Tasks | Estimate |
|-------|-------|----------|
| Phase 1 | Core implementation | - |
| Phase 2 | Testing & QA | - |

## Open Questions

- [ ] Question 1?
- [ ] Question 2?

## Appendix

### References

- Link to related docs
- WordPress Codex references
- WooCommerce documentation
```

## Usage

When asked to create a PRD:

1. Gather requirements through questions
2. Create the file using the template above
3. Fill in all sections based on discussion
4. Save to `.project/planning/prd-{feature-name}.md`
5. Add reference to roadmap if applicable

## File Naming

- Use kebab-case: `prd-oauth-improvements.md`
- Keep names descriptive but concise
- Prefix with `prd-` for easy identification
