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
