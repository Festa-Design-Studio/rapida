# Badge atom — accessibility & copy contract

`<x-atoms.badge />` — short, color-coded status label.

## Props

| Prop | Type | Default | Notes |
|---|---|---|---|
| `variant` | string | `default` | `default`, `info`, `success`/`synced`, `warning`/`pending`, `error`/`danger`, `minimal`, `partial`, `complete`, `draft`. Map to color tokens. |

## Keyboard contract

Static element — not focusable, no interactions.

## ARIA contract

- No role attribute (decorative + redundant with text).
- If used as the SOLE indicator of state (no accompanying text), wrap in `<span aria-label="Status: {label}">` for clarity.

## Focus management

N/A — not focusable.

## Screen reader behavior

- Text content read inline with surrounding context.
- Color is supplementary, never the only signal.

## Copy contract

| Variant | Use | Example text |
|---|---|---|
| `success`/`synced` | Positive completion | "Active", "Synced" |
| `warning`/`pending` | In-progress / needs attention | "Pending", "Pending sync" |
| `error`/`danger` | Failure / risk | "Failed", "Conflict" |
| `info` | Neutral metadata | "WhatsApp", "Hospital" |
| Damage variants (`minimal`/`partial`/`complete`) | Damage level | Match enum values |

Keep badge text 1-2 words. Sentence-case.
