<?php

namespace App\Http\Controllers\Api;

use App\Events\RecoveryOutcomeCreated;
use App\Http\Controllers\Controller;
use App\Models\Crisis;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecoveryOutcomeController extends Controller
{
    public function index(string $slug, Request $request): JsonResponse
    {
        $crisis = Crisis::where('slug', $slug)->firstOrFail();

        $query = $crisis->recoveryOutcomes()->latest('triggered_at');

        if ($h3Cell = $request->query('h3_cell')) {
            $query->where('h3_cell_id', $h3Cell);
        }

        return response()->json($query->get());
    }

    public function store(string $slug, Request $request): JsonResponse
    {
        $crisis = Crisis::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'h3_cell_id' => 'required|string',
            'message' => 'required|string|max:1000',
        ]);

        $outcome = $crisis->recoveryOutcomes()->create([
            ...$validated,
            'triggered_by' => $request->user('undp')->id,
            'triggered_at' => now(),
        ]);

        RecoveryOutcomeCreated::dispatch($outcome);

        return response()->json($outcome, 201);
    }
}
