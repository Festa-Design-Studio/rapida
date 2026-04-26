# Mobile Testing — RAPIDA

Two layers of mobile validation. Layer 1 ships with the codebase; Layer 2 is deferred until the deploy environment is settled.

## Layer 1 — Static smoke (shipped)

`tests/Feature/MobileUsabilitySmokeTest.php` runs as part of `php artisan test --compact`. Catches the high-value mobile-hostile patterns without needing a real browser:

- Viewport meta tag present on every public route
- No inline fixed-pixel widths > 300px (would force horizontal scroll on a 375px iPhone)
- Touch-friendly button height class (`h-12` = 48px) present in the rendered wizard

Cheap, fast, runs in CI on every PR. Catches the regressions that matter most.

## Layer 2 — Real-browser interaction (deferred)

Real-browser checks need Pest 4's browser plugin (`pestphp/pest-plugin-browser`) plus Playwright or a Selenium/ChromeDriver setup. These aren't yet committed because they triple CI runtime and need the deploy environment locked.

When the deploy URL is stable, add:

- **Visual smoke** — `visit('/crisis/accra-flood-2026')->assertNoJavaScriptErrors()->assertNoConsoleLogs()` at iPhone-14-Pro viewport (375x812) and Pixel-7 viewport (412x915).
- **Touch-target audit** — render every button/input at the smallest seeded viewport, walk the DOM via Playwright, assert each interactive element's bounding rect has `width >= 44 && height >= 44` (WCAG 2.5.5).
- **a11y axe-core** — `$page->axeCheckCritical()` on the wizard, the dashboard, and the public map page; assert zero critical violations.
- **Throttled-3G load time** — assert each public page loads in under 3 seconds with the Chrome DevTools "Fast 3G" throttle (UNDP webinar requirement).
- **Tap-flow recording** — record a 30-second video of the wizard flow on a 375px viewport for the pitch video.

Estimated effort: 1 day to wire Playwright + write the four test scripts. Run nightly, not per-PR (Playwright is slow).

## Manual checklist (pre-submission)

Run these by hand against the live deploy before submitting to UNDP:

- [ ] Open `https://rapida-main-6sutvc.laravel.cloud/crisis/accra-flood-2026` in Safari on iPhone (real device, not simulator)
- [ ] Walk the wizard end-to-end — every tap target hits cleanly, no zoom required
- [ ] Switch to Arabic — RTL layout flips correctly, no overlapping text
- [ ] Submit a report offline (airplane mode) — confirmation says "saved, will send when connected"
- [ ] Re-enable network — report syncs without manual intervention
- [ ] Open the analyst dashboard on iPad in landscape — table is scrollable horizontally, not clipped
