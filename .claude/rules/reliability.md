# Reliability & Safety Patterns

Production-grade patterns for exception handling, guardrails, and recovery.

## Exception Handling & Recovery

Agents operate in messy environments. Failures are inevitable; recovery must be designed.

### Error Classification

| Type | Examples | Strategy |
|------|----------|----------|
| Transient | Timeouts, 429 rate limits, network blips | Retry with backoff |
| Persistent | Bad credentials, missing permissions | Escalate or fail fast |
| Logical | Bad plan, wrong assumptions | Replan or ask clarification |
| Safety | Policy violation, harmful output | Block + escalate |

### Retry Strategy

- **Exponential backoff + jitter** for transient errors
- Cap retries (3-5 max); switch to fallback early on certain error types
- Use **timeouts** per operation to prevent hanging
- Implement **circuit breakers** for repeatedly failing dependencies

### Recovery Patterns

- **Persist state for long tasks**: checkpoints, resumable steps, replayable tool calls
- **Compensate instead of undo**: use compensating transactions (e.g., cancel a booking)
- **Graceful degradation**: return partial results with clear "what's missing" and next steps
- **Escalate when necessary**: human review for repeated failures or safety-sensitive cases

### Pitfalls to Avoid

- Infinite retry loops
- Silent partial failures (no audit trail)
- Non-idempotent actions causing duplicates

## Guardrails (Layered Defense)

Implement guardrails at multiple layers to prevent harmful or incorrect behavior.

### Layer 1: Input Validation

- Sanitize user inputs
- Detect prompt-injection patterns
- Enforce input schemas
- Reject ambiguous or malformed requests

### Layer 2: Planning Constraints

- Constrain allowed tool intents
- Require justification for risky actions
- Validate plans against scope and permissions

### Layer 3: Tool Restrictions

- Use explicit **allowlist** of tools; reject unknown tool names
- Validate parameters against schemas
- Restrict endpoints and operations
- Enforce rate limits per tool

### Layer 4: Output Filtering

- Enforce output formatting requirements
- Filter unsafe or inappropriate content
- Require citations for factual claims
- Validate outputs against expected schemas

### Layer 5: Escalation

- Human-in-the-loop approval for irreversible actions
- Confidence thresholds to trigger review
- Clear escalation paths for edge cases

## Human-in-the-Loop (HITL)

HITL adds human oversight at the right moments for safety and trust.

### When to Use

- Irreversible actions (deployments, data deletion, production changes)
- High-stakes domains or sensitive data
- Low-confidence outputs
- Novel or rare edge cases

### Implementation

1. Define **approval gates** (who approves what)
2. Use **confidence thresholds** to trigger review
3. Provide reviewers with:
   - Evidence (sources, tool outputs)
   - Proposed action + rationale
   - Options: approve / reject / edit / request info
4. Log decisions for **audit trail**
5. Turn feedback into improvements (update prompts, routing)

### Balance

- Overusing HITL -> slow UX
- Underusing HITL -> safety risk
- Target HITL for high-impact, low-confidence scenarios

## Observability

Structured logging and tracing are non-negotiable for production agents.

### What to Log

- Tool calls and outputs
- Routing decisions (label + confidence + reason)
- Intermediate decisions and validations
- Errors and recovery paths
- Resource usage (tokens, latency, cost)

### Best Practices

- Use **correlation IDs** across all operations in a task
- Structured logs (JSON) for machine parsing
- Log safely (no secrets, PII redaction)
- Maintain audit logs for sensitive workflows

## Least Privilege

Grant minimum permissions required for each task:

- Define tool permissions per agent role
- Use explicit allowlists for operations
- Deny by default; grant explicitly
- Scope access to required resources only

## Idempotency

Ensure repeated operations produce the same result:

- Include **idempotency keys** in write operations
- Design tool calls to be safely retryable
- Track operation state to prevent duplicate side effects
- Use optimistic locking where applicable

## Production Readiness Checklist

- [ ] Least privilege tool permissions
- [ ] Guardrails at input/output/tool layers
- [ ] HITL for high-risk actions
- [ ] Structured logs + trace IDs
- [ ] Retries/backoff + timeouts + fallbacks
- [ ] Idempotency for write operations
- [ ] Evaluation plan: offline tests + monitoring
