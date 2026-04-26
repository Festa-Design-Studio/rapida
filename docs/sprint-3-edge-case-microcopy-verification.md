# Sprint 3 — Gap 18 Edge-Case Microcopy Verification

Per Hall lesson 10, RAPIDA needs explicit copy for five edge cases. The lang keys exist; this audit confirms which are actually wired.

## Audit results

| Lang key | Status | Wired in |
|----------|--------|----------|
| `rapida.empty_map` | ✅ Wired | `resources/views/components/dashboard/⚡field-map.blade.php:122` — empty-state when no crisis is active |
| `rapida.rate_limit_global` | ✅ Wired | `app/Providers/AppServiceProvider.php:113` (route throttle response), `app/Http/Controllers/Api/ApiReportController.php:46` (controller-level handling) |
| `whatsapp.unknown_state` | ✅ Wired | `app/Services/WhatsAppBotService.php:61` — bot match-statement default |
| `rapida.rate_limit_building` | ⚠️ Dead key | No consumer. Key text reads "You've already submitted a report for this building today. Thank you for staying engaged." Should fire when a reporter tries to submit a second report for the same `building_footprint_id` within 24h. Anti-gaming rule from PRD V2 §3.5 isn't enforced anywhere. |
| `rapida.photo_too_large` | ⚠️ Dead key | No consumer. Key text reads "This photo is too large to send. Try taking a new one — you don't need high quality, just clear enough to see the damage." Currently no client-side photo size check. Server falls back to a generic Laravel "file too large" 413, ignoring this lang key. |

## Verdict

3 of 5 keys honestly wired. 2 dead keys filed as follow-ups:

- **#52 (M)** — Wire `rate_limit_building` into the per-building anti-gaming rule (PRD V2 §3.5: "One report per `building_footprint_id` + `account_id` per 24-hour window"). Implementation: add a uniqueness check in `ReportSubmissionService::submit` (or a dedicated `ReportRateLimitService`) that returns the localised string when the rule fires.
- **#53 (M)** — Wire `photo_too_large` into the client-side photo upload validation. Same component as gap #50 (client-side photo compression) — both touch `step-photo` Alpine state. Probably ship together.

Both follow-ups touch the wizard's photo + submission flows. Recommend they ship after gap #50 (client-side compression) lands so the size threshold is consistent (post-compression size ≤ 500KB).
