# Demo Personas — `/my-reports` Cookie Walk-Through

The Accra demo crisis is seeded with three "demo persona" device fingerprints so a UNDP evaluator can experience the **anonymous re-find loop** (gap-32) without having to submit a report first.

## How to use

1. Visit any page on the demo URL — the server issues a fresh `rapida_device_fingerprint` cookie (encrypted, HTTP-only, 1-year TTL).
2. Open DevTools → Application → Cookies → select the demo origin.
3. Replace the `rapida_device_fingerprint` value with one of the personas below.
4. Reload `/my-reports` — you'll see ~6 reports attributed to that persona.

The encryption layer makes the raw UUID unreadable from the cookie jar in production, but for **local demo only** the seeder uses unencrypted persona strings so you can paste them directly. In production these are random UUIDs minted by `EnsureDeviceFingerprint` middleware.

## Personas

| Persona | Cookie value | District | Behaviour |
|---|---|---|---|
| **Resident** | `demo-persona-resident-plateau-001` | Plateau | Reports their own neighborhood — mostly partial damage, walking radius ~500 m. Mix of WhatsApp + web. |
| **Shopkeeper** | `demo-persona-shopkeeper-kaneshie-002` | Kaneshie Market | Commercial focus — partial-to-complete severity, tight market-area cluster. |
| **Volunteer** | `demo-persona-volunteer-achimota-003` | Achimota | Mobile reporter covering the full damage range, web-only. |

## Why this exists

Bulk anonymous reports populate the heatmap and analyst dashboard but, by design, do **not** appear on `/my-reports` for any cookie value — those reports weren't submitted from any specific device. The personas exist so the re-find loop has something to re-find for an evaluator who lands cold on the demo URL.

For the corresponding code see `database/seeders/DamageReportSeeder.php` (`PERSONA_*` constants).
