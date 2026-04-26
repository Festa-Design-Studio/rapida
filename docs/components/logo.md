# Logo atom — accessibility & copy contract

`<x-atoms.logo />` — RAPIDA wordmark + lightning glyph used in headers.

## Props

| Prop | Type | Default | Notes |
|---|---|---|---|
| `size` | string | `md` | `sm`, `md`, `lg`. |
| `link` | bool | true | Wrap in `<a href="/">` so click returns to the map. |

## Keyboard contract

- `Tab`/`Shift+Tab` — focus when `link=true`.
- `Enter` — navigate to home.

## ARIA contract

- When `link=true`: `<a href="/" aria-label="RAPIDA — return to map">` so SRs don't read it as just "RAPIDA, link".
- When `link=false`: `<span aria-label="RAPIDA">` for header text only.
- The lightning SVG inside has `aria-hidden="true"` (decorative).

## Focus management

- Focus ring on the link wrapper.
- Skip-to-content link should appear before the logo for keyboard users.

## Screen reader behavior

- Announced as "RAPIDA, return to map, link" (when linked) or "RAPIDA" (when static).

## Copy contract

The wordmark text is always "RAPIDA" — never "Rapida" or "RAPIDA app".
