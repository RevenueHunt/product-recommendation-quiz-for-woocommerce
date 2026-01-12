# Sub-Agent Orchestration Strategy

Multi-model orchestration approach where Opus serves as the main orchestrator.

## Architecture

### Main Orchestrator: Opus

- **Role**: Central coordinator and final verifier
- **Responsibilities**:
  - Orchestrating all sub-agents
  - Performing deep research based on initial reconnaissance
  - Strategic delegation of tasks
  - Final verification of completed work
  - Providing summary to user

### Sub-Agent Roles

| Agent | Model | Purpose |
|-------|-------|---------|
| Initial Research | Sonnet | Quick reconnaissance, identify files/folders, gather context |
| Implementation | Haiku | Execute changes, fix bugs, work in parallel |
| Verification | Sonnet | Quality assurance, identify issues, verify fixes |

## Workflow Process

1. **Initial Research** - Sonnet identifies relevant files and dependencies
2. **Deep Analysis** - Opus analyzes research, develops strategy, identifies parallelizable tasks
3. **Implementation** - Haiku agents implement changes (in parallel when possible)
4. **Verification Loop** - Sonnet verifies -> issues found -> Haiku fixes -> Sonnet re-verifies
5. **Final Review** - Opus validates overall solution quality
6. **Completion** - Opus provides summary to user

## Core Patterns

### Routing

Route requests to appropriate workflows based on intent classification:

- Treat routing as a **classification problem** with defined labels and decision criteria
- Prefer cheaper/faster models (Haiku) for simple routing decisions
- Add **confidence thresholds**: if low confidence -> ask clarifying question or escalate
- Keep routing tables explicit: intent -> agent/workflow mapping
- Record routing decisions (label + confidence + reason) for debugging

### Parallelization

Execute independent tasks concurrently for latency reduction:

- **Only parallelize truly independent tasks** - verify no hidden dependencies
- **Cap concurrency** to avoid rate limits and cost blowups
- Add **timeouts + partial-success logic**: proceed if N-of-M branches succeed
- Standardize merge formats: each branch returns structured output
- Use **correlation IDs** across branches for tracing

### Reflection (Producer-Critic)

Improve output quality through structured feedback loops:

1. **Producer** generates initial output
2. **Critic** evaluates against rubric (correctness, completeness, style, safety)
3. **Refiner** addresses critique issues
4. Repeat until stopping criteria met

**Critique discipline:**
- Use a structured **rubric** for evaluation
- Output structured issues list + fix suggestions (not rewrites)
- Define **stopping criteria**: max iterations OR "no critical issues found"
- Store intermediate versions for audit/debugging

### Planning

Generate and execute structured plans for complex tasks:

- **Plan before acting** when task has dependencies or multiple steps
- Plans must include: steps, tools needed, expected outputs, success criteria
- Track progress explicitly with state machine or step tracker
- **Allow replanning** if tool results contradict assumptions
- Avoid overplanning: right granularity, no unnecessary steps

## Key Principles

- **Parallel Processing**: Launch multiple agents simultaneously when tasks are independent
- **Iterative Refinement**: Use verification loops to ensure quality
- **Model Specialization**:
  - Sonnet for research and verification (balanced speed and capability)
  - Haiku for implementation (fast execution)
  - Opus for orchestration and final review (highest capability)
- **Root Cause Resolution**: Never hide bugs with hacks or workarounds. Always fix the root cause.

## Composable Workflows

### Router -> Specialist Pipeline

1. Router classifies request intent
2. Route to specialized agent/tool chain
3. Return response + log routing decision

Use when: broad input variety with distinct workflows.

### Plan -> Execute -> Monitor -> Reflect

1. Planner generates step list with success criteria
2. Executor runs steps (tools/agents)
3. Monitor checks progress vs goal
4. Reflect fixes mistakes; optionally replan

Use when: multi-step tasks with dependencies and quality requirements.

### Parallel Research -> Merge -> Critique

1. Parallel branches gather/summarize sources independently
2. Merger synthesizes structured report
3. Critic checks completeness/logic; refiner updates

Use when: time-sensitive research/synthesis tasks.

## Context Engineering

Good agent performance requires actively constructing context:

- **Explicit context**: user request, task state, tool outputs, retrieved docs
- **Implicit context**: session history, environment state, constraints
- **Never assume missing context**: explicitly list needed files/inputs when absent
- Keep context staging reproducible and inspectable
