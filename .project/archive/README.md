# Archive Organization

Historical documentation, completed work, and reference materials.

## Structure

```
archive/
├── migrations/          # Migration completion summaries
├── refactoring/         # Major refactoring phase reports
├── plans/               # Detailed planning documents
└── docs/
    ├── completed/       # Completed implementation plans
    ├── migrations/      # Detailed migration documentation
    ├── investigations/  # Bug investigation reports
    └── refactoring/     # Feature-specific refactoring docs
```

## Folder Descriptions

### `/migrations/` - Migration Summaries

High-level completion records for major migrations:
- WordPress version compatibility updates
- WooCommerce API changes
- Plugin architecture changes

### `/refactoring/` - Phase Reports

Major refactoring analysis and completion reports:
- Code cleanup phases
- Security improvement reports
- Architecture changes

### `/docs/completed/` - Implementation Plans

Successfully completed implementation documentation:
- Feature implementations
- API endpoint additions
- Integration work

### `/docs/migrations/` - Migration Details

Detailed technical documentation for migrations:
- Step-by-step procedures
- Rollback plans
- Testing checklists

### `/docs/investigations/` - Bug Reports

Investigation reports for bugs and issues:
- Root cause analysis
- Compatibility investigations
- WooCommerce/WordPress conflicts

### `/docs/refactoring/` - Feature Refactoring

Documentation for specific feature refactoring:
- Before/after comparisons
- Decision rationale
- Test coverage notes

## When to Archive

Move documentation here when:

1. **Implementation is complete** - The feature is shipped and stable
2. **Migration is finished** - No longer need active reference
3. **Investigation is closed** - Issue is resolved
4. **Reference value only** - Useful for historical context but not active work

## Finding Information

1. **Recent work** -> Check active docs in `docs/`
2. **How something was built** -> Check `docs/completed/`
3. **Why a decision was made** -> Check `docs/investigations/`
4. **Major system changes** -> Check `migrations/` or `refactoring/`
