<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DamageReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ApiAiController extends Controller
{
    public function receive(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'job_id' => 'required|string',
            'status' => 'required|in:success,error',
            'damage_level' => 'nullable|in:minimal,partial,complete',
            'confidence' => 'nullable|numeric|between:0,1',
            'scores' => 'nullable|array',
            'error' => 'nullable|string',
        ]);

        if ($validated['status'] !== 'success') {
            return response()->json(['ok' => true]);
        }

        // Try DamageReport (web wizard path)
        $report = DamageReport::find($validated['job_id']);
        if ($report) {
            $report->update([
                'ai_suggested_level' => $validated['damage_level'],
                'ai_confidence' => $validated['confidence'],
            ]);

            return response()->json(['ok' => true]);
        }

        // Try WhatsApp session (Cache-based)
        $session = Cache::get($validated['job_id']);
        if ($session && is_array($session)) {
            $session['ai_suggested_level'] = $validated['damage_level'];
            $session['ai_confidence'] = $validated['confidence'];
            Cache::put($validated['job_id'], $session, 1800);
        }

        return response()->json(['ok' => true]);
    }
}
