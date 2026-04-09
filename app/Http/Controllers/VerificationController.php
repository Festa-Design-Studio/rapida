<?php

namespace App\Http\Controllers;

use App\Models\DamageReport;
use App\Models\Verification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function flag(DamageReport $report): RedirectResponse
    {
        $report->update(['is_flagged' => true]);

        return back()->with('success', 'Report flagged for review.');
    }

    public function assign(Request $request, DamageReport $report): RedirectResponse
    {
        Verification::updateOrCreate(
            ['report_id' => $report->id],
            [
                'assigned_to' => auth('undp')->id(),
                'status' => 'in_field',
                'assigned_at' => now(),
            ]
        );

        return back()->with('success', 'Report assigned.');
    }

    public function verify(DamageReport $report): RedirectResponse
    {
        $report->verification?->update([
            'status' => 'verified',
            'verified_at' => now(),
        ]);
        $report->update(['is_flagged' => false]);

        return back()->with('success', 'Report verified.');
    }
}
