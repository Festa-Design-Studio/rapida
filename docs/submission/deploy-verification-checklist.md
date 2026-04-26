# Deploy Verification Checklist — RAPIDA / UNDP Submission

Run this end-to-end against the live Laravel Cloud deploy before recording the pitch video (gap #3) and before pasting URLs into the InnoCentive submission form. If any single item below fails, **do not** submit — the demo is not ready.

Sign and date the table at the bottom.

## Pre-flight (local)

- [ ] `git checkout main && git pull` — on latest commit.
- [ ] `php artisan test --compact` — 100% green.
- [ ] `vendor/bin/pint --test --format agent` — exits clean.
- [ ] `php artisan test --compact tests/Feature/SubmissionFlowSmokeTest.php` — the demo-day smoke test specifically passes.
- [ ] Record the deploy commit hash here: `___________________`

## Deploy

- [ ] Deploy hook ran successfully on Laravel Cloud (check the deploy log for green).
- [ ] `php artisan migrate --force` ran cleanly (no pending migrations).
- [ ] `php artisan db:seed --class=DamageReportSeeder --force` produced 50–120 reports for `accra-flood-2026`.
- [ ] `php artisan storage:link` (or storage symlink already in place) — photos directory accessible.

## Public reporter view

- [ ] `https://rapida-main-6sutvc.laravel.cloud/crisis/accra-flood-2026` returns 200 in under 3 seconds (Chrome DevTools throttled to "Fast 3G").
- [ ] Heatmap renders with visible clusters around Plateau, Achimota, Kaneshie.
- [ ] Language switcher works for all 6 locales (en/fr/ar/es/zh/ru) and Arabic flips the layout to RTL.
- [ ] Transparency screen appears on first visit, dismisses, does not reappear on second visit (localStorage check).
- [ ] Wizard step 1 photo upload accepts a JPEG; on submit, EXIF is stripped server-side but the photo's GPS+DateTime survive (download from S3 and run `exiftool` on it).

## Conflict-mode reporter view

- [ ] `https://rapida-main-6sutvc.laravel.cloud/crisis/aleppo-conflict-2026` returns 200.
- [ ] Transparency screen renders the conflict-variant copy (no "your photo and location go to UNDP" framing).
- [ ] Submitting a report stores `device_fingerprint_id = NULL` and `reporter_tier = 'anonymous'` in the DB regardless of any cookie value.

## UNDP analyst dashboard

- [ ] `https://rapida-main-6sutvc.laravel.cloud/login` accepts `evaluator@undp.org` / current password (per `evaluator-access.md`).
- [ ] After login, redirects to `/dashboard/analyst` and the page renders.
- [ ] Analytics panel shows a damage-level breakdown matching the seeded mix (~32 minimal, ~28 partial, ~20 complete).
- [ ] Verification queue shows the ~5% pre-flagged reports.
- [ ] All six export buttons return 200 and download a non-empty file:
  - [ ] CSV (`/dashboard/export/csv`)
  - [ ] GeoJSON (`/dashboard/export/geojson`)
  - [ ] KML (`/dashboard/export/kml`)
  - [ ] Shapefile (`/dashboard/export/shapefile`) — opens in QGIS without errors.
  - [ ] GPKG (`/dashboard/export/gpkg`) — opens in QGIS without errors; `application_id` reads as `0x47504B47`.
  - [ ] PDF (`/dashboard/export/pdf`) — opens in any PDF viewer.

## WhatsApp channel

- [ ] WhatsApp QR code present on `/admin` landing or a dedicated page.
- [ ] Sandbox phone replies to `RAPIDA accra-flood-2026` with the EN welcome message.
- [ ] Sandbox phone replies to `RAPIDA aleppo-conflict-2026` with the AR welcome message (gap-48 fix — crisis.default_language honored).
- [ ] `BONJOUR` as the first message overrides the crisis default and replies in FR.
- [ ] Conflict-crisis WhatsApp flow asks for "describe where the damage is" instead of GPS pin (gap-15).
- [ ] Full submit flow ends with a `report_submitted` confirmation containing a short report ID.

## Pre-submission rotation

- [ ] Evaluator password rotated via tinker to a fresh value.
- [ ] `evaluator-access.md` updated with the new password.
- [ ] This checklist re-run after rotation to confirm new credentials still work.

## Sign-off

| Checked by | Date (UTC) | Deploy commit | Outcome |
|------------|------------|---------------|---------|
|            |            |               | pass / fail |

If any row is "fail", capture a screenshot in `docs/submission/screenshots/{YYYY-MM-DD}-deploy-verify/` and reference it in the PR or roadmap update.
