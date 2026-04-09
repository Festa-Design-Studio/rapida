# RAPIDA Builder Skill — Design Specification

**Date:** 2026-03-26
**Author:** Abayomi (Festa Design Studio) + Claude
**Status:** Draft — pending approval
**Project:** UNDP "Build the Future of Crisis Mapping" Challenge Submission

---

## Context

The RAPIDA UI design system is fully documented in Notion across 4 token pages and 41 atomic design component pages. The 4 token skills (rapida-colors, rapida-typography, rapida-layout-spacing, rapida-states) are already built and define the design tokens. What's missing is a skill that tells Claude HOW to build components — the workflow, directory structure, rules, and source of truth references.

Without this skill, Claude may improvise design decisions, hardcode values, create files in wrong locations, or skip accessibility requirements. The builder skill eliminates all of that by enforcing a strict, Notion-driven build process.

---

## Skill Identity

**Name:** `rapida-builder`
**Location:** `.claude/skills/rapida-ui/builder/SKILL.md`
**Format:** Monolithic SKILL.md (~200 lines)

**Trigger description:**
> This skill builds the interface a person in crisis will use. Every component — from a single button to a full page — must be built exactly as documented in the Notion design system. No improvisation. No hardcoded values. No design decisions by Claude. Invoke whenever building, creating, or implementing any UI component, page, template, or design system element in this project. Also invoke when asked to "build," "create," "implement," or "add" any atom, molecule, organism, or template. Covers: the complete build workflow from Notion spec to tested Blade/Livewire component. Skip for backend-only work, database, API routes, or token file updates.

**Metadata:**
```yaml
license: MIT
metadata:
  author: festa-design-studio
```

---

## Source of Truth

Two Notion pages serve as the source of truth. The skill stores their URLs and instructs Claude to fetch them before any build.

| Source | Notion URL | Purpose |
|--------|-----------|---------|
| Atomic Design System | `https://www.notion.so/74707bf1b4db45cf9a1207c3826cc544` | Component registry — all 41 component pages with full specs |
| Design Brainstorm & Ideation Log | `https://www.notion.so/e1a234419fea478d85731a495d721c05` | Decisions made, open questions, discarded directions, rationale |

**Fetch protocol:**
1. When asked to build a specific component, fetch its Notion page directly if the URL is known
2. If the URL is not known, fetch the Atomic Design System root page first to find the correct sub-page link
3. Also fetch the Design Brainstorm & Ideation Log to check for decisions or open questions affecting the component
4. Never build from memory or assumption — if the Notion page hasn't been fetched in this conversation, fetch it

---

## Build Workflow

Mandatory 4-step linear process. No step can be skipped.

### Step 1: Fetch Spec from Notion

- Use the Notion MCP tools (`notion-fetch`) to retrieve the specific component page
- Read the full page content: description, props, variants, states, accessibility requirements, microcopy, usage examples
- Check the Design Brainstorm & Ideation Log for any decisions or open questions about this component

### Step 2: Identify Token Dependencies

- Read the fetched Notion spec and identify which RAPIDA token skills apply
- Invoke the relevant token skills: `rapida-colors`, `rapida-typography`, `rapida-layout-spacing`, `rapida-states`
- Map every visual property in the spec to a specific token
- If a property cannot be mapped to a token, STOP and ask the user

### Step 3: Build the Component

- Create the Blade or Livewire component in the correct directory (see Directory Structure)
- Use ONLY values from the Notion spec + RAPIDA tokens
- If any design detail is not in the Notion spec, do NOT invent it — STOP and ask
- After building, run the Erika Hall HID audit checklist from the `tailwindcss-development` skill

### Step 4: Write a Pest Test

- Create a Pest test using `php artisan make:test --pest`
- Test that the component renders correctly
- Test that semantic tokens are used (not raw hex/Tailwind defaults)
- Test accessibility requirements from the spec (ARIA, touch targets, contrast)
- Apply RAPIDA accessibility testing guidance from the `pest-testing` skill

---

## Directory Structure

The skill enforces this exact structure:

### Components (Blade)

```
resources/views/components/
├── atoms/           ← Smallest indivisible UI blocks
│   ├── button.blade.php
│   ├── text-input.blade.php
│   ├── textarea.blade.php
│   ├── photo-upload.blade.php
│   ├── select.blade.php
│   ├── radio-group.blade.php
│   ├── checkbox.blade.php
│   ├── toggle.blade.php
│   ├── icon.blade.php
│   ├── badge.blade.php
│   ├── progress-step.blade.php
│   └── loader.blade.php
├── molecules/       ← Purposeful combinations of atoms
│   ├── damage-report-card.blade.php
│   ├── form-field-group.blade.php
│   ├── language-switcher.blade.php
│   ├── offline-queue.blade.php
│   ├── damage-classification.blade.php
│   ├── infrastructure-type.blade.php
│   ├── crisis-type.blade.php
│   ├── map-pin.blade.php
│   ├── notification.blade.php
│   └── submission-confirmation.blade.php
└── organisms/       ← Complex interface sections
    ├── submission-wizard.blade.php
    ├── map-organism.blade.php
    ├── navigation-header.blade.php
    ├── community-report-feed.blade.php
    ├── analytics-dashboard.blade.php
    ├── data-export.blade.php
    ├── engagement-panel.blade.php
    └── report-version-history.blade.php
```

