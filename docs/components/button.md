# Button atom — accessibility & copy contract

`<x-atoms.button />` — primary call-to-action element across the app.

## Props

| Prop | Type | Default | Notes |
|---|---|---|---|
| `variant` | string | `primary` | `primary`, `secondary`, `ghost`, `danger`. Maps to color tokens, never raw red — `danger` uses `crisis-rose-600`. |
| `size` | string | `md` | `sm`, `md`, `lg`. Affects padding + min-height (touch target). |
| `type` | string | `button` | `button`, `submit`, `reset`. |
| `disabled` | bool | `false` | Disables click + applies muted styling. |

## Keyboard contract

- `Tab` / `Shift+Tab` — moves focus to/from the button.
- `Enter` / `Space` — activates the button.
- `Escape` — no behavior on the button itself; passes to surrounding modal/menu if present.

## ARIA contract

- Native `<button>` element — no `role` attribute needed.
- Disabled state uses `disabled` attribute (not `aria-disabled`) so screen readers announce "dimmed" / "unavailable" consistently.
- Loading state (when used in submit forms) shows `data-loading` attribute that triggers the spinner — no aria-busy required because the button itself remains the focused element.

## Focus management

- Focus ring uses `focus-visible:ring-2 focus-visible:ring-rapida-blue-500 focus-visible:ring-offset-2` — visible on keyboard focus, suppressed on mouse focus.
- Minimum 44×44 px touch target via `min-h-[44px] min-w-[44px]` on `md`/`lg`; `sm` is for tables only and falls below the WCAG 2.5.5 enhanced target size.

## Screen reader behavior

- Button text is the accessible name. Icon-only buttons MUST set `aria-label` via slot prop (not yet enforced — see TODO).
- Variant has no semantic meaning — `danger` doesn't announce as different; use button text to convey intent ("Delete report" not "Click here").

## Copy contract

| State | Pattern | Example |
|---|---|---|
| Default action | Verb + object | "Submit report", "Delete crisis" |
| Loading | Present-continuous of action | "Submitting…", "Deleting…" |
| Disabled | Same text as default | "Submit report" (with disabled styling) |
| Confirmation | Plain "Yes, <action>" | "Yes, enable conflict context" |

NEVER use generic copy: avoid "OK", "Click here", "Continue" without an object.
