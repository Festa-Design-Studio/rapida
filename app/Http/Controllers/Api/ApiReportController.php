<?php

namespace App\Http\Controllers\Api;

use App\Events\ReportSubmitted;
use App\Http\Controllers\Controller;
use App\Models\Crisis;
use App\Models\DamageReport;
use App\Services\CompletenessScoreService;
use App\Services\PauseModeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiReportController extends Controller
{
    public function __construct(
        private readonly CompletenessScoreService $scoreService,
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
            'reporter_tier' => 'nullable|in:anonymous,device,account',
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

        // Idempotency check
        if ($key = $request->header('Idempotency-Key')) {
            $existing = DamageReport::where('idempotency_key', $key)->first();
            if ($existing) {
                return response()->json([
                    'report_id' => $existing->id,
                    'completeness_score' => $existing->completeness_score,
                    'message' => 'Report already received.',
                ], 201);
            }
        }

        // Null-force device fingerprint in conflict mode
        $fingerprintId = $crisis->conflict_context
            ? null
            : ($validated['device_fingerprint_id'] ?? null);

        $report = DamageReport::create([
            'crisis_id' => $crisis->id,
            'building_footprint_id' => $validated['building_footprint_id'] ?? null,
            'device_fingerprint_id' => $fingerprintId,
            'photo_url' => $validated['photo_url'] ?? 'https://rapida-demo.s3.amazonaws.com/placeholder.jpg',
            'photo_hash' => hash('sha256', $validated['photo_url'] ?? 'placeholder'),
            'photo_guidance_shown' => $validated['photo_guidance_shown'] ?? false,
            'damage_level' => $validated['damage_level'],
            'infrastructure_type' => $validated['infrastructure_type'],
            'crisis_type' => $validated['crisis_type'],
            'infrastructure_name' => $validated['infrastructure_name'] ?? null,
            'debris_required' => $validated['debris_required'] ?? null,
            'location_method' => $validated['location_method'] ?? 'coordinate_only',
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'w3w_code' => $validated['w3w_code'] ?? null,
            'landmark_text' => $validated['landmark_text'] ?? null,
            'description' => $validated['description'] ?? null,
            'completeness_score' => $this->scoreService->scoreFromArray($validated),
            'submitted_via' => 'web',
            'reporter_tier' => $crisis->conflict_context ? 'anonymous' : ($validated['reporter_tier'] ?? 'anonymous'),
            'idempotency_key' => $request->header('Idempotency-Key'),
            'submitted_at' => $validated['submitted_at'],
            'synced_at' => now(),
            'is_flagged' => false,
        ]);

        ReportSubmitted::dispatch($report);

        return response()->json([
            'report_id' => $report->id,
            'completeness_score' => $report->completeness_score,
            'message' => 'Report received.',
        ], 201);
    }
}
