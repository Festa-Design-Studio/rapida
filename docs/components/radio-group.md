# Radio-group atom — accessibility & copy contract

`<x-atoms.radio-group />` — single-choice from a small set (3-5 options ideal).

## Props

| Prop | Type | Default | Notes |
|---|---|---|---|
| `name` | string | required | Group name; all radios share it. |
| `label` | string | required | Group legend. |
| `options` | array | required | `[value => label]`. |
| `value` | ?string | null | Currently selected value. |
| `required` | bool | false | At least one must be selected. |
| `error` | ?string | null | Applied to the fieldset. |

## Keyboard contract

- `Tab` — focuses the selected radio (or first if none).
- `Up`/`Down` or `Left`/`Right` arrows — moves selection within the group.
- `Space` — selects the focused radio (no-op if already selected).

## ARIA contract

- `<fieldset>` wraps the group; `<legend>` is the label.
- Each `<input type="radio" name="{name}">` shares the name attribute.
- Error message linked via `aria-describedby` on the fieldset.
- Required state on the fieldset (not individual radios).

## Focus management

- Focus ring on the radio circle, not the label.
- Selected state has filled inner circle (visual) + `checked` attribute.
- Tab moves between groups, not within. Arrows move within.

## Screen reader behavior

- Group announced as "{legend}, radio group, {N of M}".
- Each option as "{label}, radio button, {selected | not selected}, {position} of {total}".

## Copy contract

| Element | Pattern |
|---|---|
| Legend | Question or noun phrase ("Damage level", "How was the photo taken?") |
| Options | Short, parallel labels ("Minimal", "Partial", "Complete") |
| Error | Cause ("Select a damage level") |
