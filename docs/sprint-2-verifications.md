# Sprint 2 Verifications — Findings

Read-only audit of four claims that needed checking against the codebase. For each: what the claim is, what the code actually does, and whether a follow-up fix is needed.

## Gap #9 — Aggregated neighborhood feedback to reporter

**Claim (PRD V2 §4):** The submission confirmation page implements a 3-state Impact Counter showing "Your report joins N others from {district}. M field teams active."

**Code reality:** Partial. `resources/views/templates/submission-confirmation.blade.php` passes `communityCount: 142` and `userReportCount: 3` to `<x-organisms.engagement-panel>` — so the UI slot exists. But the values are **hardcoded placeholders**, not computed from `AnalyticsQueryService` or scoped to the reporter's H3 cell.

**Verdict:** UI structural scaffolding present; data wiring missing.

**Follow-up gap (file as #49):** Wire the engagement-panel's `communityCount` to a real count of reports in the same H3 cell as the just-submitted report (use `AnalyticsQueryService` + the existing `h3_cell_id` index). For account-bound reporters, `userReportCount` should be `account.report_count`. State 2 (RAPIDA-integrated) and State 3 (Recovery outcome) per PRD V2 §4 are separate concerns wired through `RecoveryOutcome` broadcasts.

## Gap #10 — LibreTranslate wired

**Claim:** Open-source translation pipeline uses LibreTranslate.

**Code reality:** ✅ Honest. `app/Jobs/TranslateDescription.php:32-46` does an `Http::timeout(10)->connectTimeout(5)` against `config('services.libretranslate.url')` with a typed payload (`q`, `source`, `target='en'`, `format='text'`). Optional `api_key` supported. Wrapped by `CircuitBreakerService::isAvailable('libretranslate')` at the dispatcher level (`DispatchReportProcessing.php:40-44`). Skipped under high pressure or when the circuit is open.

**Verdict:** No code change needed. The "how to add a 7th language" doc that the master roadmap proposed is the only remaining work — that's a separate doc-only follow-up, not a code gap.

## Gap #11 — Client-side photo compression to <500KB

**Claim (proposal Section 5):** "A 5MB camera photo gets compressed to under 500KB automatically — so it uploads even on the slowest networks. If even that fails, the report saves on the phone and sends itself when connection returns."

**Code reality:** **No client-side compression exists.** Grep across `resources/js/` and `resources/views/components/wizard/` for `compress`, `resize`, `imagecreatefrom`, `FileReader`, `imagebitmap`, `toBlob`, `canvas` returns zero matches. Server-side processing (`ProcessPhotoUpload` job after gap-01) handles EXIF stripping but does no compression. So a 5MB phone photo is uploaded raw — exactly the failure mode the proposal claims is avoided.

**Verdict:** Proposal makes a false claim. Real gap.

**Follow-up gap (file as #50, S–M complexity):** Add a JS-side compression step to the photo upload component (`step-photo.blade.php` Alpine component). Two viable approaches:
1. `browser-image-compression` npm package (well-maintained, ~30KB, handles EXIF orientation correctly).
2. Hand-rolled canvas resize: `createImageBitmap(file)` → draw to canvas at max 1920px longer-edge → `canvas.toBlob('image/jpeg', 0.85)`. ~30 lines, no dependency.

Either approach should target ≤ 500KB output size and run before the upload reaches the server. The server-side EXIF strip from gap-01 still runs after, so GPS+timestamp preservation is preserved.

## Gap #13 — Telegram channel mention in proposal

**Claim (PRD V2 changelog #10):** Proposal narrative mentions Telegram as a roadmap channel.

**Code reality:** ✅ Honest. `docs/submission/written-proposal-v2.md` has two explicit mentions:
1. Section 2: "We also built the backend with a channel-agnostic adapter pattern, so Telegram and SMS can be added without restructuring."
2. Section 2 resilience paragraph: "a channel-agnostic messaging adapter that currently serves WhatsApp with Telegram architecturally ready."

**Verdict:** No change needed.

## Summary

| # | Claim | Status | Follow-up |
|---|-------|--------|-----------|
| 9 | Impact counter | Partial (hardcoded placeholder) | New gap #49 — wire to AnalyticsQueryService + H3 cell |
| 10 | LibreTranslate | ✅ Honest | None |
| 11 | Client-side photo compression | **False claim** | New gap #50 — add JS-side compression |
| 13 | Telegram mention | ✅ Honest | None |

Two new follow-up gaps surfaced (#49, #50). Both touch user-facing flows and should land before submission day.
