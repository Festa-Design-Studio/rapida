# Loader atom — accessibility & copy contract

`<x-atoms.loader />` — animated indicator that something is loading.

## Props

| Prop | Type | Default | Notes |
|---|---|---|---|
| `size` | string | `md` | `sm`, `md`, `lg`. |
| `label` | ?string | null | Optional text shown next to the spinner. |

## Keyboard contract

Static — not focusable.

## ARIA contract

- Default `role="status"` + `aria-live="polite"` so screen readers announce the loading state.
- If `label` is set, it's read by SRs; if not, falls back to a hidden "Loading…" text.
- When loading completes, the parent component should remove the loader from the DOM (announces transition automatically).

## Focus management

- Loader does NOT steal focus. Focus remains on the trigger button (which should show its own loading state via `data-loading` attribute).

## Screen reader behavior

- Announced as "Loading {label}" or just "Loading" if no label.
- Polite — does not interrupt the user.

## Copy contract

| Context | Pattern |
|---|---|
| Form submission | "Submitting…" |
| File upload | "Uploading photo…" |
| Data fetch | "Loading reports…" |
| Generic | "Loading…" (only if specifics aren't useful) |

Always present-continuous tense. Never "Loading data" — too vague.
