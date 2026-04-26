# Toggle atom — accessibility & copy contract

`<x-atoms.toggle />` — visually-distinct binary switch for settings (always-on/always-off concepts).

## Props

| Prop | Type | Default | Notes |
|---|---|---|---|
| `name` | string | required | |
| `label` | string | required | Visible. |
| `checked` | bool | false | Current state. |
| `description` | ?string | null | Caption below label. |

## Keyboard contract

- `Tab`/`Shift+Tab` — focus.
- `Space`/`Enter` — toggles state.

## ARIA contract

- Renders as `<button role="switch" aria-checked="true|false">` (NOT a checkbox — the visual style implies on/off, not selected/unselected).
- `aria-labelledby` points to the label element.
- `aria-describedby` points to description if present.

## Focus management

- Focus ring around the entire switch (track + thumb).
- Animation on toggle is brief (150ms) to avoid disorientation.

## Screen reader behavior

- Announced as "{label}, switch, {on | off}".
- State change announced live.

## Copy contract

| Element | Pattern |
|---|---|
| Label | Setting name ("Danger-zone alerts", "Multi-photo per report") |
| Description | Plain explanation of effect ("Surface H3-cell danger flags to incoming reporters") |

Use checkbox (not toggle) for one-off agreements ("I have read…"). Use toggle for persistent settings.
