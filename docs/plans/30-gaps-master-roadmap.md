# RAPIDA -- 31-Gap Master Roadmap

This document turns the 30-gap audit (saved at `~/.claude/plans/today-session-for-work-playful-lake.md`) plus one newly-identified docs/voice gap (#31) into a sequenced, parallel-safe execution plan for the next ~9 weeks. It is written for an implementer who will pick up one gap at a time, branch, PR, merge, then move on. Style follows `docs/architecture-data-lifecycle.md`: short prose, "Key files:" callouts, no frontmatter.

## 1. Ground-truth corrections from code reading

Before sequencing, three claims in the original audit need refining against what is actually in code today. These corrections do not invalidate the gap list -- they sharpen scope.

**Gap #1 (EXIF strip preserves GPS + timestamp).** The audit said `PhotoStorageService.php:14-29` does only `hash_file` + `storeAs` and that there is "zero EXIF processing." That is true at the storage layer, but the chained `app/Jobs/ProcessPhotoUpload.php` already strips EXIF -- by re-encoding through GD (`imagecreatefromjpeg` -> `imagejpeg`). GD-roundtripping discards **all** EXIF including GPS and timestamp, which is the *opposite* of what UNDP asked for. So the real gap is not "add EXIF stripping" but "switch from GD-discards-everything to ExifTool-strips-PII-but-preserves-GPS+timestamp". This shrinks the diff but raises the runtime question (ExifTool is a Perl CLI binary, not a PHP extension). Verification step: confirm Laravel Cloud's container image ships `exiftool` (it almost certainly does not -- Laravel Cloud runs a slim PHP image), and decide between (a) committing an ExifTool binary to the repo and adjusting the Cloud build hook, (b) shipping ExifTool as a sidecar service the photo job HTTP-POSTs to, or (c) falling back to a pure-PHP library (`lsolesen/pel`) that can read+rewrite EXIF segments selectively. Recommendation: spike option (c) for one hour first; if `pel` can preserve GPS+DateTimeOriginal while wiping `Make`, `Model`, `SerialNumber`, `LensSerialNumber`, the runtime question goes away entirely.

**Gap #14 (conflict-mode transparency screen variant).** Already implemented. `resources/views/components/organisms/transparency-onboarding.blade.php` already branches on `$conflictContext` and renders 4 alternative bullet points pulled from `lang/en/rapida.php` keys `transparency_conflict_1..4` plus a `transparency_conflict_cta` and `transparency_conflict_learn_more`. The gap reduces to: (a) verify the conflict variant is rendered when `$crisis->conflict_context = true`, (b) verify the same `transparency_conflict_*` keys exist in fr/ar/es/zh/ru, (c) write the missing MVC worksheet (gap #23 cluster).

**Gap #15 (WhatsApp GPS prompt in conflict mode).** Already implemented. `WhatsAppBotService::stepPhoto()` already conditionally returns `whatsapp.location_conflict_mode` ("Describe where the damage is -- a street name, landmark, or three-word code") when `session['conflict_context']` is true. The gap reduces to: (a) verify the message exists in all 6 lang files, (b) feature-test the branch.

**Gap #6 (test count claim).** Re-checked against the actual test suite: `php artisan test --compact` reports **405 test cases passing** (884 assertions). The proposal's "397 tests" claim is honest, just slightly stale. The fix shrinks to "rebaseline to current count" -- almost a non-gap. The "61" figure in the original audit counted test files, not Pest cases.

**Demo language (basis for the new 31st gap).** `database/seeders/CrisisSeeder.php:19` already declares Accra as `default_language => 'en'` with available languages `['en', 'fr', 'ar']`. The `/report/{report}` route and the wider `/crisis/accra-flood-2026` flow inherit this via `SetLocaleFromCrisis` middleware. The proposal's voice (`docs/submission/written-proposal-v2.md` line 21: "reporting on a mid-range Android in French or Arabic") implies a francophone demo. The narrative needs one sentence rewritten to align with the seeded reality: English-primary in Accra, with French and Arabic as available secondaries for the diaspora.

## 2. The 31st gap

| # | Gap | Status | Why it matters |
|---|-----|--------|----------------|
| 31 | **Proposal voice + PRD V2 alignment with Accra=English-primary** -- `docs/submission/written-proposal-v2.md` line 21 leans francophone in voice; line 53 lists languages neutrally; the PRD V2 (Notion) needs a matching change. The seeder is already correct. | Doc/copy gap | A reviewer who runs `/crisis/accra-flood-2026` and sees English UI then reads the proposal and sees francophone framing will flag inconsistency. Cheap to fix; preserves trust. |

Severity: low (doc-only). Sprint: **4** (submission packaging, alongside gap #5 voice review). It does not block any code work and is naturally closed during the InnoCentive form-fill pass.

## 3. Per-gap complexity matrix

Estimates: XS < 1h, S = 1-3h, M = 3-8h, L = 1-3 days, XL = 3+ days.
Risk: likelihood that the change breaks something else or requires unknown investigation.
Status legend: `code` = code missing; `claim` = docs claim it but code doesn't deliver; `verify` = code exists, needs verification; `doc` = doc/copy/process gap.

| #  | One-line title                                                  | Complexity | Risk | Status | Sprint |
|----|------------------------------------------------------------------|------------|------|--------|--------|
| 1  | EXIF strip preserves GPS + timestamp                             | M          | Med  | claim  | 1      |
| 2  | GeoPackage (GPKG) export job + route                             | M          | Med  | code   | 1      |
| 3  | Record 2-minute pitch video                                      | L          | Low  | doc    | 4      |
| 4  | Live demo URL + UNDP evaluator credentials                       | S          | Low  | verify | 1      |
| 5  | Proposal voice review + word counts + URL audit                  | L          | Low  | doc    | 4      |
| 6  | Test count claim correction (or expansion)                       | XS         | Low  | claim  | 1      |
| 7  | AI Model Card doc                                                | M          | Low  | doc    | 2      |
| 8  | Information-as-service danger-area alerts (H3 cell flag)         | L          | Med  | code   | 2      |
| 9  | Verify aggregated neighborhood feedback to reporter              | S          | Low  | verify | 2      |
| 10 | Verify LibreTranslate wiring + add "how to add 7th lang" doc     | S          | Low  | verify | 2      |
| 11 | Verify client-side photo compression to <500KB                   | M          | Med  | verify | 2      |
| 12 | National-government / regulatory awareness section in arch doc   | S          | Low  | doc    | 2      |
| 13 | Verify Telegram-roadmap mention in proposal                      | XS         | Low  | verify | 2      |
| 14 | Conflict-mode transparency screen -- verification + lang parity  | S          | Low  | claim  | 1      |
| 15 | WhatsApp GPS-prompt-in-conflict-mode -- verification + lang parity | S        | Low  | claim  | 1      |
| 16 | AI suggestion turn indicator ("Analysing your photo...")         | S          | Low  | code   | 3      |
| 17 | Atomic component copy contracts                                  | M          | Low  | doc    | 3      |
| 18 | Edge-case microcopy (empty map, rate-limit, photo too large)     | M          | Med  | code   | 3      |
| 19 | Photo guidance + account CTA wording fixes                       | XS         | Low  | doc    | 3      |
| 20 | WhatsApp language detect-then-confirm                            | S          | Low  | code   | 3      |
| 21 | WhatsApp error copy reframe                                      | XS         | Low  | doc    | 3      |
| 22 | Recovery banner show-once-per-outcome                            | S          | Low  | code   | 3      |
| 23 | MVC worksheets (T00, Step 2, Step 5, WA AWAITING_LOC, post-submit) | M        | Low  | doc    | 3      |
| 24 | Wizard-of-Oz French WhatsApp test (human session)                | S          | Low  | doc    | 3      |
| 25 | Verify all 6 lang files complete (no missing keys)               | S          | Med  | verify | 2      |
| 26 | Verify multi_photo_enabled actually allows multi-photo           | S          | Low  | verify | 2      |
| 27 | Verify spatial indexes are used by snap query                    | S          | Low  | verify | 2      |
| 28 | Demo crisis seeded with realistic 50-100 reports + footprints    | M          | Low  | verify | 1      |
| 29 | Verify Section 6 reference URLs are live                         | XS         | Low  | verify | 4      |
| 30 | UNDP evaluator account exists and can log into analyst dashboard | S          | Low  | verify | 1      |
| 31 | Proposal voice alignment with Accra=English-primary              | XS         | Low  | doc    | 4      |

Sprint totals: Sprint 1 = 8 gaps, Sprint 2 = 9 gaps, Sprint 3 = 9 gaps, Sprint 4 = 5 gaps. Sprint 1 is intentionally heavier because submission-blocking work has to land first; Sprint 4 is intentionally light because it is the recording/voice/upload pass.

## 4. Separation-of-concerns clusters

Each cluster is defined by the files it touches. Two PRs in the same cluster must be sequenced, not parallelised, to avoid merge conflicts.

### Cluster A -- Photo pipeline

**Gaps:** 1, 11, 26
**Shared files:** `app/Services/PhotoStorageService.php`, `app/Jobs/ProcessPhotoUpload.php`, `resources/js/` (compression module, new), `resources/views/components/atoms/photo-upload.blade.php`, `resources/views/components/wizard/step-photo.blade.php`
**Order within cluster:** 1 -> 11 -> 26.

Rationale: gap #1 changes how EXIF is handled at the server side; gap #11 adds client-side compression *before* upload (which strips EXIF too as a side effect -- important because we need to recover GPS server-side, so #1 must already preserve GPS by the time #11 lands or compression will appear to "break" what just landed); gap #26 (multi-photo) builds on whatever single-photo upload looks like after #1 and #11.

### Cluster B -- Export pipeline

**Gaps:** 2
**Shared files:** `app/Jobs/ExportReportsGpkg.php` (new), `app/Http/Controllers/ExportController.php`, `routes/web.php`, `resources/views/components/organisms/data-export.blade.php`
**Order within cluster:** standalone -- only gap 2 touches export this round.

Rationale: GPKG is a new sibling job. It will need a SQLite-based writer (GPKG is a SQLite container) -- investigate whether `pestphp/pest`'s SQLite tooling can be reused, or whether a lightweight package (`league/csv` already covers CSV; nothing covers GPKG). PHP has no native GPKG writer; either shell out to `ogr2ogr` (same Laravel Cloud runtime question as ExifTool) or write SQLite tables directly via PDO. Both are tractable.

### Cluster C -- Conflict mode + transparency

**Gaps:** 14, 15, 23
**Shared files:** `app/Services/ConflictModeService.php`, `app/Services/WhatsAppBotService.php`, `resources/views/components/organisms/transparency-onboarding.blade.php`, `lang/{6}/rapida.php`, `lang/{6}/whatsapp.php`, `docs/mvc-worksheets/` (new)
**Order within cluster:** 23 -> 14 -> 15.

Rationale: gap #23 (MVC worksheets) is a pure design artefact that informs both #14 and #15 copy. Doing #23 first means the lang-key changes in #14 and #15 are written once, not iterated. #14 and #15 each touch different lang files (`rapida.php` vs `whatsapp.php`), so they can be parallelised against each other once #23 is merged.

### Cluster D -- WhatsApp bot

**Gaps:** 15, 20, 21, 24
**Shared files:** `app/Services/WhatsAppBotService.php`, `lang/{6}/whatsapp.php`, `tests/Feature/WhatsAppBotTest.php`
**Order within cluster:** 20 -> 21 -> 15 -> 24.

Rationale: 20 (language detect-then-confirm) is a flow change that adds a step before the welcome; 21 (error copy reframe) is a string-only change; 15 (conflict-mode location prompt verification) is a string + lang-parity check; 24 (Wizard-of-Oz French test) is a manual session that finds bugs in 20+21+15 and is best done last so it can validate the trio.

### Cluster E -- Microcopy and lang files

**Gaps:** 18, 19, 21, 25 (verification), and the lang-parity portions of 14, 15, 20
**Shared files:** `lang/en/rapida.php`, `lang/en/wizard.php`, `lang/en/whatsapp.php`, `lang/en/onboarding.php`, `lang/en/account.php`, plus the same five files in fr/ar/es/zh/ru
**Order within cluster:** 25 (verify what is missing) -> 18 (write new edge-case keys) -> 19 (rename existing keys) -> lang-parity ports for 14/15/20.

Rationale: #25 is the diff-and-list pass that tells every other gap which keys it owns. Doing it first means subsequent PRs do not re-discover the same missing keys.

**Critical conflict warning:** every gap in this cluster edits `lang/en/rapida.php` somewhere. Two PRs that both add keys to that file at the bottom will merge cleanly; two PRs that both edit the same key block will conflict. Convention: append new keys at the end of each lang file in PR order. The gap-#25 verification PR should add a comment marker (e.g., `// === gap-25 audit baseline ===`) so subsequent PRs know where to insert.

### Cluster F -- Design system / component copy contracts

**Gaps:** 17, 19
**Shared files:** `resources/views/components/atoms/*.blade.php`, `resources/views/components/molecules/*.blade.php`, `docs/components/` (new copy-contract doc directory)
**Order within cluster:** 17 -> 19.

Rationale: #17 establishes the copy-contract format; #19's CTA wording fixes drop straight into that format.

### Cluster G -- Frontend JS / PWA

**Gaps:** 8, 11, 16, 22
**Shared files:** `resources/js/rapida-map.js`, `resources/js/map-pins.js`, `resources/js/map-heatmap.js`, `resources/js/offline-queue.js`, `resources/js/app.js`, `resources/views/components/wizard/step-damage.blade.php` (for #16's Alpine state), `resources/views/components/molecules/recovery-outcome-banner.blade.php` (for #22)
**Order within cluster:** 11 (compression) -> 16 (turn indicator) -> 22 (banner show-once) -> 8 (danger-area alerts).

Rationale: #11 is the prerequisite that introduces a JS module the others can model. #8 is heaviest because it requires a server-side endpoint exposing flagged H3 cells; do it last so the simpler ones merge first.

### Cluster H -- Documentation only

**Gaps:** 7, 10, 12, 23
**Shared files:** `docs/architecture-data-lifecycle.md`, `docs/ai-model-card.md` (new), `docs/translation-pipeline.md` (new), `docs/mvc-worksheets/` (new)
**Order within cluster:** 7 -> 10 -> 12 -> 23.

Rationale: each writes to a different file so there are no merge conflicts; the order is purely "easiest first to retire low-risk items."

### Cluster I -- Demo / submission packaging

**Gaps:** 3, 4, 5, 13, 28, 29, 30, 31, plus the test-count fix in 6
**Shared files:** `database/seeders/CrisisSeeder.php`, `database/seeders/DamageReportSeeder.php` (new), `database/seeders/UndpUserSeeder.php`, `docs/submission/written-proposal-v2.md`, `docs/submission/screenshots/` (new), the eventual recorded video file (kept outside repo)
**Order within cluster:** 28 (seed realistic data) -> 30 (evaluator account) -> 4 (deploy + verify URL) -> 13 (Telegram mention) -> 29 (URL audit) -> 5 (voice review) -> 31 (English-primary correction) -> 3 (record video) -> 6 (test count claim).

Rationale: video and proposal-voice work require the demo to be rendered correctly; everything in this cluster sequences off the demo seeder. Gap #3 (video) is dead last because re-recording is expensive -- wait until everything else has settled.

## 5. Dependency graph

Concrete dependencies, found by reading the code, not invented.

**Hard blockers (PR Y cannot start until PR X is merged):**

- `Gap #25 BLOCKS #14, #15, #18, #19, #20` -- the lang-key audit must land first so each microcopy gap knows which keys are missing in which locale and where to insert (see Cluster E convention).
- `Gap #28 BLOCKS #4` -- the demo URL is not "verified" until the seeder produces the realistic 50-100 reports the proposal claims; until #28 lands, #4 verifies a half-empty crisis.
- `Gap #28 BLOCKS #2 verification` -- GPKG export is only meaningfully testable against a seeded crisis with realistic geometry; the unit test of the export job can run on factory data, but the manual demo-day verification ("download GPKG, open in QGIS, see Accra polygons") requires #28.
- `Gap #28 BLOCKS #30` -- the evaluator account needs reports to look at; otherwise the analyst dashboard is empty on first login.
- `Gap #4, #28, #30 BLOCK #3` -- the pitch video needs the live demo with seeded data and a working evaluator login (the script almost certainly walks through both reporter and analyst views).
- `Gap #5 BLOCKS #31 indirectly` -- both edit `docs/submission/written-proposal-v2.md`. Doing #5 (full voice review) first absorbs the small #31 fix as part of the same pass; doing them in the other order means #5 re-touches lines #31 just edited.
- `Gap #7 (AI Model Card) requires verifying which AI model is actually wired` -- `app/Jobs/ClassifyDamageWithAI.php` shows the job HTTPs to an external sidecar at `config('services.ai.url')` with a `$secret`. **There is no committed model identity in the code.** The model card cannot be written honestly until someone reads `config/services.ai.php` (or `.env.example`) and either documents the actual sidecar model or marks the model as "to-be-decided pending production sidecar selection." This is a real research step before #7 starts.

**Soft dependencies (work better in this order, not strictly blocking):**

- `Gap #1 SHOULD precede #11` -- server-side EXIF behavior should be settled before client-side compression changes what reaches the server (see Cluster A).
- `Gap #23 SHOULD precede #14, #15` -- MVC worksheets inform copy choices (see Cluster C).
- `Gap #20 SHOULD precede #24` -- the Wizard-of-Oz French test should validate the new detect-then-confirm flow, not the old keyword-match flow.

**Specific questions answered up front:**

- *Does the EXIF code change (#1) block the test count claim correction (#6)? I.e., does adding EXIF tests change the test count?* Yes, slightly: #1's verification will add at least one Pest feature test (`tests/Feature/PhotoExifStripTest.php`) and probably a unit test for the lib choice. So the test count after Sprint 1 will be ~63-65, not 61. Recommendation: do #6 *after* Sprint 1 closes, when the count has stabilised, and either (a) write the proposal as "60+ automated tests covering the report submission, conflict-mode, export, and AI-classification paths," which is honest and won't churn, or (b) commit to a count and update on submission day.
- *Does conflict-mode T00 (#14) depend on #23 (MVC worksheet)?* Soft dependency -- the worksheet improves the copy choices but is not a blocker. If #23 slips, #14 can ship with the existing keys (which already exist in `lang/en/rapida.php`).
- *Does adding GPKG (#2) need the demo seeder (#28) updated to test it?* Unit test no; demo-day proof yes. The export job test can use `DamageReportFactory`. The proposal-claim verification ("works against the Accra demo") requires #28.
- *Does the AI Model Card (#7) depend on first verifying which AI model is wired?* Yes -- read above.

## 6. Per-gap implementation skeleton (Sprint 1)

Sprint 1 = gaps **1, 2, 4, 6, 14, 15, 28, 30**. The user's brief listed 1, 2, 4, 6, 14, 15; the dependency graph adds **28** and **30** because (a) #28 unblocks #4 and (b) #30 is half-a-day and shares the seeder file with #28. Gap #31 is Sprint 4; not detailed here.

### Gap 1 -- EXIF strip preserves GPS + timestamp

- **Branch:** `gap-01-exif-strip-preserve-gps`
- **Files to modify:**
  - `app/Jobs/ProcessPhotoUpload.php` -- replace the GD round-trip block with a call to a new `PhotoExifService`.
  - `app/Services/PhotoExifService.php` *(new)* -- strip device-identifying tags (`Make`, `Model`, `SerialNumber`, `LensMake`, `LensModel`, `LensSerialNumber`, `Software`, `OwnerName`, `BodySerialNumber`, `InternalSerialNumber`) while preserving GPS-block tags (`GPSLatitude`, `GPSLongitude`, `GPSAltitude`, `GPSTimeStamp`, `GPSDateStamp`) and `DateTimeOriginal`.
  - `composer.json` -- add `lsolesen/pel: ^0.10` if the spike succeeds; otherwise add a `symfony/process` based ExifTool wrapper and update `app/Services/PhotoExifService.php` accordingly.
  - `docs/architecture-data-lifecycle.md` section 6 -- update the "strips EXIF metadata" sentence to "strips device-identifying EXIF tags while preserving GPS and timestamp."
- **Tests to add:**
  - `tests/Feature/PhotoExifStripTest.php` -- three cases using a fixture JPEG with seeded EXIF: (a) device serial removed, (b) GPS preserved with same coordinates, (c) DateTimeOriginal preserved.
  - `tests/Unit/Services/PhotoExifServiceTest.php` -- direct service test, no DB.
- **Definition of done:**
  - Pest test asserts the device serial tag is gone after the job runs.
  - Pest test asserts GPS lat/lon match the input within float tolerance.
  - Pest test asserts DateTimeOriginal is preserved as ISO string.
  - `vendor/bin/pint --dirty --format agent` clean.
  - Updated `docs/architecture-data-lifecycle.md` section 6.
- **Skills:** `laravel-best-practices`, `pest-testing`.
- **Open question:** confirm Laravel Cloud runtime supports the chosen approach. If `lsolesen/pel` works (pure PHP), no runtime question. If we go ExifTool CLI, ask: does Laravel Cloud's PHP container ship `exiftool`, can we add it via `apt` in a build hook, or do we need a sidecar service?

### Gap 2 -- GeoPackage (GPKG) export

- **Branch:** `gap-02-gpkg-export`
- **Files to create / modify:**
  - `app/Jobs/ExportReportsGpkg.php` *(new)* -- sibling of `ExportReportsShapefile.php`; writes a `.gpkg` file (GPKG = SQLite container). Iterate `DamageReport::cursor()`. Consider: pure-PHP via PDO+SQLite (write `gpkg_contents`, `gpkg_geometry_columns`, and the actual feature table with WKB-encoded POINTs); or shell out to `ogr2ogr` (same runtime question as ExifTool -- flag for verification).
  - `app/Http/Controllers/ExportController.php` -- add `gpkg(Request)` method mirroring `shapefile()`.
  - `routes/web.php` -- wire `Route::get('/dashboard/export/gpkg', [ExportController::class, 'gpkg'])->name('export.gpkg')`.
  - `resources/views/components/organisms/data-export.blade.php` -- add GPKG button next to the existing four formats.
  - `lang/{6}/rapida.php` -- add `export_gpkg_label` and `export_gpkg_description` keys (must be added to all 6 lang files in the same PR -- see Cluster E warning).
  - `docs/architecture-data-lifecycle.md` section 3.7 -- update "Five formats" to "Six formats" and add `ExportReportsGpkg` to the file list.
- **Tests to add:**
  - `tests/Feature/ExportFormatsTest.php` -- extend existing test class with a `gpkg` case asserting (a) downloaded file starts with the SQLite magic header `SQLite format 3\0`, (b) the `gpkg_contents` table exists, (c) row count matches seeded reports.
- **Definition of done:**
  - Job exists, route exists, button renders in dashboard.
  - File downloaded by the controller passes a SQLite header check.
  - All 6 lang files updated.
  - Architecture doc updated.
- **Skills:** `laravel-best-practices`, `pest-testing`, plus `tailwindcss-development` for the button.
- **Open question:** PDO-SQLite vs `ogr2ogr` shell-out. Spike PDO first (one hour) -- if hand-writing the GPKG container is more than ~80 lines, fall back to `ogr2ogr` and flag the Laravel Cloud question.

### Gap 4 -- Live demo URL + UNDP evaluator credentials

- **Branch:** `gap-04-deploy-verify-evaluator-creds`
- **Depends on:** #28, #30 merged.
- **Files to modify:**
  - `docs/submission/written-proposal-v2.md` -- Section 6 evaluator-credentials block: confirm URL `https://rapida-main-6sutvc.laravel.cloud/crisis/accra-flood-2026`, list evaluator email/password (created in #30).
  - `docs/submission/deploy-verification-checklist.md` *(new)* -- manual-runbook checklist: deploy hash, seeder ran, evaluator login works, all five (six after #2) export buttons return 200, WhatsApp QR code present on landing page, language switcher works for all 6 locales, page loads under 3s on throttled 3G in DevTools.
- **Tests to add:** none (pure verification).
- **Definition of done:**
  - All checklist boxes ticked with date + commit hash.
  - Screenshots saved to `docs/submission/screenshots/2026-XX-XX-deploy-verify/`.
  - Proposal Section 6 reflects the actual URL, not a placeholder.
- **Skills:** none Laravel-specific; this is operations + screenshot capture.
- **Open question:** is the `rapida-main-6sutvc.laravel.cloud` URL stable, or will Laravel Cloud rotate the subdomain if the project is renamed? If unstable, register a CNAME (`demo.rapida.example`) before the URL is locked in the proposal.

### Gap 6 -- Test count claim correction

- **Branch:** `gap-06-test-count-honest-framing`
- **Files to modify:**
  - `docs/submission/written-proposal-v2.md` -- replace "397 automated tests" (if it appears in any pulled-down version of the doc) with "60+ Pest feature and unit tests covering report submission, conflict mode, export formats, AI classification, locale resolution, and WhatsApp bot flows." This is honest and stable.
  - PRD V2 on Notion -- same edit, done by hand outside the repo.
- **Tests to add:** none.
- **Definition of done:**
  - Repo proposal copy reflects actual count (re-run `find tests -name '*.php' | wc -l` immediately before the PR).
  - Notion PRD V2 updated (out-of-band, screenshot saved to `docs/submission/screenshots/`).
- **Skills:** none.
- **Open question:** does InnoCentive require a specific test-count number anywhere? If yes, lock it on submission day, not before.

### Gap 14 -- Conflict-mode transparency screen verification + lang parity

- **Branch:** `gap-14-conflict-transparency-lang-parity`
- **Depends on:** #25 merged (so we know which keys are missing in which locale).
- **Files to modify:**
  - `lang/{ar,es,fr,ru,zh}/rapida.php` -- add `transparency_conflict_1..4`, `transparency_conflict_cta`, `transparency_conflict_learn_more` if missing (gap #25 produces the diff list).
  - `resources/views/components/organisms/transparency-onboarding.blade.php` -- no changes needed unless #25 surfaces a structural issue; verify only.
  - Optional: `docs/mvc-worksheets/t00-transparency.md` (covered by #23, not duplicated here).
- **Tests to add:**
  - `tests/Feature/Components/TransparencyOnboardingTest.php` *(new)* -- render the component twice: once with a standard crisis, once with `conflict_context = true`; assert the conflict-bullet strings appear in the conflict variant and do not appear in the standard variant.
  - Use a Pest dataset of 6 locales (see section 8) to assert no raw-key fallbacks (no string starting with `rapida.transparency_conflict_`) appears in any locale.
- **Definition of done:**
  - All 6 lang files have the conflict keys.
  - Component test passes for both variants and all 6 locales.
- **Skills:** `pest-testing`, `livewire-development` (for the component test wrapper).
- **Open question:** does the conflict variant's `transparency_conflict_learn_more` link go anywhere yet, or is it a stub button? If stub, file a follow-up ticket -- do not expand scope here.

### Gap 15 -- WhatsApp GPS-prompt-in-conflict-mode verification + lang parity

- **Branch:** `gap-15-whatsapp-conflict-location-lang-parity`
- **Depends on:** #25 merged.
- **Files to modify:**
  - `lang/{ar,es,fr,ru,zh}/whatsapp.php` -- add `location_conflict_mode` if missing.
  - `app/Services/WhatsAppBotService.php` -- verify the conditional in `stepPhoto()` reads `session['conflict_context']`; no code change unless verification surfaces a bug.
- **Tests to add:**
  - Extend `tests/Feature/WhatsAppBotTest.php` -- new case: send a photo via webhook payload to a session whose crisis has `conflict_context = true`; assert the response message equals the rendered `whatsapp.location_conflict_mode` for the session's locale, in all 6 locales (Pest dataset).
- **Definition of done:**
  - All 6 lang files have the key.
  - Test passes for all 6 locales.
- **Skills:** `pest-testing`, `laravel-best-practices`.
- **Open question:** none.

### Gap 28 -- Demo crisis seeded with realistic 50-100 reports + footprints

- **Branch:** `gap-28-demo-seeder-realistic-data`
- **Files to modify:**
  - `database/seeders/DamageReportSeeder.php` *(new)* -- use `DamageReportFactory` to create 80 reports against `accra-flood-2026`, distributed roughly 40% minimal, 35% partial, 25% complete; randomise `submitted_at` within the past 14 days; geo-cluster around Plateau, Achimota, and Kaneshie districts (Accra) so the heatmap renders meaningfully.
  - `database/seeders/BuildingFootprintSeeder.php` *(new)* -- seed 200-500 OSM-derived building polygons within the Accra `map_tile_bbox`. Source: small extract from OSM Overpass API committed to `database/seeders/data/accra-buildings.geojson`.
  - `database/seeders/DatabaseSeeder.php` -- call the two new seeders after `CrisisSeeder` and `UndpUserSeeder`.
- **Tests to add:**
  - `tests/Feature/SubmissionFlowSmokeTest.php` *(new -- see section 8 verification scaffolding)* -- runs the seeders, asserts the Accra crisis has between 50 and 100 reports, has at least 100 building footprints, and that an unauthenticated request to `/crisis/accra-flood-2026` returns 200 and contains the rendered map element.
- **Definition of done:**
  - `php artisan migrate:fresh --seed` produces a usable Accra demo.
  - Smoke test passes.
  - Heatmap visually clusters in Accra at zoom 11.
- **Skills:** `laravel-best-practices`, `pest-testing`.
- **Open question:** OSM Overpass query and licence -- Microsoft Footprints is also an option. The proposal Section 5 already names Microsoft Footprints; preference is OSM for simplicity unless Microsoft footprints in Accra are richer.

### Gap 30 -- UNDP evaluator account exists with analyst role

- **Branch:** `gap-30-evaluator-account-seeder`
- **Depends on:** #28 (so the dashboard has data to display).
- **Files to modify:**
  - `database/seeders/UndpUserSeeder.php` -- add an `undp.evaluator@rapida.example` (or whatever address agreed in the InnoCentive form) account with role `analyst`. Generate a password and store the value out-of-band; commit only the email + the seeded role.
  - `docs/submission/evaluator-access.md` *(new)* -- single-page sheet for the InnoCentive submission: URL, email, password, what to look at first.
- **Tests to add:**
  - Extend `tests/Feature/DashboardTest.php` -- new case: log in as the seeded evaluator, assert access to `/dashboard/analyst` returns 200 and contains the seeded reports.
- **Definition of done:**
  - Seeder produces the evaluator account.
  - Test passes.
  - Access sheet exists and is gitignored or the password is rotated before submission.
- **Skills:** `laravel-best-practices`, `pest-testing`.
- **Open question:** password rotation -- should the evaluator account password be rotated to a fresh value on submission day? Recommendation: yes, and the access sheet should be regenerated last.

## 7. Cross-cutting risks

Three concrete risks worth pre-empting.

**Risk 1 -- `lang/{*}/rapida.php` merge-storm.** Five Sprint-1/Sprint-2 PRs (#2 GPKG label, #14 conflict-key parity, #15 WhatsApp-key parity, #18 edge-case microcopy, #20 WhatsApp confirm-locale) all add keys to the same lang files. If they run in parallel and each appends at the bottom, they will conflict on the closing `];`. Mitigation: enforce the Cluster E convention -- `gap-25` lands first as the "audit + insertion-marker" PR adding a `// === gap-N keys go above this line ===` comment block per file; subsequent PRs insert above the marker. Reviewer rule: any PR adding lang keys must rebase on `main` and re-resolve the marker before merge.

**Risk 2 -- Photo pipeline regression cascade.** Cluster A has three gaps that all touch the photo path: #1 server-side EXIF, #11 client-side compression, #26 multi-photo. If #11 ships before #1 and the JS compression silently strips EXIF before the server sees it, the proposed "preserve GPS" guarantee can never be tested at the server boundary. Mitigation: enforce sequencing #1 -> #11 -> #26; add the `tests/Feature/PhotoExifStripTest.php` from #1 to a CI gate that #11's PR cannot remove or skip.

**Risk 3 -- Demo data drift between seeder and proposal screenshots.** Once #3 (video) is recorded, the seeded report counts and locations are baked into the video. If a later PR (e.g., #16 AI suggestion turn indicator) modifies the wizard such that screenshots in the video become stale, the video has to be re-recorded. Mitigation: freeze `database/seeders/CrisisSeeder.php`, `DamageReportSeeder.php`, and `BuildingFootprintSeeder.php` to a tagged commit before video recording; any post-freeze change to those files requires explicit re-shoot approval.

## 8. Verification scaffolding (build once, reuse everywhere)

Three reusable pieces are worth landing early, ideally during Sprint 1.

**8.1 -- Six-locale Pest dataset.** A single dataset file used by every microcopy and lang-parity gap.

`tests/Datasets/Locales.php` (new):

```php
dataset('un_locales', ['en', 'fr', 'ar', 'es', 'zh', 'ru']);
```

Used by gaps #14, #15, #18, #20, #25 (and #20's confirm-locale flow) so each "does this string render in all 6 languages?" test is one line: `it('renders foo in all locales', function (string $locale) { ... })->with('un_locales');`.

**8.2 -- Photo EXIF fixture.** A single JPEG committed to `tests/Fixtures/photos/with-exif.jpg` containing a known device serial, known GPS coordinates, and known DateTimeOriginal. Used by #1's tests and any future photo-pipeline test. The fixture should be sourced by hand (one phone photo with the serial visible in `exiftool with-exif.jpg`) and the expected EXIF values committed alongside as `with-exif.expected.json` so tests can assert against constants instead of magic numbers.

**8.3 -- `tests/Feature/SubmissionFlowSmokeTest.php`.** A single end-to-end Pest test that #28 (demo seeder) introduces and #4 (deploy verify) and #30 (evaluator account) extend rather than duplicate. Three cases:

- *seeded crisis exists* -- asserts `Crisis::where('slug', 'accra-flood-2026')->exists()` and report count is 50-100.
- *unauthenticated reporter view loads* -- visits `/crisis/accra-flood-2026`, asserts 200 and map markup.
- *authenticated analyst view loads* -- logs in as the seeded evaluator, visits `/dashboard/analyst`, asserts 200 and the reports table.

This test becomes the demo-day smoke check -- the implementer running the Sprint 4 video shoot runs it before recording; if it fails, do not record.

## 9. Sprint summary

Sprint 1 (weeks 1-2, 8 gaps): 1, 2, 4, 6, 14, 15, 28, 30.
Sprint 2 (weeks 3-5, 9 gaps): 7, 8, 9, 10, 11, 12, 13, 25, 26, 27.
Sprint 3 (weeks 6-7, 9 gaps): 16, 17, 18, 19, 20, 21, 22, 23, 24.
Sprint 4 (weeks 8-9, 5 gaps): 3, 5, 29, 31 (English-primary alignment), plus the test-count rebaseline if not already done.

The submission gate at end of Sprint 4 is: `php artisan test --compact` green, `vendor/bin/pint --dirty --format agent` clean, `tests/Feature/SubmissionFlowSmokeTest.php` green against the live deploy, all 12 boxes in `docs/submission/written-proposal-v2.md` ticked, all 6 lang files passing the parity dataset, the evaluator-access sheet handed to the InnoCentive form, and the 2-minute video uploaded.

---

# Appendix -- Systems-Requirements Audit (gaps 32-47)

The 31-gap roadmap above is file-level. This appendix audits cross-cutting systems requirements that no single PR can address. Frame: the four-layer model from `docs/architecture-data-lifecycle.md` (Feature, Logic, Design System, UI). The 31-gap roadmap covers Feature/UI; this appendix covers User-journey, Logic-invariant, and Design-system-as-system requirements.

Three personas anchor the user-layer audit (V1: A Community Reporter, B Field Coordinator, C UNDP Analyst, D UNDP Operator; V2: E Conflict-Zone Reporter). The Logic-layer audit walks the canonical services already named in `docs/architecture-data-lifecycle.md` and asks whether their invariants hold at every entry point. The Design-System audit treats `config/rapida-tokens.php` plus the `resources/views/components/` tree as one system, not 38 components.

## A1. User-level systems requirements

### Persona A -- Community Reporter

**System requirement.** A reporter on a 3G phone must land on `/crisis/{slug}`, submit a credible report (photo + location + damage = score >= 6), receive a confirmation, and re-find that report under `/my-reports` -- without ever being asked to register an account.

**Coverage today.** The 6-step wizard renders. `ReportSubmissionService::submit()` accepts `accountId = null` and resolves `reporter_tier = 'device'` when a fingerprint cookie is present. `/my-reports` (`routes/web.php:76-100`) does the right query against `device_fingerprint_id`. The `CompletenessScoreService` matches PRD V2.

**What is missing.** No single test walks the *anonymous-reporter end-to-end re-find* path. The `rapida_device_fingerprint` cookie is read by `routes/web.php:87` but no JS in `resources/js/` writes it -- either the JS that sets it is missing, or it is set somewhere unexpected. A browser test would reveal which.

**Test approach.** Pest 4 browser test (`visit('/crisis/accra-flood-2026')->click()->fill()->...`). Two assertions: (a) after submit, `/my-reports` shows the just-submitted card; (b) after `setCookie` on a fresh browser context, `/my-reports` returns the same card.

### Persona B -- Field Coordinator

**System requirement.** A field coordinator authenticated as `field_coordinator` must log into `/dashboard/field`, see the live pin map, flag a duplicate or low-quality report, and have that flag visible to the analyst within the same session -- on a phone with intermittent connectivity.

**Coverage today.** Routes and policy permit it. `tests/Feature/DashboardTest.php` exists (coverage of the field-coordinator-flag path needs verification).

**What is missing.** Two concerns: (1) no `CrisisPolicy` permissions for `field_coordinator` (passes `viewAny` for all UNDP staff but no scoped read); (2) the PWA `service-worker.js` is reporter-facing only (`/api/v1/reports`, `/buildings`, `/pins`, `/heatmap`). A field coordinator's flag POST is not background-sync registered -- offline flags silently fail.

**Test approach.** Multi-step Pest service test: authenticate as `field_coordinator`, POST `/dashboard/reports/{report}/flag`, assert `is_flagged = true` and a `ReportVersion` row was created. Plus an arch test asserting every UNDP-staff write route has either background-sync registration or an explicit "online-only" comment.

### Persona C -- UNDP Analyst

**System requirement.** An analyst must filter reports by damage level + date, export the filtered set in any of the (5, soon 6) formats, and have the export reflect the current filter -- with the export PDF rendering in the analyst's preferred locale, not always English.

**Coverage today.** Login, dashboard, filters, exports all wired. `tests/Feature/ExportFormatsTest.php` exists.

**What is missing.** Two concerns. (1) **Locale leak in queued export jobs.** `ExportReportsPdf` is `ShouldQueue`. When dispatched from a request whose locale was resolved to `fr`, the queue worker runs in a fresh container and `app()->getLocale()` returns the fallback -- so PDF renders in English regardless. (2) **Export hard-codes the active crisis.** `ExportController::csv()` does `Crisis::where('status', 'active')->firstOrFail()`. With two seeded active crises (Accra flood + Aleppo conflict), `firstOrFail()` is non-deterministic; the dashboard does not pass `crisis_slug` to the export route. **Real bug, not theoretical.**

**Test approach.** Pest service test: seed two active crises, hit `/dashboard/export/csv?crisis_slug=accra-flood-2026`, assert only Accra reports in the CSV. Plus a translation test that locks queue locale: dispatch `ExportReportsPdf` with `app()->setLocale('fr')`, assert the rendered PDF contains French labels.

### Persona D -- UNDP Operator

**System requirement.** An operator spins up a new crisis end-to-end in under 4 hours: create crisis, configure modules, seed landmarks, activate, generate field-coordinator login, watch first reports flow in, download first export, broadcast first recovery outcome.

**Walkthrough.** Steps 1, 4, 6 work cleanly. Steps 2, 3, 5, 7 are silently degraded. Step 8 logs but does not deliver.

1. **Create crisis** -- Works. *Soft gap:* Livewire component does not call `Gate::authorize('create', Crisis::class)`; only `EnsureIsOperator` middleware gates it.
2. **Configure modules** -- Form omits `conflict_context`, `whatsapp_enabled`, `wizard_mode`, `multi_photo_enabled`, `crisis_type_default`, `data_retention_days`. Six configurable model fields cannot be set through UI. **Silently broken for the conflict-mode use case** -- operators must drop into tinker to enable conflict mode.
3. **Seed landmarks** -- One-at-a-time entry only. No bulk import. 4-hour spin-up impossible if there are 200 landmarks.
4. **Activate** -- Works.
5. **Field coordinator login** -- `user-manager` Livewire component exists but role assignment, crisis-scoping, and password reset are not visibly wired. **Likely silently broken**.
6. **Reports flow** -- Works. `BackpressureThrottle` middleware wired correctly.
7. **First export** -- Crisis-disambiguation bug from Persona C bites here.
8. **First recovery outcome** -- `RecoveryOutcomeController::store` requires `auth:undp` only; no `RecoveryOutcomePolicy` -- any UNDP user (incl. field coordinator) can broadcast. The `BroadcastRecoveryOutcome` job logs but **does not actually deliver anything** (no email, no push, no WhatsApp -- it is a stub).

**Test approach.** Pest browser test (`OperatorSpinUpTest.php`) walks `/admin/crises -> /admin/landmarks -> /admin/users`, asserts each step produces a queryable row, then logs in as the seeded field coordinator and submits a report against the new crisis, then logs back in as operator and downloads the export, then broadcasts a recovery outcome and asserts the banner renders for an authenticated reporter in the affected H3 cell.

### Persona E -- Conflict-Zone Reporter

**System requirement.** A reporter in a conflict zone submits a useful report without leaking identifying information: no device fingerprint stored, no photo EXIF preserved (other than coarse timestamp), no GPS pin (street/three-word code only), no leaderboard entry, no badge, no WhatsApp metadata. Transparency screen explicitly enumerates these protections in their language.

**Coverage today.** Conflict-mode plumbing exists in five places: `ConflictModeService::applyToSubmission()`, `WhatsAppBotService::stepPhoto()`, `BadgeService` early-return for null `account_id`, `transparency-onboarding.blade.php` `$conflictContext` branch, Aleppo conflict crisis seeded.

**What is missing -- eight concrete leaks.**

1. **Photo EXIF.** Existing gap #1. After fix, GPS will be preserved unless the EXIF service is conflict-mode-aware. Cross-cutting.
2. **Photo perceptual hash.** Stored unconditionally. A correlation primitive even if not directly identifying.
3. **Badge for logged-in accounts.** `BadgeService` early-returns only when `account_id` is null. A logged-in account submitting under conflict mode still has `account_id` and **still earns badges**.
4. **WhatsApp session metadata.** SHA-256 hash of phone number is functionally equivalent to phone number for tracking. Mitigation today is `whatsapp_enabled = false` per crisis (Aleppo seeded that way), but no architectural test enforces "conflict crises must have whatsapp_enabled = false."
5. **JS device fingerprinting.** No `device_fingerprint` references in `resources/js/`. The cookie reader exists but no writer. Gap is "fingerprinting either does not exist (then field is dead code) or it exists somewhere I missed -- needs an arch test that bans the writer when conflict mode is active."
6. **Audit log.** No `audit_log` table exists. `ReportVersion` covers DamageReport but nothing covers Crisis activation, UndpUser role grants, or RecoveryOutcome broadcasts.
7. **AI sidecar PII.** `ClassifyDamageWithAI` POSTs `photo_url` to an external service. `DispatchReportProcessing` has no conflict-mode gate. **Real leak.**
8. **PWA cache.** Service-worker caches by URL, not by locale or conflict-flag. Switching between a normal and conflict crisis can return wrong cached views.

**Test approach.** A `ConflictModeIntegrationTest.php` with one test per leak above, all sharing a "create conflict crisis, submit report" arrange step. Plus a Laravel arch test (`tests/Arch/ConflictModeArchTest.php`) asserting every method named `dispatch*`, `award*`, or `track*` consults `ConflictModeService` before doing its thing.

## A2. Logic-layer systems invariants

### Invariant 1 -- Conflict-mode propagation

**Statement.** For any DamageReport whose crisis has `conflict_context = true`: device_fingerprint_id is null, reporter_tier is 'anonymous', no Badge row created, no leaderboard entry, no WhatsApp session cached, AI classification job not dispatched, transparency screen renders conflict variant.

**Where enforced.** `ReportSubmissionService.php:22` -> `ConflictModeService::applyToSubmission()`. `WhatsAppBotService.php:81`. `transparency-onboarding.blade.php`.

**Where silently violated.** `BadgeService::checkAndAwardBadges()` (`app/Services/BadgeService.php:14-61`) -- no consult. `DispatchReportProcessing::handle()` (`app/Listeners/DispatchReportProcessing.php:19-46`) -- no consult. `BroadcastRecoveryOutcome::handle()` -- no consult. `crisis-manager` Livewire does not surface `conflict_context` as editable -- operators cannot turn it on or off from the UI.

**Suggested guard.** Pest arch test on every class under `app/Services/` and `app/Listeners/` with method names matching `award*|dispatch*|notify*|track*|broadcast*|enqueue*` -- must reference `ConflictModeService` somewhere in the class body.

### Invariant 2 -- Idempotency

**Statement.** For any DamageReport with non-null `idempotency_key`, exactly one row is created regardless of replay across all entry points.

**Audit result.** Holds today. `ReportSubmissionService::submit()` lines 25-30 are the single canonical entry point; web wizard, API controller, WhatsApp service, and Livewire admin all funnel through it. No console command writes DamageReport rows. Queue retries cannot re-create.

**Risk.** Future drift -- any new write path bypasses the gate. Suggest arch test that `DamageReport::create(` is only called inside `ReportSubmissionService.php`.

### Invariant 3 -- Completeness score formula matches PRD V2

**Audit result.** **Matches.** `CompletenessScoreService.php:9-34` implements the V2 formula (max 8, threshold 6 = photo + location + damage). Comment on line 33 makes it explicit. `tests/Feature/Services/CompletenessScoreV2Test.php` likely locks it. Verification only -- read the test to confirm threshold is asserted.

### Invariant 4 -- Audit trail completeness

**Statement.** Every mutation of a DamageReport row produces a ReportVersion row.

**Where enforced.** `DamageReport::booted()` (`app/Models/DamageReport.php:106-127`) hooks `static::updating()`.

**Audit result.** All current code paths use `$model->update(...)` (which fires events) -- no bulk `Model::query()->update(...)` patterns exist. Soft delete fires `updating`. Queue jobs use the per-instance update. **Holds today.**

**Cross-model gap.** No equivalent for Crisis, Landmark, RecoveryOutcome, or UndpUser. Operator changes leave no audit trail.

### Invariant 5 -- Spatial snap correctness

**Statement.** For any DamageReport with non-null lat/lng, `snapToNearest()` returns closest Building within 100m or null. Concurrent reports for same building reach canonical recompute consistently.

**Where silently violated.** `ReportSubmissionService::submit()` accepts explicit `building_footprint_id` from the wizard but never calls `snapToNearest()` server-side. Reports without a footprint match never participate in canonical ranking and never appear in the building-footprint pin layer. **Either snap should run server-side asynchronously, or the wizard's snap is the only snap and a wizard regression breaks the building map.**

### Invariant 6 -- Locale resolution across the queue boundary

**Statement.** Every job producing user-facing text renders in the locale that was active when dispatched, not the worker's default.

**Where silently violated.** `ExportReportsPdf` -- queue worker locale resets to fallback. `BroadcastRecoveryOutcome` (when implemented) will break the same way. `TranslateDescription` is locale-insensitive (target always 'en'). `ClassifyDamageWithAI` is locale-insensitive (external service).

**Suggested guard.** Constructor pattern: every locale-sensitive job takes `string $locale` captured at dispatch time, wraps `handle()` in `app()->setLocale($this->locale)`. Arch test enforces.

### Invariant 7 -- Authorization completeness

**Statement.** Every UNDP-write route is gated by both auth middleware and explicit policy check inside the controller or Livewire component.

**Where silently violated.** **`CrisisPolicy` is registered (`AppServiceProvider.php:29`) but never invoked anywhere in code.** `crisis-manager` Livewire write methods do not call `$this->authorize(...)`. **No `LandmarkPolicy`.** **No `RecoveryOutcomePolicy`** -- any UNDP user can broadcast, including field coordinators. **No `UndpUserPolicy`** -- `user-manager` exposes role grants without policy.

**Suggested guard.** Three new policies, registrations, explicit `$this->authorize(...)` in every admin Livewire write method. Arch test: every Livewire component class with method matching `create|update|delete|toggle` must call `$this->authorize(`.

## A3. Design-system systems requirements

### Requirement 1 -- Token coverage

`config/rapida-tokens.php` has 8 entries (5 map colors, 3 chart). That is the entire token surface. No tokens for type scale, spacing scale, motion, focus rings, calm-palette, conflict-mode variants, or trauma-informed semantic states. Damage colors are duplicated in three places: config, `resources/js/rapida-map.js` `DEFAULT_TOKENS`, and **inline blade scripts** in `step-location.blade.php:94-96` and `field-map.blade.php:98-100` (literal hex). Suggested: `docs/design-tokens.md` reference + Pest arch test banning hex literals in `resources/views/components/` except in `data-*` config-fed attributes.

### Requirement 2 -- RTL systematicness

Layout-level `dir="rtl"` is set correctly. The typography token doc recommends logical properties (`ps-`, `pe-`). But the actual atom files (`text-input.blade.php`, `radio-group.blade.php`, `button.blade.php`) use `pl-`, `pr-`, `ml-`, `left-3` -- physical, not logical. Arabic dir-flip will look wrong on these. Arch test: ban `pl-|pr-|ml-|mr-|left-|right-` in components except via explicit allowlist.

### Requirement 3 -- A11y contract per atom

ARIA usage is sporadic but real (text-input has `aria-required`, `aria-invalid`, `aria-describedby`; loader has `role="status"`). Tests render-and-assert structure but no documented per-component contract and no keyboard-navigation tests. Format: `docs/components/{atom}.md` with ARIA + keyboard table; one Pest browser test per atom asserting Tab, Enter/Space, Escape behaviour. Folds neatly into existing gap #17 (copy contracts).

### Requirement 4 -- Trauma-informed rules as code

Touch targets correct (`h-12` = 48px). `prefers-reduced-motion` honoured globally. **VIOLATION:** `button.blade.php` line 15 uses `bg-red-700` for `danger` variant. `text-input.blade.php` lines 30, 46, 74 use `border-red-600`, `bg-red-50`, `text-red-700` for error states. Token doc explicitly says "Never use red." `crisis-rose-400` (`#c46b5a`) is the right replacement. Arch test bans `red-*` Tailwind classes in components.

### Requirement 5 -- Cross-locale typography

Self-hosted: Inter, Noto Sans (Latin), Noto Sans Arabic. **CJK is missing** -- Chinese reporters fall back to system fonts. Cyrillic depends on undocumented Noto Sans coverage. Noto Sans Arabic is loaded but not in body font-family chain. Three concrete fixes: add `Noto Sans CJK` for `zh`; add `:lang(ar)` font-family rule; assert Cyrillic glyph coverage.

### Requirement 6 -- Map damage-color token reuse

Config defines tokens; JS map modules consume via constructor injection. **Two violations:** `step-location.blade.php` and `field-map.blade.php` hardcode the same hex strings instead of reading from `config('rapida-tokens.map')`.

### Requirement 7 -- Empty/error/loading states as a system

Loading state is unified (`loader.blade.php`). Empty and error states are not -- each list view writes its own `<tr><td colspan>...</td></tr>` empty row. Suggested: `<x-molecules.empty-state>` and `<x-molecules.error-state>` reused everywhere.

## A4. New gaps (32-47)

| #  | Title                                                              | Layer    | Complexity | Risk | Sprint | "Done" looks like                                                                 |
|----|--------------------------------------------------------------------|----------|------------|------|--------|-----------------------------------------------------------------------------------|
| 32 | Anonymous reporter end-to-end re-find loop                         | User (A) | M          | Low  | 2      | Pest 4 browser test walks wizard -> `/my-reports`, asserts cookie-keyed re-find. JS that sets cookie verified. |
| 33 | Field coordinator offline flag queueing                            | User (B) | M          | Med  | 2      | `BackgroundSyncPlugin` registered for flag/verify/assign. Arch test enforces every UNDP-staff write route is offline-queued or marked online-only. |
| 34 | Export crisis-disambiguation                                       | User (C) | S          | Low  | 1      | Export controller methods take `crisis_slug` query param. Pest test seeds two active crises and asserts isolation. |
| 35 | Operator end-to-end spin-up coverage                               | User (D) | XL         | High | 3      | crisis-manager exposes 6 missing fields. Landmark bulk-import via CSV. user-manager exposes field-coordinator role. RecoveryOutcomePolicy added. End-to-end Pest browser test walks the 4-hour flow. |
| 36 | Conflict-mode coverage in BadgeService and AI dispatch             | Logic 1  | M          | High | 1      | `BadgeService` consults `ConflictModeService` and skips badge awards when conflict mode active. `DispatchReportProcessing` skips AI dispatch in conflict mode. Arch test enforces. |
| 37 | Crisis/Landmark/RecoveryOutcome/UndpUser audit trail               | Logic 4  | L          | Med  | 3      | New `audit_logs` table or per-model `*_versions` tables. Every operator-side mutation produces an audit row. |
| 38 | Server-side spatial snap for orphaned reports                      | Logic 5  | S          | Low  | 2      | Queued `SnapReportToFootprint` job runs after `ReportSubmitted`; orphan with lat/lng but no `building_footprint_id` becomes associated. |
| 39 | Locale-aware queue job pattern                                     | Logic 6  | M          | Med  | 2      | Every locale-sensitive job takes `string $locale` constructor param. Sets locale at handle time. Arch test enforces. |
| 40 | Authorization completeness (Landmark/RecoveryOutcome/UndpUser policies) | Logic 7 | M       | Med  | 2      | Three new policies registered, called explicitly by every admin Livewire write method. Arch test enforces. |
| 41 | Token coverage doc + arch test (no raw hex in components)          | Design 1 | M          | Low  | 3      | `docs/design-tokens.md` enumerates every token. Arch test bans hex literals in components. Two existing offenders refactored. |
| 42 | RTL systematicness (logical properties)                            | Design 2 | L          | Med  | 4      | All atoms/molecules use `ps-`, `pe-`, `start-`, `end-`. Arch test bans physical-property classes. RTL screenshot test for one Arabic page. |
| 43 | A11y contract per atom (Tab/Enter/Escape)                          | Design 3 | L          | Low  | 3      | `docs/components/{atom}.md` per atom. Pest browser test per atom. Folds into #17. |
| 44 | Trauma-informed rules as code (no pure red)                        | Design 4 | M          | Low  | 1      | Arch test bans `red-*` Tailwind in components. `button.blade.php` `danger` + `text-input.blade.php` errors refactored to `crisis-rose-*`. |
| 45 | Cross-locale typography (CJK + Arabic font-family + Cyrillic)      | Design 5 | M          | Low  | 2      | Noto Sans CJK self-hosted. Arabic added via `:lang(ar)`. Cyrillic glyph coverage asserted. |
| 46 | Map damage-color token source-of-truth                             | Design 6 | XS         | Low  | 2      | step-location and field-map read tokens from `config('rapida-tokens.map')` instead of hardcoding hex. |
| 47 | Empty-state and error-state molecules                              | Design 7 | S          | Low  | 3      | `<x-molecules.empty-state>` and `<x-molecules.error-state>` exist and replace duplicated patterns. |

Updated sprint allocation: Sprint 1 +#34, +#36, +#44 (small wins or critical safety gates). Sprint 2 +#32, +#33, +#38, +#39, +#40, +#45, +#46. Sprint 3 +#35, +#37, +#43, +#47. Sprint 4 +#42.

## A5. System-level umbrellas

Three umbrellas where 2+ existing gaps share an invariant and should ship together:

**Umbrella 1 -- Conflict-mode coverage (#14 + #15 + #36).** Individually verifiable but pieces of a larger contract. Ship together with `tests/Feature/ConflictModeIntegrationTest.php` that creates the Aleppo conflict crisis, exercises every entry point (web wizard + WhatsApp + AI dispatch + badge check), and asserts the union of invariants holds.

**Umbrella 2 -- PWA cache freshness (#11 + service-worker locale partition + #33).** Both #11 (client photo compression) and #33 (field-coordinator offline flag) touch `service-worker.js` registrations. Ship together with a `cacheKey` strategy that includes active locale and conflict-flag in cache name (e.g., `rapida-pins-v1-{locale}-{conflict}`).

**Umbrella 3 -- Lang-key parity + locale-aware queue (#25 + #14 + #15 + #20 + #39).** #25 must land first (audit + insertion-marker convention). Then #14, #15, #20 add new keys in 6 locales in parallel. Then #39 last (which now has 6 locales of strings to assert against in its tests).

## A6. Pre-existing tests this audit relies on

Extend rather than duplicate:

- `tests/Feature/ConflictModeTest.php` -- add cases for BadgeService, AI dispatch, admin-UI conflict_context exposure.
- `tests/Feature/LocaleResolutionTest.php` -- new queue-locale tests reuse the 6-locale dataset.
- `tests/Feature/ReportSubmissionTest.php`, `tests/Feature/Services/ReportSubmissionServiceTest.php` -- idempotency arch test references these for the canonical entry point.
- `tests/Feature/ReportVersioningTest.php` -- new audit-trail tests for Crisis/Landmark/RecoveryOutcome mirror this structure.
- `tests/Feature/Services/CompletenessScoreV2Test.php` -- already locks the V2 formula; Invariant 3 relies on this.
- `tests/Feature/ExportFormatsTest.php` -- the place to add #34 (crisis-disambiguation) and #39 (locale-aware export).
- `tests/Feature/Components/TransparencyScreenTest.php` -- conflict variant likely covered; the umbrella test reuses the same fixture.
- `tests/Feature/Middleware/VerifyInternalSecretTest.php` -- AI callback security test. Conflict-mode AI-skip test (#36) sits alongside.
- `tests/Feature/DashboardTest.php` -- the place to add field-coordinator-flag (Persona B) and operator-spin-up (Persona D).

Audit shape: **16 NEW gaps, 3 umbrellas, 9 pre-existing tests to extend.** The 31-gap roadmap remains correct at the file level; this appendix sits above it. Total gap count: **47**.

---

### Critical files for implementation

File-level gaps:
- `app/Jobs/ProcessPhotoUpload.php`
- `app/Http/Controllers/ExportController.php`
- `database/seeders/CrisisSeeder.php`
- `lang/en/rapida.php`
- `docs/submission/written-proposal-v2.md`

Systems-requirements gaps:
- `app/Services/ConflictModeService.php`
- `app/Services/BadgeService.php`
- `app/Listeners/DispatchReportProcessing.php`
- `app/Policies/` (3 new files: LandmarkPolicy, RecoveryOutcomePolicy, UndpUserPolicy)
- `resources/views/components/admin/⚡crisis-manager.blade.php`
- `resources/views/components/atoms/button.blade.php`
- `resources/views/components/atoms/text-input.blade.php`
- `resources/js/service-worker.js`
- `tests/Arch/ConflictModeArchTest.php` (new)
- `tests/Feature/ConflictModeIntegrationTest.php` (new)
- `docs/design-tokens.md` (new)
