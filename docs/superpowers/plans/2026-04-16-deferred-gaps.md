# RAPIDA Deferred Gaps Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Resolve all non-critical gaps identified in the Notion source of truth (Gap Audit, PRD V2) and code review that were deferred from the complexity separation sprint — covering WhatsApp service refactoring, QR code display, pHash computation, analytics caching, and assorted quality fixes.

**Architecture:** Seven independent tasks that can be executed in any order. Each produces a self-contained commit. No task depends on another — they all build on the completed complexity separation refactoring (381 tests passing).

**Tech Stack:** Laravel 13, Livewire 4, Pest 4, PHP 8.4 GD library

**Prerequisite:** All 17 tasks from `docs/superpowers/plans/2026-04-16-complexity-separation.md` must be completed first.

---

## File Structure

### Files to Create

```
app/Services/AnalyticsQueryService.php        — Cached dashboard queries (extracted from analytics-panel)
database/factories/BadgeFactory.php            — Missing factory for Badge model
```

### Files to Modify

```
app/Services/WhatsAppBotService.php            — Refactor to use ReportSubmissionService + ConflictModeService
app/Jobs/ProcessPhotoUpload.php                — Add pHash computation
resources/views/components/admin/⚡crisis-manager.blade.php — Add QR code display
resources/views/components/dashboard/⚡analytics-panel.blade.php — Delegate to AnalyticsQueryService
routes/web.php                                 — Fix N+1 on /confirmation route
```

---

### Task 1: WhatsAppBotService Refactor — Use ReportSubmissionService

**Files:**
- Modify: `app/Services/WhatsAppBotService.php:220-268` (stepConfirm method)
- Modify: `app/Services/WhatsAppBotService.php:1-20` (constructor)
- Test: `tests/Feature/WhatsAppBotTest.php` (existing — verify still passes)

This is the last submission path still using inline `DamageReport::create()`. After this, all 3 paths (web, API, WhatsApp) share `ReportSubmissionService`.

- [ ] **Step 1: Read the current WhatsAppBotService**

Read `app/Services/WhatsAppBotService.php` in full. Note:
- Constructor currently injects only `CompletenessScoreService`
- `stepConfirm()` at line 224 does inline `DamageReport::create()` with 20+ fields
- Conflict mode is checked inline at line 235: `$isConflict = $crisis->conflict_context ?? false`
- `reporter_tier` is hardcoded as `'anonymous'` at line 254

- [ ] **Step 2: Update the constructor**

