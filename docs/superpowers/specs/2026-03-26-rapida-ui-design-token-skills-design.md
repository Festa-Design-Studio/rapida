# RAPIDA UI Design Token Skills — Design Specification

**Date:** 2026-03-26
**Author:** Abayomi (Festa Design Studio) + Claude
**Status:** Draft — pending approval
**Project:** UNDP "Build the Future of Crisis Mapping" Challenge Submission

---

## Context

Festa Design Studio is building a trauma-informed, multilingual, offline-capable PWA for the UNDP crisis mapping challenge (deadline: June 23, 2026, prize: $50,000). The design system — **RAPIDA UI** — is fully documented in Notion across 4 design token pages and 41 atomic design component pages.

The problem: when Claude Code assists with building components, pages, and features, it has no persistent knowledge of the RAPIDA UI token system. It may use raw Tailwind defaults, invent spacing values, or apply colors that violate trauma-informed principles. Every UI decision in this project is a psychological safety decision for users in active crisis.

This spec defines **4 Claude Code skills** that embed the complete RAPIDA UI design token system into Claude's working context, ensuring every piece of frontend code follows the design system automatically.

---

## Design Principles

### Erika Hall — Conversational Design Applied

Every skill carries these principles as implementation constraints (source: Festa's Notion page "Lessons from Conversational Design (Erika Hall) for Improving Human Interface Design"):

1. **Clarity IS performance** — reduce cognitive latency as aggressively as network latency
2. **Context-awareness is a safety feature** — audit for assumption landmines that could retraumatize
3. **Politeness is attention-respect** — never waste a crisis user's cognitive load
4. **Words are first-class design material** — microcopy is as important as color
5. **Design the interaction, not the container** — states and triggers before layout
6. **Truth is expectation management** — every CTA answers "what happens next?"
7. **Make turn-taking explicit** — show status, confirm actions, validate input early

### Trauma-Informed Design (Dr. G's Lab)

Six principles govern all design decisions:

1. **Safety** — calm visual environment, no alarming colors
2. **Trust & Transparency** — honest microcopy, predictable patterns
3. **Empowerment & Choice** — always an exit, partial submissions valid
4. **Community & Support** — confirmation loop closes dignity circle
5. **Cultural Awareness** — RTL-first, Noto Sans Unicode, 6 UN languages
6. **Iterative Improvement** — versioned reports, community corrections

---

## Architecture

### Format

Claude Code skills — markdown SKILL.md files with YAML frontmatter, loaded when triggered during conversations.

### Pattern

Monolithic: each skill is a single SKILL.md containing all tokens, rules, and implementation guidance from its corresponding Notion page. No sub-files or rules/ directories.

### Location

```
.claude/skills/rapida-ui/
├── colors/
│   └── SKILL.md
├── typography/
│   └── SKILL.md
├── layout-spacing/
│   └── SKILL.md
└── states/
    └── SKILL.md
```

### Tailwind Version

**Tailwind CSS v3** with `tailwind.config.js` and JavaScript token modules in `rapida-ui/tokens/`. Tailwind v4 (currently installed) will be downgraded to v3.

### Activation Strategy

**Always-on for any UI/frontend work.** Each skill activates automatically whenever Claude writes or edits Blade templates, Tailwind classes, CSS, or any visual frontend code. Human context leads every trigger description.

---

## Skill 1: rapida-colors

### Frontmatter

```yaml
name: rapida-colors
description: >
  Users of this interface are community members in active crisis — they may be
  standing in rubble, shaking, on a cracked screen in poor lighting. Every color
  is a psychological safety decision, not an aesthetic one. Invoke whenever
  building or editing any visual surface: Blade templates with color classes,
  backgrounds, borders, gradients, or damage classification UI. Covers: RAPIDA UI
  palette (rapida-blue, ground-green, alert-amber, crisis-rose, neutrals),
  semantic color mappings, calm gradients, WCAG contrast, absolute rules (no red,
  no pure black). Erika Hall principle: context-awareness is a safety feature.
  Skip for backend PHP, database, API code.
license: MIT
metadata:
  author: festa-design-studio
```

### Content Sections

1. **Psychological Foundation** — brief table mapping psychological states to color responses (trust→blue, grounding→green, urgency→rose, breathing→cream)
2. **Primary Palette** — Rapida Blue (8 stops: 950→50) and Ground Green (6 stops: 900→50) with hex, HSL, and use per token
3. **Functional Palette** — Alert Amber (6 stops) and Crisis Rose (6 stops) with hex, HSL, and use
4. **Neutral Palette** — neutral-900, grey-900→100, neutral-50, white with uses
5. **Semantic Color Mapping** — full `rapida-ui/tokens/semantic-colors.js` module: surfaces, text, brand/interactive, status, damage classification, confirmation loop
6. **Calm Gradient System** — 5 named gradients (Trust Wash, Ground Calm, Selenite Rest, Dawn Warmth, Map Overlay) with CSS and usage rules (max 2 stops, 160-180 degrees, never on interactive elements)
7. **WCAG Contrast Ratios** — 9 verified combinations with ratios and grades
8. **Absolute Color Rules** — 7 hard rules as a table (never red, never pure black, never bright yellow, crisis-rose must never become more saturated, etc.)
9. **Tailwind Config** — exact `rapida-ui/tokens/colors.js` module export
10. **Cross-references** — pointers to rapida-typography, rapida-layout-spacing, rapida-states

### Token File Governed

`rapida-ui/tokens/colors.js` + `rapida-ui/tokens/semantic-colors.js`

### Source

Notion page: "Color System" (https://www.notion.so/da94eeb31d4e4f5d9c564a20dacfc41c)

---

## Skill 2: rapida-typography

### Frontmatter

```yaml
name: rapida-typography
description: >
  Users of this interface may be reading in poor lighting, on low-DPI screens,
  with shaking hands, in a language that is not their first. Every type decision
  must prioritise legibility under stress above all else. Invoke whenever writing
  text, headings, labels, buttons, form fields, microcopy, or RTL content.
  Covers: Inter (headings/UI) and Noto Sans (body/forms), full type scale,
  weight rules, line height, letter spacing, RTL Arabic, font loading, offline
  bundling. Erika Hall principle: clarity reduces the subjective sensation of
  slowness. Skip for backend PHP, database, API code.
license: MIT
metadata:
  author: festa-design-studio
```

### Content Sections

1. **Typeface Rationale** — why Inter + Noto Sans (not Playfair/Montserrat from Dr. G's Lab): screen-optimised, full Unicode, offline-bundleable
2. **Typeface 1: Inter** — role (headings/UI), weights (400-700), CSS declaration, psychological rationale
3. **Typeface 2: Noto Sans** — role (body/forms), weights (400-700), RTL support, CSS declaration
4. **Type Scale — Mobile-First** — 12 tokens table (display→btn-sm) with px, rem, line-height, weight, font, use
5. **Type Scale — Tablet (md: 768px+)** — delta table showing size increases
6. **Type Scale — Desktop (lg: 1024px+)** — delta table showing size increases
7. **Weight Usage Rules** — table: which weights for which contexts, what each weight must never be used for
8. **Line Height Philosophy** — table by context (display 1.25 → body 1.6 → buttons 1.25)
9. **Letter Spacing** — 3 contexts (tight for display, normal for body, widest for uppercase labels only)
10. **Typography Absolute Rules** — never thin weights (<400), never ALL-CAPS body/buttons/labels
11. **RTL Typography Adaptation** — Noto Sans Arabic, dir="rtl", text-align: start, Inter not used in RTL
12. **Font Loading Strategy** — Blade `<head>` snippet with preconnect, conditional Arabic loading, offline/PWA note
13. **Tailwind Config** — exact `rapida-ui/tokens/typography.js` module export
14. **Cross-references** — pointers to sibling skills

### Token File Governed

`rapida-ui/tokens/typography.js`

### Source

Notion page: "Typography System" (https://www.notion.so/e541420756ce49ae8c115d6536338b69)

---

## Skill 3: rapida-layout-spacing

### Frontmatter

```yaml
name: rapida-layout-spacing
description: >
  Users of this interface need generous space as a trauma-informed intervention —
  crowded interfaces feel like walls closing in during a crisis. Every spacing
  value is deliberate. Invoke whenever writing spacing, padding, grids, touch
  targets, shadows, radius, or motion. Covers: 8px-base scale, semantic spacing
  tokens, 48px minimum touch targets, 12-column grid, wizard layout, border
  radius (no sharp corners), blue-hued shadows, motion tokens,
  prefers-reduced-motion. Erika Hall principle: politeness is attention-respect —
  never compress a layout to fit more on screen. Skip for backend PHP, database,
  API code.
license: MIT
metadata:
  author: festa-design-studio
```

### Content Sections

1. **Spacing Scale** — 12 tokens (space-1 4px → space-24 96px) with px, rem, Tailwind class, use
2. **Semantic Spacing Tokens** — `rapida-ui/tokens/spacing-semantic.js`: padding-input, gap-field, touch-min/ideal/large, padding-page per breakpoint
3. **Touch Target System** — 48px minimum, component-specific targets table (damage buttons 56px, submit CTA 64px, infrastructure grid 64x64px, map pins 48px, nav rows 48px, checkboxes 48px, language toggle 48px)
4. **Grid System** — Mobile (single column, 16px gutter), Tablet (12-col, 720px max, 24px gaps), Desktop (12-col, 1200px max, 32px gaps) with column allocation per screen type
5. **Wizard Layout Specification** — ASCII diagram of the 5-step wizard with exact spacing tokens per zone, mobile vs tablet values
6. **Border Radius System** — 5 tokens (sm 6px → full 9999px) with Tailwind class and use. Absolute rule: no component has 0px radius
7. **Shadow System** — 5 shadows using `rapida-blue-900` as hue (not black), CSS values, uses. Plus shadow-focus ring
8. **Motion & Animation** — 5 motion tokens (instant→calm), permitted animations table (5 allowed), prohibited animations list (no spinners, no parallax, no auto-play, no loops), `prefers-reduced-motion` CSS implementation
9. **Tailwind Config** — exact `rapida-ui/tokens/spacing.js` module export (spacing, borderRadius, boxShadow, transitionDuration, transitionTimingFunction)
10. **Cross-references** — pointers to sibling skills

### Token File Governed

`rapida-ui/tokens/spacing.js`

### Source

Notion page: "Layout & Spacing System" (https://www.notion.so/bff87f3b5b7a4a3d9ea51d7097f0569f)

---

## Skill 4: rapida-states

### Frontmatter

```yaml
name: rapida-states
description: >
  Users of this interface must always know the current state of any element they
  interact with — no ambiguity, no silent failures. A person in crisis needs to
  trust that their report was received, their photo is uploading, their
  connection will return. Invoke whenever writing interactive states, validation,
  loading patterns, sync indicators, or damage badges. Covers: interaction states,
  validation states, data/sync states, loading/skeleton, damage levels, transition
  timing, RTL mirroring. Erika Hall principles: make turn-taking explicit; truth
  is expectation management. Skip for backend PHP, database, API code.
license: MIT
metadata:
  author: festa-design-studio
```

### Content Sections

1. **Interaction States** — 7 states table (Default→Loading) with trigger, visual rule, Tailwind classes. Focus ring spec table (standard, dark background, error, success)
2. **Interaction State Tokens** — `rapida-ui/tokens/states/interaction.js`: focus rings, hover surfaces, active, disabled opacity
3. **Validation States** — 8 states table (Default→Read-only) with border, icon, message color, ARIA attributes. Blade/HTML implementation examples for each state
4. **Validation State Tokens** — `rapida-ui/tokens/states/validation.js`: borders, text, surfaces
5. **Data / Sync States** — 8 states table (Synced→Archived) with badge color, icon, border modifier
6. **Data State Tokens** — `rapida-ui/tokens/states/data.js`
7. **Loading States** — skeleton, spinner (3 sizes), progress ring, progress bar with reduced-motion fallbacks. Timeout policy table (photo 60s, form 30s, map tiles 15s, page 10s, API 20s)
8. **Damage Level States** — 4 states (Minimal→Unclassified) with surface, text, border, dot, pin values. Note: standard Tailwind colors are permitted specifically for interaction/validation/data states: teal-400/600 for hover/focus, red-400/600 for error borders/text, green-500/700 for success, amber-400/700 for warning, slate-200/300/400/500/600 for neutral states. The "no red" rule applies to primary surfaces and damage classification UI only
9. **Damage State Tokens** — `rapida-ui/tokens/states/damage.js`
10. **Motion & Transition States** — duration/easing per transition type table, `prefers-reduced-motion` CSS override
11. **RTL State Considerations** — 6-row table for toggle, error icon, chevron, progress bar, toast, step indicator mirroring
12. **Composed Token File** — `rapida-ui/tokens/states.js` requiring all sub-modules, imported into `tailwind.config.js`
13. **Cross-references** — pointers to sibling skills

### Token File Governed

`rapida-ui/tokens/states.js` (composed from `states/interaction.js`, `states/validation.js`, `states/data.js`, `states/damage.js`, `states/motion.js`)

### Source

Notion page: "States Comprehensive System" (https://www.notion.so/85ba75dc0ff9492693d501e24c3f2b69)

---

## Existing Skills Modifications

### tailwindcss-development

**File:** `.claude/skills/tailwindcss-development/SKILL.md`

**Add section: RAPIDA UI Project Context**

- When working in this project, all Tailwind classes must reference the RAPIDA token system — never use raw Tailwind defaults for colors, spacing, or typography
- This project uses Tailwind v3 with `tailwind.config.js` and `rapida-ui/tokens/` JS modules
- Reference the 4 RAPIDA skills by name for token details

**Add section: Erika Hall HID Audit Checklist**

Post-implementation verification — run these checks on any UI work:
- Goal clarity: Is the user's goal explicit and every step necessary?
- Truthfulness: Do labels/CTAs precisely match outcomes?
- Clarity: Is any text ambiguous, technical, or anxiety-producing?
- Turn-taking: Does the system show status and confirm actions?
- Repair: When things go wrong, does the UI help recovery?
- Context safety: Are there hidden assumptions that could retraumatize?
- Politeness: Are you respecting attention and objectives?
- Consistency: Is voice coherent across all states?

### laravel-best-practices

**File:** `.claude/skills/laravel-best-practices/SKILL.md`

**Add section: Trauma-Informed Blade Components**

- Every Blade component must reference RAPIDA semantic color tokens — never raw hex
- Interactive elements must follow RAPIDA states (rapida-states skill)
- All text must use the RAPIDA type scale (rapida-typography skill)
- Erika Hall #8: audit Blade templates for assumption landmines in forms, errors, validation
- Erika Hall #1: microcopy in Blade templates follows trauma-informed standards

### pest-testing

**File:** `.claude/skills/pest-testing/SKILL.md`

**Add section: RAPIDA UI Accessibility Testing**

- Browser tests should verify WCAG contrast ratios on key surfaces
- Touch target tests: verify 48px minimum on all interactive elements
- Focus ring visibility: verify focus rings on keyboard navigation
- `prefers-reduced-motion`: verify animations disabled when preference active
- No raw red: smoke tests should verify no saturated red (#ff0000 or similar) in rendered output
- Microcopy: validation error messages should follow trauma-informed patterns (never blame, always offer alternative)

---

## CLAUDE.md Update

Add to the Skills Activation section:

```markdown
- `rapida-colors` — Users are in active crisis. Every color is a psychological safety decision.
  Invoke for any Blade template with color, background, border, gradient, or damage classification.
  Covers the full RAPIDA UI palette, semantic mappings, WCAG contrast, and absolute color rules.

- `rapida-typography` — Users may be reading under stress, poor lighting, non-primary language.
  Invoke for any text, headings, labels, buttons, microcopy, or RTL content.
  Covers Inter + Noto Sans typefaces, full type scale, weight rules, RTL, font loading.

- `rapida-layout-spacing` — Generous space is a trauma-informed intervention.
  Invoke for spacing, padding, grids, touch targets, shadows, radius, or motion.
  Covers 8px spacing scale, 48px touch targets, grid system, wizard layout, shadows, animation.

- `rapida-states` — No ambiguity, no silent failures for users in crisis.
  Invoke for interactive states, validation, loading, sync indicators, or damage badges.
  Covers interaction, validation, data/sync, loading, damage level, and transition states.
```

---

## Platform Guidelines Integration (Apple HIG + Material Design 3)

The people using this app are in crisis, often low-income, on cheap Android phones with cracked screens. They don't care about features. They need: content that fits their screen, text they can read, and colors that don't make things worse. The platform guidelines below are filtered to ONLY what serves that person.

### What Goes Into rapida-colors

**Calm color on any screen:**
- Colors must be readable in bright outdoor sunlight — crisis zones are not offices. Desaturated tokens (rapida-blue, ground-green, alert-amber, crisis-rose) already handle this
- Don't use color as the only indicator of meaning — always pair with a label or icon. A color-blind user must understand the damage level without seeing the color
- Semantic token hierarchy (Apple HIG + M3): raw hex values are never used directly in templates. Always use semantic names (`surface-page`, `damage-partial`) so the meaning is clear and changeable in one place

### What Goes Into rapida-typography

**Readable text on any device:**
- Use `rem` not `px` for all font sizes — users with accessibility zoom or large text settings must get properly scaled text
- Minimum body text: 16px (1rem). Never go below this on any screen. RAPIDA's `text-body` at 16px is the floor
- On iOS Safari, input fields below 16px trigger auto-zoom — which disorients users. All form inputs must be 16px minimum
- Type scale should flow naturally from small screen to large: mobile sizes are the default, tablet and desktop add breathing room — not complexity

### What Goes Into rapida-layout-spacing

**Content that fits the screen:**
- Mobile is the primary device. Single column. No multi-column layouts on compact screens — ever
- Apple HIG responsive margins: 16px on compact (phones), 24px on medium (tablets), 32px on expanded (desktop). RAPIDA's existing padding-page-x values match this exactly
- M3 window size classes map directly to RAPIDA's grid:
  - Compact (0–599dp): single column, 16px margins — the phone in someone's hand
  - Medium (600–839dp): 12-column grid, 24px gaps — tablet in a field office
  - Expanded (840dp+): 12-column grid, 32px gaps — UNDP analyst desktop
- M3 list-detail canonical layout for the analyst dashboard: on compact screens show a full-screen list (tap to navigate to detail). On expanded screens show list + detail side by side. This is exactly RAPIDA's dashboard spec (4-col sidebar + 8-col map panel)
- Touch targets: Apple specifies 44pt minimum. M3 specifies 48dp minimum. RAPIDA uses 48px — meets both
- Maximum content width on large screens: prevent text lines from stretching too wide. The wizard on desktop is 6 columns centred — content stays readable

### What Goes Into rapida-states

**Clear feedback, no confusion:**
- Every state change must be communicated with more than one signal — border AND icon AND text. Not just a color shift. A person under stress may not notice a subtle color change
- Disabled elements: M3 uses 38% opacity. RAPIDA uses 40%. Both communicate "you can't interact with this" clearly on any screen quality
- No complex gesture requirements — no long-press, no swipe, no pinch. Tap only. The person may be injured, shaking, or using a non-dominant hand

### References

Filtered from Apple Human Interface Guidelines (Layout, Typography, Color) and Material Design 3 (Design Tokens, Layout, Canonical Layouts, Inputs, States). Only guidelines that serve a crisis user on a low-end device were retained. Feature-oriented guidelines (hover effects, gesture systems, motion choreography, swipe interactions) were intentionally excluded — they add complexity without serving the user

---

## Cross-Reference Strategy

Each RAPIDA skill includes at the end:

```markdown
## Related Skills
- For color tokens see `rapida-colors`
- For typography tokens see `rapida-typography`
- For spacing, layout, shadows, and motion see `rapida-layout-spacing`
- For interactive and validation states see `rapida-states`
- For Tailwind patterns see `tailwindcss-development`
- This project uses Tailwind v3 with `tailwind.config.js` + `rapida-ui/tokens/` JS modules
```

---

## Verification

### How to test the skills work

1. **Trigger test:** Start a new Claude Code conversation in this project. Ask Claude to "create a damage report card component." Verify that all 4 RAPIDA skills activate and Claude uses the correct tokens (rapida-blue-900 for headers, ground-green for success, crisis-rose for complete damage, 48px touch targets, Inter for headings, Noto Sans for body, semantic spacing tokens)

2. **Constraint test:** Ask Claude to "add a red error banner." Verify that Claude uses the crisis-rose palette or standard validation red-400 for states — not raw #ff0000 — and explains the trauma-informed rationale

3. **Microcopy test:** Ask Claude to "add a form validation error message." Verify it follows the trauma-informed pattern ("A few things are needed before we can send your report. No rush.") not the standard pattern ("Required fields missing.")

4. **RTL test:** Ask Claude to "add Arabic language support to a form." Verify it loads Noto Sans Arabic, uses dir="rtl", text-align: start, and mirrors state indicators

5. **Accessibility test:** Ask Claude to "create a submit button." Verify it meets 48px minimum touch target (64px for submit CTA), has a visible focus ring (teal-600, 2px offset), and respects prefers-reduced-motion

6. **Existing skills test:** Ask Claude to "write a Pest browser test for the wizard." Verify the pest-testing skill includes RAPIDA accessibility checks

### Implementation artifacts to verify

- [ ] 4 SKILL.md files created in `.claude/skills/rapida-ui/`
- [ ] 3 existing SKILL.md files updated (tailwindcss, laravel, pest)
- [ ] CLAUDE.md updated with 4 new skill activation entries
- [ ] Tailwind v4 downgraded to v3
- [ ] `tailwind.config.js` created with RAPIDA token imports
- [ ] `rapida-ui/tokens/` directory with all JS module files
- [ ] `.superpowers/` added to `.gitignore`

---

## Deliverable Summary

| Item | Type | Count |
|------|------|-------|
| New RAPIDA skills | SKILL.md | 4 |
| Existing skills modified | SKILL.md edits | 3 |
| CLAUDE.md update | Config edit | 1 |
| Tailwind downgrade | Package change | 1 |
| Token JS modules | New files | ~8 |
| **Total files touched** | | **~17** |
