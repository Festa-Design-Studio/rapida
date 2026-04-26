# RAPIDA -- Architecture & Data Lifecycle

## 1. Principle

In a crisis deployment, multiple teams -- UNDP operators, field coordinators, community members -- interact with the system simultaneously across different channels. Clean separation of concerns means each channel (web wizard, REST API, WhatsApp) shares the same submission logic via a single service class, and changes to one channel cannot break the others. This is not an academic preference; it is an operational requirement when a system must remain reliable while being extended mid-crisis.

## 2. The Four Layers

| Layer | Responsibility | Owns |
|-------|---------------|------|
| Feature | Orchestrates a user-facing capability end-to-end | Controllers, Livewire components, API endpoints |
| Logic | Encapsulates domain rules independent of transport | Services (`ReportSubmissionService`, `ConflictModeService`), DTOs, Jobs |
| Design System | Defines visual constraints as tokens, not opinions | `config/rapida-tokens.php`, Tailwind config, token CJS files |
| UI | Renders markup; stateless beyond Alpine/Livewire reactivity | Blade templates, Alpine components, JS map modules |

## 3. Data Lifecycle -- Birth to Export

### 3.1 Capture

Three channels converge on `ReportSubmissionService::submit(SubmitReportData)`: the Livewire web wizard dispatches a `SubmitReportData` DTO directly; the REST API (`ApiReportController`) hydrates the same DTO from JSON; WhatsApp (`WhatsAppWebhookController`) builds it from Twilio webhook payloads verified by `VerifyTwilioSignature` middleware. The DTO carries an `idempotency_key` so offline-queued submissions are safely deduplicated on replay. In conflict mode, `ConflictModeService::applyToSubmission()` nullifies device fingerprints before the report is persisted.

**Key files:** `app/Services/ReportSubmissionService.php`, `app/DataTransferObjects/SubmitReportData.php`, `app/Http/Controllers/WhatsAppWebhookController.php`

### 3.2 Validate

`SubmitReportData` is a typed PHP 8.4 DTO that enforces structure at the transport boundary. Idempotency is checked via `idempotency_key` lookup before any write. `ConflictModeService` gates sensitive fields: if the crisis has `conflict_context = true`, fingerprinting is disabled and reporter tier is forced to `anonymous`. `BackpressureThrottle` middleware attaches `X-Rapida-Queue-Pressure` headers so offline clients can self-throttle during high load.

**Key files:** `app/DataTransferObjects/SubmitReportData.php`, `app/Services/ConflictModeService.php`, `app/Http/Middleware/BackpressureThrottle.php`

### 3.3 Classify

After persistence, a `ReportSubmitted` event dispatches `ClassifyDamageWithAI` as a queued job. The job POSTs the photo URL to an external AI sidecar with a shared secret and a callback URL (`/api/v1/internal/ai-result`). The callback endpoint is protected by `VerifyInternalSecret` middleware. AI classification is advisory: the suggested damage level is stored in `ai_suggested_level` alongside the reporter's original `damage_level`. `CircuitBreakerService` (5-failure threshold, 30s open window) prevents cascading failures if the AI service is down.

**Key files:** `app/Jobs/ClassifyDamageWithAI.php`, `app/Http/Middleware/VerifyInternalSecret.php`, `app/Services/CircuitBreakerService.php`

### 3.4 Store

Reports are stored in PostgreSQL with PostGIS extensions. `BuildingFootprintService::snapToNearest()` uses `ST_DWithin` to associate reports with the closest building footprint within 100m. `CanonicalReportService::recompute()` maintains one "best" report per building, ranked by `completeness_score` then `submitted_at`, so the map always shows the highest-quality data. The `DamageReport` model uses UUIDs, soft deletes, and automatic versioning via an Eloquent `updating` hook that snapshots changed fields into `ReportVersion`.

**Key files:** `app/Models/DamageReport.php`, `app/Services/BuildingFootprintService.php`, `app/Services/CanonicalReportService.php`

### 3.5 Verify

UNDP analysts flag, assign, and verify reports through `VerificationController`. `DamageReportPolicy` enforces role-based access: analysts and field coordinators can flag and verify; only operators and superadmins can delete. Every mutation is captured by `ReportVersion` with the actor type (`undp`, `account`, or `system`) and the changed fields, providing a complete audit trail.

**Key files:** `app/Http/Controllers/VerificationController.php`, `app/Policies/DamageReportPolicy.php`, `app/Models/ReportVersion.php`

### 3.6 Display

MapLibre GL JS renders three distinct layers, each in its own JS module: `map-buildings.js` (vector polygon footprints), `map-pins.js` (individual report markers with polling), and `map-heatmap.js` (H3 hexagonal aggregation for reporter-facing privacy). Color tokens flow from `config/rapida-tokens.php` through Blade data attributes into Alpine, then into each JS module. Reporter mode shows the heatmap by default; dashboard mode shows individual pins with auto-polling.

**Key files:** `resources/js/rapida-map.js`, `resources/js/map-buildings.js`, `resources/js/map-pins.js`, `resources/js/map-heatmap.js`