### Templates (Page Layouts)

```
resources/views/templates/
├── onboarding.blade.php
├── map-home.blade.php
├── submission-wizard.blade.php
├── submission-confirmation.blade.php
├── my-reports.blade.php
├── report-detail.blade.php
├── analytics-dashboard.blade.php
├── data-export.blade.php
└── pitch-video.blade.php
```

### Tests

```
tests/Feature/Components/
├── Atoms/
├── Molecules/
├── Organisms/
└── Templates/
```

### Naming Conventions

- File names: kebab-case matching the Notion component name
- Blade component usage: `<x-atoms.button>`, `<x-molecules.damage-report-card>`, `<x-organisms.submission-wizard>`
- Templates are full page layouts connected to routes, not Blade components

### Livewire Rule

Organisms that need interactivity (submission wizard, map, dashboard) may use Livewire. The Notion spec for each organism will indicate if Livewire is needed. If the spec doesn't specify, default to Blade + Alpine.js.

---

## Hard Rules

### Rule 1: Notion is the only spec
- Never build from memory, assumption, or "common sense"
- If the Notion page hasn't been fetched in this conversation, fetch it before writing a single line
- If the Notion page is empty or incomplete for a component, STOP and tell the user

### Rule 2: No invented values
- Every color, spacing, radius, shadow, font, weight, and size must trace back to a RAPIDA token or a specific value in the Notion component spec
- If a visual property is needed but not specified, STOP and ask
- Never use raw Tailwind defaults when the spec doesn't call for them

### Rule 3: Build bottom-up
- Atoms before molecules. Molecules before organisms. Organisms before templates
- If asked to build a molecule whose atoms don't exist yet, tell the user which atoms need to be built first
- Never inline an atom's code into a molecule — always use the `<x-atoms.*>` component reference

### Rule 4: One component per build
- Each build cycle produces exactly one component
- Never batch multiple components into a single step
- This keeps each component reviewable and testable in isolation

### Rule 5: Accessibility is not optional
- Every interactive element must have ARIA attributes as specified in the Notion page
- Every component must meet touch target, contrast, and RTL requirements from the token skills
- If the Notion page doesn't specify ARIA for an interactive element, STOP and ask

### Rule 6: Microcopy comes from the spec
- All user-facing text must come from the Notion page
- Never write placeholder microcopy
- If the spec doesn't include microcopy for a required text element, STOP and ask

---

## CLAUDE.md Registration

Add to Skills Activation:

```markdown
- `rapida-builder` — Builds RAPIDA UI components from Notion specs. Invoke whenever
  creating or implementing any atom, molecule, organism, or template. Fetches the spec
  from Notion, applies token skills, builds to the enforced directory structure, writes
  a Pest test. Never deviates from the Notion spec.
```

---

## Related Skills

The builder skill references and depends on:

| Skill | Used in step | Purpose |
|-------|-------------|---------|
| `rapida-colors` | Step 2 | Color token mapping |
| `rapida-typography` | Step 2 | Typography token mapping |
| `rapida-layout-spacing` | Step 2 | Spacing, radius, shadow, motion tokens |
| `rapida-states` | Step 2 | Interaction, validation, data, damage states |
| `tailwindcss-development` | Step 3 | Erika Hall HID audit checklist |
| `pest-testing` | Step 4 | RAPIDA accessibility testing guidance |
| `laravel-best-practices` | Step 3 | Blade component patterns, trauma-informed microcopy |

---

## Verification

### How to test the builder skill works

1. **Workflow test:** Ask Claude to "build the Button atom." Verify it fetches the Notion page first, identifies token dependencies, creates `resources/views/components/atoms/button.blade.php`, and writes a test
2. **No-deviation test:** Ask Claude to "build a card component" (not in the Notion system). Verify it checks Notion, finds no matching spec, and tells the user instead of inventing one
3. **Bottom-up test:** Ask Claude to "build the Damage Report Card molecule." Verify it checks whether prerequisite atoms exist and flags any missing ones
4. **No-invented-values test:** Edit a Notion component page to remove the border-radius value. Ask Claude to build it. Verify it stops and asks instead of picking a default

### Implementation artifacts

- [ ] `.claude/skills/rapida-ui/builder/SKILL.md` created
- [ ] CLAUDE.md updated with rapida-builder skill activation entry
- [ ] Builder skill references correct Notion URLs
- [ ] Builder skill references all 4 token skills + 3 existing skills
