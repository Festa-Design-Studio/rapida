<?php

namespace App\Services;

use App\Models\DamageReport;
use Illuminate\Support\Facades\Log;

class DuplicateDetectionService
{
    public function checkForDuplicates(DamageReport $report): bool
    {
        if (! $report->photo_hash || ! $report->building_footprint_id) {
            return false;
        }

        // Check for same photo hash to same building within 1 hour
        $duplicate = DamageReport::where('id', '!=', $report->id)
            ->where('building_footprint_id', $report->building_footprint_id)
            ->where('photo_hash', $report->photo_hash)
            ->where('submitted_at', '>=', now()->subHour())
            ->exists();

        if ($duplicate) {
            $report->update(['is_flagged' => true]);
            Log::warning("Potential duplicate detected for report {$report->id}");

            return true;
        }

        // Check for same account submitting > 3 reports to same building in 24 hours
        if ($report->account_id) {
            $rapidSubmissions = DamageReport::where('account_id', $report->account_id)
                ->where('building_footprint_id', $report->building_footprint_id)
                ->where('submitted_at', '>=', now()->subDay())
                ->count();

            if ($rapidSubmissions > 3) {
                $report->update(['is_flagged' => true]);
                Log::warning("Rate limit flag: account {$report->account_id} submitted {$rapidSubmissions} reports to same building in 24h");

                return true;
            }
        }

        return false;
    }
}
