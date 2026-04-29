<?php

namespace App\Http\Controllers;

use App\Models\Crisis;
use App\Models\DamageReport;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $crisis = Crisis::where('status', 'active')->first();

        // Cap is generous (50) so the panel reflects the magnitude of
        // what's pinned on the map, not just the last hour. Below 50
        // the list felt disconnected from the heatmap density.
        //
        // verification is eager-loaded so the verification-gated photo
        // logic in templates/map-home.blade.php does not N+1 across the
        // 50-card panel.
        $reports = $crisis
            ? DamageReport::with('verification')
                ->where('crisis_id', $crisis->id)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->latest('submitted_at')
                ->limit(50)
                ->get()
            : collect();

        return view('templates.map-home', compact('crisis', 'reports'));
    }
}
