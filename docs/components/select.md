# Select atom — accessibility & copy contract

`<x-atoms.select />` — single-choice dropdown.

## Props

| Prop | Type | Default | Notes |
|---|---|---|---|
| `name` | string | required | Used for `id`, `name`, error binding. |
| `label` | string | required | Always visible. |
| `options` | array | required | `[value => label]`. Use stringly-typed keys. |
| `placeholder` | ?string | null | Renders as disabled first option. |
| `required` | bool | false | Adds asterisk. |
| `error` | ?string | null | Applies `aria-invalid` + `aria-describedby`. |

## Keyboard contract

- `Tab`/`Shift+Tab` — focus.
- `Enter`/`Space`/`Alt+Down` — open dropdown.
- `Up`/`Down` arrows — navigate options.
- Type to jump to matching option (typeahead).
- `Escape` — close dropdown.

## ARIA contract

- Native `<select>` element — full a11y handled by browser.
- `<label for>` ↔ `<select id>` linkage.
- `aria-invalid` + `aria-describedby` on error.

## Focus management

- Native browser focus ring augmented with `focus-visible:ring-2 focus-visible:ring-rapida-blue-500`.
- Open state visually distinct from closed.

## Screen reader behavior

- Announced as "{label}, combobox, {selected value}, {N of M}".
- Each option announced as user navigates with arrow keys.

## Copy contract

| Element | Pattern |
|---|---|
| Label | Noun ("Crisis", "Default language") |
| Placeholder | "Select {noun}…" ("Select crisis…") |
| Option labels | Sentence-case nouns ("English", "Government building") |
| Error | Cause ("Select a crisis to continue") |
