# RAPIDA — Complexity Separation & Architecture Design

**Date:** April 16, 2026
**Author:** Abayomi Ogundipe (Festa Design Studio) + Claude Code
**Status:** Approved — ready for implementation planning
**Parent:** Gap Analysis (April 16, 2026) + Code Review findings

---

## 1. Purpose

RAPIDA is a trauma-informed, offline-first PWA for UNDP crisis damage mapping. The codebase has reached functional completeness (358 tests passing, deployed to Laravel Cloud) but architectural complexity has concentrated in two monolithic files and business logic has leaked across layer boundaries.

This spec defines a separation of concerns across four layers — Feature, Logic, Design System, UI — guided by the principle **"fix the seams"**: refactor only where the 15 identified gap fixes require touching the code, with full decomposition of the wizard-shell component.

The architecture analysis document produced alongside this work serves double duty:
1. UNDP submission artifact demonstrating "architecture clarity" (explicit evaluation criterion)
2. Post-submission roadmap for scaling the codebase

---

## 2. The Four Layers

| Layer | Responsibility | Owns | Does NOT Own |
|-------|---------------|------|-------------|
| **Feature** | What the product does — use cases, business rules, workflows | Services, Jobs, Events, Listeners | How things look, how data is stored |
| **Logic** | How data moves — validation, persistence, authorization, integration | Models, FormRequests, Policies, Middleware, API contracts | UI decisions, design tokens |
| **Design System** | How things look and feel — tokens, component contracts, interaction patterns | Token config, Atom/Molecule specs, copy contracts | Business logic, data persistence |
| **User Interface** | What the user sees and touches — Livewire components, Alpine state, Blade views | Component rendering, client-side state, navigation | Business rules, data queries, token definitions |

**Communication rule:** Each layer talks only to its immediate neighbor. UI consumes Design System tokens. UI calls Feature services. Feature services use Logic layer models. No layer skips.

```
UI Layer (Livewire components, Alpine, Blade)
    |-- consumes tokens from --> Design System Layer (rapida-ui/tokens/, config/rapida-tokens.php)
    |-- calls methods on --> Feature Layer (Services, Jobs, Events)
                                |-- persists via --> Logic Layer (Models, DTOs, Policies, Middleware)
```

---

## 3. Feature Layer — Service Extraction

### 3.1 New Services

#### ReportSubmissionService

**Extracted from:** wizard-shell.blade.php submit() (lines 122-218), ApiReportController::store(), WhatsAppBotService::stepConfirm()

**Responsibility:** Single unified workflow for all 3 submission paths.

```php
class ReportSubmissionService
{
    public function __construct(
        private ConflictModeService $conflictMode,
        private CompletenessScoreService $completeness,
        private PhotoStorageService $photoStorage,
        private ModularFieldService $modularFields,
    ) {}

    public function submit(SubmitReportData $data): DamageReport
    {
        // 1. Apply conflict mode rules
        $this->conflictMode->applyToSubmission($data);

        // 2. Check idempotency
        if ($existing = DamageReport::where('idempotency_key', $data->idempotencyKey)->first()) {
            return $existing;
        }

        // 3. Detect reporter tier
        $data->reporterTier = $this->resolveReporterTier($data);

        return DB::transaction(function () use ($data) {
            // 4. Store photo if provided
            $photoResult = $data->photoPath
                ? $this->photoStorage->store($data->photoPath)
                : null;

            // 5. Create damage report
            $report = DamageReport::create([
                'crisis_id'             => $data->crisis->id,
                'photo_url'             => $photoResult?->url,
                'photo_hash'            => $photoResult?->hash,
                'photo_size_bytes'      => $photoResult?->sizeBytes,
                'latitude'              => $data->latitude,
                'longitude'             => $data->longitude,
                'w3w_code'              => $data->w3wCode,
                'landmark_text'         => $data->landmarkText,
                'damage_level'          => $data->damageLevel,
                'infrastructure_type'   => $data->infrastructureType,
                'crisis_type'           => $data->crisisType,
                'description'           => $data->description,
                'device_fingerprint_id' => $data->deviceFingerprintId,
                'idempotency_key'       => $data->idempotencyKey,
                'submitted_via'         => $data->submittedVia,
                'reporter_tier'         => $data->reporterTier,
                'submitted_at'          => now(),
            ]);

            // 6. Create modular field responses
            $this->modularFields->storeResponses($report, $data->moduleResponses);

            // 7. Score completeness
            $report->update([
                'completeness_score' => $this->completeness->score($report),
            ]);

            // 8. Dispatch event (triggers AI, translation, canonical, badges)
            event(new ReportSubmitted($report));

            return $report;
        });
    }

    private function resolveReporterTier(SubmitReportData $data): string
    {
        if ($this->conflictMode->isConflict($data->crisis)) {
            return 'anonymous';
        }
        if ($data->accountId) {
            return 'account';
        }
        if ($data->deviceFingerprintId) {
            return 'device';
        }
        return 'anonymous';
    }
}
```