### 3.7 Export

Six formats: CSV, GeoJSON, KML, Shapefile, GeoPackage (GPKG), PDF. Each is a dedicated job class. All use Eloquent `cursor()` for memory-safe iteration over large datasets. Exports are written to storage and streamed to the client via `ExportController`. Rate limiting is applied at the route level. The GPKG writer is pure-PHP via PDO-SQLite — a GeoPackage is a SQLite database with a documented application_id and required-table schema, so we avoid a system-binary runtime dependency (ogr2ogr) that may not ship with Laravel Cloud's PHP image.

**Key files:** `app/Jobs/ExportReportsCsv.php`, `app/Jobs/ExportReportsGeoJson.php`, `app/Jobs/ExportReportsGpkg.php`, `app/Http/Controllers/ExportController.php`

### 3.8 Archive

Each crisis defines `data_retention_days`. The `ArchiveCrisisData` job runs on the `exports` queue and archives crises that have exceeded their retention window using a PostgreSQL interval comparison. Archived crises are soft-transitioned to `status = 'archived'`, preserving data for audit while removing them from active queries.

**Key files:** `app/Jobs/ArchiveCrisisData.php`, `app/Models/Crisis.php`

## 4. Cross-Cutting Concerns

### 4.1 Conflict Mode

`ConflictModeService` is the single gate for all conflict-sensitive behavior. When `crisis.conflict_context = true`, it nullifies device fingerprints, forces anonymous reporter tier, and disables the leaderboard. Previously these checks were scattered inline across 5 files. Now one service, called from each consumer: submission, display, badge, and WhatsApp flows.

**Key files:** `app/Services/ConflictModeService.php`

### 4.2 Offline-First

The service worker (Workbox) caches the app shell, self-hosted fonts, and map tiles with strategy-appropriate policies (CacheFirst for static assets, NetworkFirst for live pins). `offline-queue.js` uses IndexedDB to queue reports locally, keyed by `idempotency_key`. Workbox `BackgroundSyncPlugin` retries POSTs on connectivity. The client reads the `X-Rapida-Queue-Pressure` response header and applies graduated back-off delays (0s normal, 5s moderate, 15s high, 30s critical).

**Key files:** `resources/js/service-worker.js`, `resources/js/offline-queue.js`, `app/Http/Middleware/BackpressureThrottle.php`

### 4.3 Localization

Six UN languages (en, fr, ar, es, zh, ru). RTL layout for Arabic via `dir="rtl"`. Locale resolution chain in `SetLocaleFromCrisis` middleware: session toggle, account preference, cookie, Accept-Language header (negotiated, de-regioned), crisis default, framework fallback. The crisis default is deliberately a fallback, not a mandate -- a reporter's browser preference is honored if it matches the crisis's available languages.

**Key files:** `app/Http/Middleware/SetLocaleFromCrisis.php`

### 4.4 Resilience

`CircuitBreakerService` wraps external calls (AI sidecar, translation) with a 5-failure threshold and 30-second open window, using cache-backed state (closed/open/half-open). `BackpressureThrottle` middleware measures queue depth and signals pressure to clients. `PauseModeService` allows operators to pause an entire crisis's intake via a cache flag, checked before submission processing.

**Key files:** `app/Services/CircuitBreakerService.php`, `app/Services/PauseModeService.php`, `app/Http/Middleware/BackpressureThrottle.php`

## 5. Design System Contract

Token flow: `config/rapida-tokens.php` defines semantic color values server-side. These are passed through Blade data attributes into Alpine components, then forwarded to JS map modules as constructor arguments. The Tailwind configuration consumes the same values via CJS token files. Trauma-informed constraint: no pure red anywhere in the system. Destroyed/complete damage uses `#c46b5a` (crisis-rose-400), a muted terracotta that communicates severity without triggering distress.

**Key files:** `config/rapida-tokens.php`, `resources/js/rapida-map.js`

## 6. Security Posture

All external integration points are authenticated: the AI callback endpoint uses `VerifyInternalSecret` (shared-secret header validation), WhatsApp webhooks use `VerifyTwilioSignature` (Twilio HMAC verification). Role-based access is enforced via `DamageReportPolicy` using Laravel Gates. Photos are processed through `PhotoStorageService` and then sanitised by `PhotoExifService`, which strips device-identifying EXIF tags (Make, Model, Software, Artist, Copyright, HostComputer, plus all unknown EXIF 2.3+ tags such as BodySerialNumber and LensSerialNumber that are dropped automatically by the underlying PEL library) while preserving GPS coordinates and DateTimeOriginal so analysts retain spatial provenance when downloading the photo file. All export and API endpoints are rate-limited. The `BackpressureThrottle` middleware prevents queue flooding. UUIDs are used for all primary keys to prevent enumeration attacks.

**Key files:** `app/Http/Middleware/VerifyInternalSecret.php`, `app/Http/Middleware/VerifyTwilioSignature.php`, `app/Policies/DamageReportPolicy.php`, `app/Services/PhotoStorageService.php`, `app/Services/PhotoExifService.php`