Add `ReportSubmissionService` and `ConflictModeService` to the constructor. Keep `CompletenessScoreService` for now (it's used elsewhere in the class for the scoreFromArray preview):

```php
public function __construct(
    private readonly CompletenessScoreService $scoreService,
    private readonly ReportSubmissionService $submissionService,
    private readonly ConflictModeService $conflictModeService,
) {}
```

Add imports at the top:

```php
use App\DataTransferObjects\SubmitReportData;
use App\Services\ConflictModeService;
use App\Services\ReportSubmissionService;
```

- [ ] **Step 3: Replace stepConfirm() body**

Replace the `stepConfirm()` method (lines 224-268) with:

```php
    private function stepConfirm(string $key, array $session, string $body, string $lang): array
    {
        if (! $this->isConfirmation($body)) {
            return ['message' => __('whatsapp.confirm_or_restart', [], $lang)];
        }

        $crisis = Crisis::where('slug', $session['crisis_slug'])->first();
        if (! $crisis) {
            $crisis = Crisis::where('status', 'active')->first();
        }

        $data = new SubmitReportData(
            crisis: $crisis,
            latitude: isset($session['latitude']) ? (float) $session['latitude'] : null,
            longitude: isset($session['longitude']) ? (float) $session['longitude'] : null,
            w3wCode: $session['w3w_code'] ?? null,
            landmarkText: $session['landmark_text'] ?? null,
            damageLevel: $session['damage_level'],
            infrastructureType: $session['infrastructure_type'],
            crisisType: $session['crisis_type'] ?? 'flood',
            debrisRequired: $session['debris_required'] ?? false,
            deviceFingerprintId: $session['device_fingerprint_id'] ?? null,
            idempotencyKey: $session['idempotency_key'] ?? null,
            buildingFootprintId: $session['building_footprint_id'] ?? null,
            locationMethod: $session['location_method'] ?? 'coordinate_only',
            submittedVia: 'whatsapp',
        );

        $report = $this->submissionService->submit($data);

        Cache::forget($key);

        return ['message' => __('whatsapp.report_submitted', [
            'report_id' => substr($report->id, 0, 8),
        ], $lang)];
    }
```

- [ ] **Step 4: Replace inline conflict_context checks**

Search the file for other `conflict_context` checks and replace with `$this->conflictModeService->isConflict($crisis)`. Key locations:
- `stepStart()` — where it checks `$crisis?->conflict_context ?? false`
- `stepLocation()` — where it may modify the GPS prompt for conflict mode

- [ ] **Step 5: Fix step type consistency**

In the same file, find where steps are set as strings vs integers. Standardize all step values to strings for consistency:

Change step assignments from:
```php
$session['step'] = 1;    // integer
$session['step'] = '4b'; // string
```

To consistently use strings:
```php
$session['step'] = '1';
$session['step'] = '2';
$session['step'] = '3';
$session['step'] = '4';
$session['step'] = '4b';
$session['step'] = '4c';
$session['step'] = '5';
```

And update the match expression to use string keys:
```php
return match ((string) $step) {
    '0' => $this->stepStart(...),
    '1' => $this->stepPhoto(...),
    '2' => $this->stepLocation(...),
    '3' => $this->stepDamage(...),
    '4' => $this->stepInfra(...),
    '4b' => $this->stepCrisisType(...),
    '4c' => $this->stepDebris(...),
    '5' => $this->stepConfirm(...),
    default => ['message' => __('whatsapp.unknown_state', [], $lang)],
};
```

The `(string) $step` cast ensures consistent matching regardless of how the session stores the value.

- [ ] **Step 6: Run WhatsApp tests**

```bash
php artisan test --compact --filter=WhatsApp
```

Expected: All WhatsApp tests pass

- [ ] **Step 7: Run full test suite**

```bash
php artisan test --compact
```

Expected: 381 passed

- [ ] **Step 8: Pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Services/WhatsAppBotService.php
git commit -m "refactor: WhatsAppBotService delegates to ReportSubmissionService, fixes step type consistency"
```

---

### Task 2: QR Code Display in Operator Admin Panel

**Files:**
- Modify: `resources/views/components/admin/⚡crisis-manager.blade.php`

The Gap Audit (L1) requires a WhatsApp QR code to be visible in the operator admin panel for each crisis. The QR encodes the WhatsApp deeplink: `https://api.whatsapp.com/send?phone=14155238886&text=RAPIDA+{slug}`.

- [ ] **Step 1: Read the current crisis-manager component**

Read `resources/views/components/admin/⚡crisis-manager.blade.php` in full to understand the blade template structure — specifically the crisis list section where each crisis is displayed.

- [ ] **Step 2: Add QR code display to the crisis list**

In the blade template section where each crisis is shown (inside the `@foreach($crises as $crisis)` loop), add a QR code section after the existing crisis details. Use a third-party QR code API for simplicity (no package install needed):

```blade
{{-- WhatsApp QR Code --}}
@if($crisis->status === 'active' && ($crisis->whatsapp_enabled ?? true))
    @php
        $whatsappUrl = 'https://api.whatsapp.com/send?phone=' . ltrim(config('services.twilio.whatsapp_from', 'whatsapp:+14155238886'), 'whatsapp:+') . '&text=RAPIDA+' . $crisis->slug;
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($whatsappUrl);
    @endphp
    <div class="mt-4 p-4 bg-slate-50 rounded-lg border border-slate-200">
        <p class="text-sm font-medium text-slate-700 mb-2">WhatsApp Report Link</p>
        <div class="flex items-start gap-4">
            <img src="{{ $qrUrl }}" alt="WhatsApp QR code for {{ $crisis->name }}" width="100" height="100" class="rounded" />
            <div class="text-xs text-slate-500 space-y-1">
                <p class="font-mono break-all">{{ $whatsappUrl }}</p>
                <a href="{{ $qrUrl }}" download="rapida-qr-{{ $crisis->slug }}.png" class="inline-flex items-center text-rapida-blue-700 hover:underline">
                    Download QR
                </a>
            </div>
        </div>
    </div>
@endif
```

- [ ] **Step 3: Run full test suite**

```bash
php artisan test --compact
```

- [ ] **Step 4: Commit**

```bash
git add resources/views/components/admin/⚡crisis-manager.blade.php
git commit -m "feat: add WhatsApp QR code display to operator admin panel (Gap L1)"
```

---

### Task 3: pHash Computation in ProcessPhotoUpload

**Files:**
- Modify: `app/Jobs/ProcessPhotoUpload.php`

The PRD V2 specifies perceptual hashing (pHash) for duplicate detection. The current job computes SHA256 only. pHash generates a hash based on visual similarity — two photos of the same building from slightly different angles will have similar pHashes.

- [ ] **Step 1: Add pHash computation to ProcessPhotoUpload**

After the EXIF stripping section (line 55) and before the hash update (line 58), add a pHash computation. Use GD's image resizing to compute a simple average hash (aHash) which is a lightweight pHash alternative that doesn't require external libraries:

```php
        // Compute perceptual hash (average hash) for duplicate detection
        $pHash = $this->computePerceptualHash($path);
        if ($pHash) {
            $this->report->update([
                'photo_hash' => hash_file('sha256', $path),
                'photo_phash' => $pHash,
            ]);
        } else {
            $this->report->update([
                'photo_hash' => hash_file('sha256', $path),
            ]);
        }
```

Remove the existing hash update at line 58-60 (it's now handled above).

Add the method at the end of the class:

```php
    /**
     * Compute a perceptual hash (average hash / aHash) using GD.
     * Resize to 8x8 grayscale, compare each pixel to the mean.
     */
    private function computePerceptualHash(string $path): ?string
    {
        $image = @imagecreatefromstring(file_get_contents($path));
        if (! $image) {
            return null;
        }

        // Resize to 8x8
        $small = imagecreatetruecolor(8, 8);
        imagecopyresampled($small, $image, 0, 0, 0, 0, 8, 8, imagesx($image), imagesy($image));
        imagedestroy($image);

        // Convert to grayscale values
        $pixels = [];
        for ($y = 0; $y < 8; $y++) {
            for ($x = 0; $x < 8; $x++) {
                $rgb = imagecolorat($small, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                $pixels[] = (int) (0.299 * $r + 0.587 * $g + 0.114 * $b);
            }
        }
        imagedestroy($small);

        // Compare each pixel to the mean
        $mean = array_sum($pixels) / count($pixels);
        $hash = '';
        foreach ($pixels as $pixel) {
            $hash .= $pixel >= $mean ? '1' : '0';
        }

        // Convert 64-bit binary string to hex (16 chars)
        return str_pad(base_convert($hash, 2, 16), 16, '0', STR_PAD_LEFT);
    }
```

- [ ] **Step 2: Check if photo_phash column exists**

Check if the `damage_reports` table has a `photo_phash` column. If not, create a migration:

```bash
php artisan make:migration add_photo_phash_to_damage_reports --no-interaction
```

Migration content:

```php
Schema::table('damage_reports', function (Blueprint $table) {
    $table->string('photo_phash', 16)->nullable()->after('photo_hash')->index();
});
```

- [ ] **Step 3: Run migration**

```bash
php artisan migrate
```

- [ ] **Step 4: Update DuplicateDetectionService to use pHash**

In `app/Services/DuplicateDetectionService.php`, find where it checks `photo_hash` for duplicates. Add an additional check: if two reports have a Hamming distance of <= 5 between their `photo_phash` values, flag as potential duplicate.

Read the file first to understand where to add the check.

- [ ] **Step 5: Run full test suite**

```bash
php artisan test --compact
```

- [ ] **Step 6: Pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Jobs/ProcessPhotoUpload.php app/Services/DuplicateDetectionService.php database/migrations/
git commit -m "feat: add perceptual hash (aHash) to ProcessPhotoUpload for visual duplicate detection"
```

---

### Task 4: BadgeFactory Creation

**Files:**
- Create: `database/factories/BadgeFactory.php`

The Badge model declares `HasFactory` but no factory exists. This prevents seeding and testing badge-related functionality.

- [ ] **Step 1: Check Badge model for fillable fields**

Read `app/Models/Badge.php` to see which fields the factory needs.

- [ ] **Step 2: Create the factory**

```bash
php artisan make:factory BadgeFactory --model=Badge --no-interaction
```

Then update the factory definition to match the model's schema:

```php
<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

class BadgeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'account_id' => Account::factory(),
            'name' => fake()->randomElement(['first_report', 'five_reports', 'twenty_five_reports', 'fifty_reports']),
            'icon_emoji' => fake()->randomElement(['🏅', '⭐', '🏆', '💎']),
            'earned_at' => fake()->dateTimeBetween('-30 days'),
        ];
    }
}
```

- [ ] **Step 3: Verify factory works**

```bash
php artisan tinker --execute 'App\Models\Badge::factory()->make(); echo "OK";'
```

- [ ] **Step 4: Run full test suite**

```bash
php artisan test --compact
```

- [ ] **Step 5: Commit**

```bash
vendor/bin/pint --dirty --format agent
git add database/factories/BadgeFactory.php
git commit -m "feat: add BadgeFactory for Badge model (was missing despite HasFactory trait)"
```

---

### Task 5: AnalyticsQueryService Extraction with Caching

**Files:**
- Create: `app/Services/AnalyticsQueryService.php`
- Modify: `resources/views/components/dashboard/⚡analytics-panel.blade.php`

The analytics panel currently has 6 raw database queries as Livewire computed properties. Extract them into a service with `Cache::remember()` for 5-minute caching.

- [ ] **Step 1: Read the current analytics-panel**

Read `resources/views/components/dashboard/⚡analytics-panel.blade.php` in full to capture all 6 query methods.

- [ ] **Step 2: Create AnalyticsQueryService**

Write `app/Services/AnalyticsQueryService.php`:

```php
<?php