#### ConflictModeService

**Extracted from:** 5 files, 12 total checks of `$crisis->conflict_context`

**Responsibility:** Single source of truth for conflict mode behavior.

```php
class ConflictModeService
{
    public function isConflict(Crisis $crisis): bool
    {
        return $crisis->conflict_context ?? false;
    }

    public function applyToSubmission(SubmitReportData $data): void
    {
        if (!$this->isConflict($data->crisis)) {
            return;
        }
        $data->deviceFingerprintId = null;
        $data->reporterTier = 'anonymous';
    }

    public function shouldDisableWhatsApp(Crisis $crisis): bool
    {
        return $this->isConflict($crisis) && !$crisis->whatsapp_enabled;
    }

    public function shouldDisableFingerprinting(Crisis $crisis): bool
    {
        return $this->isConflict($crisis);
    }

    public function shouldDisableLeaderboard(Crisis $crisis): bool
    {
        return $this->isConflict($crisis);
    }
}
```

**Files that change:**
- `wizard-shell.blade.php` — 5 inline checks → `$this->conflictMode->isConflict()`
- `ApiReportController.php` — 2 inline checks → service call
- `WhatsAppBotService.php` — 3 inline checks → service call
- `SetLocaleFromCrisis.php` — 1 inline check → service call
- `transparency-onboarding.blade.php` — receives boolean prop from parent (no direct service call)

#### PhotoStorageService

**Extracted from:** wizard-shell.blade.php lines 133-140

```php
class PhotoStorageService
{
    public function store(UploadedFile|TemporaryUploadedFile $file): PhotoResult
    {
        $hash = md5_file($file->getRealPath());
        $size = $file->getSize();
        $path = $file->storeAs(
            'damage-photos',
            Str::uuid() . '.' . $file->guessExtension(),
            's3'
        );

        return new PhotoResult(
            url: $path,  // S3 object key, not public URL
            hash: $hash,
            sizeBytes: $size,
        );
    }
}
```

#### ModularFieldService

**Extracted from:** wizard-shell.blade.php lines 170-189

```php
class ModularFieldService
{
    public function getQuestionsForCrisis(Crisis $crisis): array
    {
        $modules = $crisis->active_modules ?? [];
        // Map crisis modules to question definitions
        return collect($modules)->flatMap(fn ($module) =>
            config("rapida-modules.{$module}", [])
        )->toArray();
    }

    public function storeResponses(DamageReport $report, array $responses): void
    {
        foreach ($responses as $moduleName => $fields) {
            ReportModule::create([
                'report_id'       => $report->id,
                'module_name'     => $moduleName,
                'field_responses' => $fields,
            ]);
        }
    }
}
```

#### AnalyticsQueryService

**Extracted from:** analytics-panel.blade.php lines 18-100

```php
class AnalyticsQueryService
{
    public function totalReports(string $crisisId): int { /* cached query */ }
    public function byDamageLevel(string $crisisId): array { /* cached query */ }
    public function byInfrastructure(string $crisisId): array { /* cached query */ }
    public function coveragePercentage(string $crisisId): float { /* cached query */ }
    public function reportsOverTime(string $crisisId, int $days = 30): array { /* cached query */ }
    public function topBuildings(string $crisisId, int $limit = 10): array { /* cached query */ }
}
```

All queries cached for 5 minutes via `Cache::remember()`.

### 3.2 Existing Service Fixes

