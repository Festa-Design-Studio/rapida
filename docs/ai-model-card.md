# RAPIDA — AI Model Card

UNDP webinar (April 8, 2026) requires AI-assisted damage classification to be **explainable** with documented model assumptions and reasoning. This card describes the AI component that ships with RAPIDA: how it is wired, what it claims, what it does not claim, and how a UNDP analyst should interpret its output.

## What the AI does

When a community reporter submits a photo via the wizard or WhatsApp, RAPIDA dispatches `ClassifyDamageWithAI` (`app/Jobs/ClassifyDamageWithAI.php`) which POSTs the photo URL to an external AI sidecar service. The sidecar returns a damage-level suggestion (one of `minimal`, `partial`, `complete`) and a confidence score (0.0–1.0) via callback to `/api/v1/internal/ai-result`.

The suggestion is stored on `damage_reports.ai_suggested_level` alongside the reporter's own choice in `damage_level`. The reporter's choice is always authoritative; the AI suggestion is advisory only and is shown to analysts in the dashboard for cross-reference.

**Key files:** `app/Jobs/ClassifyDamageWithAI.php`, `app/Http/Controllers/Api/ApiAiController.php`, `app/Models/DamageReport.php` (`ai_suggested_level`, `ai_confidence`)

## Model identity (planned)

The AI sidecar is a separate service designed to be swappable. The sidecar URL and shared secret are configured via `services.ai.url` and `services.ai.secret`; the sidecar implementation is not committed to this repo because it runs as a Python FastAPI process on its own runtime (ONNX inference does not need PHP).

The planned model per PRD V2 §3.6:

| Field | Value |
|-------|-------|
| Architecture | EfficientNet-B3 |
| Format | ONNX (CPU-inference, no GPU required) |
| Training data | xBD disaster-imagery dataset (Maxar Technologies, open-source) |
| Output classes | 3 (minimal / partial / complete damage) |
| P95 latency target | < 2 seconds end-to-end |
| Hosted on | Laravel Cloud sidecar process (planned) |

**Status as of 2026-04-25:** the application-side wiring (job, callback endpoint, secret-validation middleware) is complete and tested. The sidecar service is not yet deployed; until it ships, `services.ai.url` is unset in production and `ClassifyDamageWithAI` no-ops with a warning log line. The data model (`ai_suggested_level`, `ai_confidence` columns) is in place and ready to receive callbacks the moment the sidecar comes online.

## Known biases and limitations

These are documented up front so a UNDP analyst can correctly weight the AI suggestion against ground truth.

1. **Geographic bias.** xBD coverage skews toward post-disaster sites in the Americas, the Pacific, and parts of Europe. Reports from regions under-represented in xBD (informal urban settlements in West Africa, Central Asian villages, low-rise mud-brick construction) will see lower model confidence and a higher reporter-override rate. This is expected, not a defect.
2. **Vegetation interference.** Buildings obscured by tree cover or post-event vegetation damage are systematically under-classified by EfficientNet-B3. The model learns from clear top-down or front-on shots; oblique angles with foreground clutter degrade performance.
3. **Roof-damage vs structural-damage conflation.** The xBD class labels do not separate "intact frame, roof torn off" from "frame collapsed." A report classified `complete` by the model may be either, and only the photo's full context (visible to the reporter and the analyst) can disambiguate. This is why the reporter's choice overrides.
4. **Two-class blur (partial vs complete).** Inter-class confusion is highest between `partial` and `complete` in the training data. The model's confidence score is calibrated such that 0.6–0.8 in either of these classes typically means "the model is genuinely uncertain"; values above 0.9 are more reliable.
5. **No detection of secondary impacts.** The model classifies *visible structural damage only*. It does not infer service-disruption (water, electricity, healthcare access) or pressing needs — those come from the modular form responses, never from the photo.

## How analysts should read confidence scores

The dashboard displays `ai_confidence` as a percentage alongside the suggested level. Suggested interpretation:

| Confidence | What it means | Analyst action |
|------------|---------------|----------------|
| ≥ 0.90 | Model is highly confident in this class | Use as a quick triage signal; if reporter agrees, proceed to verification queue normally |
| 0.70–0.89 | Reasonable suggestion | Trust the model less; weight reporter's choice and photo evidence equally |
| 0.50–0.69 | Genuine model uncertainty | Treat as "no AI signal" — base verification entirely on the reporter's choice and the photo |
| < 0.50 | Below-floor; model is guessing | Same as no signal. The reporter–AI disagreement metric should not flag these |

These thresholds are conservative starting points. Production tuning should re-baseline after the first 10,000 callbacks in real crises.

## Reporter–AI divergence as a quality signal

Over time, the divergence rate between `damage_level` (reporter's choice) and `ai_suggested_level` (model's suggestion) is itself a useful signal:

- **High divergence in a localized area** suggests either (a) building stock the model wasn't trained on, or (b) a coordinated bad-actor flood inflating damage levels.
- **High divergence from a single account** is a fingerprint of a bad actor (and is a separate input to the reputation system in `BadgeService` and `DuplicateDetectionService`).
- **High divergence at high model confidence** is the strongest "investigate" signal — reporter and model both think they're right and disagree.

Analysts can sort the verification queue by `|damage_level - ai_suggested_level|` plus `ai_confidence` to surface these cases first.

## Privacy posture

In conflict crises (`crisis.conflict_context = true`), the AI dispatch is gated by `ConflictModeService` (see `app/Listeners/DispatchReportProcessing.php` after gap-36) — the photo URL never leaves the controlled storage environment for an external classification call. This is a deliberate trade-off: in conflict zones, photo content reaching a third-party endpoint is a real privacy risk that outweighs the marginal benefit of an AI suggestion.

For non-conflict crises, photo URLs are sent to the sidecar but the URL itself reveals only the storage path (`photos/{uuid}.jpg`), which is non-PII. The photo content does leave the controlled environment to be inspected by the model.

## What this card is not

- **Not a benchmark report.** No accuracy / precision / recall numbers are claimed because the production sidecar is not yet live and no baseline has been measured against the seeded demo data.
- **Not a research paper.** xBD methodology is documented at xview2.org; this card does not re-derive that.
- **Not legal compliance documentation.** UNDP data-governance compliance specifics are tracked separately in `docs/architecture-data-lifecycle.md` §5.

## Updating this card

When the production sidecar is wired, update Sections "Model identity (planned)" with the actual deployment hash and "Known biases" with measured rather than projected behaviour.
