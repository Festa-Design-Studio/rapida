# Icon atom — accessibility & copy contract

`<x-atoms.icon />` — Heroicons-style SVG glyph.

## Props

| Prop | Type | Default | Notes |
|---|---|---|---|
| `name` | string | required | Icon name (e.g. `camera`, `map-pin`, `check`). |
| `size` | string | `md` | `sm`, `md`, `lg`, `xl`. |
| `class` | ?string | null | Color via Tailwind text-* utilities. |

## Keyboard contract

Static — not focusable.

## ARIA contract

- Default: `aria-hidden="true"` — icon is decorative, accompanying text carries meaning.
- When used as a STANDALONE meaning carrier (e.g. icon-only button), the parent element MUST provide `aria-label`.
- When used as a status indicator alongside text, keep `aria-hidden="true"` to avoid duplicate announcement.

## Focus management

N/A.

## Screen reader behavior

- Decorative by default — silent.
- If meaningful, parent button/link supplies the accessible name.

## Copy contract

N/A — icons have no text. The accompanying label/aria-label is what matters.

Common pairings:
- `camera` + "Take photo"
- `map-pin` + "Share location"
- `check` + "Submitted" / "Synced"
- `alert-triangle` + warning copy (use sparingly — trauma-informed UX)
