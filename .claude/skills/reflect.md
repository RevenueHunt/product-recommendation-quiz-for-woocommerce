# Learning Capture Skill

Capture learnings from development sessions to improve future interactions.

## When to Use

- After completing a feature or fixing a bug
- When discovering new patterns or approaches
- After making mistakes that should be avoided
- When user provides corrections or feedback

## Signal Categories

### 1. Corrections (High Priority)

User explicitly corrected an approach or output:
- "No, not like that"
- "Actually, I meant..."
- "That's wrong because..."

### 2. Successes (Medium Priority)

User expressed satisfaction:
- "Perfect!"
- "Great, exactly what I needed"
- Accepted output without modification

### 3. Edge Cases (Medium Priority)

Unexpected scenarios discovered:
- WooCommerce version differences
- WordPress version differences
- Plugin conflicts
- Theme compatibility issues

### 4. Preferences (Low Priority)

User preferences for this project:
- Coding style preferences
- Communication style
- Workflow preferences

## Capture Format

When capturing learnings, document:

```markdown
## Learning: {Brief Title}

**Date**: YYYY-MM-DD
**Category**: Correction | Success | Edge Case | Preference
**Confidence**: High | Medium | Low

### Context
What was happening when this was discovered.

### Learning
What was learned and why it matters.

### Application
How to apply this in the future.

### Evidence
Quote or reference to the conversation.
```

## Storage Location

Learnings should be added to relevant rule files in `.claude/rules/` if they represent:
- Code patterns to follow
- Patterns to avoid
- Project-specific conventions

Or documented in `.project/archive/learnings/` for historical reference.

## Triggering Reflection

The orchestrator should reflect when:
- Session is ending
- Context is about to be compacted
- Explicit correction is detected
- Significant milestone is reached

## Anti-Patterns

Do NOT capture:
- Trivial preferences
- One-time issues unlikely to recur
- Learnings that contradict established rules
- Vague or unclear feedback
