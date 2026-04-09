<?php

namespace App\Http\Controllers\Api;

use App\Events\ReportSubmitted;
use App\Http\Controllers\Controller;
use App\Models\Crisis;
use App\Models\DamageReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiReportController extends Controller
{
    public function store(Request $request): JsonResponse
    {
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
            'submitted_at' => 'required|date',
        ]);

        $crisis = Crisis::where('slug', $validated['crisis_slug'])->firstOrFail();

        $report = DamageReport::create([
            'crisis_id' => $crisis->id,
            'building_footprint_id' => $validated['building_footprint_id'] ?? null,
            'photo_url' => $validated['photo_url'] ?? 'https://rapida-demo.s3.amazonaws.com/placeholder.jpg',
            'photo_hash' => hash('sha256', $validated['photo_url'] ?? 'placeholder'),
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
            'completeness_score' => $this->calculateScore($validated),
            'submitted_via' => 'web',
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

    private function calculateScore(array $data): int
    {
        $score = 0;
        if (! empty($data['photo_url'])) {
            $score += 2;
        }
        if (! empty($data['damage_level']) && ! empty($data['infrastructure_type']) && ! empty($data['crisis_type'])) {
            $score += 3;
        }
        if (! empty($data['infrastructure_name'])) {
            $score += 1;
        }

        return $score;
    }
}
