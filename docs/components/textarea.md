# Textarea atom — accessibility & copy contract

`<x-atoms.textarea />` — multi-line text entry.

## Props

| Prop | Type | Default | Notes |
|---|---|---|---|
| `name` | string | required | Used for `id`, `name`, error binding. |
| `label` | string | required | Always visible. |
| `rows` | int | 4 | Initial height in rows; resizes vertically per browser default. |
| `placeholder` | ?string | null | Hint, not label. |
| `error` | ?string | null | Applies `aria-invalid="true"` + `aria-describedby`. |
| `maxlength` | ?int | null | Native HTML attribute; counter not yet auto-rendered. |

## Keyboard contract

Same as text-input plus:
- `Enter` — inserts newline (does NOT submit form).
- `Shift+Enter` — same as `Enter`.

## ARIA contract

- `<label for>` and `<textarea id>` linked.
- `aria-invalid` on error.
- `aria-describedby` for help/error.

## Focus management

- Same focus ring as text-input.
- Auto-resize NOT enabled — operators set `rows` explicitly.

## Screen reader behavior

- Announced as "edit, multi-line, {label}".
- Character count NOT auto-announced; if added, must use `aria-live="polite"`.

## Copy contract

| Element | Pattern |
|---|---|
| Label | Noun ("Description", "Notes") |
| Placeholder | Example fragment ("e.g. Two homes flooded near the bridge") |
| Help | Optional usage hint ("Optional. Up to 500 characters.") |
| Error | Cause + remediation |
