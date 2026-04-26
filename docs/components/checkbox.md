# Checkbox atom — accessibility & copy contract

`<x-atoms.checkbox />` — single binary toggle (on/off, agree/disagree, multi-select item).

## Props

| Prop | Type | Default | Notes |
|---|---|---|---|
| `name` | string | required | Used for `id`, `name`. |
| `label` | string | required | Visible to the right of the box. |
| `value` | ?string | null | Form-submitted value when checked. |
| `checked` | bool | false | Current state. |
| `required` | bool | false | Adds asterisk; uses native `required`. |

## Keyboard contract

- `Tab`/`Shift+Tab` — focus.
- `Space` — toggles checked state.
- `Enter` — submits form (does NOT toggle).

## ARIA contract

- Native `<input type="checkbox">` — full a11y from browser.
- Label linked via `<label for>` ↔ `<input id>` OR by wrapping the input.
- Required uses HTML `required` attribute.

## Focus management

- Focus ring on the checkbox square.
- Checked state shows a checkmark inside the box.

## Screen reader behavior

- Announced as "{label}, checkbox, {checked | not checked}".
- State changes announced live.

## Copy contract

| Element | Pattern |
|---|---|
| Label | Statement-of-fact phrase ("I have read the privacy notice", "Multi-photo per report") |
| Helper | Optional caption below explaining consequences |
| Error | "You must accept the privacy notice" |
