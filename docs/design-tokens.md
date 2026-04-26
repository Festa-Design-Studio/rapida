# RAPIDA — Design Tokens Reference

The single source of truth for every visual decision in the codebase. If a colour, type size, spacing value, radius, or shadow appears in a component file as a literal, it's drift — wire it through this system instead.

## Layers

| Layer | File | Purpose |
|-------|------|---------|
| Server config | `config/rapida-tokens.php` | Map and chart hex values consumed by Blade + JS |
| Tailwind colors | `rapida-ui/tokens/colors.cjs` | Named palettes (rapida-blue, ground-green, alert-amber, crisis-rose, neutral, grey) wired into `tailwind.config.cjs` |
| Tailwind semantic | `rapida-ui/tokens/semantic-colors.cjs` | Role-based aliases (color-critical, surface-page, text-primary, …) |
| Tailwind state | `rapida-ui/tokens/states.cjs` | Hover/focus/disabled state overlays |
| Tailwind type | `rapida-ui/tokens/typography.cjs` | Font family + fontSize scale |
| Tailwind spacing | `rapida-ui/tokens/spacing.cjs`, `spacing-semantic.cjs` | Spacing/border-radius/shadow/duration |

## Color palettes (raw values)

All defined in `rapida-ui/tokens/colors.cjs`. Use the Tailwind class form (`text-rapida-blue-900`, `bg-crisis-rose-50`) — never the hex literal in markup.

- **rapida-blue** (primary, calm): 950 / 900 / 800 / 700 / 500 / 300 / 100 / 50 — `#0f2330` to `#f0f7fa`
- **ground-green** (success, recovery): 900 / 800 / 700 / 500 / 200 / 50 — `#1e3d2f` to `#f0f9f4`
- **alert-amber** (warning, in-progress): 900 / 700 / 500 / 300 / 100 / 50 — `#7a4510` to `#fdf6ec`
- **crisis-rose** (danger, critical, error): 900 / 700 / 400 / 300 / 100 / 50 — `#5c2420` to `#fdf3f1`
- **neutral** / **grey** for text and surfaces

## Trauma-informed rules (enforced by tests)

1. **Never use red.** Pure-red Tailwind classes (`text-red-*`, `bg-red-*`, `border-red-*`, `ring-red-*`, `from-red-*`, `to-red-*`, `via-red-*`, `fill-red-*`, `stroke-red-*`, `divide-red-*`, `placeholder-red-*`, `caret-red-*`, `accent-red-*`, `outline-red-*`, `shadow-red-*`, `decoration-red-*`) are banned in `resources/views/components/`. Enforced by `tests/Arch/DesignSystemTest.php::it forbids pure-red Tailwind classes in Blade components` (gap-44).
2. **No raw hex literals in component markup.** `#xxxxxx` color literals in component blade files are banned with a small allowlist: SVG fill/stroke attributes that need a literal, lines reading from `config('rapida-tokens.*')` via `@json()`, lines containing `// allowed:` justification, and HTML numeric entities (`&#10003;`, `&#039;`). Enforced by `tests/Arch/DesignSystemTest.php::it forbids hex color literals in Blade components` (gap-41).

## Map color tokens (the one place hex values live)

`config/rapida-tokens.php` defines the six values map JS modules consume:

```php
'map' => [
    'damage_minimal'   => '#22c55e',  // green
    'damage_partial'   => '#f59e0b',  // amber
    'damage_complete'  => '#c46b5a',  // crisis-rose-400
    'footprint_fill'   => '#2e6689',  // rapida-blue-700
    'footprint_stroke' => '#1a3a4a',  // rapida-blue-900
    'user_dot'         => '#2e6689',  // rapida-blue-700
],
```

Every map-rendering blade file (`step-location`, `field-map`, `map-report`, `map-organism`) must read this config via `@json(config('rapida-tokens.map'))` rather than hardcoding the hex values inline. The arch test catches any drift.

## Adding a new token

1. Decide which layer it belongs to (raw palette, semantic, state, type, spacing).
2. Add it to the appropriate `rapida-ui/tokens/*.cjs` file.
3. Reference it via Tailwind class in markup.
4. If JS needs the raw hex (e.g., MapLibre layer paints), add a parallel entry to `config/rapida-tokens.php` and read via `@json(config('rapida-tokens.X'))`.
5. Document the token's purpose in this file.