namespace App\Services;

use App\Models\DamageReport;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class AnalyticsQueryService
{
    private const CACHE_TTL = 300; // 5 minutes

    public function totalReports(string $crisisId): int
    {
        return Cache::remember(
            "analytics:{$crisisId}:total",
            self::CACHE_TTL,
            fn () => DamageReport::where('crisis_id', $crisisId)->count()
        );
    }

    public function byDamageLevel(string $crisisId): Collection
    {
        return Cache::remember(
            "analytics:{$crisisId}:by_damage",
            self::CACHE_TTL,
            fn () => DamageReport::where('crisis_id', $crisisId)
                ->selectRaw('damage_level, count(*) as count')
                ->groupBy('damage_level')
                ->pluck('count', 'damage_level')
        );
    }

    public function byInfrastructure(string $crisisId): Collection
    {
        return Cache::remember(
            "analytics:{$crisisId}:by_infra",
            self::CACHE_TTL,
            fn () => DamageReport::where('crisis_id', $crisisId)
                ->selectRaw('infrastructure_type, count(*) as count')
                ->groupBy('infrastructure_type')
                ->pluck('count', 'infrastructure_type')
        );
    }

    public function reportsOverTime(string $crisisId, int $days = 30): Collection
    {
        return Cache::remember(
            "analytics:{$crisisId}:over_time:{$days}",
            self::CACHE_TTL,
            fn () => DamageReport::where('crisis_id', $crisisId)
                ->where('submitted_at', '>=', now()->subDays($days))
                ->selectRaw("DATE(submitted_at) as date, count(*) as count")
                ->groupByRaw('DATE(submitted_at)')
                ->orderBy('date')
                ->pluck('count', 'date')
        );
    }

    /**
     * @return Collection<int, object{building_footprint_id: string, report_count: int}>
     */
    public function topBuildings(string $crisisId, int $limit = 10): Collection
    {
        return Cache::remember(
            "analytics:{$crisisId}:top_buildings:{$limit}",
            self::CACHE_TTL,
            fn () => DamageReport::where('crisis_id', $crisisId)
                ->whereNotNull('building_footprint_id')
                ->selectRaw('building_footprint_id, count(*) as report_count')
                ->groupBy('building_footprint_id')
                ->orderByDesc('report_count')
                ->limit($limit)
                ->get()
        );
    }

    public function recentReports(string $crisisId, int $limit = 10): Collection
    {
        return Cache::remember(
            "analytics:{$crisisId}:recent:{$limit}",
            self::CACHE_TTL,
            fn () => DamageReport::where('crisis_id', $crisisId)
                ->with('building')
                ->latest('submitted_at')
                ->limit($limit)
                ->get()
        );
    }
}
```

- [ ] **Step 3: Refactor analytics-panel to use the service**

In the analytics-panel inline class, replace the computed property bodies with service calls. Change the class to inject `AnalyticsQueryService` in `mount()` or use `app()`:

Replace each `get*Property()` method body. For example:

```php
public function getTotalReportsProperty(): int
{
    return app(AnalyticsQueryService::class)->totalReports($this->crisisId);
}

