# UNDP Evaluator Access — RAPIDA

Single-page reference for the InnoCentive submission. Copy the credentials below into the InnoCentive form's "Demo URL & Credentials" field.

## Demo URL

`https://rapida-main-6sutvc.laravel.cloud/crisis/accra-flood-2026`

The landing page is the public reporter view (no login needed). It loads the seeded Accra Urban Flood 2026 demo crisis with ~80 community-submitted damage reports clustered around three districts.

## Analyst dashboard login

Navigate to `https://rapida-main-6sutvc.laravel.cloud/login` and use:

| Field | Value |
|-------|-------|
| Email | `evaluator@undp.org` |
| Password | `rapida-demo-2026` |
| Role | `analyst` |

The dashboard at `/dashboard/analyst` shows the analyst verification queue, exports panel, and analytics. The exports panel offers six formats (CSV, GeoJSON, KML, Shapefile, GPKG, PDF) — all six are wired and downloadable.

## Suggested 5-minute review path

1. **Public reporter view** (`/crisis/accra-flood-2026`) — see the heatmap clusters around Plateau, Achimota, Kaneshie. Switch language via the top-right toggle (en/fr/ar/es/zh/ru).
2. **Conflict-mode reporter view** (`/crisis/aleppo-conflict-2026`) — note the different transparency screen, no WhatsApp prompt, no fingerprinting. Persona E (conflict-zone reporter) flow.
3. **Analyst dashboard login** (`/login` then `/dashboard/analyst`) — verification queue with pre-flagged reports, export panel.
4. **Trigger an export** — download the GeoJSON or GPKG. Open in QGIS or any GIS tool.
5. **WhatsApp flow demo** — scan the QR on the admin-panel landing or text the Twilio sandbox number with `RAPIDA accra-flood-2026`. Bot speaks English by default, Arabic if you start with an Arabic-script message.

## Pre-submission checklist (rotate before sending to UNDP)

- [ ] Rotate the evaluator password to a fresh value via tinker:
      `UndpUser::where('email', 'evaluator@undp.org')->first()->update(['password' => bcrypt('NEW-PASSWORD-HERE')])`
- [ ] Re-run `tests/Feature/SubmissionFlowSmokeTest.php` against the live deploy URL — green = ready to record video / submit.
- [ ] Update this file with the rotated password before pasting into the InnoCentive form.

## Notes

- The seeded credentials above are dev defaults committed to the repo. They are safe to publish for the submission window only — rotate after evaluation closes.
- The evaluator account has the `analyst` role: read everything, flag/verify reports, run exports. It cannot delete reports or modify crisis configuration (those require `operator` or `superadmin`).
