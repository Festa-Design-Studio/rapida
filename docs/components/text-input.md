# Text-input atom — accessibility & copy contract

`<x-atoms.text-input />` — single-line text entry.

## Props

| Prop | Type | Default | Notes |
|---|---|---|---|
| `name` | string | required | Used for `id`, `name`, error binding. |
| `label` | string | required | Always visible (no placeholder-only labels). |
| `type` | string | `text` | `text`, `email`, `password`, `number`, `tel`, `url`. |
| `placeholder` | ?string | null | Hint, not a substitute for label. |
| `required` | bool | false | Adds visible asterisk + `required` attribute. |
| `error` | ?string | null | When set, applies `aria-invalid="true"` and renders `aria-describedby` error message. |
| `help` | ?string | null | Renders below input with `aria-describedby` linkage. |

## Keyboard contract

- `Tab`/`Shift+Tab` — moves focus.
- All standard text editing shortcuts (Cmd/Ctrl+A, copy, paste, arrows).

## ARIA contract

- `<label for="{name}">` and `<input id="{name}">` linked by `for`/`id`.
- Help text linked by `aria-describedby="{name}-help"`.
- Error message linked by `aria-describedby="{name}-error"` AND `aria-invalid="true"` on the input.
- Required field uses HTML `required` attribute (not `aria-required`).

## Focus management

- Focus ring: `focus-visible:ring-2 focus-visible:ring-rapida-blue-500`.
- Error state border uses `border-crisis-rose-600` (no pure red — see arch test).
- Auto-scroll into view on focus when keyboard-navigating in long forms (browser default).

## Screen reader behavior

- Label is announced first, then input type, then current value.
- Error message announced after input value via `aria-describedby`.
- Required state announced as "required" by all major screen readers.

## Copy contract

| Element | Pattern |
|---|---|
| Label | Noun phrase, sentence case ("Email address", "Crisis name") |
| Placeholder | Example value with no instructions ("e.g. accra-flood-2026") |
| Help | Plain explanation when format matters ("Use only lowercase + hyphens") |
| Error | Specific cause + remediation ("Email is required", "Slug must be unique") |
