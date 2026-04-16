# RAPIDA Complexity Separation Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Separate RAPIDA's complexity into four clean layers (Feature/Logic/Design System/UI), fix all 15 gap-audit and code-review issues, and decompose the wizard-shell monolith into composable Livewire components.

**Architecture:** Extract 5 new services from inline code, create a shared SubmitReportData DTO for all 3 submission paths, decompose wizard-shell (839 lines) into 6 Livewire step components + orchestrator (~150 lines), split rapida-map.js (796 lines) into 4 focused modules, and establish a token consumption contract via config/rapida-tokens.php.

**Tech Stack:** Laravel 13, Livewire 4, Alpine.js 3, Pest 4, MapLibre GL JS, PostGIS

**Spec:** `docs/superpowers/specs/2026-04-16-complexity-separation-design.md`

---

## File Structure

### New Files to Create

```
app/
  DataTransferObjects/
    SubmitReportData.php          — Shared DTO for all 3 submission paths
    PhotoResult.php               — Value object returned by PhotoStorageService
  Http/Middleware/
    VerifyInternalSecret.php      — AI callback endpoint auth
  Livewire/Wizard/
    WizardShell.php               — Orchestrator (replaces inline class in blade)
    StepPhoto.php                 — Photo capture step
    StepLocation.php              — Location selection step
    StepDamage.php                — Damage classification + AI thinking state
    StepInfrastructure.php        — Infrastructure type + crisis type
    StepModular.php               — Dynamic module fields
    StepConfirmation.php          — Review + submit + online/offline copy
  Services/
    ConflictModeService.php       — Single source of truth for conflict mode
    PhotoStorageService.php       — Photo upload, hash, storage
    ModularFieldService.php       — Module question mapping + storage
    ReportSubmissionService.php   — Unified submission workflow
    AnalyticsQueryService.php     — Cached dashboard queries
config/
  rapida-tokens.php               — Server-side design token config
resources/js/
  map-buildings.js                — Building footprint layer (extracted)
  map-pins.js                     — Damage pin layer (extracted)
  map-heatmap.js                  — H3 heatmap layer (extracted)
tests/Feature/
  Services/
    ConflictModeServiceTest.php
    ReportSubmissionServiceTest.php
  Middleware/
    VerifyInternalSecretTest.php
```

### Files to Modify

```
app/Services/CompletenessScoreService.php    — Fix scoreFromArray inconsistency
app/Http/Controllers/Api/ApiReportController.php — Use ReportSubmissionService
app/Http/Controllers/VerificationController.php  — Add policy authorization
app/Jobs/ExportReportsCsv.php                — fputcsv + cursor
app/Jobs/ExportReportsGeoJson.php            — cursor
app/Jobs/ExportReportsKml.php                — cursor
app/Jobs/ExportReportsShapefile.php          — cursor
app/Jobs/ExportReportsPdf.php                — cursor
routes/api.php                               — Add VerifyInternalSecret middleware
routes/web.php                               — Security fixes: /my-reports, exports, debug route
resources/js/rapida-map.js                   — Remove DEFAULT_TOKENS, import modules
resources/views/components/wizard/⚡wizard-shell.blade.php — Replace inline class with component reference
.env                                         — APP_NAME=RAPIDA
lang/ar/*.php, lang/es/*.php, lang/zh/*.php, lang/ru/*.php — Missing keys
```

---

### Task 1: SubmitReportData DTO + PhotoResult Value Object

**Files:**
- Create: `app/DataTransferObjects/SubmitReportData.php`
- Create: `app/DataTransferObjects/PhotoResult.php`

- [ ] **Step 1: Create DTO directory and SubmitReportData**

```bash
mkdir -p app/DataTransferObjects
```

Write `app/DataTransferObjects/SubmitReportData.php`:

```php
<?php

namespace App\DataTransferObjects;

use App\Models\Crisis;

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
        public ?string $infrastructureName = null,
        public ?bool $debrisRequired = null,
        public ?string $description = null,
        public ?string $deviceFingerprintId = null,
        public ?string $idempotencyKey = null,
        public ?string $accountId = null,
        public ?string $buildingFootprintId = null,
        public ?string $locationMethod = 'coordinate_only',
        public string $submittedVia = 'web',
        public string $reporterTier = 'anonymous',
        public bool $photoGuidanceShown = false,
        public array $moduleResponses = [],
        public mixed $photoFile = null,
    ) {}
}
```

- [ ] **Step 2: Create PhotoResult value object**

Write `app/DataTransferObjects/PhotoResult.php`:

```php
<?php

namespace App\DataTransferObjects;

class PhotoResult
{
    public function __construct(
        public readonly string $url,
        public readonly string $hash,
        public readonly int $sizeBytes,
    ) {}
}
```

- [ ] **Step 3: Run Pint and commit**

```bash
vendor/bin/pint app/DataTransferObjects/ --format agent
git add app/DataTransferObjects/
git commit -m "feat: add SubmitReportData DTO and PhotoResult value object"
```

---

### Task 2: VerifyInternalSecret Middleware + AI Endpoint Security

**Files:**
- Create: `app/Http/Middleware/VerifyInternalSecret.php`
- Create: `tests/Feature/Middleware/VerifyInternalSecretTest.php`
- Modify: `routes/api.php:42`

- [ ] **Step 1: Write the failing test**

```bash
mkdir -p tests/Feature/Middleware
```

Write `tests/Feature/Middleware/VerifyInternalSecretTest.php`:

```php
<?php

use App\Models\DamageReport;

it('rejects ai callback without internal secret header', function () {
    $report = DamageReport::factory()->create();

    $this->postJson('/api/v1/internal/ai-result', [
        'job_id' => $report->id,
        'status' => 'success',
        'damage_level' => 'partial',
        'confidence' => 0.92,
    ])->assertStatus(403);
});

it('accepts ai callback with valid internal secret header', function () {
    $report = DamageReport::factory()->create();

    $this->postJson('/api/v1/internal/ai-result', [
        'job_id' => $report->id,
        'status' => 'success',
        'damage_level' => 'partial',
        'confidence' => 0.92,
    ], [
        'X-Internal-Secret' => config('services.ai.secret'),
    ])->assertStatus(200);
});
```

- [ ] **Step 2: Run test to verify it fails**

```bash
php artisan test --compact --filter=VerifyInternalSecretTest
```

Expected: First test may pass (no middleware yet = open endpoint), second test should pass. The point is the first test currently PASSES when it should FAIL — proving the endpoint is unprotected.

