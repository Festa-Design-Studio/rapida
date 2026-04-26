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
        $reports = $crisis
            ? DamageReport::where('crisis_id', $crisis->id)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->latest('submitted_at')
                ->limit(50)
                ->get()
            : collect();

        return view('templates.map-home', compact('crisis', 'reports'));
    }
}