public function getReportsByDamageLevelProperty(): Collection
{
    return app(AnalyticsQueryService::class)->byDamageLevel($this->crisisId);
}
```

Repeat for all 6 properties.

- [ ] **Step 4: Run full test suite**

```bash
php artisan test --compact
```

- [ ] **Step 5: Pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Services/AnalyticsQueryService.php resources/views/components/dashboard/⚡analytics-panel.blade.php
git commit -m "refactor: extract AnalyticsQueryService with 5-minute caching from analytics-panel"
```

---

### Task 6: Fix N+1 Query on /confirmation Route

**Files:**
- Modify: `routes/web.php` (the `/confirmation` route closure)

- [ ] **Step 1: Find and fix the /confirmation route**

In `routes/web.php`, find:

```php
    Route::get('/confirmation', function (Request $request) {
        $report = $request->query('report')
            ? DamageReport::find($request->query('report'))
            : null;

        return view('templates.submission-confirmation', ['report' => $report]);
    })->name('confirmation');
```

Replace with:

```php
    Route::get('/confirmation', function (Request $request) {
        $report = $request->query('report')
            ? DamageReport::with(['crisis', 'building', 'verification', 'modules'])->find($request->query('report'))
            : null;

        return view('templates.submission-confirmation', ['report' => $report]);
    })->name('confirmation');
```