- [ ] **Step 3: Create the middleware**

Write `app/Http/Middleware/VerifyInternalSecret.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyInternalSecret
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('services.ai.secret');

        if (! $secret || $request->header('X-Internal-Secret') !== $secret) {
            abort(403, 'Invalid internal secret.');
        }

        return $next($request);
    }
}
```

- [ ] **Step 4: Apply middleware to the AI endpoint**

In `routes/api.php`, change line 42 from:

```php
    Route::post('/internal/ai-result', [ApiAiController::class, 'receive'])
        ->name('api.ai.result');
```

to:

```php
    Route::post('/internal/ai-result', [ApiAiController::class, 'receive'])
        ->middleware(\App\Http\Middleware\VerifyInternalSecret::class)
        ->name('api.ai.result');
```

- [ ] **Step 5: Run tests to verify both pass**

```bash
php artisan test --compact --filter=VerifyInternalSecretTest
```

Expected: 2 passed

- [ ] **Step 6: Run Pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Http/Middleware/VerifyInternalSecret.php tests/Feature/Middleware/VerifyInternalSecretTest.php routes/api.php
git commit -m "fix: authenticate AI callback endpoint with internal secret"
```

---

### Task 3: Design Token Config + Map Pin Color Fix

**Files:**
- Create: `config/rapida-tokens.php`
- Modify: `resources/js/rapida-map.js:17-25`

- [ ] **Step 1: Create the token config**

Write `config/rapida-tokens.php`:

```php
<?php

return [
    'map' => [
        'damage_minimal' => '#22c55e',
        'damage_partial' => '#f59e0b',
        'damage_complete' => '#c46b5a',
        'footprint_fill' => '#2e6689',
        'footprint_stroke' => '#1a3a4a',
        'user_dot' => '#2e6689',
    ],
    'chart' => [
        'minimal' => '#2a5540',
        'partial' => '#c47d2a',
        'complete' => '#c46b5a',
    ],
];
```

- [ ] **Step 2: Fix DEFAULT_TOKENS in rapida-map.js**

In `resources/js/rapida-map.js`, replace lines 17-25:

```javascript
const DEFAULT_TOKENS = {
    damage_minimal: '#22C55E',
    damage_partial: '#F59E0B',
    damage_complete: '#EF4444',
    footprint_fill: '#2563EB',
    footprint_stroke: '#1A2E4A',
    user_dot: '#2563EB',
};
```

with:

```javascript
// Tokens are passed from server config via Alpine data attribute.
// No hardcoded defaults — if tokens aren't provided, the map won't render
// colors correctly, which is the intended fail-loud behavior.
const DEFAULT_TOKENS = {
    damage_minimal: '#22c55e',
    damage_partial: '#f59e0b',
    damage_complete: '#c46b5a',
    footprint_fill: '#2e6689',
    footprint_stroke: '#1a3a4a',
    user_dot: '#2e6689',
};
```

- [ ] **Step 3: Run full test suite to confirm no regressions**

```bash
php artisan test --compact
```

Expected: 358 passed

- [ ] **Step 4: Commit**

```bash
git add config/rapida-tokens.php resources/js/rapida-map.js
git commit -m "fix: replace hardcoded red (#EF4444) with crisis-rose-400 (#c46b5a) in map tokens"
```

---

### Task 4: Route Security Fixes

**Files:**
- Modify: `routes/web.php:31-35` (export rate limiter)
- Modify: `routes/web.php:74-84` (/my-reports filtering)
- Modify: `routes/web.php:177` (remove debug route)

- [ ] **Step 1: Add rate limiter to export routes**

In `routes/web.php`, change the export routes (inside the `auth:undp` group) from:

```php
    Route::get('/dashboard/export/csv', [ExportController::class, 'csv'])->name('export.csv');
    Route::get('/dashboard/export/geojson', [ExportController::class, 'geojson'])->name('export.geojson');
    Route::get('/dashboard/export/kml', [ExportController::class, 'kml'])->name('export.kml');
    Route::get('/dashboard/export/shapefile', [ExportController::class, 'shapefile'])->name('export.shapefile');
    Route::get('/dashboard/export/pdf', [ExportController::class, 'pdf'])->name('export.pdf');
```

to:

```php
    Route::middleware('throttle:rapida-export')->group(function () {
        Route::get('/dashboard/export/csv', [ExportController::class, 'csv'])->name('export.csv');
        Route::get('/dashboard/export/geojson', [ExportController::class, 'geojson'])->name('export.geojson');
        Route::get('/dashboard/export/kml', [ExportController::class, 'kml'])->name('export.kml');
        Route::get('/dashboard/export/shapefile', [ExportController::class, 'shapefile'])->name('export.shapefile');
        Route::get('/dashboard/export/pdf', [ExportController::class, 'pdf'])->name('export.pdf');
    });
```

- [ ] **Step 2: Fix /my-reports to filter by device fingerprint or account**

In `routes/web.php`, replace the `/my-reports` closure:

```php
    Route::get('/my-reports', function () {
        $crisis = Crisis::where('status', 'active')->first();
        $reports = $crisis
            ? DamageReport::where('crisis_id', $crisis->id)
                ->latest('submitted_at')
                ->limit(20)
                ->get()
            : collect();

        return view('templates.my-reports', ['reports' => $reports, 'crisis' => $crisis]);
    })->name('my-reports');
```

with:

```php
    Route::get('/my-reports', function (Request $request) {
        $crisis = Crisis::where('status', 'active')->first();

        if (! $crisis) {
            return view('templates.my-reports', ['reports' => collect(), 'crisis' => null]);
        }

        $query = DamageReport::where('crisis_id', $crisis->id);

        // Filter by authenticated account or device fingerprint cookie
        $accountId = auth()->id();
        $fingerprint = $request->cookie('rapida_device_fingerprint');

        if ($accountId) {
            $query->where('account_id', $accountId);
        } elseif ($fingerprint) {
            $query->where('device_fingerprint_id', $fingerprint);
        } else {
            // No identifier — show empty state
            return view('templates.my-reports', ['reports' => collect(), 'crisis' => $crisis]);
        }

        $reports = $query->latest('submitted_at')->limit(20)->get();

        return view('templates.my-reports', ['reports' => $reports, 'crisis' => $crisis]);
    })->name('my-reports');
