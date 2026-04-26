# RTL conventions — logical-direction Tailwind utilities

RAPIDA serves 6 UN languages, one of which (Arabic) is right-to-left. To make every view flip cleanly when the user switches to `ar`, **all directional Tailwind utilities in `resources/views/components/` must be logical, not physical.**

## The rule

| Don't use (physical) | Use (logical) | Notes |
|---|---|---|
| `pl-N` | `ps-N` | padding-inline-start |
| `pr-N` | `pe-N` | padding-inline-end |
| `ml-N` | `ms-N` | margin-inline-start |
| `mr-N` | `me-N` | margin-inline-end |
| `text-left` | `text-start` | |
| `text-right` | `text-end` | |
| `left-0` | `start-0` | for absolute/fixed positioning |
| `right-0` | `end-0` | |
| `border-l` | `border-s` | |
| `border-r` | `border-e` | |
| `rounded-l-*` | `rounded-s-*` | |
| `rounded-r-*` | `rounded-e-*` | |

Tailwind 3.3+ ships these natively. They flip automatically based on the `<html dir>` attribute, which `resources/views/layouts/rapida.blade.php` sets to `rtl` for Arabic.

## How this is enforced

`tests/Arch/RtlLogicalDirectionTest.php` scans every `*.blade.php` under `resources/views/components/` and fails CI if any physical-direction utility appears. Adding a new component with `pl-4` instead of `ps-4` will block the PR.

## When you legitimately need physical direction

Rare, but possible:
- SVG `transform` attributes that rotate icons in one direction.
- Animation keyframes where the direction is intentional regardless of locale.

In those cases, document the reason inline and add the file path to an explicit allowlist in the arch test (none exist today).

## How to test in the browser

1. Open DevTools → Application → Cookies for the local origin.
2. Set `rapida_locale=ar`.
3. Reload — the page should flip layout. Check that:
   - Sidebar lives on the right.
   - Form labels align to the right.
   - Button icons are mirrored where directional (`arrow-right` becomes `arrow-left`).
   - Map controls remain in the corners (these are absolutely-positioned and use logical `start-`/`end-`).

Visual-regression coverage via Pest 4 browser plugin is on the roadmap — currently the server-side render is asserted by `tests/Feature/RtlLayoutTest.php`.