The `->with()` eager-loads relationships to prevent N+1 queries when the view accesses `$report->crisis`, `$report->building`, etc.

- [ ] **Step 2: Run full test suite**

```bash
php artisan test --compact
```

- [ ] **Step 3: Commit**

```bash
git add routes/web.php
git commit -m "fix: eager-load relationships on /confirmation route to prevent N+1 queries"
```

---

### Task 7: Production Data Verification Script

**Files:**
- No files created — this is a verification task

This task verifies that the production database at `https://rapida-main-6sutvc.laravel.cloud/` has both demo crises and the evaluator credentials that the Demo Video Script requires.

- [ ] **Step 1: Verify local database matches seeder expectations**

```bash
php artisan tinker --execute '
$crises = App\Models\Crisis::all(["name", "slug", "status", "conflict_context", "whatsapp_enabled"]);
echo "Crises:\n";
foreach ($crises as $c) {
    echo "  - {$c->name} ({$c->slug}) | status={$c->status} | conflict={$c->conflict_context} | whatsapp={$c->whatsapp_enabled}\n";
}
echo "\nUNDP Users:\n";
$users = App\Models\UndpUser::all(["name", "email", "role", "is_active"]);
foreach ($users as $u) {
    echo "  - {$u->name} ({$u->email}) | role={$u->role->value} | active={$u->is_active}\n";
}
echo "\nBuildings: " . App\Models\Building::count();
echo "\nReports: " . App\Models\DamageReport::count();
echo "\n";
'
```

- [ ] **Step 2: If second crisis is missing, run the seeder**

```bash
php artisan db:seed --class=CrisisSeeder
php artisan db:seed --class=UndpUserSeeder
```

- [ ] **Step 3: Verify the seeder creates the correct evaluator credentials**

The Demo Video Script requires:
- **Analyst:** `evaluator@undp.org` / `rapida-demo-2026`
- **Operator:** `abayomi@rapida.app` (check the seeder for this)

Read `database/seeders/UndpUserSeeder.php` to confirm both exist.

- [ ] **Step 4: Document findings**

Report which crises exist, how many buildings/reports, and whether evaluator credentials match the Demo Video Script at:

```
Prototype URL: https://rapida-main-6sutvc.laravel.cloud/crisis/accra-flood-2026
Analyst Login: evaluator@undp.org / rapida-demo-2026
Operator Login: abayomi@rapida.app / [password from seeder]
```

---

## Verification Checklist

After all tasks are complete:

- [ ] `php artisan test --compact` — all tests pass
- [ ] `npm run build` — frontend compiles
- [ ] `vendor/bin/pint --dirty --format agent` — no formatting issues
- [ ] WhatsApp submission creates report via ReportSubmissionService (not inline)
- [ ] Operator admin panel shows QR code for active crises with WhatsApp enabled
- [ ] ProcessPhotoUpload computes perceptual hash
- [ ] Badge::factory() works without error
- [ ] Analytics panel queries are cached (5-minute TTL)
- [ ] /confirmation route eager-loads relationships
- [ ] Both demo crises exist in database (standard + conflict)
- [ ] Evaluator credentials match Demo Video Script