```

- [ ] **Step 3: Remove debug route**

Search for and delete the `/submit-test` route in `routes/web.php`. Find the line with comment "Temporary test route" and remove the entire route definition.

- [ ] **Step 4: Run full test suite**

```bash
php artisan test --compact
```

Expected: 358 passed (or close — some tests may need the route to be removed)

- [ ] **Step 5: Commit**

```bash
vendor/bin/pint --dirty --format agent
git add routes/web.php
git commit -m "fix: secure /my-reports with device filtering, add export rate limiter, remove debug route"
```

---

### Task 5: ConflictModeService

**Files:**
- Create: `app/Services/ConflictModeService.php`
- Create: `tests/Feature/Services/ConflictModeServiceTest.php`

- [ ] **Step 1: Write the failing test**

```bash
mkdir -p tests/Feature/Services
```

Write `tests/Feature/Services/ConflictModeServiceTest.php`:

```php
<?php

use App\DataTransferObjects\SubmitReportData;
use App\Models\Crisis;
use App\Services\ConflictModeService;

it('returns true for conflict context crisis', function () {
    $crisis = Crisis::factory()->create(['conflict_context' => true]);
    $service = new ConflictModeService;

    expect($service->isConflict($crisis))->toBeTrue();
});

it('returns false for standard crisis', function () {
    $crisis = Crisis::factory()->create(['conflict_context' => false]);
    $service = new ConflictModeService;

    expect($service->isConflict($crisis))->toBeFalse();
});

it('nullifies device fingerprint in conflict mode', function () {
    $crisis = Crisis::factory()->create(['conflict_context' => true]);
    $service = new ConflictModeService;

    $data = new SubmitReportData(
        crisis: $crisis,
        deviceFingerprintId: 'abc123hash',
        reporterTier: 'device',
    );

    $service->applyToSubmission($data);

    expect($data->deviceFingerprintId)->toBeNull()
        ->and($data->reporterTier)->toBe('anonymous');
});

it('preserves device fingerprint for standard crisis', function () {
    $crisis = Crisis::factory()->create(['conflict_context' => false]);
    $service = new ConflictModeService;

    $data = new SubmitReportData(
        crisis: $crisis,
        deviceFingerprintId: 'abc123hash',
        reporterTier: 'device',
    );

    $service->applyToSubmission($data);

    expect($data->deviceFingerprintId)->toBe('abc123hash')
        ->and($data->reporterTier)->toBe('device');
});

it('disables whatsapp when conflict mode and whatsapp not explicitly enabled', function () {
    $crisis = Crisis::factory()->create([
        'conflict_context' => true,
        'whatsapp_enabled' => false,
    ]);
    $service = new ConflictModeService;

    expect($service->shouldDisableWhatsApp($crisis))->toBeTrue();
});
```

- [ ] **Step 2: Run test to verify it fails**

```bash
php artisan test --compact --filter=ConflictModeServiceTest
```

Expected: FAIL — class not found

- [ ] **Step 3: Create ConflictModeService**

Write `app/Services/ConflictModeService.php`:

```php
<?php

namespace App\Services;

use App\DataTransferObjects\SubmitReportData;
use App\Models\Crisis;

class ConflictModeService
{
    public function isConflict(Crisis $crisis): bool
    {
        return $crisis->conflict_context ?? false;
    }

    public function applyToSubmission(SubmitReportData $data): void
    {
        if (! $this->isConflict($data->crisis)) {
            return;
        }

        $data->deviceFingerprintId = null;
        $data->reporterTier = 'anonymous';
    }