| Service | Fix |
|---------|-----|
| `CompletenessScoreService` | Align `scoreFromArray()` with `score()` — add `debris_required` check |
| `WhatsAppBotService` | Call `ReportSubmissionService::submit()` instead of inline `DamageReport::create()`. Call `ConflictModeService` instead of inline checks. Move session from Cache to `whatsapp_sessions` table. |

---

## 4. Logic Layer — Validation, Authorization, Security

### 4.1 SubmitReportData DTO

Shared validation across all 3 submission paths. Not HTTP-bound (WhatsApp doesn't come through HTTP).

```php
class SubmitReportData
{
    public function __construct(
        public Crisis $crisis,
        public ?string $photoPath = null,
        public ?float $latitude = null,
        public ?float $longitude = null,
        public ?string $w3wCode = null,
        public ?string $landmarkText = null,
        public ?string $damageLevel = null,
        public ?string $infrastructureType = null,
        public ?string $crisisType = null,
        public ?string $description = null,
        public ?string $deviceFingerprintId = null,
        public ?string $idempotencyKey = null,
        public ?string $accountId = null,
        public string $submittedVia = 'web',
        public string $reporterTier = 'anonymous',
        public array $moduleResponses = [],
    ) {}
}
```

### 4.2 Security Fixes

| Fix | Target | Change |
|-----|--------|--------|
| AI endpoint auth | `routes/api.php` | New `VerifyInternalSecret` middleware on `/api/v1/internal/ai-result` |
| /my-reports filtering | `routes/web.php` | Filter by device_fingerprint_id cookie or authenticated account |
| Export rate limiter | `routes/web.php` | Apply existing `throttle:rapida-export` to export route group |
| Policy enforcement | `VerificationController` | Add `$this->authorize()` calls for flag, assign, verify |
| Debug route | `routes/web.php` | Delete `/submit-test` route |

### 4.3 New Middleware

```php
class VerifyInternalSecret
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('services.ai.internal_secret');

        if (!$secret || $request->header('X-Internal-Secret') !== $secret) {
            abort(403, 'Invalid internal secret');
        }

        return $next($request);
    }
}
```

---

## 5. Design System Layer — Token Consumption Contract

### 5.1 Token Flow

```
rapida-ui/tokens/*.cjs
    |-- consumed by --> tailwind.config.cjs --> Tailwind utility classes --> Blade components
    |-- mirrored in --> config/rapida-tokens.php --> Blade views passing tokens to JavaScript
```

### 5.2 New File: config/rapida-tokens.php

```php
return [
    'map' => [
        'damage_minimal'   => '#22c55e',
        'damage_partial'   => '#f59e0b',
        'damage_complete'  => '#c46b5a',  // crisis-rose-400, NOT red
        'footprint_fill'   => '#2e6689',  // rapida-blue-700
        'footprint_stroke' => '#1a3a4a',  // rapida-blue-900
        'user_dot'         => '#2e6689',
    ],
    'chart' => [
        'minimal' => '#2a5540',  // ground-green-800
        'partial' => '#c47d2a',  // alert-amber-500
        'complete' => '#c46b5a', // crisis-rose-400
    ],
];
```

### 5.3 Component Changes

| Component | Before | After |
|-----------|--------|-------|
| wizard-shell (map section) | Hardcoded `tokens: { damage_minimal: '#22c55e' }` | `tokens: @js(config('rapida-tokens.map'))` |
| rapida-map.js DEFAULT_TOKENS | Hardcoded fallback `'#EF4444'` | No defaults — always receives tokens from Alpine data |
| analytics-panel chart colors | Hardcoded hex strings | `config('rapida-tokens.chart')` |

### 5.4 What Does NOT Change

- Blade atoms/molecules using Tailwind classes (`bg-damage-complete-ui`) — already correct
- `rapida-ui/` documentation views — stay as showcase
- Token CJS files — remain the source of truth for Tailwind

---

## 6. UI Layer — Wizard Decomposition

### 6.1 WizardShell Becomes Orchestrator

From ~839 lines to ~150 lines. Responsibilities:
- Track current step (1-6)
- Hold Crisis model reference
- Compute `$conflictMode` via `ConflictModeService`
- Listen for `step-completed` events from children
- Store collected step data
- Pass `$crisis` and `$conflictMode` as props to current step

### 6.2 Six Livewire Step Components

All created in `app/Livewire/Wizard/`:

| Component | Class | Key Props In | Event Out |
|-----------|-------|-------------|-----------|
| StepPhoto | `App\Livewire\Wizard\StepPhoto` | `$crisis`, `$conflictMode` | `step-completed` with photoPath, photoHash, photoSize |
| StepLocation | `App\Livewire\Wizard\StepLocation` | `$crisis`, `$conflictMode` | `step-completed` with latitude, longitude, w3wCode, landmarkText, buildingId, locationMethod |
| StepDamage | `App\Livewire\Wizard\StepDamage` | `$crisis`, `$conflictMode` | `step-completed` with damageLevel, aiSuggestedLevel, aiConfidence |
| StepInfrastructure | `App\Livewire\Wizard\StepInfrastructure` | `$crisis` | `step-completed` with infrastructureType, crisisType |
| StepModular | `App\Livewire\Wizard\StepModular` | `$crisis` | `step-completed` with moduleResponses |
| StepConfirmation | `App\Livewire\Wizard\StepConfirmation` | `$crisis`, `$conflictMode`, all collected data | `report-submitted` with reportId |

### 6.3 Gap Fixes Embedded in Step Components

- **V6b (AI thinking state):** StepDamage owns `$aiPending` property. Shows "Analysing your photo..." while classification job runs. If AI arrives before reporter selects → pre-select + show suggestion copy. If reporter selects first → accept choice, cancel AI silently.
- **V6a (offline confirmation):** StepConfirmation reads Alpine `$store.offlineQueue.isOnline`. Online: "Your report was received." Offline: "Your report is saved. It will send automatically when you're connected."
- **reporter_tier:** ReportSubmissionService detects tier from auth state + conflict mode. No more hardcoded 'anonymous'.

### 6.4 Communication Pattern

```
WizardShell
  |-- renders --> @livewire('wizard.step-photo', ['crisis' => $crisis, 'conflictMode' => $conflictMode])
  |-- listens --> $on('step-completed', function ($data) { $this->stepData[...] = $data; $this->nextStep(); })
  |-- listens --> $on('report-submitted', function ($reportId) { redirect('/confirmation?report=' . $reportId); })
```

---

## 7. UI Layer — JavaScript Module Split

### 7.1 rapida-map.js Decomposition

From 796 lines to 4 focused modules:

| Module | Est. Lines | Responsibility |
|--------|-----------|----------------|
| `rapida-map.js` | ~120 | Map lifecycle (init, destroy, resize), GPS tracking, module composition, Alpine component registration |
| `map-buildings.js` | ~150 | Building footprint layer: fetch GeoJSON from API, render polygons, tap handler, visual feedback, `building-selected` dispatch |
| `map-pins.js` | ~180 | Damage pin layer: fetch pins from API, cluster rendering, unclustered pin rendering, color mapping from tokens, polling interval |
| `map-heatmap.js` | ~100 | H3 heatmap layer: fetch aggregation data, render hexagon fill layer |

### 7.2 Import Pattern

```javascript
// rapida-map.js
import { initBuildingLayer, destroyBuildingLayer } from './map-buildings.js';
import { initPinLayer, destroyPinLayer } from './map-pins.js';
import { initHeatmapLayer, destroyHeatmapLayer } from './map-heatmap.js';

export default (config) => ({
    map: null,
    init() {
        this.map = new maplibregl.Map({ container: this.$refs.map, ... });
        this.map.on('load', () => {
            initBuildingLayer(this.map, config.crisisSlug, config.tokens, this.$dispatch.bind(this));
            initPinLayer(this.map, config.crisisSlug, config.tokens);
            initHeatmapLayer(this.map, config.crisisSlug);
        });
    },
    destroy() {
        destroyPinLayer();
        destroyBuildingLayer();
        destroyHeatmapLayer();
        this.map?.remove();
    },
});
```

### 7.3 Token Flow

Map modules receive tokens as a parameter — never define defaults:

```javascript
// map-pins.js
export function initPinLayer(map, crisisSlug, tokens) {
    // tokens.damage_minimal, tokens.damage_partial, tokens.damage_complete
    // All come from config('rapida-tokens.map') via Alpine data
}
```

The `#EF4444` red bug is eliminated because no module has hardcoded color defaults.

---

## 8. Additional Fixes (Embedded in Refactoring)

| Issue | Fix Location | Change |
|-------|-------------|--------|
| APP_NAME = "Laravel" | `.env` | Change to `APP_NAME=RAPIDA` |
| CSV field escaping | `ExportReportsCsv` | Replace manual comma join with `fputcsv()` |
| Export memory | All 5 export jobs | Replace `$query->get()` with `$query->cursor()` |
| CompletenessScore inconsistency | `CompletenessScoreService` | Align `scoreFromArray()` with `score()` |
| Translation gaps | `lang/{ar,es,zh,ru}/*.php` | Add missing keys to match English baseline |

---

## 9. Architecture Narrative Document

A separate document traced through the damage report data lifecycle, structured for UNDP evaluators:

```
1. Principle — Why separation matters for crisis tools
2. The Four Layers
3. Data Lifecycle — Birth to Export
   3.1 Capture (3 channels → 1 service)
   3.2 Validate (DTO, conflict mode, idempotency)
   3.3 Classify (AI sidecar, confidence, override)
   3.4 Store (PostGIS, building snap, canonical)
   3.5 Verify (analyst workflow, policy enforcement)
   3.6 Display (map pins, heatmap, dashboard)
   3.7 Export (CSV, GeoJSON, KML, Shapefile, PDF)
   3.8 Archive (retention, conflict cleanup)
4. Cross-cutting Concerns
   4.1 Conflict Mode (ConflictModeService)
   4.2 Offline-first (IndexedDB → BackgroundSync → Backpressure)
   4.3 Localization (6 UN languages, RTL, locale chain)
   4.4 Resilience (circuit breaker, queue pressure)
5. Design System Contract
6. Security Posture
```

Each subsection follows: What happens → Which layer owns it → Key files → Conflict mode behavior → Offline behavior.

---

## 10. What Does NOT Change

- Blade atom/molecule components (already consume Tailwind correctly)
- `rapida-ui/` documentation views (stay as showcase)
- Event/Listener chain (already clean separation)
- Database schema (no new migrations needed)
- Models, Factories, Seeders (no structural changes)
- Service Worker and offline-queue.js (already clean)

---

## 11. Test Strategy

Existing 358 tests remain. New tests added for:

| New Test | Covers |
|----------|--------|
| `ReportSubmissionServiceTest` | All 3 paths converge, idempotency, conflict mode, tier detection |
| `ConflictModeServiceTest` | isConflict, applyToSubmission, disable checks |
| `PhotoStorageServiceTest` | Upload, hash, S3 mock |
| `ModularFieldServiceTest` | Question mapping, response storage |
| `VerifyInternalSecretTest` | AI endpoint auth middleware |
| `StepDamageTest` | AI pending state, suggestion display, override |
| `StepConfirmationTest` | Online vs offline copy |

Tests for existing behavior (wizard submit, API submit, WhatsApp flow) are updated to call through the new service rather than testing controller internals.

---

## 12. Dependency Order

```
Phase 1 — Logic Layer (no UI changes, safe)
  1. SubmitReportData DTO
  2. VerifyInternalSecret middleware
  3. config/rapida-tokens.php
  4. Route security fixes (/my-reports, export rate limiter, debug route removal)

Phase 2 — Feature Layer (services, no UI changes yet)
  5. ConflictModeService
  6. PhotoStorageService
  7. ModularFieldService
  8. ReportSubmissionService (depends on 5, 6, 7)
  9. AnalyticsQueryService
  10. CompletenessScoreService fix

Phase 3 — UI Layer (depends on Phase 2 services)
  11. WizardShell orchestrator refactor
  12. StepPhoto, StepLocation, StepInfrastructure, StepModular components
  13. StepDamage (V6b AI thinking state)
  14. StepConfirmation (V6a offline copy)
  15. rapida-map.js module split + token consumption
  16. analytics-panel refactor to use AnalyticsQueryService

Phase 4 — Cross-cutting
  17. Translation gap fills
  18. APP_NAME fix
  19. CSV escaping + export chunking
  20. VerificationController policy enforcement
  21. WhatsAppBotService refactor to use services

Phase 5 — Documentation
  22. Architecture Narrative Document
  23. Update existing tests for new service boundaries
  24. New service + component tests
```
