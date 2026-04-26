# Photo-upload atom — accessibility & copy contract

`<x-atoms.photo-upload />` — file picker styled as a drop zone for damage photos.

## Props

| Prop | Type | Default | Notes |
|---|---|---|---|
| `name` | string | required | Form field name. |
| `label` | string | required | Visible. |
| `accept` | string | `image/*` | MIME filter. |
| `capture` | ?string | `environment` | `environment` (rear camera) or `user` (selfie). |
| `error` | ?string | null | Error state. |

## Keyboard contract

- `Tab`/`Shift+Tab` — focus the drop-zone label.
- `Enter`/`Space` — opens the OS file picker.

## ARIA contract

- `<label for="{name}">` wrapping the visual drop-zone, linked to a visually-hidden `<input type="file" id="{name}">`.
- Accept attribute restricts file types at the OS level.
- Error message via `aria-describedby` on the input.

## Focus management

- Focus ring on the drop-zone (the visible parent), not the hidden input.
- After file selection, focus returns to the drop-zone by default.
- Preview state replaces the drop-zone; focus moves to the "Change" / "Remove" controls.

## Screen reader behavior

- Announced as "{label}, file picker, button". Activating it opens the OS file picker — that flow is OS-controlled, not browser-controlled.
- After upload: announces the file name briefly.

## Copy contract

| State | Pattern |
|---|---|
| Empty | "Take a photo or choose from gallery" + accept hint ("JPEG, PNG, WebP — max 10 MB") |
| Loading | "Uploading…" |
| Loaded | Filename + "Change" + "Remove" controls |
| Error | Specific cause ("Photo too large", "Photo type not supported") |