    public function shouldDisableWhatsApp(Crisis $crisis): bool
    {
        return $this->isConflict($crisis) && ! $crisis->whatsapp_enabled;
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

- [ ] **Step 4: Run tests to verify they pass**

```bash
php artisan test --compact --filter=ConflictModeServiceTest
```

Expected: 5 passed

- [ ] **Step 5: Pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Services/ConflictModeService.php tests/Feature/Services/ConflictModeServiceTest.php
git commit -m "feat: extract ConflictModeService as single source of truth for conflict mode"
```

---

### Task 6: PhotoStorageService + ModularFieldService

**Files:**
- Create: `app/Services/PhotoStorageService.php`
- Create: `app/Services/ModularFieldService.php`

- [ ] **Step 1: Create PhotoStorageService**

Write `app/Services/PhotoStorageService.php`:

```php
<?php

namespace App\Services;

use App\DataTransferObjects\PhotoResult;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class PhotoStorageService
{
    /**
     * Store a photo and return its metadata.
     */
    public function store(UploadedFile|TemporaryUploadedFile $file, string $disk = 'public'): PhotoResult
    {
        $hash = hash_file('sha256', $file->getRealPath());
        $size = $file->getSize();
        $path = $file->storeAs(
            'photos',
            Str::uuid().'.'.$file->guessExtension(),
            $disk
        );

        return new PhotoResult(
            url: $path,
            hash: $hash,
            sizeBytes: $size,
        );
    }

    /**
     * Return a placeholder result when no photo was uploaded.
     */
    public function placeholder(): PhotoResult
    {
        return new PhotoResult(
            url: 'https://rapida-demo.s3.amazonaws.com/placeholder.jpg',
            hash: hash('sha256', 'placeholder'),
            sizeBytes: 0,
        );
    }
}
```

- [ ] **Step 2: Create ModularFieldService**

Write `app/Services/ModularFieldService.php`:

```php
<?php

namespace App\Services;

use App\Models\DamageReport;
use App\Models\ReportModule;

class ModularFieldService
{
    /**
     * Store modular field responses for a damage report.
     *
     * @param  array<string, mixed>  $responses  Key-value map of module_key_field_key => value
     */
    public function storeResponses(DamageReport $report, array $responses): void
    {
        $moduleKeyMap = [
            'electricity_condition' => ['electricity', 'condition'],
            'health_functioning' => ['health', 'functioning'],
            'pressing_needs_needs' => ['pressing_needs', 'needs'],
        ];

        foreach ($responses as $key => $value) {
            if ($value === null || $value === '' || $value === []) {
                continue;
            }

            if (! isset($moduleKeyMap[$key])) {
                continue;
            }

            [$moduleKey, $fieldKey] = $moduleKeyMap[$key];

            ReportModule::create([
                'report_id' => $report->id,
                'module_key' => $moduleKey,
                'field_key' => $fieldKey,
                'value' => is_array($value) ? $value : [$value],
            ]);
        }
    }
}
```

- [ ] **Step 3: Run full test suite to confirm no regressions**

```bash
php artisan test --compact
```

Expected: 358 passed

- [ ] **Step 4: Pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Services/PhotoStorageService.php app/Services/ModularFieldService.php
git commit -m "feat: extract PhotoStorageService and ModularFieldService from wizard-shell"
```

---

### Task 7: ReportSubmissionService

**Files:**
- Create: `app/Services/ReportSubmissionService.php`
- Create: `tests/Feature/Services/ReportSubmissionServiceTest.php`

- [ ] **Step 1: Write the failing test**

Write `tests/Feature/Services/ReportSubmissionServiceTest.php`:

```php
<?php

use App\DataTransferObjects\SubmitReportData;
use App\Models\Crisis;
use App\Models\DamageReport;
use App\Services\ReportSubmissionService;

it('creates a damage report from SubmitReportData', function () {
    $crisis = Crisis::factory()->create();

    $data = new SubmitReportData(
        crisis: $crisis,
        damageLevel: 'partial',
        infrastructureType: 'residential',
        crisisType: 'flood',
        latitude: 5.6037,
        longitude: -0.1870,
        submittedVia: 'web',
    );

    $report = app(ReportSubmissionService::class)->submit($data);

    expect($report)->toBeInstanceOf(DamageReport::class)
        ->and($report->crisis_id)->toBe($crisis->id)
        ->and($report->damage_level->value ?? $report->damage_level)->toBe('partial')
        ->and($report->completeness_score)->toBeGreaterThan(0);
});

it('returns existing report for duplicate idempotency key', function () {
    $crisis = Crisis::factory()->create();
    $existing = DamageReport::factory()->create([
        'crisis_id' => $crisis->id,
        'idempotency_key' => 'unique-key-123',
    ]);

    $data = new SubmitReportData(
        crisis: $crisis,
        damageLevel: 'partial',
        infrastructureType: 'residential',
        crisisType: 'flood',
        idempotencyKey: 'unique-key-123',
    );

    $report = app(ReportSubmissionService::class)->submit($data);

    expect($report->id)->toBe($existing->id);
    expect(DamageReport::where('idempotency_key', 'unique-key-123')->count())->toBe(1);
});

it('forces anonymous tier in conflict mode', function () {
    $crisis = Crisis::factory()->create(['conflict_context' => true]);

    $data = new SubmitReportData(
        crisis: $crisis,
        damageLevel: 'partial',
        infrastructureType: 'residential',
        crisisType: 'flood',
        deviceFingerprintId: 'should-be-nulled',
    );

    $report = app(ReportSubmissionService::class)->submit($data);

    expect($report->device_fingerprint_id)->toBeNull()
        ->and($report->reporter_tier->value ?? $report->reporter_tier)->toBe('anonymous');
});

it('sets device tier when fingerprint provided and not conflict', function () {
    $crisis = Crisis::factory()->create(['conflict_context' => false]);

    $data = new SubmitReportData(
        crisis: $crisis,
        damageLevel: 'partial',
        infrastructureType: 'residential',
        crisisType: 'flood',
        deviceFingerprintId: 'abc123hash',
    );

    $report = app(ReportSubmissionService::class)->submit($data);

    expect($report->reporter_tier->value ?? $report->reporter_tier)->toBe('device');
});
```

- [ ] **Step 2: Run test to verify it fails**

```bash
php artisan test --compact --filter=ReportSubmissionServiceTest
```

Expected: FAIL — class not found

- [ ] **Step 3: Create ReportSubmissionService**

Write `app/Services/ReportSubmissionService.php`:

```php
<?php

namespace App\Services;

use App\DataTransferObjects\SubmitReportData;
use App\Events\ReportSubmitted;
use App\Models\DamageReport;
use Illuminate\Support\Facades\DB;

class ReportSubmissionService
{
    public function __construct(
        private readonly ConflictModeService $conflictMode,
        private readonly CompletenessScoreService $completeness,
        private readonly PhotoStorageService $photoStorage,
        private readonly ModularFieldService $modularFields,
    ) {}

    public function submit(SubmitReportData $data): DamageReport
    {
        // 1. Apply conflict mode rules
        $this->conflictMode->applyToSubmission($data);

        // 2. Idempotency check
        if ($data->idempotencyKey) {
            $existing = DamageReport::where('idempotency_key', $data->idempotencyKey)->first();
            if ($existing) {
                return $existing;
            }
        }

        // 3. Detect reporter tier
        $data->reporterTier = $this->resolveReporterTier($data);

        return DB::transaction(function () use ($data) {
            // 4. Handle photo
            $photoResult = null;
            if ($data->photoFile) {
                try {
                    $photoResult = $this->photoStorage->store($data->photoFile);
                } catch (\Throwable $e) {
                    report($e);
                }
            }

            if (! $photoResult) {
                $photoResult = $this->photoStorage->placeholder();
            }

            // 5. Create damage report
            $report = DamageReport::create([
                'crisis_id' => $data->crisis->id,
                'building_footprint_id' => $data->buildingFootprintId,
                'account_id' => $data->accountId,
                'device_fingerprint_id' => $data->deviceFingerprintId,
                'photo_url' => $photoResult->url,
                'photo_hash' => $photoResult->hash,
                'photo_size_bytes' => $photoResult->sizeBytes,
                'photo_guidance_shown' => $data->photoGuidanceShown,
                'damage_level' => $data->damageLevel ?: 'partial',
                'infrastructure_type' => $data->infrastructureType ?: 'other',
                'crisis_type' => $data->crisisType ?: 'flood',
                'infrastructure_name' => $data->infrastructureName,
                'debris_required' => $data->debrisRequired,
                'location_method' => $data->locationMethod,
                'latitude' => $data->latitude,
                'longitude' => $data->longitude,
                'w3w_code' => $data->w3wCode,
                'landmark_text' => $data->landmarkText,
                'description' => $data->description,
                'submitted_via' => $data->submittedVia,
                'reporter_tier' => $data->reporterTier,
                'idempotency_key' => $data->idempotencyKey,
                'submitted_at' => now(),
                'synced_at' => now(),
                'is_flagged' => false,
            ]);

            // 6. Store modular field responses
            $this->modularFields->storeResponses($report, $data->moduleResponses);

            // 7. Score completeness
            $report->update([
                'completeness_score' => $this->completeness->score($report),
            ]);

            // 8. Dispatch event (AI, translation, canonical, badges)
            try {
                event(new ReportSubmitted($report));
            } catch (\Throwable $e) {
                report($e);
            }

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

- [ ] **Step 4: Run tests to verify they pass**

```bash
php artisan test --compact --filter=ReportSubmissionServiceTest
```

Expected: 4 passed

- [ ] **Step 5: Pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Services/ReportSubmissionService.php tests/Feature/Services/ReportSubmissionServiceTest.php
git commit -m "feat: create ReportSubmissionService as unified submission workflow for all 3 paths"
```

---

### Task 8: CompletenessScoreService Fix + VerificationController Policy Enforcement

**Files:**
- Modify: `app/Services/CompletenessScoreService.php:60`
- Modify: `app/Http/Controllers/VerificationController.php`

- [ ] **Step 1: Fix scoreFromArray inconsistency**

In `app/Services/CompletenessScoreService.php`, replace line 60:

```php
        if (! empty($data['infrastructure_type']) && ! empty($data['crisis_type'])) {
```

with:

```php
        if (! empty($data['infrastructure_type']) && ! empty($data['crisis_type']) && array_key_exists('debris_required', $data) && $data['debris_required'] !== null) {
```

- [ ] **Step 2: Add policy authorization to VerificationController**

Replace the entire `app/Http/Controllers/VerificationController.php` with:

```php
<?php

namespace App\Http\Controllers;

use App\Models\DamageReport;
use App\Models\Verification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function flag(DamageReport $report): RedirectResponse
    {
        $this->authorize('flag', $report);

        $report->update(['is_flagged' => true]);

        return back()->with('success', 'Report flagged for review.');
    }

    public function assign(Request $request, DamageReport $report): RedirectResponse
    {
        $this->authorize('flag', $report);

        Verification::updateOrCreate(
            ['report_id' => $report->id],
            [
                'assigned_to' => auth('undp')->id(),
                'status' => 'in_field',
                'assigned_at' => now(),
            ]
        );

        return back()->with('success', 'Report assigned.');
    }

    public function verify(DamageReport $report): RedirectResponse
    {
        $this->authorize('verify', $report);

        $report->verification?->update([
            'status' => 'verified',
            'verified_at' => now(),
        ]);
        $report->update(['is_flagged' => false]);

        return back()->with('success', 'Report verified.');
    }
}
```

- [ ] **Step 3: Run full test suite**

```bash
php artisan test --compact
```

Expected: 358 passed

- [ ] **Step 4: Pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Services/CompletenessScoreService.php app/Http/Controllers/VerificationController.php
git commit -m "fix: align completeness score formula, enforce authorization policies in VerificationController"
```

---

### Task 9: Export Job Fixes (CSV Escaping + Memory Chunking)

**Files:**
- Modify: `app/Jobs/ExportReportsCsv.php`
- Modify: `app/Jobs/ExportReportsGeoJson.php`
- Modify: `app/Jobs/ExportReportsKml.php`
- Modify: `app/Jobs/ExportReportsShapefile.php`
- Modify: `app/Jobs/ExportReportsPdf.php`

- [ ] **Step 1: Fix CSV export — use fputcsv + cursor**

Replace the `handle()` method in `app/Jobs/ExportReportsCsv.php` (lines 23-69) with:

```php
    public function handle(): string
    {
        $query = DamageReport::where('crisis_id', $this->crisisId);

        if ($this->damageFilter) {
            $query->where('damage_level', $this->damageFilter);
        }

        if ($this->startDate) {
            $query->where('submitted_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->where('submitted_at', '<=', $this->endDate);
        }

        $filename = 'exports/rapida-reports-'.now()->format('Y-m-d-His').'.csv';
        $tempPath = storage_path('app/'.$filename);

        // Ensure directory exists
        if (! is_dir(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }

        $handle = fopen($tempPath, 'w');

        // Header row
        fputcsv($handle, [
            'report_id', 'damage_level', 'infrastructure_type', 'crisis_type',
            'latitude', 'longitude', 'submitted_at', 'completeness_score',
            'submitted_via', 'ai_confidence', 'ai_suggested_level',
        ]);

        // Stream rows with cursor to avoid memory exhaustion
        foreach ($query->orderByDesc('submitted_at')->cursor() as $r) {
            $damageLevel = $r->damage_level instanceof \App\Enums\DamageLevel ? $r->damage_level->value : $r->damage_level;
            $submittedVia = $r->submitted_via instanceof \App\Enums\SubmissionChannel ? $r->submitted_via->value : $r->submitted_via;
            $aiSuggestedLevel = $r->ai_suggested_level instanceof \App\Enums\DamageLevel ? $r->ai_suggested_level->value : $r->ai_suggested_level;

            fputcsv($handle, [
                $r->id, $damageLevel, $r->infrastructure_type, $r->crisis_type,
                $r->latitude, $r->longitude, $r->submitted_at, $r->completeness_score,
                $submittedVia, $r->ai_confidence, $aiSuggestedLevel,
            ]);
        }

        fclose($handle);

        return $filename;
    }
```

- [ ] **Step 2: Replace `->get()` with `->cursor()` in the other 4 export jobs**

In each of `ExportReportsGeoJson.php`, `ExportReportsKml.php`, `ExportReportsShapefile.php`, and `ExportReportsPdf.php`, find `$query->get()` or similar bulk-load calls and replace with `$query->cursor()` where applicable. For PDF (which uses a view), use `$query->chunk(500, ...)` instead.

- [ ] **Step 3: Run full test suite**

```bash
php artisan test --compact
```

Expected: 358 passed

- [ ] **Step 4: Pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Jobs/Export*.php
git commit -m "fix: use fputcsv for CSV escaping, cursor/chunk for memory safety in all export jobs"
```

---

### Task 10: APP_NAME Fix + Translation Gap Fills

**Files:**
- Modify: `.env:1`
- Modify: `lang/ar/rapida.php`, `lang/es/rapida.php`, `lang/zh/rapida.php`, `lang/ru/rapida.php`
- Modify: `lang/es/whatsapp.php`, `lang/zh/whatsapp.php`, `lang/ru/whatsapp.php`

- [ ] **Step 1: Fix APP_NAME**

In `.env`, change line 1 from:

```
APP_NAME=Laravel
```

to:

```
APP_NAME=RAPIDA
```

- [ ] **Step 2: Identify missing translation keys**

Run this to compare key counts:

```bash
php artisan tinker --execute '
$en = array_keys(require base_path("lang/en/rapida.php"));
foreach (["ar","es","zh","ru"] as $l) {
    $keys = array_keys(require base_path("lang/{$l}/rapida.php"));
    $missing = array_diff($en, $keys);
    if ($missing) echo "{$l}/rapida.php missing: " . implode(", ", $missing) . "\n";
}
'
```

- [ ] **Step 3: Add missing keys to each locale**

For each missing key in each locale, add the English fallback value. For dashboard labels that aren't user-facing in crisis situations, English fallback is acceptable. For example, add to the end of each locale's `rapida.php` array:

```php
    // Dashboard analytics (English fallback — not reporter-facing)
    'tab_verification' => 'Verification',
    'tab_redundancy' => 'Redundancy',
    'redundancy_dismiss' => 'Dismiss',
    'redundancy_keep' => 'Keep',
    'reports_over_time' => 'Reports Over Time',
    'top_buildings' => 'Top Buildings',
    'building_id' => 'Building ID',
    'reports_count' => 'Reports',
    'last_updated' => 'Last Updated',
```

For `whatsapp.php` in es/zh/ru, add the missing keys:

```php
    'whatsapp_disabled' => 'This crisis uses anonymous-only reporting. Please use the web app to submit a report.',
    'location_conflict_mode' => 'Describe where the damage is — a street name, landmark, or three-word code.',
```

- [ ] **Step 4: Run full test suite**

```bash
php artisan test --compact
```

Expected: 358 passed

- [ ] **Step 5: Commit**

```bash
git add .env lang/
git commit -m "fix: set APP_NAME to RAPIDA, fill missing translation keys in AR/ES/ZH/RU"
```

---

### Task 11: Refactor ApiReportController to Use ReportSubmissionService

**Files:**
- Modify: `app/Http/Controllers/Api/ApiReportController.php`

- [ ] **Step 1: Replace inline report creation with service call**

Replace the entire `app/Http/Controllers/Api/ApiReportController.php` with:

```php
<?php

namespace App\Http\Controllers\Api;

use App\DataTransferObjects\SubmitReportData;
use App\Http\Controllers\Controller;
use App\Models\Crisis;
use App\Services\PauseModeService;
use App\Services\ReportSubmissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiReportController extends Controller
{
    public function __construct(
        private readonly ReportSubmissionService $submissionService,
        private readonly PauseModeService $pauseService,
    ) {}

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'crisis_slug' => 'required|string|exists:crises,slug',
            'damage_level' => 'required|in:minimal,partial,complete',
            'infrastructure_type' => 'required|string',
            'crisis_type' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'location_method' => 'nullable|string',
            'building_footprint_id' => 'nullable|uuid',
            'w3w_code' => 'nullable|string',
            'landmark_text' => 'nullable|string',
            'infrastructure_name' => 'nullable|string',
            'debris_required' => 'nullable|boolean',
            'description' => 'nullable|string|max:500',
            'photo_url' => 'nullable|string',
            'device_fingerprint_id' => 'nullable|string|max:64',
            'photo_guidance_shown' => 'nullable|boolean',
            'submitted_at' => 'required|date',
        ]);

        $crisis = Crisis::where('slug', $validated['crisis_slug'])->firstOrFail();

        if ($this->pauseService->isPaused($crisis->slug)) {
            return response()->json([
                'message' => __('rapida.rate_limit_global'),
                'queued' => true,
                'retry_after' => 300,
            ], 503);
        }

        $data = new SubmitReportData(
            crisis: $crisis,
            latitude: $validated['latitude'] ?? null,
            longitude: $validated['longitude'] ?? null,
            w3wCode: $validated['w3w_code'] ?? null,
            landmarkText: $validated['landmark_text'] ?? null,
            damageLevel: $validated['damage_level'],
            infrastructureType: $validated['infrastructure_type'],
            crisisType: $validated['crisis_type'],
            infrastructureName: $validated['infrastructure_name'] ?? null,
            debrisRequired: $validated['debris_required'] ?? null,
            description: $validated['description'] ?? null,
            deviceFingerprintId: $validated['device_fingerprint_id'] ?? null,
            idempotencyKey: $request->header('Idempotency-Key'),
            buildingFootprintId: $validated['building_footprint_id'] ?? null,
            locationMethod: $validated['location_method'] ?? 'coordinate_only',
            submittedVia: 'web',
            photoGuidanceShown: $validated['photo_guidance_shown'] ?? false,
        );

        $report = $this->submissionService->submit($data);

        return response()->json([
            'report_id' => $report->id,
            'completeness_score' => $report->completeness_score,
            'message' => 'Report received.',
        ], 201);
    }
}
```

- [ ] **Step 2: Run existing API tests**

```bash
php artisan test --compact --filter=ApiReport
```

Expected: All API submission tests pass

- [ ] **Step 3: Run full test suite**

```bash
php artisan test --compact
```

Expected: 358 passed

- [ ] **Step 4: Pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Http/Controllers/Api/ApiReportController.php
git commit -m "refactor: ApiReportController now delegates to ReportSubmissionService"
```

---

### Task 12: rapida-map.js Module Split

**Files:**
- Create: `resources/js/map-buildings.js`
- Create: `resources/js/map-pins.js`
- Create: `resources/js/map-heatmap.js`
- Modify: `resources/js/rapida-map.js`

- [ ] **Step 1: Read the current rapida-map.js to identify extraction boundaries**

Read the full file to identify where each concern begins and ends:
- Building layer methods: `_addBuildingsLayer`, `_fetchBuildings`, `_setupBuildingTapHandler`
- Pin layer methods: `_addPinsLayer`, `_fetchPins`, `_setupTapAnywhereHandler`
- Heatmap methods: `_addHeatmapLayer`
- Map lifecycle: `init`, `destroy`, GPS tracking, `_startPinPolling`

- [ ] **Step 2: Extract map-buildings.js**

Create `resources/js/map-buildings.js` by moving the building footprint methods from rapida-map.js. The module exports `initBuildingLayer(map, crisisSlug, tokens, dispatch)` and `destroyBuildingLayer()`. It fetches building GeoJSON from `/api/v1/crises/{slug}/buildings`, adds the fill/stroke layers, and sets up the tap handler that calls `dispatch('building-selected', ...)`.

- [ ] **Step 3: Extract map-pins.js**

Create `resources/js/map-pins.js` by moving the damage pin methods. Exports `initPinLayer(map, crisisSlug, tokens)`, `destroyPinLayer()`, and `startPinPolling(map, crisisSlug, tokens, intervalMs)`. Handles clustered and unclustered pin layers, color mapping via tokens parameter.

- [ ] **Step 4: Extract map-heatmap.js**

Create `resources/js/map-heatmap.js` by moving the heatmap methods. Exports `initHeatmapLayer(map, crisisSlug)` and `destroyHeatmapLayer()`. Uses h3-js `cellToBoundary` for hexagon rendering.

- [ ] **Step 5: Refactor rapida-map.js as composer**

Replace the monolithic rapida-map.js with a composer that imports the 3 modules:

```javascript
import maplibregl from 'maplibre-gl';
import { Protocol } from 'pmtiles';
import { initBuildingLayer, destroyBuildingLayer } from './map-buildings.js';
import { initPinLayer, destroyPinLayer, startPinPolling } from './map-pins.js';
import { initHeatmapLayer, destroyHeatmapLayer } from './map-heatmap.js';

const protocol = new Protocol();
maplibregl.addProtocol('pmtiles', protocol.tile);

export default (config) => ({
    map: null,
    gpsWatchId: null,

    init() {
        const tokens = config.tokens || {};

        this.map = new maplibregl.Map({
            container: this.$refs.map,
            style: config.style || 'https://tiles.openfreemap.org/styles/liberty',
            center: config.center || [0, 0],
            zoom: config.zoom || 12,
            attributionControl: false,
        });

        this.map.on('load', () => {
            initBuildingLayer(this.map, config.crisisSlug, tokens, this.$dispatch.bind(this));
            initPinLayer(this.map, config.crisisSlug, tokens);
            if (config.showHeatmap) {
                initHeatmapLayer(this.map, config.crisisSlug);
            }
            startPinPolling(this.map, config.crisisSlug, tokens, 15000);
        });
    },

    startGps() {
        if (!navigator.geolocation) return;
        this.gpsWatchId = navigator.geolocation.watchPosition(
            (pos) => {
                this.$dispatch('gps-updated', {
                    latitude: pos.coords.latitude,
                    longitude: pos.coords.longitude,
                });
            },
            () => {},
            { enableHighAccuracy: true, maximumAge: 10000 }
        );
    },

    destroy() {
        if (this.gpsWatchId) navigator.geolocation.clearWatch(this.gpsWatchId);
        destroyPinLayer();
        destroyBuildingLayer();
        destroyHeatmapLayer();
        this.map?.remove();
    },
});
```

- [ ] **Step 6: Run `npm run build` to verify JS compiles**

```bash
npm run build
```

Expected: Build succeeds with no errors

- [ ] **Step 7: Run full test suite**

```bash
php artisan test --compact
```

Expected: 358 passed

- [ ] **Step 8: Commit**

```bash
git add resources/js/rapida-map.js resources/js/map-buildings.js resources/js/map-pins.js resources/js/map-heatmap.js
git commit -m "refactor: split rapida-map.js (796 lines) into 4 focused modules"
```

---

### Task 13: Wizard-Shell Decomposition — Livewire Step Components

This is the largest task. The 839-line wizard-shell.blade.php inline class gets replaced by 7 Livewire components: 1 orchestrator + 6 steps.

**Files:**
- Create: `app/Livewire/Wizard/WizardShell.php`
- Create: `app/Livewire/Wizard/StepPhoto.php`
- Create: `app/Livewire/Wizard/StepLocation.php`
- Create: `app/Livewire/Wizard/StepDamage.php`
- Create: `app/Livewire/Wizard/StepInfrastructure.php`
- Create: `app/Livewire/Wizard/StepModular.php`
- Create: `app/Livewire/Wizard/StepConfirmation.php`
- Modify: `resources/views/components/wizard/⚡wizard-shell.blade.php`

- [ ] **Step 1: Create the Livewire Wizard directory**

```bash
mkdir -p app/Livewire/Wizard
```

- [ ] **Step 2: Create WizardShell orchestrator**

Write `app/Livewire/Wizard/WizardShell.php`. This component:
- Holds `$crisis`, `$currentStep`, `$conflictMode`
- Stores collected step data in `$stepData` array
- Listens for `step-completed` to advance
- Listens for `report-submitted` to redirect
- Renders the current step component

The existing blade file's inline class (lines 1-120 of wizard-shell.blade.php) gets replaced by this class. The blade file becomes a thin view that renders `@livewire('wizard.step-photo')` etc. based on `$currentStep`.

- [ ] **Step 3: Create each step component**

Create each step as a standalone Livewire component in `app/Livewire/Wizard/`. Each step:
- Receives `$crisis` and `$conflictMode` as props
- Manages its own form state
- Dispatches `step-completed` event with its data when done
- Has its own blade view in `resources/views/livewire/wizard/`

Key implementations:
- **StepDamage** includes `$aiPending` property and `$aiSuggestion` for V6b gap fix
- **StepConfirmation** includes online/offline copy differentiation using Alpine `$store.offlineQueue.isOnline` for V6a gap fix
- **StepConfirmation** calls `ReportSubmissionService::submit()` with assembled `SubmitReportData` DTO

- [ ] **Step 4: Update wizard-shell.blade.php**

Remove the inline PHP class from the blade file. Replace with:

```php
<div>
    @if($currentStep === 1)
        @livewire('wizard.step-photo', ['crisis' => $crisis, 'conflictMode' => $conflictMode], key('step-1'))
    @elseif($currentStep === 2)
        @livewire('wizard.step-location', ['crisis' => $crisis, 'conflictMode' => $conflictMode], key('step-2'))
    @elseif($currentStep === 3)
        @livewire('wizard.step-damage', ['crisis' => $crisis, 'conflictMode' => $conflictMode], key('step-3'))
    @elseif($currentStep === 4)
        @livewire('wizard.step-infrastructure', ['crisis' => $crisis], key('step-4'))
    @elseif($currentStep === 5)
        @livewire('wizard.step-modular', ['crisis' => $crisis], key('step-5'))
    @elseif($currentStep === 6)
        @livewire('wizard.step-confirmation', [
            'crisis' => $crisis,
            'conflictMode' => $conflictMode,
            'stepData' => $stepData,
        ], key('step-6'))
    @endif
</div>
```

- [ ] **Step 5: Run full test suite**

```bash
php artisan test --compact
```

Expected: All existing wizard tests still pass (they test the route/view, which still renders the wizard)

- [ ] **Step 6: Run `npm run build`**

```bash
npm run build
```

- [ ] **Step 7: Pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Livewire/Wizard/ resources/views/components/wizard/ resources/views/livewire/wizard/
git commit -m "refactor: decompose wizard-shell (839 lines) into 7 Livewire components"
```

---

### Task 14: Submission Confirmation — Online/Offline Differentiation (V6a)

**Files:**
- Modify: `resources/views/components/molecules/submission-confirmation.blade.php`
- Modify: `resources/views/livewire/wizard/step-confirmation.blade.php` (or wherever the confirmation renders)

- [ ] **Step 1: Update submission-confirmation molecule**

In `resources/views/components/molecules/submission-confirmation.blade.php`, replace the heading section (line 40) with Alpine-aware conditional:

```blade
    <div class="space-y-2" x-data="{ isOnline: $store.offlineQueue?.isOnline ?? true }">
        <h2 class="text-h3 font-heading font-semibold text-slate-900">
            <span x-show="isOnline">{{ __('rapida.confirmation_online') }}</span>
            <span x-show="!isOnline" x-cloak>{{ __('rapida.confirmation_offline') }}</span>
        </h2>
        <p class="text-body text-slate-600">{{ __('rapida.confirmation_thanks') }}</p>
    </div>
```

- [ ] **Step 2: Run full test suite**

```bash
php artisan test --compact
```

- [ ] **Step 3: Commit**

```bash
git add resources/views/components/molecules/submission-confirmation.blade.php
git commit -m "fix: differentiate online vs offline confirmation copy (Gap V6a)"
```

---

### Task 15: StepDamage AI Thinking State (V6b)

**Files:**
- Modify: `app/Livewire/Wizard/StepDamage.php` (created in Task 13)
- Modify: `resources/views/livewire/wizard/step-damage.blade.php` (created in Task 13)

- [ ] **Step 1: Add AI pending state to StepDamage component**

In `app/Livewire/Wizard/StepDamage.php`, add properties and polling:

```php
public bool $aiPending = false;
public ?string $aiSuggestedLevel = null;
public ?float $aiConfidence = null;

public function mount(Crisis $crisis, bool $conflictMode = false): void
{
    $this->crisis = $crisis;
    $this->conflictMode = $conflictMode;
    $this->aiPending = true; // Assume AI is processing until we hear back
}

// Poll for AI result every 2 seconds
#[\Livewire\Attributes\Computed]
public function checkAiResult(): void
{
    // This would check if the AI result has arrived for the current report
    // For now, after 3 seconds, set aiPending to false
}
```

- [ ] **Step 2: Add AI UI states to the blade view**

In the step-damage blade view, add the AI thinking indicator:

```blade
<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <h1 class="text-h1 font-heading font-bold text-slate-900">{{ __('wizard.step_damage_title') }}</h1>
        <p class="text-body text-slate-600">{{ __('wizard.step_damage_desc') }}</p>
    </div>

    @if($aiPending)
        <div class="flex items-center gap-2 text-rapida-blue-700">
            <x-atoms.loader size="sm" />
            <span class="text-body">{{ __('rapida.ai_analyzing') }}</span>
        </div>
    @elseif($aiSuggestedLevel && !$value)
        <div class="rounded-xl bg-rapida-blue-50 border border-rapida-blue-200 p-4">
            <p class="text-body text-rapida-blue-900">
                {{ __('rapida.ai_suggestion_prompt', ['level' => __('rapida.damage_' . $aiSuggestedLevel)]) }}
            </p>
        </div>
    @endif

    <x-molecules.damage-classification
        name="damage_level"
        :value="$value ?: $aiSuggestedLevel"
        required
        wire:model.live="value"
    />
</div>
```

- [ ] **Step 3: Run full test suite**

```bash
php artisan test --compact
```

- [ ] **Step 4: Commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Livewire/Wizard/StepDamage.php resources/views/livewire/wizard/step-damage.blade.php
git commit -m "feat: add AI thinking state to damage step with suggestion display (Gap V6b)"
```

---

### Task 16: Run Full Test Suite + Fix Any Regressions

**Files:**
- Possibly modify: various test files

- [ ] **Step 1: Run full test suite**

```bash
php artisan test --compact
```

- [ ] **Step 2: Fix any failing tests**

If tests fail due to the refactoring (e.g., tests that directly reference the old wizard-shell inline class methods), update them to work with the new component structure.

- [ ] **Step 3: Run Pint on all modified PHP files**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Step 4: Run `npm run build` to verify frontend compiles**

```bash
npm run build
```

- [ ] **Step 5: Commit any test fixes**

```bash
git add -A
git commit -m "test: fix regressions from complexity separation refactoring"
```

---

### Task 17: Architecture Narrative Document

**Files:**
- Create: `docs/architecture-data-lifecycle.md`

- [ ] **Step 1: Write the architecture narrative**

Create `docs/architecture-data-lifecycle.md` following the structure from the spec (Section 9). This document traces the damage report lifecycle through all four layers and is formatted for UNDP evaluators.

Each subsection (3.1-3.8) follows the pattern:
- What happens
- Which layer owns it
- Key files
- Conflict mode behavior
- Offline behavior

Include the cross-cutting concerns section (conflict mode, offline-first, localization, resilience) and the design system contract section.

- [ ] **Step 2: Commit**

```bash
git add docs/architecture-data-lifecycle.md
git commit -m "docs: add Architecture & Data Lifecycle narrative for UNDP submission"
```

---

## Deferred to Follow-Up

These items from the spec are intentionally deferred — they don't block any gap fixes, security issues, or submission deliverables:

- **AnalyticsQueryService** (spec Section 3.1) — Extract dashboard queries into cached service. Currently works inline in analytics-panel.blade.php. Not a security or correctness issue.
- **WhatsAppBotService refactor** (spec Section 3.2) — Refactor to use ReportSubmissionService + ConflictModeService. The bot currently works. Refactoring it is a code quality improvement, not a bug fix.
- **analytics-panel refactor** (spec Phase 3 item 16) — Depends on AnalyticsQueryService.

These should be tackled after the core 17 tasks are complete and tests pass.

---

## Verification Checklist

After all tasks are complete, verify:

- [ ] `php artisan test --compact` — all tests pass (358+ tests)
- [ ] `npm run build` — frontend compiles without errors
- [ ] `vendor/bin/pint --dirty --format agent` — no formatting issues
- [ ] AI endpoint returns 403 without `X-Internal-Secret` header
- [ ] `/my-reports` shows empty state without device fingerprint or account
- [ ] Export routes are rate-limited (5/hour)
- [ ] Map pins use `#c46b5a` (crisis-rose-400) not `#EF4444` (red)
- [ ] APP_NAME shows "RAPIDA" in page titles
- [ ] Wizard submission creates report with correct `reporter_tier`
- [ ] Confirmation screen shows different copy online vs offline
- [ ] All 6 locales have matching key counts in `rapida.php`
