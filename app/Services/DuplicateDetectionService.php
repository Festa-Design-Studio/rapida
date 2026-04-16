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

        // Check for visually similar photos via perceptual hash (Hamming distance <= 5)
        if ($report->photo_phash) {
            $similarByPHash = DamageReport::where('crisis_id', $report->crisis_id)
                ->where('id', '!=', $report->id)
                ->whereNotNull('photo_phash')
                ->where('submitted_at', '>=', now()->subHours(24))
                ->get()
                ->filter(function ($existing) use ($report) {
                    return $this->hammingDistance($report->photo_phash, $existing->photo_phash) <= 5;
                });

            if ($similarByPHash->isNotEmpty()) {
                $report->update(['is_flagged' => true]);

                return true;
            }
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

    private function hammingDistance(string $hash1, string $hash2): int
    {
        $distance = 0;
        for ($i = 0; $i < 16; $i++) {
            $xor = intval($hash1[$i], 16) ^ intval($hash2[$i], 16);
            $distance += substr_count(decbin($xor), '1');
        }

        return $distance;
    }
}
