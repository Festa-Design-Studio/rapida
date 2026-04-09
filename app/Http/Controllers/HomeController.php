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
        $reports = $crisis
            ? DamageReport::where('crisis_id', $crisis->id)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->latest('submitted_at')
                ->limit(10)
                ->get()
            : collect();

        return view('templates.map-home', compact('crisis', 'reports'));
    }
}
